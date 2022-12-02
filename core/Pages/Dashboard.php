<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core\Pages;

use DW\ContentPilot\Lib\{WPPage};

class Dashboard extends WPPage {

    public function __construct(){
        parent::__construct();

        $this -> page = [
            ...$this -> page,
            'parent_slug' => dw_cp_slug.'home',
            'page_title' => 'Dashboard',
            'menu_title' => 'Dashboard',
            'menu_slug' => dw_cp_slug.'home',
            'callback' => array($this, 'callback')
        ];
    }

    // public function admin_menu(){
    //     // do_action(dw_cp_prefix.'register_admin_scripts');

    //     add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    // }

    public function admin_enqueue_scripts(){
        wp_enqueue_script('bundle.js', dw_cp_url.'build/bundle.js', ['jquery', 'wp-element'], 'abc', true);
        wp_localize_script('bundle.js', 'bundle_js', [
            'apiUrl' => home_url('/wp-json'),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    }
}