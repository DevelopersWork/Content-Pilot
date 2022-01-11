<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot;

use Dev\WpContentAutopilot\Core\Services;

class Main {

    private $store, $version;

    function __construct($store, $version = '1.0') {

        $this->store = $store;
        $this -> version = $version;
    }

    public function init() {

        add_action('admin_enqueue_scripts', array($this, 'enqueue'));

        add_filter( 'cron_schedules', array( $this, 'content_pilot_add_cron_interval') );

        $services = array(
            Features\Dashboard:: class,
            Features\Job:: class,
            Features\Secret:: class,
            Features\CronJob:: class
        );

        $_service = new Services($this -> store, $services);
        $_service -> register();

    }

    public function enqueue() {

        wp_register_script('jquery3', 'https://code.jquery.com/jquery-3.3.1.min.js', array(), '3.3.1', true); // jQuery v3
        wp_enqueue_script('jquery3');
        wp_script_add_data('jquery3', array( 'integrity', 'crossorigin' ) , array( 'sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=', 'anonymous' ));

        wp_register_script('bootstrap.bundle.min.js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array(), '5.1.3', true);
        wp_enqueue_script('bootstrap.bundle.min.js');
        wp_script_add_data('bootstrap.bundle.min.js', array( 'integrity', 'crossorigin' ) , array( ));

        wp_enqueue_style('bootstrap.min.css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', array(), '5.1.3', 'all');

        wp_enqueue_script(PLUGIN_NAME . '_script.js', PLUGIN_URL . 'assets/js/script.js', array(), $this->version, 'all' );
        wp_enqueue_style(PLUGIN_NAME . '_style.css', PLUGIN_URL . 'assets/css/style.css', array(), $this->version, 'all' );

    }


    public function content_pilot_add_cron_interval( $schedules ) { 
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
        // wordpress database object
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $query = "SELECT * FROM " . PLUGIN_PREFIX . "_triggers WHERE disabled = 0";
    
        $_result = $wpdb->get_results( $query, 'ARRAY_A' );
    
        foreach($_result as $_ => $row) {

            $name = PLUGIN_SLUG . '_' . $row['name'];

            $schedules[ $row['type'] ] = array(
                'interval' => $row['seconds'] + ( $row['minutes'] + ( $row['hours'] + $row['days'] * 24 ) * 60 ) * 60,
                'display'  => esc_html__( str_replace('_', ' ', $row['type']) ) 
            );

        }
    
        return $schedules;
    }
    
}