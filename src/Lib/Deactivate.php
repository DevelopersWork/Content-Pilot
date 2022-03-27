<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

use DW\ContentPilot\Core\Store;
use DW\ContentPilot\Lib\Validations;

class Deactivate
{

    private $store;
    private $name;

    public function __construct()
    {
        $this -> store = new Store();
    }

    public function deactivate()
    {
        $this -> store -> log(get_class($this).':deactivate()', '{STARTED}');

        flush_rewrite_rules();

        // this -> removeCronJobs();
    }

    public function removeCronJobs()
    {
        global $wpdb;

        $query = "
            SELECT 
                DISTINCT jobs.id AS job_id,
                triggers.name AS trigger_name, 
                triggers.type AS trigger_type, 
                jobs.hash AS job_hash
            FROM 
                ".dw_cp_PLUGIN_PREFIX."_jobs AS jobs
            JOIN 
                ".dw_cp_PLUGIN_PREFIX."_triggers AS triggers ON triggers.id = jobs.trigger_id
        ";

        try {
            $_result = $wpdb->get_results($query, 'ARRAY_A');
        } catch (Exception $e) {
            $_result = array();
        }

        foreach ($_result as $_ => $row) {
            $name = dw_cp_PLUGIN_SLUG . '_' . $row['trigger_name'] . '#' . $row['job_id'];

            wp_clear_scheduled_hook($name);
        }
    }
}
