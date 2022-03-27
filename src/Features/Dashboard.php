<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Features;

use DW\ContentPilot\Lib\WPPage;

class Dashboard extends WPPage
{

    private $parent;
    private $load_flag = true;

    function __construct()
    {
        
        parent::__construct();

        $this -> parent = new Home();

        $this -> store -> debug(get_class($this).':__construct()', '{STARTED}');

        $class_name = explode('\\', get_class($this));
        $class_name = array_pop($class_name);
        $this -> store -> set('name', $class_name);

        $this -> addSubPage(array(
            'parent_slug' => dw_cp_plugin_name,
            'page_title' => $this -> store -> get('name'),
            'menu_title' => $this -> store -> get('name'),
            'capability' => 'manage_options',
            'menu_slug' => dw_cp_plugin_name,
            'function' => array( $this, 'render_page' )
        ));

        if ($this -> store -> get('_ERROR')) {
            return $this -> store -> log(get_class($this).':__construct()', '{FAILED}');
        }
    }

    public function register()
    {

        if ($this -> store -> get('_ERROR')) {
            return false;
        }
            
        $this -> store -> debug(get_class($this).':register()', '{STARTED}');

        $this -> parent -> register();

        add_action(DWContetPilotPrefix.'register_actions', array( $this, 'register_actions'));
    }

    public function register_actions()
    {

        if ($this -> store -> get('_ERROR')) {
            return false;
        }

        add_action(DWContetPilotPrefix.'register_menus', array($this, 'register_page'));
    }
}
