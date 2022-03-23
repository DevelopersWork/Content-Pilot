<?php
/**
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\TriggerManager;

use Dev\WpContentAutopilot\Manager;

class TriggerManager
{

    private $store;

    function __construct($store)
    {

        $this -> store = $store;
    }

    public function register()
    {

        if (! $this -> store) {
            return $this;
        }

        $api -> addPages($page) -> asSubPage('Dashboard') -> register();

        $page = array(
            array(
                'page_title' => dw_cp_PLUGIN_NAME,
                'menu_title' => dw_cp_PLUGIN_NAME,
                'capability' => 'manage_options',
                'menu_slug' => dw_cp_PLUGIN_SLUG,
                'callback' => array( $this, 'render' ),
                'icon' => 'dashicons-hammer',
                'position' => 110
            )
        );

        $API = $this -> store -> get('SetupAPI');
        $api = new $API();
        $api -> addPages($page) -> asSubPage('Dashboard') -> register();
    }

    public function render()
    {
        // require_once dw_cp_PLUGIN_PATH . "/src/Pages/Dashboard.php";

        $yt = new YouTube($this -> store);
        $response = $yt -> createClient() -> createService() -> search();


        print_r($response);
    }
}
