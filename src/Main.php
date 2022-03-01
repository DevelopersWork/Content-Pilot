<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot;

use DW\ContentPilot\Lib\{ Validations, Activate, Deactivate };
use DW\ContentPilot\Core\{ Store, Service };

class Main {

    private $store, $version, $__FILE__;

    private $service;

    private $menus = array();

    function __construct($version = '1.0', $__FILE__ = 'DWContentPilot') {

        $this -> store = new Store();
        $this -> __FILE__ = $__FILE__;
        $this -> version = $version;

        $this -> store -> log( get_class($this).':__construct()', '{STARTED}' );
        
    }

    public function admin_init() {
            
        $this -> store -> log( get_class($this).':admin_init()', '{STARTED}' );

        if ( !is_user_logged_in() ) return $this -> store -> log( get_class($this).':admin_init()', '{WP AUTH BROKEN}' );
        
        $this -> register_scripts() -> register_styles() -> register_menus();

    }

    public function init() {

        $this -> store -> log( get_class($this).':init()', '{STARTED}' );

        if(!$this -> compatibilityCheck()) return;

        $this -> service = new Service($this -> __FILE__);

        add_action( 'wp_loaded', array($this, 'wp_loaded') );
    }

    public function wp_loaded() {
        $this -> register_actions() -> register_filters() -> register_post_types();
    }

    private function compatibilityCheck() {

        $php_version_check = Validations::validate_php_version($this -> store, $this -> __FILE__);

        if( !$php_version_check ) return $php_version_check;

        $wp_version_check = Validations::validate_wp_version($this -> store, $this -> __FILE__);

        if( !$wp_version_check ) return $wp_version_check;

        $db_tables_check = Validations::checkSQLTables($this -> store, $this -> __FILE__);

        if( !$db_tables_check ) return $db_tables_check;
        
        return $this;
    }

    private function register_actions() {
            
        $this -> store -> log( get_class($this).':register_actions()', '{STARTED}' );

        add_action( 'admin_init', array( $this, 'admin_init' ) );

        do_action(DWContetPilotPrefix.'register_actions');

        return $this;

    }

    private function register_post_types() {
            
        $this -> store -> log( get_class($this).':register_post_types()', '{STARTED}' );

        do_action(DWContetPilotPrefix.'register_post_types');

        return $this;
    
    }

    private function register_scripts(){

        $this -> store -> log( get_class($this).':register_scripts()', '{STARTED}' );

        $PLUGIN_URL = plugin_dir_url( $this -> __FILE__ );

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

        do_action(DWContetPilotPrefix.'register_scripts');

        return $this;
    
    }

    private function register_styles(){

        $this -> store -> log( get_class($this).':register_styles()', '{STARTED}' );

        $PLUGIN_URL = plugin_dir_url( $this -> __FILE__ );

        // Bootstrap v5.1.3
        wp_enqueue_style(DWContetPilotPrefix . '-bootstrap.min', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', array(), '5.1.3', 'all');
        // Admin Style
        wp_enqueue_style(DWContetPilotPrefix . '-style.admin', $PLUGIN_URL . 'assets/css/style.admin.css', array(), $this->version, 'all' );

        do_action(DWContetPilotPrefix.'register_styles');

        return $this;
    
    }

    private function register_filters() {

        $this -> store -> log( get_class($this).':register_filters()', '{STARTED}' );

        add_filter( 'cron_schedules', array( $this, 'add_cron_triggers') );

        do_action(DWContetPilotPrefix.'register_filters');

        return $this;
    }

    private function register_menus() {

        $this -> store -> log( get_class($this).':register_menus()', '{STARTED}' );

        do_action(DWContetPilotPrefix.'register_menus');

    }

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