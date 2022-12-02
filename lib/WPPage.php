<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

class WPPage {

    public $page;

    public function __construct(){
        $this -> page = [
            'page_title' => dw_cp_name, 
            'menu_title' => dw_cp_name, 
            'capability' => 'manage_options', 
            'menu_slug' => dw_cp_name
        ];

        add_action(dw_cp_prefix.'admin_menu', array($this, 'admin_menu'));

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }

    public function admin_menu(){
        if (!array_key_exists('parent_slug', $this -> page)) 
            add_menu_page(...$this -> page);
        else 
            add_submenu_page(...$this -> page);
    }

    public function callback(){
        // Use this to render the page tag which will be used by the react-app
        echo '<div class="wrap"><div id="dwcp-admin-root"></div></div>';
    }

    public function admin_enqueue_scripts(){}

}