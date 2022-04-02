<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot;

use DW\ContentPilot\Lib\Activate;
use DW\ContentPilot\Lib\Deactivate;
use DW\ContentPilot\Lib\Validations;
use DW\ContentPilot\Core\Store;
use DW\ContentPilot\Core\Service;
use DW\ContentPilot\Core\CronJob;

class Main
{

    private $store;

    private $service;

    private $cronJob;

    private $menus = array();

    public function __construct()
    {

        $this -> store = new Store();

        $this -> store -> debug(get_class($this).':__construct()', '{STARTED}');
    }

    public function adminMenu()
    {
            
        $this -> store -> debug(get_class($this).':adminMenu()', '{STARTED}');

        if (!is_user_logged_in()) {
            return $this -> store -> debug(get_class($this).':adminMenu()', '{WP AUTH BROKEN}');
        }
        
        $this -> registerScripts() -> registerStyles() -> registerMenus();
    }

    public function init()
    {

        $this -> store -> debug(get_class($this).':init()', '{STARTED}');

        if (!$this -> compatibilityCheck()) {
            return;
        }

        $this -> service = new Service();
        $this -> cronJob = new CronJob();

        if ($this -> service -> register() && $this -> cronJob -> register()) {
            $this -> registerActions() -> registerFilters() -> registerPostTypes();

            add_action('admin_menu', array( $this, 'adminMenu' ));
            
            add_action('admin_init', array($this, 'adminInit'));
        }
    }

    public function adminInit()
    {
        $this -> store -> debug(get_class($this).':adminInit()', '{STARTED}');
    }

    private function compatibilityCheck()
    {

        $php_version_check = Validations::validatePHPVersion($this -> store);

        if (!$php_version_check) {
            return $php_version_check;
        }

        $wp_version_check = Validations::validateWPVersion($this -> store);

        if (!$wp_version_check) {
            return $wp_version_check;
        }

        $db_tables_check = Validations::checkSQLTables($this -> store);

        if (!$db_tables_check) {
            return $db_tables_check;
        }
        
        return $this;
    }

    private function registerActions()
    {
            
        $this -> store -> debug(get_class($this).':registerActions()', '{STARTED}');

        do_action(DWContetPilotPrefix.'register_actions');

        return $this;
    }

    private function registerPostTypes()
    {
            
        $this -> store -> debug(get_class($this).':registerPostTypes()', '{STARTED}');

        do_action(DWContetPilotPrefix.'register_post_types');

        return $this;
    }

    private function registerScripts()
    {

        $this -> store -> debug(get_class($this).':registerScripts()', '{STARTED}');

        do_action(DWContetPilotPrefix.'register_scripts');

        return $this;
    }

    private function registerStyles()
    {

        $this -> store -> debug(get_class($this).':registerStyles()', '{STARTED}');

        do_action(DWContetPilotPrefix.'register_styles');

        return $this;
    }

    private function registerFilters()
    {

        $this -> store -> debug(get_class($this).':registerFilters()', '{STARTED}');

        add_filter('cron_schedules', array( $this, 'addCronTriggers'));

        do_action(DWContetPilotPrefix.'register_filters');

        return $this;
    }

    private function registerMenus()
    {

        $this -> store -> debug(get_class($this).':registerMenus()', '{STARTED}');

        do_action(DWContetPilotPrefix.'register_menus');
    }

    public function addCronTriggers($schedules)
    {

        $this -> store -> debug(get_class($this).':addCronTriggers()', '{STARTED}');
        
        global $wpdb;

        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);
        
        $query = "SELECT * FROM " . $table_prefix . "_triggers WHERE disabled = 0 AND deleted = 0";
    
        $_result = $wpdb -> get_results($query, 'ARRAY_A');
    
        foreach ($_result as $_ => $row) {
            $name = DWContetPilotPrefix . '_' . $row['name'];

            $schedules[ DWContetPilotPrefix . '_' .$row['type'] ] = array(
                'interval' => $row['seconds'] + ( $row['minutes'] + ( $row['hours'] + $row['days'] * 24 ) * 60 ) * 60,
                'display'  => esc_html__(str_replace('_', ' ', $row['type']))
            );
        }
    
        return $schedules;
    }
}
