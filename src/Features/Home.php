<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Features;

use DW\ContentPilot\Core\{ Store };
use DW\ContentPilot\Lib\{ WPPage };

class Home extends WPPage {

    private $store;
    private $load_flag = True;

    function __construct( ) {

        $this -> store = new Store();
        $this -> store -> log( get_class($this).':__construct()', '{STARTED}' );

        parent::__construct();

        $_result = $this -> addPage (array(
            'page_title' => dw_cp_plugin_name, 
            'menu_title' => dw_cp_plugin_name, 
            'capability' => 'manage_options', 
            'menu_slug' => dw_cp_plugin_name, 
            'icon_url' => 'dashicons-hammer', 
            'position' => 22
        ));

        if(!$_result) {

            $this -> load_flag = false;
            return $this -> store -> debug( get_class($this).':__construct()', '{FAILED}' );

        }
    
    }

    public function register() {

        if(!$this -> load_flag) return false;
            
        $this -> store -> log( get_class($this).':register()', '{STARTED}' );

        add_action(DWContetPilotPrefix.'register_actions', array( $this, 'register_actions'));
    }

    public function register_actions() {

        if(!$this -> load_flag) return false;

        add_action(DWContetPilotPrefix.'register_menus', array($this, 'register_page'));
        
    }

}

