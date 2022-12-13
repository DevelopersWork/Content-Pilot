<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core\Pages;

use DW\ContentPilot\Lib\{WPPage};

class Secrets extends WPPage {

    public function __construct($parent_slug){
        parent::__construct();

        $this -> page = [
            ...$this -> page,
            'parent_slug' => $parent_slug,
            'callback' => array($this, 'callback')
        ];

    }

    public function rest_api_init(){

        $route = dw_cp_prefix.'api/v1';
        $endpoint = '/'.$this -> class_name;

        register_rest_route( $route, $endpoint, [
            'methods' => 'GET',
            'callback' => [ $this, 'handleGetRequest' ],
            'permission_callback' => [ $this, 'getRequestPermission' ]
        ] );
        register_rest_route( $route, $endpoint, [
            'methods' => 'POST',
            'callback' => [ $this, 'handlePostRequest' ],
            'permission_callback' => [ $this, 'postRequestPermission' ]
        ] );
    }

    public function handleGetRequest(){
        $response = ['GET RESPONSE'];
        // $firstname = get_option( 'wprk_settings_firstname' );
        // $lastname  = get_option( 'wprk_settings_lastname' );
        // $email     = get_option( 'wprk_settings_email' );

        return rest_ensure_response( $response );
    }
    public function getRequestPermission(){
        return True;
    }

    public function handlePostRequest(){
        return rest_ensure_response( 'success' );
    }
    public function postRequestPermission(){
        return current_user_can( 'publish_posts' );
    }
}