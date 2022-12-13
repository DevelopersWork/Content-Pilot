<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

class WPPage {

    public $page, $class_name;

    public function __construct(){

        $this -> class_name = explode('\\', get_class($this));
        $this -> class_name = array_pop($this -> class_name);

        $this -> page = [
            'page_title' => $this -> class_name, 
            'menu_title' => $this -> class_name, 
            'capability' => 'manage_options', 
            'menu_slug' => strtolower(dw_cp_slug.$this -> class_name),
        ]; 
        
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action( 'rest_api_init', array($this, 'rest_api_init'));

        add_action(dw_cp_prefix.'admin_menu', array($this, 'admin_menu'));
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

        wp_register_script(dw_cp_prefix.'bundle', dw_cp_url.'build/bundle.js', ['jquery', 'wp-element'], $version, true);
        wp_localize_script(dw_cp_prefix.'bundle', dw_cp_prefix.'app', [
            'apiUrl' => home_url('/wp-json'),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
        wp_enqueue_script(dw_cp_prefix.'bundle');

    }

    public function admin_enqueue_styles(){
        $version = md5(
            dw_cp_json_version -> buildDate.
            dw_cp_json_version -> version.
            dw_cp_json_git -> branch.
            dw_cp_json_git -> commits.
            dw_cp_json_git -> hash
        );

        wp_register_style(dw_cp_prefix.'main', dw_cp_url.'build/css/main.css', array(), $version, 'all');
        wp_enqueue_style(dw_cp_prefix.'main');
    }

    public function rest_api_init(){}

}