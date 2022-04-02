<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core;

use DW\ContentPilot\Core\Store;
use DW\ContentPilot\Lib\{
    YouTube
};

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

        add_action(DWContetPilotPrefix.'register_actions', array( $this, 'register_actions'));

        return $this;
    }

    public function register_actions()
    {
        add_action(DWContetPilotPrefix.'register_post_types', array($this, 'schedule'));
    }

    public function schedule()
    {
        global $wpdb;

        $this -> store -> debug(get_class($this).':schedule()', '{STARTED}');

        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);

        $query_1 = "select * from ".$wpdb -> base_prefix."postmeta where meta_key like '%interval%'";

        $query_2 = "select j.id as job_id, jm.meta_value, md5(j.id) as job_hash from ".$wpdb -> base_prefix."posts as j join (".$query_1.") jm on j.ID = jm.post_id and j.post_status = 'publish'";

        $query = 'SELECT * FROM '.$table_prefix."_triggers as t join (".$query_2.") j on j.meta_value = t.id where disabled <> 1 and deleted <> 1";

        $result = $wpdb -> get_results("$query", 'ARRAY_A' );

        foreach ($result as $_ => $row) {

            $name = esc_attr(DWContetPilotPrefix) . '_' . $row['name'] . '#' . $row['job_id'];

            $args = array ( $row['job_hash'] );

            add_action($name, array($this, 'run'));
            
            // if ( !wp_next_scheduled($name, $args) ) 
            //     wp_schedule_event(time() + 3, DWContetPilotPrefix .'_'. $row['type'], $name, $args);

            $this -> run($row['job_hash']);

        }

        return $result;
    }

    public function run(string $job_hash)
    {
        global $wpdb;
        
        $this -> store -> debug(get_class($this).':run()', $job_hash);

        $query_1 = "select meta_key, meta_value, post_id from ".$wpdb -> base_prefix."postmeta where md5(post_id) = '".$job_hash."'";

        $query = "select pm.meta_key, pm.meta_value, p.post_content, pm.post_id from ".$wpdb -> base_prefix."posts as p join (".$query_1.") pm on p.id = pm.post_id";

        $result = $wpdb -> get_results("$query", 'ARRAY_A' );

        if(count($result) < 1)
            return null;

        $service = explode(md5(DWContetPilotPrefix), $result[0]['post_content'])[0];
        $service = explode('=', $service);

        if(count($service) > 1)
            $service = $service[1];
        else
            return null;

        if(strtolower($service) == 'youtube') {

            $meta = array(
                'secret' => '',
                'yt_channel' => '',
                'yt_video' => '',
                'yt_keyword' => '',
                'yt_video_type' => ''
            );
            foreach ($result as $_ => $row) 
                $meta[$row['meta_key']] = $row['meta_value'];

            $this -> store -> log(get_class($this).':run()', json_encode($meta));

            if(!$meta['secret'])
                return null;

            $results = YouTube:: getVideos($meta);

            $this -> store -> log(get_class($this).':run()', json_encode($results));
        }

        return $result;
    }
}