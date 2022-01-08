<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class Activate {

    public static function activate() {
        flush_rewrite_rules();

        Activate:: createTables();
        Activate:: createViews();
        Activate:: loadReferenceData();
        Activate:: createCronJobs();
    }

    public static function createTables() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // wordpress database object
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();

        $path = PLUGIN_PATH . 'assets/ddl/';
        $ddls = array_diff(scandir($path), array('.', '..'));

        foreach($ddls as $ddl) {

            $sql = file_get_contents( $path . $ddl );
            $sql = str_replace( "%table_prefix%", PLUGIN_PREFIX, $sql );
            $sql = str_replace( "%charset_collate%", $charset_collate, $sql );

            dbDelta( $sql );
        }

    }

    public static function createViews() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // wordpress database object
        global $wpdb;

        $path = PLUGIN_PATH . 'assets/sql_views/';
        $ddls = array_diff(scandir($path), array('.', '..'));

        foreach($ddls as $ddl) {

            $sql = file_get_contents( $path . $ddl );
            $sql = str_replace( "%table_prefix%", PLUGIN_PREFIX, $sql );

            $wpdb -> query( $sql );
        }

    }

    public static function loadReferenceData() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // wordpress database object
        global $wpdb;

        $path = PLUGIN_PATH . 'assets/dml/';
        $dmls = array_diff(scandir($path), array('.', '..'));

        foreach ( $dmls as $dml ) {

            $queries = file_get_contents( $path . $dml );
            $queries = str_replace( "%table_prefix%", PLUGIN_PREFIX, $queries );
            
            foreach ( explode( '\n', $queries ) as $query ) {

                dbDelta( $query );

            }

        }

    }

    public static function createCronJobs() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // wordpress database object
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $query = "SELECT * FROM " . PLUGIN_PREFIX . "_triggers WHERE disabled = 0";

        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        foreach($_result as $_ => $row) {

            $name = PLUGIN_SLUG . '_' . $row['name'];

            if ( ! wp_next_scheduled( $name ) ) {

                wp_schedule_event( time() + 3, $row['type'], $name );

            }
        }

    }

}