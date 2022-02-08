<?php
/** 
 * @package DevWPContentAutopilot
 */

/*
    Plugin Name: Content Pilot
    Description: This plugin is worst
    Version: 0.1.1
    Author: Developers@Work
    Author URI: https://developerswork.online
    License: GPLv2 or later
    Text Domain: dw-content-pilot
*/

$CORRUPTED = FALSE;

// If someone already using my prefix hell with them
if( defined('ContetPilotPrefix') ) $CORRUPTED = TRUE;
else define('ContetPilotPrefix', 'dw_cp_');

// If Absolute Path is not defined no point in starting this script.
if( ! defined('ABSPATH') ) $CORRUPTED = TRUE;

// Require once the Composer Autoload
if ( file_exists(dirname(__FILE__).'/vendor/autoload.php' ) ){
    require_once dirname(__FILE__).'/vendor/autoload.php';
} else $CORRUPTED = TRUE;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

use Dev\WpContentAutopilot\Main;
use Dev\WpContentAutopilot\Core\{Store};

class DevWPContentAutopilot {

    protected $process;
    private $store;

    public function __construct() {

        global $wpdb;
        $wpdb->show_errors();

        define( 'dw_cp_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
        define( 'dw_cp_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        define( 'dw_cp_PLUGIN_SLUG', 'dw-content-pilot' );
        define( 'dw_cp_PLUGIN_PREFIX', $wpdb -> base_prefix . str_replace('-', '', dw_cp_PLUGIN_SLUG) );

        $this -> store = new Store();

        $this -> store -> log('DevWPContentAutopilot:__construct()', '{STARTED}');

        $this -> store->set('name', plugin_basename(__FILE__));

        $this -> store->set('Google_Client', Google_Client:: class);
        $this -> store->set('Google_Service_YouTube', Google_Service_YouTube:: class);
        
        $this->process = new Main($this -> store, dw_cp_PLUGIN_VERSION);

        register_activation_hook( __FILE__, 'onActivate' );

        register_deactivation_hook( __FILE__, 'onDeactivate' );
    }

    public function init() { 
        
        $this -> store -> log('DevWPContentAutopilot:init()', '{STARTED}');

        add_action( 'admin_enqueue_scripts', array($this->process, 'admin_enqueue') );

        if( is_plugin_active(plugin_basename(__FILE__)) ) $this->process->init(); 
    }

}

/**
 * The code that runs during plugin activation
 */
function onActivate() {

	Dev\WpContentAutopilot\Core\Activate:: activate();

}

/**
 * The code that runs during plugin deactivation
 */
function onDeactivate() {
	Dev\WpContentAutopilot\Core\Deactivate:: deactivate();
}

/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists('DevWPContentAutopilot') && $CORRUPTED == FALSE ) {
    
    define('dw_cp_PLUGIN_NAME', 'Content Pilot');
    define('dw_cp_PLUGIN_VERSION', '0.1.1');
    
    file_put_contents('php://stdout', print_r(plugin_basename(__FILE__) . ":: {STARTED}\n", TRUE));

    $devWPContentAutopilot = new DevWPContentAutopilot();
    
    add_action( 'init', array($devWPContentAutopilot, 'init') );

}
