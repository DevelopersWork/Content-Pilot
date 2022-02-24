<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot;

use DW\ContentPilot\Lib\{ Validations, Activate, Deactivate };
use DW\ContentPilot\Core\{ Store };
// use Dev\ContentPilot\Core\Services;

class Main {

    private $store, $version;

    private $name;

    function __construct($version = '1.0', $name = 'DWContentPilot') {

        $this -> store = new Store();
        $this -> name = $name;
        $this -> version = $version;

        $this -> store -> log( get_class($this).':__construct()', '{STARTED}' );
    }

    public function admin_init() {
            
        $this -> store -> log( get_class($this).':admin_init()', '{STARTED}' );

        if ( !is_user_logged_in() ) return $this -> store -> log( get_class($this).':admin_init()', '{WP AUTH BROKEN}' );;
        
        $this -> register_scripts() -> register_styles() -> register_menus();

    }

    public function init() {

        $this -> store -> log( get_class($this).':init()', '{STARTED}' );

        $this -> compatibilityCheck() -> checkSQLTables();

        $this -> register_filters() -> register_actions();

    }

    private function checkSQLTables() {
        global $wpdb;

        $tables = array('triggers');
        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);

        foreach ($tables as $table) {
            $_result = $wpdb -> get_results( "SHOW TABLES LIKE '%".$table_prefix .'_'. $table."%'", 'ARRAY_A' );

            if(!$_result) {
                $this -> store -> set('admin_notice', array(
                    'msg' => 'Internal error occurred while checking the database tables', 
                    'type' => 'error', 
                    'domain' => 'sql-tables-dw-content-pilot'
                ));
                deactivate_plugins( plugin_basename( $this -> name ) );
                return add_action( 'admin_notices', array( $this -> store, 'admin_notice') );
            }
        }

        $this -> store -> log( get_class($this).':checkSQLTables()', '{ALL REQUIRED SQL TABLES EXIST}' );
        
