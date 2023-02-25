<?php

/**
 * @package DWContentPilot
 */

namespace DW\ContentPilot\Lib;

use \WP_REST_Request as WP_REST_Request;
use \WP_Error as WP_Error;

class WPPage
{

    public $page, $class_name;

    public function __construct()
    {

        $this->class_name = explode('\\', get_class($this));
        $this->class_name = array_pop($this->class_name);

        $this->page = [
            'page_title' => $this->class_name,
            'menu_title' => $this->class_name,
            'capability' => 'manage_options',
            'menu_slug' => strtolower(dw_cp_slug . $this->class_name),
        ];

        $this->admin_hooks();
    }

    private function admin_hooks()
    {
        // calls the function when hook is triggered

        // register the custom post types hook
        add_action(dw_cp_prefix . 'register_post_type', array($this, 'register_post_type'));

        // register the custom categories types hook
        add_action(dw_cp_prefix . 'register_categories', array($this, 'register_categories'));

        // register the custom api endpoints hook
        add_action('rest_api_init', array($this, 'rest_api_init'));

        // register the custom scripts and styles hook
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        // register the admin menu pages hook
        add_action(dw_cp_prefix . 'admin_menu', array($this, 'admin_menu'));
    }

    public function admin_menu()
    {
        if (!array_key_exists('parent_slug', $this->page))
            add_menu_page(...$this->page);
        else
            add_submenu_page(...$this->page);
    }

    public function menu_page_callback()
    {
        // Use this to render the page tag which will be used by the react-app
        echo '<div class="wrap"><div id="dwcp-admin-root"></div></div>';
    }

    public function admin_enqueue_scripts()
    {
        if (!$this->isCurrentPage()) return $this;

        $version = md5(
            dw_cp_json_version->buildDate .
                dw_cp_json_version->version .
                dw_cp_json_git->branch .
                dw_cp_json_git->commits .
                dw_cp_json_git->hash
        );

        wp_register_script(dw_cp_prefix . 'main', dw_cp_url . 'build/main.js', ['jquery', 'wp-element'], $version, true);
        wp_localize_script(dw_cp_prefix . 'main', dw_cp_prefix . 'app', [
            'apiUrl' => home_url('/wp-json') . '/' . dw_cp_prefix . 'api/v1/',
            'nonce' => wp_create_nonce('wp_rest'),
            'dwcp_nonce' => wp_create_nonce(dw_cp_prefix . strtolower($this->class_name))
        ]);
        wp_enqueue_script(dw_cp_prefix . 'main');

        return $this;
    }

    public function admin_enqueue_styles()
    {
        if (!$this->isCurrentPage()) return $this;

        $version = md5(
            dw_cp_json_version->buildDate .
                dw_cp_json_version->version .
                dw_cp_json_git->branch .
                dw_cp_json_git->commits .
                dw_cp_json_git->hash
        );

        wp_register_style(dw_cp_prefix . 'main', dw_cp_url . 'build/css/main.css', array(), $version, 'all');
        wp_enqueue_style(dw_cp_prefix . 'main');

        return $this;
    }

    public function rest_api_init()
    {
        $route = dw_cp_prefix . 'api/v1';
        $endpoint = '/' . strtolower($this->class_name);

        $this->register_post_type();
        $this->register_categories();

        register_rest_route($route, $endpoint, [
            'methods' => 'GET',
            'permission_callback' => [$this, 'getRequestPermission']
        ]);

        register_rest_route($route, $endpoint, [
            'methods' => 'POST',
            'permission_callback' => [$this, 'postRequestPermission']
        ]);
    }

    public function getRequestPermission(WP_REST_Request $request)
    {
        if (!is_user_logged_in())
            return new WP_Error('user_not_logged_in', __('You are not currently logged in.'), array('status' => 401));

        if (!current_user_can('read'))
            return new WP_Error('user_not_authorised', __('You are not currently authorised to access the content.'), array('status' => 403));

        return true;
    }

    public function postRequestPermission(WP_REST_Request $request)
    {

        if (!is_user_logged_in())
            return new WP_Error('user_not_logged_in', __('You are not currently logged in.'), array('status' => 401));

        if (!current_user_can('publish_posts'))
            return new WP_Error('user_not_authorised', __('You are not currently authorised to access the content.'), array('status' => 403));

        $nonce = $request->get_header('X-DWCP-Nonce');
        if (!$nonce || !wp_verify_nonce($nonce, dw_cp_prefix . strtolower($this->class_name)))
            return new WP_Error('rest_forbidden', __('Invalid request'), array('status' => 403));

        return true;
    }

    public function register_post_type()
    {
    }

    public function register_categories()
    {
    }

    private function isCurrentPage()
    {
        return (isset($_GET['page']) && $_GET['page'] == $this->page['menu_slug']);
    }
}
