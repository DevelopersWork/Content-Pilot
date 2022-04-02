<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Features;

use DW\ContentPilot\Lib\WPPage;
use DW\ContentPilot\Features\Home;

class Dashboard extends WPPage
{

    private $parent;

    public function __construct()
    {
        
        parent::__construct();

        $this -> parent = new Home();

        $this -> addSubPage(array(
            'parent_slug' => dw_cp_plugin_name,
            'page_title' => $this -> store -> get('name'),
            'menu_title' => $this -> store -> get('name'),
            'capability' => 'manage_options',
            'menu_slug' => dw_cp_plugin_name,
            'function' => array( $this, 'renderPage' )
        ));
    }

    public function register()
    {

        $this -> parent -> register();

        parent::register();
    }
}
