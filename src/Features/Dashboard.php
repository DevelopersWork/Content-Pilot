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

        $post_type = array(
            'description' => 'Logs of the Content Pilot plugin',
            'public' => false,
            'has_archive' => true,
            'can_export' => false,
            'delete_with_user' => true,
            'exclude_from_search' =>  false,
            'show_in_rest' => false,
            'capability_type' =>  array( 'post', 'page' ),
            '__name' => dw_cp_plugin_name
        );
        $this -> store -> set('post_type', $post_type);

        $this -> store -> set('tabs', array('view'));
    }

    public function register()
    {

        $this -> parent -> register();

        parent::register();
    }

    public function view()
    {

        return array('tbody' => '', 'args' => '');
    }
}
