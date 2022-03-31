<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot;

use DW\ContentPilot\Lib\{
    Activate, Deactivate, Validations
};
use DW\ContentPilot\Core\{
    Store, Service, CronJob
};

class Main
{

    private $store;

    private $service;

    private $cronJob;

    private $menus = array();

    function __construct()
    {

        $this -> store = new Store();

        $this -> store -> debug(get_class($this).':__construct()', '{STARTED}');
    }

    public function admin_menu()
    {
            
        $this -> store -> debug(get_class($this).':admin_menu()', '{STARTED}');

        if (!is_user_logged_in()) {
            return $this -> store -> debug(get_class($this).':admin_menu()', '{WP AUTH BROKEN}');
        }
        
        $this -> register_scripts() -> register_styles() -> register_menus();
        
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
            
            $this -> register_actions() -> register_filters() -> register_post_types();

            add_action('admin_menu', array( $this, 'admin_menu' ));
            
            add_action('admin_init', array($this, 'admin_init'));
        }
    }

    public function admin_init()
    {
        $this -> store -> debug(get_class($this).':admin_init()', '{STARTED}');
    }

    private function compatibilityCheck()
    {

        $php_version_check = Validations::validate_php_version($this -> store);

        if (!$php_version_check) {
            return $php_version_check;
        }

        $wp_version_check = Validations::validate_wp_version($this -> store);

        if (!$wp_version_check) {
            return $wp_version_check;
        }

        $db_tables_check = Validations::checkSQLTables($this -> store);

        if (!$db_tables_check) {
            return $db_tables_check;
        }
        
        return $this;
    }

    private function register_actions()
    {
            
        $this -> store -> debug(get_class($this).':register_actions()', '{STARTED}');

        do_action(DWContetPilotPrefix.'register_actions');

        return $this;
    }

    private function register_post_types()
    {
            
        $this -> store -> debug(get_class($this).':register_post_types()', '{STARTED}');

        do_action(DWContetPilotPrefix.'register_post_types');

        return $this;
    }

    private function register_scripts()
    {

        $this -> store -> debug(get_class($this).':register_scripts()', '{STARTED}');

        do_action(DWContetPilotPrefix.'register_scripts');

        return $this;
    }

    private function register_styles()
    {

        $this -> store -> debug(get_class($this).':register_styles()', '{STARTED}');

        do_action(DWContetPilotPrefix.'register_styles');

        return $this;
    }

    private function register_filters()
    {

        $this -> store -> debug(get_class($this).':register_filters()', '{STARTED}');

        add_filter('cron_schedules', array( $this, 'add_cron_triggers'));

        do_action(DWContetPilotPrefix.'register_filters');

        return $this;
    }

    private function register_menus()
    {

        $this -> store -> debug(get_class($this).':register_menus()', '{STARTED}');

        do_action(DWContetPilotPrefix.'register_menus');
    }

    public function add_cron_triggers($schedules)
    {

        $this -> store -> debug(get_class($this).':add_cron_jobs()', '{STARTED}');
        
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