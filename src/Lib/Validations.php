<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

class Validations
{

    public static function validate_php_version($store, $__FILE__, $version = '7.4')
    {

        $notice = array(
            'msg' => 'Plugin requires PHP v'.$version.' or higher!',
            'type' => 'error',
            'domain' => 'activate-dw-content-pilot'
        );

        if (!defined('PHP_VERSION')) {
            $store -> set('admin_notice', $notice);
            
            deactivate_plugins(plugin_basename($__FILE__));

            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }

            add_action('admin_notices', array( $store, 'admin_notice'));
            
            return false;
        }

        if (version_compare(PHP_VERSION, $version, '<=')) {
            $store -> set('admin_notice', $notice);
            
            deactivate_plugins(plugin_basename($__FILE__));
            
            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
            
            add_action('admin_notices', array( $store, 'admin_notice'));
            
            return false;
        }

        return PHP_VERSION;
    }

    public static function validate_wp_version($store, $__FILE__, $version = '5.9')
    {

        $notice = array(
            'msg' => 'Plugin requires Wordpress v'.$version.' or higher!',
            'type' => 'error',
            'domain' => 'activate-dw-content-pilot'
        );

        if (!isset($GLOBALS['wp_version'])) {
            $store -> set('admin_notice', $notice);
            
            deactivate_plugins(plugin_basename($__FILE__));

            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }

            add_action('admin_notices', array( $store, 'admin_notice'));
            
            return false;
        }

        if (version_compare($GLOBALS['wp_version'], $version, '<')) {
            $store -> set('admin_notice', $notice);
            
            deactivate_plugins(plugin_basename($__FILE__));

            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }

            add_action('admin_notices', array( $store, 'admin_notice'));
            
            return false;
        }

        return $GLOBALS['wp_version'];
    }

    public static function checkSQLTables($store, $__FILE__)
    {
        global $wpdb;

        $tables = array('triggers');
        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);

        foreach ($tables as $table) {
            $_result = $wpdb -> get_results("SHOW TABLES LIKE '%".$table_prefix .'_'. $table."%'", 'ARRAY_A');

            if (!$_result) {
                $store -> set('admin_notice', array(
                    'msg' => 'Internal error occurred while checking the database tables',
                    'type' => 'error',
                    'domain' => 'sql-tables-dw-content-pilot'
                ));
                
                deactivate_plugins(plugin_basename($__FILE__));
                
                if (isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
                
                add_action('admin_notices', array( $store, 'admin_notice'));

                return false;
            }
        }
        
        return true;
    }
}
