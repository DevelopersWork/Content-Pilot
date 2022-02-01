<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Activate {

    public static function activate() {

        if ( version_compare( PHP_VERSION, '5.4', '<=' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'This plugin requires PHP Version 5.4 or greater.  Sorry about that.', 'textdomain' ) );
        }

        flush_rewrite_rules();

        Activate:: createTables();
        Activate:: createViews();
        Activate:: loadReferenceData();
    }

    public static function createTables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();

        $path = PLUGIN_PATH . 'assets/ddl/';
        $ddls = array_diff(scandir($path), array('.', '..'));

        $regex = "/^.*\.(sql)$/i";

        $tables = array('services', 'triggers', 'secrets', 'metas', 'jobs', 'audits');

        try {

            for($i = 0; $i < count($tables); $i++) {
                $table = $tables[$i];
                $ddl = $table . '.sql';

                if ( ! in_array($ddl, $ddls, TRUE) ) continue;

                $sql = file_get_contents( $path . $ddl );
                $sql = str_replace( "%table_prefix%", PLUGIN_PREFIX, $sql );
                $sql = str_replace( "%charset_collate%", $charset_collate, $sql );

                dbDelta( $sql );

            }

        } catch(Exception $e) {
            file_put_contents('php://stderr', print($e->getMessage()));
        }

    }

    public static function createViews() {
        global $wpdb;

        $path = PLUGIN_PATH . 'assets/sql_views/';
        $ddls = array_diff(scandir($path), array('.', '..'));

        $regex = "/^.*\.(sql)$/i";

        try {

            foreach($ddls as $ddl) {

                if ( ! preg_match($regex, $ddl) ) continue;

                $sql = file_get_contents( $path . $ddl );
                $sql = str_replace( "%table_prefix%", PLUGIN_PREFIX, $sql );

                $wpdb -> query( $sql );
            }

        } catch(Exception $e) {
            file_put_contents('php://stderr', print($e->getMessage()));
        }

    }

    public static function loadReferenceData() {
        global $wpdb;

        $path = PLUGIN_PATH . 'assets/dml/';
        $dmls = array_diff(scandir($path), array('.', '..'));

        $regex = "/^.*\.(sql)$/i";
        
        try {

            foreach ( $dmls as $dml ) {

                if ( ! preg_match($regex, $dml) ) continue;

                $queries = file_get_contents( $path . $dml );
                $queries = str_replace( "%table_prefix%", PLUGIN_PREFIX, $queries );
                
                foreach ( explode( '\n', $queries ) as $query ) {

                    dbDelta( $query );

                }

            }

        } catch(Exception $e) {
            file_put_contents('php://stderr', print($e->getMessage()));
        }

    }

}