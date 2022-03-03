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

        $class_name = explode('\\', get_class($this));
        $class_name = array_pop($class_name);

        $slug = strtolower(str_replace('_', '-', DWContetPilotPrefix . '-' . $class_name));

        print($slug);

        $_result = $this -> addPage (array(
            'page_title' => 'Content Pilot', 
            'menu_title' => 'Content Pilot', 
            'capability' => 'manage_options', 
            'menu_slug' => $slug, 
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

