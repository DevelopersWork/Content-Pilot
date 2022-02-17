<?php
/** 
 * @package DWContentPilot
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

$error = false;

// If Absolute Path is not defined no point in starting this script.
if( ! defined('ABSPATH') ) $error = true;
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

// Require once the Composer Autoload
if ( file_exists(dirname(__FILE__).'/vendor/autoload.php' ) ){
    require_once dirname(__FILE__).'/vendor/autoload.php';
} else $error = true;

use DW\ContentPilot\Main;
use DW\ContentPilot\Lib\{ Activate, Deactivate };

// If someone already using the prefix hell and heaven with them
if( defined('DWContetPilotPrefix') ) $error = true;
else define('DWContetPilotPrefix', 'dw_cp');

class DWContentPilot {

    private $main;

    public function __construct() {

        $_activate = new Activate(__FILE__);
        register_activation_hook( __FILE__, array($_activate, 'activate') );

        $_deactivate = new Deactivate(__FILE__);
        register_deactivation_hook( __FILE__, array($_deactivate, 'deactivate') );

        if ( !is_plugin_active( plugin_basename( __FILE__ ) ) ) {
            return;
        }

        $this -> main = new Main( '0.1.1', __FILE__ );

        // global $wpdb;
        // $wpdb->show_errors();

        // define( 'dw_cp_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
        // define( 'dw_cp_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        // define( 'dw_cp_PLUGIN_SLUG', 'dw-content-pilot' );
        // define( 'dw_cp_PLUGIN_PREFIX', $wpdb -> base_prefix . str_replace('-', '', dw_cp_PLUGIN_SLUG) );

        // $this -> store = new Store();

        // $this -> store -> log('DevWPContentAutopilot:__construct()', '{STARTED}');

        // $this -> store->set('name', plugin_basename(__FILE__));

        // $this -> store->set('Google_Client', Google_Client:: class);
        // $this -> store->set('Google_Service_YouTube', Google_Service_YouTube:: class);
        
        

        add_action( 'plugins_loaded', array($this, 'init') );
    }

    public function init() {
        $this -> main -> compatibilityCheck() -> checkSQLTables();

        add_action( 'init', array($this -> main, 'init') );
    }

}

/**
 * The code that runs during plugin activation
 */
// function onActivate() {

// 	Dev\WpContentAutopilot\Core\Activate:: activate();

// }

/**
 * The code that runs during plugin deactivation
 */
// function onDeactivate() {
// 	Dev\WpContentAutopilot\Core\Deactivate:: deactivate();
// }

/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists('DWContentPilot') && $error == FALSE ) {

    new DWContentPilot();

}
