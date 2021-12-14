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
    }

    public static function createTables() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // wordpress database object
        global $wpdb;
        
        $table_prefix = $wpdb->dbname . '.' . $wpdb->prefix . 'ContentPilot_';
        $charset_collate = $wpdb->get_charset_collate();

        $path = PLUGIN_PATH . 'assets/ddl/';
        $ddls = array_diff(scandir($path), array('.', '..'));

        foreach($ddls as $ddl) {

            $sql = file_get_contents( $path . $ddl );
            $sql = str_replace( "%table_prefix%", $table_prefix, $sql );
            $sql = str_replace( "%charset_collate%", $charset_collate, $sql );

            dbDelta( $sql );
        }

    }

    public static function createViews() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // wordpress database object
        global $wpdb;
        
        $table_prefix = $wpdb->dbname . '.' . $wpdb->prefix . 'ContentPilot_';

        $path = PLUGIN_PATH . 'assets/sql_views/';
        $ddls = array_diff(scandir($path), array('.', '..'));

        foreach($ddls as $ddl) {

            $sql = file_get_contents( $path . $ddl );
            $sql = str_replace( "%table_prefix%", $table_prefix, $sql );

            $wpdb -> query( $sql );
        }

    }

    public static function loadReferenceData() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // wordpress database object
        global $wpdb;
        
        $table_prefix = $wpdb->dbname . '.' . $wpdb->prefix . 'ContentPilot_';

        $path = PLUGIN_PATH . 'assets/dml/';
        $dmls = array_diff(scandir($path), array('.', '..'));

        foreach ( $dmls as $dml ) {

            $queries = file_get_contents( $path . $dml );
            $queries = str_replace( "%table_prefix%", $table_prefix, $queries );
            
            foreach ( explode( '\n', $queries ) as $query ) {

                dbDelta( $query );

            }

        }

    }

}