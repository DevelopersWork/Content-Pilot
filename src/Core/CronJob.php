<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core;

use DW\ContentPilot\Core\Store;
use DW\ContentPilot\Lib\YouTube;

class CronJob
{

    private $store;

    public function __construct()
    {

        $this -> store = new Store();

        $this -> store -> debug(get_class($this).':__construct()', '{STARTED}');
    }

    public function register()
    {

        add_action(DWContetPilotPrefix.'register_actions', array( $this, 'registerActions'));

        return $this;
    }

    public function registerActions()
    {
        add_action(DWContetPilotPrefix.'register_post_types', array($this, 'schedule'));
    }

    public function schedule()
    {
        global $wpdb;

        $this -> store -> debug(get_class($this).':schedule()', '{STARTED}');

        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);

        $query_1 = "
            select 
                * 
            from 
                ".$wpdb -> base_prefix."postmeta 
            where meta_key like '%interval%'
        ";

        $query_2 = "
            select 
                j.id as job_id, jm.meta_value, md5(j.id) as job_hash 
            from 
                ".$wpdb -> base_prefix."posts as j 
            join (".$query_1.") jm on 
                    j.ID = jm.post_id and 
                    j.post_status = 'publish'
        ";

        $query = '
            SELECT 
                * 
            FROM 
                '.$table_prefix."_triggers as t 
            join (".$query_2.") j on 
                j.meta_value = t.id 
            where disabled <> 1 and deleted <> 1
        ";

        $result = $wpdb -> get_results("$query", 'ARRAY_A');

        foreach ($result as $_ => $row) {
            $name = esc_attr(DWContetPilotPrefix) . '_' . $row['name'] . '#' . $row['job_id'];

            $args = array ( $row['job_hash'] );

            add_action($name, array($this, 'run'));
            
            if (!wp_next_scheduled($name, $args)) {
                wp_schedule_event(time() + 3, DWContetPilotPrefix .'_'. $row['type'], $name, $args);
            }

            // $this -> run($row['job_hash']);
        }

        return $result;
    }


    public function run(string $job_hash)
    {
        global $wpdb;
        
        $this -> store -> log(get_class($this).':run()', $job_hash);

        $query_1 = "select meta_key, meta_value, post_id from ".$wpdb -> base_prefix."postmeta where md5(post_id) = '".$job_hash."'";

        $query = "select pm.meta_key, pm.meta_value, p.post_content, pm.post_id, p.post_author from ".$wpdb -> base_prefix."posts as p join (".$query_1.") pm on p.id = pm.post_id";

        $result = $wpdb -> get_results("$query", 'ARRAY_A');

        if (count($result) < 1) {
            return $this -> store -> error(get_class($this).':run('.$job_hash.')', '{JOB NOT FOUND}');
        }

        $post_id = $result[0]['post_id'];

        $service = explode(md5(DWContetPilotPrefix), $result[0]['post_content'])[0];
        $service = explode('=', $service);

        if (count($service) > 1) {
            $service = $service[1];
        } else {
            return $this -> store -> error(get_class($this).':run('.$post_id.')', '{JOB NOT FOUND}');
        }

        if (strtolower($service) == 'youtube') {
            $query = "select max(meta_value) as published from ".$wpdb -> base_prefix."postmeta where meta_key like '".$result[0]['post_id']."_yt_published_after'";
            $_result = $wpdb -> get_results("$query", 'ARRAY_A');

            $last_insert = "";
            if (count($_result) > 1) {
                $last_insert = $_result[0]['published'];
            }

            $meta = array(
                'secret' => '',
                'author' => $result[0]['post_author'],
                'yt_channel' => '',
                'yt_video' => '',
                'yt_keyword' => '',
                'yt_video_type' => '',
                'yt_published_after' => $last_insert ? $last_insert : '1970-01-01T00:00:00Z'
            );
            foreach ($result as $_ => $row) {
                $meta[$row['meta_key']] = $row['meta_value'];
            }

            if (!$meta['secret']) {
                return $this -> store -> error(get_class($this).':run('.$post_id.')', '{API KEY NOT FOUND}');
            }

            $results = YouTube:: getVideos($meta);

            if (!$results || !isset($results['items'])) {
                return $this -> store -> error(get_class($this).':run('.$post_id.')', '{NO NEW VIDEOS FOUND}');
            }

            $length = ($results['pageInfo']['resultsPerPage'] % 17) + 1;
            $ids = array();

            for ($i=0; $i < $length; $i++) {
                $item = $results['items'][$i];

                if (isset($item['id'])) {
                    array_push($ids, $item['id']['videoId']);
                }
            }

            $results = YouTube:: fetchVideo($ids, $meta);

            if (!$results || !isset($results['kind']) || !isset($results['item'])) {
                return $this -> store -> error(get_class($this).':run('.$post_id.')', '{YouTube API FAILED}');
            }

            foreach ($results['items'] as $item) {
                if (isset($item['id'])) {
                    $id = $item['id'];
                }

                if (isset($item['snippet'])) {
                    $snippet = $item['snippet'];
                }

                if (isset($item['statistics'])) {
                    $statistics = $item['statistics'];
                }

                YouTube:: makePost($id, $snippet, $statistics);
            }

        }

        return $result;
    }
}