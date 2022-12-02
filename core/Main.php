<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core;

use DW\ContentPilot\Core\Pages\{Dashboard};
use DW\ContentPilot\Lib\{WPPage};

class Main{

    private $wp_page;

    public function plugins_loaded(){
        // Adding an action to init
        add_action('init', array($this, 'init'));

        // Adding an action to wp_loaded
        add_action('wp_loaded', array($this, 'wp_loaded'));

        // Adding an action to admin_init
        add_action('admin_init', array($this, 'admin_init'));
    }

    public function init(){

        // checking everything is working
        // else necessary action need to be taken
        $this -> compatibilityCheck();

        // if user is logged in
        if (is_user_logged_in()){

            $this -> wp_page = new WPPage();

            // TODO: making roles dynamic
            // allowed roles
            $roles = array('administrator', 'author', 'editor');
            // Fetching current user object
            $user = wp_get_current_user();

            // checking if  current user has allowed roles
            if(array_intersect($roles, $user -> roles)){

                // if current request is for a user admin screen
                if(is_user_admin()){} else{}

                add_action('admin_menu', array($this, 'admin_menu'));

            } else {}

        }else {}

        // everything else
        // do_action(dw_cp_prefix.'register_actions');
        // do_action(dw_cp_prefix.'register_actions');
        // do_action(dw_cp_prefix.'register_actions');
        // do_action(dw_cp_prefix.'register_actions');
        // do_action(dw_cp_prefix.'register_actions');
        // do_action(dw_cp_prefix.'register_actions');

        // add_action('admin_init', array($this, 'adminInit'));
        
        
    }

    public function wp_loaded(){
        
    }

    public function admin_init(){
        do_action(dw_cp_prefix.'register_admin_pages');
    }

    private function compatibilityCheck(){
        // Validations related to
        // PHP Version
        // WP Version
        // MySQL Tables
    }

    public function admin_menu(){
        
        $root = new WPPage();
        $root -> page = [
            ...$root -> page,
            'menu_slug' => dw_cp_slug . 'home',
            'icon_url' => 'dashicons-hammer',
            'position' => 22
        ];

        new Dashboard();

        do_action(dw_cp_prefix.'admin_menu');
    }
}