        return $this;

    }

    private function compatibilityCheck() {

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
        
        $this -> store -> log( get_class($this).':compatibilityCheck()', 'PHP v'.$php_version_check.', Wordpress v'.$wp_version_check );
        return $this;
    }

    private function register_actions() {
            
        $this -> store -> log( get_class($this).':register_actions()', '{STARTED}' );

        add_action( 'admin_init', array( $this, 'admin_init' ) );

        return $this;

    }

    private function register_scripts(){

        $this -> store -> log( get_class($this).':register_scripts()', '{STARTED}' );

        $PLUGIN_URL = plugin_dir_url( $this -> name );

        // jQuery v3.3.1
        wp_register_script(DWContetPilotPrefix . '-jquery3', 'https://code.jquery.com/jquery-3.3.1.min.js', array(), '3.3.1', true);
        wp_script_add_data(DWContetPilotPrefix . '-jquery3', array( 'integrity', 'crossorigin' ) , array( 'sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=', 'anonymous' ));
        wp_enqueue_script(DWContetPilotPrefix . '-jquery3');
        // Bootstrap v5.1.3
        wp_register_script(DWContetPilotPrefix . '-bootstrap.bundle.min', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array(), '5.1.3', true);
        wp_script_add_data(DWContetPilotPrefix . '-bootstrap.bundle.min', array( 'integrity', 'crossorigin' ) , array( ));
        wp_enqueue_script(DWContetPilotPrefix . '-bootstrap.bundle.min');
        // Admin Script
        wp_enqueue_script(DWContetPilotPrefix . '-script.admin', $PLUGIN_URL . 'assets/js/script.admin.js', array(), $this -> version, true );

        return $this;
    
    }

    private function register_styles(){

        $this -> store -> log( get_class($this).':register_styles()', '{STARTED}' );

        $PLUGIN_URL = plugin_dir_url( $this -> name );

        // Bootstrap v5.1.3
        wp_enqueue_style(DWContetPilotPrefix . '-bootstrap.min', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', array(), '5.1.3', 'all');
        // Admin Style
        wp_enqueue_style(DWContetPilotPrefix . '-style.admin', $PLUGIN_URL . 'assets/css/style.admin.css', array(), $this->version, 'all' );

        return $this;
    
    }

    private function register_filters() {

        $this -> store -> log( get_class($this).':register_filters()', '{STARTED}' );

        add_filter( 'cron_schedules', array( $this, 'add_cron_triggers') );

        return $this;
    }

    private function register_menus() {

        $this -> store -> log( get_class($this).':register_menus()', '{STARTED}' );

    }

    // public function init() {
    //     global $wpdb;

    //     $this -> store -> log('Main:init()', '{STARTED}');

    //     $tables = array('_audits', '_jobs', '_metas', '_secrets', '_services', '_triggers');

    //     foreach($tables as $table) {
            
    //         $query = "SHOW TABLES LIKE '".dw_cp_PLUGIN_PREFIX.$table."'";

    //         $result = $wpdb->get_results( $query, 'ARRAY_A' );

    //         if( ! $result ) {
    //             $this -> store -> error('Main:init()', $wpdb->last_error);
    //             return False;
    //         }
    //     }

    //     add_filter( 'cron_schedules', array( $this, 'content_pilot_add_cron_interval') );

    //     // public Services doesn't required any authentication
    //     $services = array(
    //         Features\CronJob:: class
    //     );

    //     if ( is_user_logged_in() ) {
    //         // protected services require authentication
    //         $services = array_merge_recursive($services, array(
    //             Features\Dashboard:: class,
    //             Features\Secret:: class,
    //             Features\Meta:: class,
    //             Features\Job:: class
    //         ));

    //         // private services require authentication and authorisation
    //         $services = array_merge_recursive($services, array());
    //     }

    //     $_service = new Services($this -> store, $services);
    //     return $_service -> register();

    // }

    // public function admin_enqueue() {

    //     $this -> store -> log( get_class($this).':admin_enqueue()', '{STARTED}' );

    //     $regex = "/^". $this -> name .".*$/i";
    //     if ( ! isset( $_GET['page'] ) ) return;
    //     if ( ! preg_match($regex, $_GET['page']) ) return;

    //     wp_register_script(DWContetPilotPrefix . '-jquery3', 'https://code.jquery.com/jquery-3.3.1.min.js', array(), '3.3.1', true); // jQuery v3
    //     wp_enqueue_script(DWContetPilotPrefix . '-jquery3');
    //     wp_script_add_data(DWContetPilotPrefix . '-jquery3', array( 'integrity', 'crossorigin' ) , array( 'sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=', 'anonymous' ));

    //     wp_register_script(DWContetPilotPrefix . '-bootstrap.bundle.min', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array(), '5.1.3', true);
    //     wp_enqueue_script(DWContetPilotPrefix . '-bootstrap.bundle.min');
    //     wp_script_add_data(DWContetPilotPrefix . '-bootstrap.bundle.min', array( 'integrity', 'crossorigin' ) , array( ));

    //     wp_enqueue_style( 
    //         DWContetPilotPrefix . '-bootstrap.min', 
    //         'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', 
    //         array(), 
    //         '5.1.3', 
    //         'all'
    //     );

    //     wp_enqueue_script(DWContetPilotPrefix . '-script.admin', dw_cp_PLUGIN_URL . 'assets/js/script.admin.js', array(), $this->version, true );
    //     wp_enqueue_style(DWContetPilotPrefix . '-style.admin', dw_cp_PLUGIN_URL . 'assets/css/style.admin.css', array(), $this->version, 'all' );

    //     return $this;
    // }


    public function add_cron_triggers( $schedules ) { 

        $this -> store -> log( get_class($this).':add_cron_jobs()', '{STARTED}' );
        
        global $wpdb;

        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);
        
        $query = "SELECT * FROM " . $table_prefix . "_triggers WHERE disabled = 0 AND deleted = 0";
    
        $_result = $wpdb -> get_results( $query, 'ARRAY_A' );
    
        foreach($_result as $_ => $row) {

            $name = DWContetPilotPrefix . '_' . $row['name'];

            $schedules[ DWContetPilotPrefix . '_' .$row['type'] ] = array(
                'interval' => $row['seconds'] + ( $row['minutes'] + ( $row['hours'] + $row['days'] * 24 ) * 60 ) * 60,
                'display'  => esc_html__( str_replace('_', ' ', $row['type']) ) 
            );

        }
    
        return $schedules;
    }
    
}