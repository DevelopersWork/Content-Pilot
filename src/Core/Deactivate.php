<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Deactivate {

    public static function deactivate() {
        flush_rewrite_rules();

        Deactivate:: removeCronJobs();
    }

    public static function removeCronJobs() {
        global $wpdb;

        $query = "
            SELECT 
                DISTINCT jobs.id AS job_id,
                triggers.name AS trigger_name, 
                triggers.type AS trigger_type, 
                jobs.hash AS job_hash
            FROM 
                ".PLUGIN_PREFIX."_jobs AS jobs
            JOIN 
                ".PLUGIN_PREFIX."_triggers AS triggers ON triggers.id = jobs.trigger_id
        ";

        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        foreach($_result as $_ => $row) {

            $name = PLUGIN_SLUG . '_' . $row['trigger_name'] . '#' . $row['job_id'];

            wp_clear_scheduled_hook( $name );

        }

    }
}