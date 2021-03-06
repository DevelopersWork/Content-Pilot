<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Features;

use DW\ContentPilot\Lib\WPPage;

class Home extends WPPage
{

    public function __construct()
    {

        parent::__construct();

        $this -> addPage(array(
            'page_title' => dw_cp_plugin_name,
            'menu_title' => dw_cp_plugin_name,
            'capability' => 'manage_options',
            'menu_slug' => dw_cp_plugin_name,
            'icon_url' => 'dashicons-hammer',
            'position' => 22
        ));

        $categories = array('name' => 'Posts', 'value' => array('Default'));
        $this -> store -> set('categories', $categories);
    }
}
