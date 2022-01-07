<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Core\YouTube;

class Dashboard {

    private $store;

    function __construct( $store ) {

        $this -> store = $store;
    
    }

    public function register() {

        if (! $this -> store) return $this;

        $page = array(
			array(
				'page_title' => PLUGIN_NAME, 
				'menu_title' => PLUGIN_NAME, 
				'capability' => 'manage_options', 
				'menu_slug' => PLUGIN_SLUG, 
				'callback' => array( $this, 'render' ), 
				'icon' => 'dashicons-hammer', 
				'position' => 110
			)
		);

        $API = $this -> store -> get('SetupAPI');
        $api = new $API();
        $api -> addPages($page) -> asSubPage('Dashboard') -> register();

       
    }

    public function render(){
        // require_once PLUGIN_PATH . "/src/Pages/Dashboard.php";

        $yt = new YouTube($this -> store);
        $yt -> createService() -> makePost(0);

    }

}

