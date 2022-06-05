<?php
/** 
 * @package DWContentPilot
 */

/*
    Plugin Name: Content Pilot
    Description: Crawls internet to create automated posts
    Version: 0.1.1
    Author: Developers@Work
    Author URI: https://thedevelopers.work
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

define( 'dw_cp_plugin_name', 'Content Pilot');
define( 'dw_cp_plugin_version', '0.1.1');
define( 'dw_cp_plugin_dir_path', plugin_dir_path(__FILE__));
define( 'dw_cp_plugin_dir_url', plugin_dir_url(__FILE__));
define( 'dw_cp_plugin_base_name', plugin_basename(__FILE__));

class DWContentPilot {

    private $main;

    public function __construct() {

        $_activate = new Activate();
        register_activation_hook( __FILE__, array($_activate, 'activate') );

        $_deactivate = new Deactivate();
        register_deactivation_hook( __FILE__, array($_deactivate, 'deactivate') );

        if ( !is_plugin_active( dw_cp_plugin_base_name ) ) {
            return;
        }

        if(isset($_REQUEST[DWContetPilotPrefix.'_API'])) {
            
        } else {
            $this -> main = new Main();
            add_action( 'plugins_loaded', array($this, 'plugins_loaded') );
        }
    }

    public function plugins_loaded() {

        add_action( 'init', array($this -> main, 'init') );

        add_action( 'wp_loaded', array($this, 'wp_loaded') );

    }

    public function wp_loaded() {

    }

}

if ( class_exists('DWContentPilot') && $error == FALSE ) {

    $classes = array(
        'Google_Client' => Google_Client:: class,
        'Google_Service_YouTube' => Google_Service_YouTube:: class,
        'PHPHtmlParser' => PHPHtmlParser\Dom:: class
    );
    define( 'dw_cp_classes', $classes);

    new DWContentPilot();

}