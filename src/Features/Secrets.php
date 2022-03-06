<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Features;

use DW\ContentPilot\Core\{ Store };
use DW\ContentPilot\Lib\{ WPPage };

class Secrets extends WPPage {

    private $store;
    public $__FILE__;
    private $load_flag = True;

    function __construct( $__FILE__ = 'DWContentPilot' ) {

        $this -> __FILE__ = $__FILE__;

        $this -> store = new Store();
        $this -> store -> log( get_class($this).':__construct()', '{STARTED}' );

        parent::__construct();
    
    }

    public function register() {

        if(!$this -> load_flag) return false;
            
        $this -> store -> log( get_class($this).':register()', '{STARTED}' );

        $class_name = explode('\\', get_class($this));
        $class_name = array_pop($class_name);

        $args = array(
            'description' => 'API keys of the Content Pilot plugin',
            'public' => false,
            'has_archive' => true,
            'can_export' => false,
            'delete_with_user' => true,
            'exclude_from_search' =>  false,
            'show_in_rest' => true
        );
        register_post_type( DWContetPilotPrefix .'_'. $class_name, $args );

        $_result = $this -> addSubPage (array(
            'parent_slug' => dw_cp_plugin_name,
            'page_title' => $class_name, 
            'menu_title' => 'API Key\'s', 
            'capability' => 'manage_options', 
            'menu_slug' => DWContetPilotPrefix .'_'. $class_name,
            'function' => array( $this, 'render_page' )
        ));

        if(!$_result) {

            $this -> load_flag = false;
            return $this -> store -> debug( get_class($this).':__construct()', '{FAILED}' );

        }

        add_action(DWContetPilotPrefix.'register_actions', array( $this, 'register_actions'));
    }

    public function register_actions() {

        if(!$this -> load_flag) return false;

        add_action(DWContetPilotPrefix.'register_menus', array($this, 'register_page'));
        add_action(DWContetPilotPrefix.'register_menus', array($this, 'form_submissions'));
        
    }

    public function form_submissions(){
        if(isset($_POST['form-submitted']) && $_POST['form-submitted'] == 'true')
        if(isset($_POST['form-name'])) {
            if($_POST['form-name'] == md5(DWContetPilotPrefix . '_add_secrets')) {
                $this -> add_secrets();
            }
            
        }
    }

    public function render_page(){

        $path = plugin_dir_path($this -> __FILE__);

        $class_name = explode('\\', get_class($this));
        $class_name = array_pop($class_name);

        $auth_key = md5($this -> auth_key . '_' . $this -> get('menu_slug'));

        $slug = explode('?', $_SERVER['REQUEST_URI'])[0] . '?';

        if(isset($_GET)) foreach($_GET as $key => $value) {
            if($key != 'tab') $slug .= '&'.$key.'='.$value;
        }

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">API KEY\'s</h1>';
        echo '<a href="'.$slug.'&amp;tab=add" class="page-title-action">Add New</a>';
        echo '<hr class="wp-header-end">';
        echo settings_errors();

        if(isset($_GET['tab'])) {
            $active_tab = $_GET['tab'];
        } else {
            $active_tab = 'view';
        }

        if($active_tab == 'view') {
            $posts = $this -> view_secrets();

            return include_once $path . "/src/Pages/".'Tabs/secrets/view_secrets.php';
        } else if($active_tab == 'modify')
            return include_once $path . "/src/Pages/".'Tabs/secrets/modify_secrets.php';
        else if($active_tab == 'add')
            return include_once $path . "/src/Pages/".'Tabs/secrets/add_secrets.php';

        echo '</div>';
    }

    private function add_secrets(){

        $notice = array(
            'msg' => 'Adding Secrets Failed!!!', 
            'type' => 'error', 
            'domain' => 'add-secrets-dw-content-pilot'
        );

        $keys = array('secret_name', 'secret_key', 'secret_service', 'auth_key');

        foreach($keys as $key) {
            if(!isset($_POST[$key]) || !$_POST[$key]) {
                $this -> store -> set('admin_notice', $notice);
                return add_action( 'admin_notices', array( $this -> store, 'admin_notice') );
            }
        }

        $auth_key = md5($this -> auth_key . '_' . $this -> get('menu_slug'));

        if($auth_key != $_POST['auth_key']) {
            $this -> store -> set('admin_notice', $notice);
            return add_action( 'admin_notices', array( $this -> store, 'admin_notice') );
        }

        $data = array(
			'post_title' => $_POST['secret_name'],
            'post_name' => str_replace('%', '', urlencode($_POST['secret_name'])),
			'post_content' => $_POST['secret_key'],
			'post_status' => 'publish',
			'post_type' => $this -> get('menu_slug')
		);

        $post_id = wp_insert_post( $data );

        if ( $post_id && !is_wp_error( $post_id ) ) {
            add_post_meta( $post_id, 'service', $_POST['secret_service']);

            $notice['type'] = 'success';
            $notice['msg'] = 'New key was successfully added!';
        } 

        $this -> store -> set('admin_notice', $notice);
        return add_action( 'admin_notices', array( $this -> store, 'admin_notice') );
    }

    private function view_secrets() {
        $args = array(
            'post_type' => $this -> get('menu_slug')
        );

        $posts = array();

        $_posts = get_posts( $args );

        foreach ($_posts as $_post) {
            $post = $_post -> to_array();
            $post['service'] = get_post_meta( $post['ID'], 'service', true );
            array_push($posts, $post);
        }

        return $posts;
    }

}

