<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot;

use Dev\WpContentAutopilot\Core\Services;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Main {

    private $store, $version;

    function __construct($store, $version = '1.0') {

        $this->store = $store;
        $this -> version = $version;
    }

    public function init() {
        global $wpdb;

        $this -> store -> log('Main:init()', '{STARTED}');

        $tables = array('_audits', '_jobs', '_metas', '_secrets', '_services', '_triggers');

        foreach($tables as $table) {
            
            $query = "SHOW TABLES LIKE '".dw_cp_PLUGIN_PREFIX.$table."'";

            $result = $wpdb->get_results( $query, 'ARRAY_A' );

            if( ! $result ) {
                $this -> store -> error('Main:init()', $wpdb->last_error);
                return False;
            }
        }

        add_filter( 'cron_schedules', array( $this, 'content_pilot_add_cron_interval') );

        $services = array(
            Features\Dashboard:: class,
            Features\Secret:: class,
            Features\Meta:: class,
            Features\Job:: class,
            Features\CronJob:: class
        );

        $_service = new Services($this -> store, $services);
        return $_service -> register();

    }

    public function admin_enqueue() {

        $this -> store -> log('Main:admin_enqueue()', '{STARTED}');

        $regex = "/^".dw_cp_PLUGIN_SLUG.".*$/i";
        if ( ! isset( $_GET['page'] ) ) return;
        if ( ! preg_match($regex, $_GET['page']) ) return;

        wp_register_script(dw_cp_PLUGIN_SLUG . '-jquery3', 'https://code.jquery.com/jquery-3.3.1.min.js', array(), '3.3.1', true); // jQuery v3
        wp_enqueue_script(dw_cp_PLUGIN_SLUG . '-jquery3');
        wp_script_add_data(dw_cp_PLUGIN_SLUG . '-jquery3', array( 'integrity', 'crossorigin' ) , array( 'sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=', 'anonymous' ));

        wp_register_script(dw_cp_PLUGIN_SLUG . '-bootstrap.bundle.min', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array(), '5.1.3', true);
        wp_enqueue_script(dw_cp_PLUGIN_SLUG . '-bootstrap.bundle.min');
        wp_script_add_data(dw_cp_PLUGIN_SLUG . '-bootstrap.bundle.min', array( 'integrity', 'crossorigin' ) , array( ));

        wp_enqueue_style( 
            dw_cp_PLUGIN_SLUG . '-bootstrap.min', 
            'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', 
            array(), 
            '5.1.3', 
            'all'
        );

        wp_enqueue_script(dw_cp_PLUGIN_NAME . '-script.admin', dw_cp_PLUGIN_URL . 'assets/js/script.admin.js', array(), $this->version, true );
        wp_enqueue_style(dw_cp_PLUGIN_NAME . '-style.admin', dw_cp_PLUGIN_URL . 'assets/css/style.admin.css', array(), $this->version, 'all' );

    }


    public function content_pilot_add_cron_interval( $schedules ) { 
        global $wpdb;

        $this -> store -> log('Main:content_pilot_add_cron_interval()', '{STARTED}');
        
        $query = "SELECT * FROM " . dw_cp_PLUGIN_PREFIX . "_triggers WHERE disabled = 0 AND deleted = 0";
    
        $_result = $wpdb->get_results( $query, 'ARRAY_A' );
    
        foreach($_result as $_ => $row) {

            $name = dw_cp_PLUGIN_SLUG . '_' . $row['name'];

            $schedules[ $row['type'] ] = array(
                'interval' => $row['seconds'] + ( $row['minutes'] + ( $row['hours'] + $row['days'] * 24 ) * 60 ) * 60,
                'display'  => esc_html__( str_replace('_', ' ', $row['type']) ) 
            );

        }
    
        return $schedules;
    }
    
}