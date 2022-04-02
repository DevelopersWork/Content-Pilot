<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Features;

use DW\ContentPilot\Lib\WPPage;

class Settings extends WPPage
{

    public function __construct()
    {

        parent::__construct();

        $this -> addSubPage(array(
            'parent_slug' => dw_cp_plugin_name,
            'page_title' => $this -> store -> get('name'),
            'menu_title' => $this -> store -> get('name'),
            'capability' => 'manage_options',
            'menu_slug' => DWContetPilotPrefix .'_'. $this -> store -> get('name'),
            'function' => array( $this, 'renderPagePath' )
        ));
    }
}
