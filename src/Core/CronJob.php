<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core;

use DW\ContentPilot\Core\Store;
use DW\ContentPilot\Lib\YouTube;
use DW\ContentPilot\Lib\RSS;

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
        
        $this -> store -> log(get_class($this).':run('.$job_hash.')', '{STARTED}');

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
            YouTube:: run($result);
        } elseif (strtolower($service) == 'rss') {
            RSS:: run($result);
        }

        return $result;
    }
}
