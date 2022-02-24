<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

use DW\ContentPilot\Core\{ Store };
use DW\ContentPilot\Lib\{ Validations };

class Activate {

    private $store;
    private $name;

    public function __construct(string $__FILE__) {
        $this -> store = new Store();
        $this -> name = $__FILE__;
    }

    public function activate() {
        $this -> store -> log( get_class($this).':activate()', '{STARTED}' );

        $this -> compatibilityCheck();
        $this -> createSQLTables();
        $this -> loadReferenceData();

        flush_rewrite_rules();
    }

    private function createSQLTables() {
        global $wpdb;

        $this -> store -> log( get_class($this).':createSQLTables()', '{STARTED}' );

        $charset_collate = $wpdb->get_charset_collate();
        $plugin_path = plugin_dir_path( $this -> name );

        $ddl_path = $plugin_path . 'assets/ddl/';
        $ddls = array_diff(scandir($ddl_path), array('.', '..'));

        $regex = "/^.*\.(sql)$/i";

        $tables = array('triggers');

        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);


        for($i = 0; $i < count($tables); $i++) {
            $table = $tables[$i];
            $ddl = $table . '.sql';

            if ( ! in_array($ddl, $ddls, TRUE) ) continue;

            $sql = file_get_contents( $ddl_path . $ddl );
            $sql = str_replace( "%table_prefix%", $table_prefix, $sql );
            $sql = str_replace( "%charset_collate%", $charset_collate, $sql );

            dbDelta( $sql );

        }
    }

    private function compatibilityCheck() {

        $this -> store -> log( get_class($this).':compatibilityCheck()', '{STARTED}' );

        $php_version_check = Validations::validate_php_version();

        if( !$php_version_check ) {
            $this -> store -> set('admin_notice', array(
                'msg' => 'Plugin requires PHP 7.4 or higher!', 
                'type' => 'error', 
                'domain' => 'activate-dw-content-pilot'
            ));

            deactivate_plugins( plugin_basename( $this -> name ) );
            
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }

            return add_action( 'admin_notices', array( $this -> store, 'admin_notice') );
        }

        $wp_version_check = Validations::validate_wp_version();

        if( !$wp_version_check ) {
            $this -> store -> set('admin_notice', array(
                'msg' => 'Plugin requires Wordpress 5.9 or higher!', 
                'type' => 'error', 
                'domain' => 'activate-dw-content-pilot'
            ));

            deactivate_plugins( plugin_basename( $this -> name ) );
            
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }

            return add_action( 'admin_notices', array( $this -> store, 'admin_notice') );
        }

        return $this -> store -> log( get_class($this).':compatibilityCheck()', 'PHP v'.$php_version_check.', Wordpress v'.$wp_version_check );
    }

    private function loadReferenceData() {
        global $wpdb;

        $this -> store -> log( get_class($this).':loadReferenceData()', '{STARTED}' );

        $charset_collate = $wpdb->get_charset_collate();
        $plugin_path = plugin_dir_path( $this -> name );

        $path = $plugin_path . 'assets/dml/';
        $dmls = array_diff(scandir($path), array('.', '..'));

        $regex = "/^.*\.(sql)$/i";

        $tables = array('triggers');

        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);
        

        for($i = 0; $i < count($tables); $i++) {

            $table = $tables[$i];
            $dml = $table . '.sql';

            if ( ! preg_match($regex, $dml) ) continue;

            $queries = file_get_contents( $path . $dml );
            $queries = str_replace( "%table_prefix%", $table_prefix, $queries );
            
            foreach ( explode( '\n', $queries ) as $query ) {

                dbDelta( $query );

            }

        }

    }



}