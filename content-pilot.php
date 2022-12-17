<?php

/**
 * Plugin Name
 *
 * @package           DWContentPilot
 * @author            DevelopersWork
 * @copyright         2022 DevelopersWork
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Content Pilot
 * Plugin URI:        https://github.com/DevelopersWork/Content-Pilot
 * Description:       Crawls internet to create automated posts.
 * Version:           0.1.1
 * Requires at least: 6.1.1
 * Requires PHP:      8.1
 * Author:            DevelopersWork
 * Author URI:        https://thedevelopers.work
 * Text Domain:       dw-content-pilot
 * License:           GPL v2 or later
 * License URI:       https://raw.githubusercontent.com/DevelopersWork/Content-Pilot/release/master/license.txt
 * Update URI:        https://github.com/DevelopersWork/Content-Pilot/tags
**/

// If someone already using the prefix hell and heaven with them
define('dw_cp_prefix', 'dwcp_');
// Constants
define('dw_cp_name', 'Content Pilot');
define('dw_cp_version', '0.1.1');
define('dw_cp_dir', plugin_dir_path(__FILE__));
define('dw_cp_url', plugin_dir_url(__FILE__));
define('dw_cp_base_name', plugin_basename(__FILE__));
define('dw_cp_slug', 'dw-cp-');

define('dw_cp_json_git', json_decode(file_get_contents(dw_cp_dir.'build/git.json')));
define('dw_cp_json_version', json_decode(file_get_contents(dw_cp_dir.'build/version.json')));

$error = FALSE;

// If Absolute Path is not defined no point in starting this script.
if (!defined('ABSPATH')) $error = TRUE;
else require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

// Require once the Composer Autoload
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
} else $error = TRUE;

use DW\ContentPilot\Core\Main;
// use DW\ContentPilot\Lib\{Activate, Deactivate};

class DWContentPilot
{

    public function __construct()
    {
        // Hook for the activation of the plugin
        // $_activate = new Activate();
        // register_activation_hook(__FILE__, array($_activate, 'activate'));

        
        // If plugin isn't active just stop here
        if (!is_plugin_active(dw_cp_base_name)) return;

        
        // Hook for the deactivation of the plugin
        // $_deactivate = new Deactivate();
        // register_deactivation_hook(__FILE__, array($_deactivate, 'deactivate'));

        // Adding an action to plugins_loaded
        add_action('plugins_loaded', array(new Main(), 'plugins_loaded'));
    }
}

// If there are no errors start the execution of the plugin
if (class_exists('DWContentPilot') && !$error) {

    new DWContentPilot();

}
