<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Features;

use DW\ContentPilot\Lib\WPPage;

class Home extends WPPage
{

    function __construct()
    {

        parent::__construct();
        
        $this -> store -> debug(get_class($this).':__construct()', '{STARTED}');

        $this -> addPage(array(
            'page_title' => dw_cp_plugin_name,
            'menu_title' => dw_cp_plugin_name,
            'capability' => 'manage_options',
            'menu_slug' => dw_cp_plugin_name,
            'icon_url' => 'dashicons-hammer',
            'position' => 22
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

        add_action(DWContetPilotPrefix . 'register_actions', array( $this, 'register_actions'));
    }

    public function register_actions()
    {

        if ($this -> store -> get('_ERROR')) {
            return false;
        }

        add_action(DWContetPilotPrefix . 'register_menus', array($this, 'register_page'));
    }
}
