<?php
/** 
 * @package DevWPContentAutopilot
 */

/*
    Plugin Name: Content Pilot
    Description: This plugin is worst
    Version: 0.0.1
    Author: Developers@Work
    Author URI: https://developerswork.online
    License: GPLv2 or later
    Text Domain: dev-content-pilot
*/

// If Absolute Path is not defined no point in starting this script.
defined('ABSPATH') or die("This is gonna be a highlight real for sure...");

// Require once the Composer Autoload
if(file_exists(dirname(__FILE__).'/vendor/autoload.php')){
    require_once dirname(__FILE__).'/vendor/autoload.php';
}

use Dev\WpContentAutopilot\Main;
use Dev\WpContentAutopilot\Core\{Store};

class DevWPContentAutopilot {

    protected $process;

    public function __construct() {

        $store = new Store();

        $store->set('name', plugin_basename(__FILE__));

        $store->set('Google_Client', Google_Client:: class);
        $store->set('Google_Service_YouTube', Google_Service_YouTube:: class);
        
        $this->process = new Main($store, '0.0.1');
    }

    public function init() { $this->process->init(); }

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
if ( class_exists('DevWPContentAutopilot')) {
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    global $wpdb;

    define('PLUGIN_NAME', 'Content Pilot');
    define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
    define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    define( 'PLUGIN_SLUG', 'dev-content-pilot' );
    define( 'PLUGIN_PREFIX', $wpdb -> base_prefix . str_replace('-', '', PLUGIN_SLUG) );
    
    file_put_contents('php://stderr', print_r(PLUGIN_NAME . ": {STARTED}\n", TRUE));

    $devWPContentAutopilot = new DevWPContentAutopilot();

    register_activation_hook( __FILE__, 'onActivate' );

    register_deactivation_hook( __FILE__, 'onDeactivate' );

    add_action( 'init', array($devWPContentAutopilot, 'init') );

}
