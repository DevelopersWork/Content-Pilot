<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class Deactivate {

    public static function deactivate() {
        flush_rewrite_rules();

        Deactivate:: removeCronJobs();
    }

    public static function removeCronJobs() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // wordpress database object
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $query = "SELECT * FROM " . PLUGIN_PREFIX . "_triggers";

        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        foreach($_result as $_ => $row) {

            $name = PLUGIN_SLUG . '_' . $row['name'];
            $timestamp = wp_next_scheduled( $name );

            if ( $timestamp ) {

                wp_unschedule_event( $timestamp, $name );

            }
        }

    }
}