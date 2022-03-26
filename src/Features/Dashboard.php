<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Features;

use DW\ContentPilot\Lib\WPPage;

class Dashboard extends WPPage
{

    private $parent;
    public $__FILE__;
    private $load_flag = true;

    function __construct($__FILE__ = 'DWContentPilot')
    {
        
        parent::__construct();

        $this -> __FILE__ = $__FILE__;

        $this -> parent = new Home();

        $this -> store -> log(get_class($this).':__construct()', '{STARTED}');

        $class_name = explode('\\', get_class($this));
        $class_name = array_pop($class_name);

        $_result = $this -> addSubPage(array(
            'parent_slug' => dw_cp_plugin_name,
            'page_title' => $class_name,
            'menu_title' => $class_name,
            'capability' => 'manage_options',
            'menu_slug' => dw_cp_plugin_name,
            'function' => array( $this, 'render_page' )
        ));

        if (!$_result) {
            $this -> load_flag = false;
            return $this -> store -> debug(get_class($this).':__construct()', '{FAILED}');
        }
    }

    public function register()
    {

        if (!$this -> load_flag) {
            return false;
        }
            
        $this -> store -> log(get_class($this).':register()', '{STARTED}');

        $this -> parent -> register();

        add_action(DWContetPilotPrefix.'register_actions', array( $this, 'register_actions'));
    }

    public function register_actions()
    {

        if (!$this -> load_flag) {
            return false;
        }

        add_action(DWContetPilotPrefix.'register_menus', array($this, 'register_page'));
    }
}
