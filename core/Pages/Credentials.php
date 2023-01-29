<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core\Pages;

use DW\ContentPilot\Lib\{WPPage};

use \WP_REST_Request as WP_REST_Request;
use \WP_REST_Response as WP_REST_Response;
use \WP_Error as WP_Error;

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
            'post_name' => md5($title.$author_id),
            'post_title' => $title,
            'post_content' => $value,
            'post_status' => 'publish',
            'post_author' => $author_id,
            'post_type' => strtolower(dw_cp_prefix.$this -> class_name),
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

        // echo $this -> post_type -> name;
    }

    public function register_categories(){
        $parent_category = wp_create_category(strtolower(dw_cp_prefix.$this -> class_name));
        $this -> categories = array(
            'YouTube' => wp_create_category('YouTube', $parent_category),
            'RSS' => wp_create_category('RSS', $parent_category)
        );
    }

    public function rest_api_init(){
        parent::rest_api_init();

        $route = dw_cp_prefix.'api/v1';
        $endpoint = '/'.strtolower($this -> class_name);

        if(register_rest_route( $route, $endpoint, [
            'methods' => 'GET',
            'callback' => [ $this, 'handleGetRequest' ],
            'permission_callback' => [ $this, 'getRequestPermission' ]
        ]))
        if(register_rest_route( $route, $endpoint, [
            'methods' => 'POST',
            'callback' => [ $this, 'handlePostRequest' ],
            'permission_callback' => [ $this, 'postRequestPermission' ],
            'args' => [
                'title' => ['required' => true],
                'value' => ['required' => true],
                'category' => ['required' => true]
            ],
        ]))
            return true;
    }

    public function handleGetRequest(WP_REST_Request $request){

        $queryParams = $request -> get_query_params();

        $posts_per_page = 10;
        if(isset($queryParams['posts_per_page']))
            $posts_per_page = $queryParams['posts_per_page'];

        $offset = 0;
        if(isset($queryParams['offset']))
            $offset = $queryParams['offset'];

        $post_status = '';
        if(isset($queryParams['post_status']))
            $post_status = $queryParams['post_status'];

        $posts = get_posts(array(
            'numberposts' => $posts_per_page, 
            'offset'=> $offset,
            'post_type' => $this -> post_type -> name,
            'post_author' => get_current_user_id(),
            'post_status' => $post_status,            
        ));

        $_ts = time();
        $_hash = md5(json_encode($posts).$_ts);
        return rest_ensure_response( new WP_REST_Response(data: [
            'total_posts' => wp_count_posts($this -> post_type -> name, 'readable') -> publish,
            'posts' => $posts,
            'author' => wp_get_current_user() -> display_name,
            '_ts' => $_ts,
            '_hash' => $_hash
        ], status: 200) );
    }
    public function getRequestPermission(){
        return current_user_can('read');
    }

    public function handlePostRequest(WP_REST_Request $request){

        $name = sanitize_text_field( $request['name'] );
        $value = sanitize_text_field( $request['value'] );
        $category = sanitize_text_field( $request['category'] );

        $response = new WP_REST_Response(data: [
            '_ts' => time()
        ], status: 200);
        
        // if($this -> add($name, $value, $categories))
        if(1==1)
        return rest_ensure_response( $response );
        else
        return rest_ensure_response( $response );
    }
    public function postRequestPermission(){
        return current_user_can( 'publish_posts' );
    }
}