<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core\Pages;

use DW\ContentPilot\Lib\{WPPage};

class Credentials extends WPPage {

    private $post_type, $categories;

    public function __construct($parent_slug){
        parent::__construct();

        $this -> page = [
            ...$this -> page,
            'parent_slug' => $parent_slug,
            'callback' => array($this, 'menu_page_callback')
        ];

    }

    private function add($name, $value, $categories = array()){
        
        $title = wp_strip_all_tags( $name );
        $author_id = get_current_user_id();

        $args = array(
            'post_name'    => md5($title.$author_id),
            'post_title'    => $title,
            'post_content'  => $value,
            'post_status'   => 'publish',
            'post_author'   => $author_id,
            'post_type'     => strtolower(dw_cp_prefix.$this -> class_name),
            'post_category' => $categories
        );

        return wp_insert_post($args);
    }

    public function register_post_type(){
        $this -> post_type = register_post_type(
            strtolower(dw_cp_prefix.$this -> class_name), 
            array(
                'description' => 'Credentials',
                'public' => false,
                'has_archive' => true,
                'can_export' => false,
                'delete_with_user' => true,
                'exclude_from_search' =>  true,
                'show_in_rest' => false,
                'capability_type' =>  'post'
            )
        );
    }

    public function register_categories(){
        $parent_category = wp_create_category(strtolower(dw_cp_prefix.$this -> class_name));
        $this -> categories = array(
            'YouTube' => wp_create_category('YouTube', $parent_category),
            'RSS' => wp_create_category('RSS', $parent_category)
        );
    }

    public function rest_api_init(){

        $route = dw_cp_prefix.'api/v1';
        $endpoint = '/'.strtolower($this -> class_name);

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
        $response = get_posts(array(
            'post_type' => strtolower(dw_cp_prefix.$this -> class_name)
        ));

        return rest_ensure_response( $response );
    }
    public function getRequestPermission(){
        return True;
    }

    public function handlePostRequest($request){

        if(!isset($request['name']) || !isset($request['value']) || !isset($request['category']))
            return rest_ensure_response('failed');

        $name = sanitize_text_field( $request['name'] );
        $value = sanitize_text_field( $request['value'] );
        $category = sanitize_text_field( $request['category'] );
        
        // if($this -> add($name, $value, $categories))
        if(1==1)
        return rest_ensure_response( 'success' );
        else
        return rest_ensure_response( 'failed' );
    }
    public function postRequestPermission(){
        return current_user_can( 'publish_posts' );
    }
}