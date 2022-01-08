<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot;

use Dev\WpContentAutopilot\Core\Services;

class Main {

    private $store;

    function __construct($store, $version = '0.0.1') {

        $this->store = $store;

        $this -> version = $version;
    
    }

    public function init() {

        add_filter( 'cron_schedules', array( $this, 'content_pilot_add_cron_interval') );

        $services = array(
            Features\Dashboard:: class,
            Features\Configuration:: class,
            Features\CronJob:: class
        );

        $_service = new Services($this -> store, $services);
        $_service -> register();

        add_action('admin_enqueue_scripts', array($this, 'enqueue'));

    }

    public function enqueue() {

        wp_enqueue_script(PLUGIN_NAME . ' Script', PLUGIN_URL . 'assets/js/script.js', array(), $this->version, 'all' );
        wp_enqueue_style(PLUGIN_NAME . ' Style', PLUGIN_URL . 'assets/css/style.css', array(), $this->version, 'all' );

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