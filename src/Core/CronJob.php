<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core;

use DW\ContentPilot\Core\Store;

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

        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);

        $query_1 = "select * from ".$wpdb -> base_prefix."postmeta where meta_key like '%interval%'";

        $query_2 = "select j.id as job_id, jm.meta_value, md5(j.id) as job_hash from ".$wpdb -> base_prefix."posts as j join (".$query_1.") jm on j.ID = jm.post_id";

        $query = 'SELECT * FROM '.$table_prefix."_triggers as t join (".$query_2.") j on j.meta_value = t.id where disabled <> 1 and deleted <> 1";

        $result = $wpdb -> get_results("$query", 'ARRAY_A' );

        foreach ($result as $_ => $row) {

            $name = esc_attr(DWContetPilotPrefix) . '_' . $row['name'] . '#' . $row['job_id'];

            $args = array ( $row['job_hash'] );

            add_action($name, array($this, 'run'));
            
            if (! wp_next_scheduled($name, $args)) {
                wp_schedule_event(time() + 3, $row['type'], $name, $args);
            }
        }

        return $result;
    }

    public function run(string $job_hash)
    {
        print('well done');
    }
}