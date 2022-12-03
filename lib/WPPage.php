<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

class WPPage {

    public $page;

    public function __construct(){

        $class_name = explode('\\', get_class($this));
        $class_name = array_pop($class_name);

        $this -> page = [
            'page_title' => $class_name, 
            'menu_title' => $class_name, 
            'capability' => 'manage_options', 
            'menu_slug' => strtolower(dw_cp_slug.$class_name),
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

    public function admin_enqueue_scripts(){
        $version = md5(
            dw_cp_json_version -> buildDate.
            dw_cp_json_version -> version.
            dw_cp_json_git -> branch.
            dw_cp_json_git -> commits.
            dw_cp_json_git -> hash
        );

        wp_enqueue_script('bundle.js', dw_cp_url.'build/bundle.js', ['jquery', 'wp-element'], $version, true);
        wp_localize_script('bundle.js', 'bundle_js', [
            'apiUrl' => home_url('/wp-json'),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    }

}