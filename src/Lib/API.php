<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

use DW\ContentPilot\Core\Store;

class API
{

    public $store;

    public function __construct()
    {
        $this -> store = new Store();

        $class_name = explode('\\', get_class($this));
        $class_name = array_pop($class_name);
        $this -> store -> set('name', $class_name);

        $this -> store -> set('_ERROR', false);
        $this -> store -> set('_AUTH_KEY', wp_get_session_token());

        $categories = array('name' => '', 'value' => array());
        $this -> store -> set('categories', $categories);
    }

    protected function fetchPosts($post_meta = array(), $orderby = 'date', $order = 'DESC', $post_type = '', $post_status = array())
    {

        $args = array(
            'post_type' => $post_type ? $post_type : $this -> get('menu_slug'),
            'numberposts' => $this -> store -> get('posts_per_page'),
            'orderby' => $orderby,
            'order' => $order,
            'post_status' => $post_status,
            'author' => get_current_user_id()
        );

        $posts = array();

        $_posts = get_posts($args);

        foreach ($_posts as $_post) {
            $post = $_post -> to_array();
            foreach ($post_meta as $meta) {
                $post[$meta] = get_post_meta($post['ID'], $meta, true);
            }
            array_push($posts, $post);
        }

        return array(
            'posts' => $posts,
            'args' => $args
        );
    }

    protected function createCategories()
    {
        $this -> store -> debug(get_class($this).':createCategories()', '{STARTED}');
        $categories = [];

        $_categories = $this -> store -> get('categories');

        if ($_categories['name'] == '') {
            $this -> store -> set('_CATEGORY_ERROR', 'parent');
            return false;
        }

        $parent = wp_create_category(strtoupper(DWContetPilotPrefix) .'_'. $_categories['name']);

        if (!$parent) {
            $this -> store -> set('_CATEGORY_ERROR', 'parent');
            return false;
        } else {
            $categories[$_categories['name']] = $parent;
        }

        $category_error = array();
        foreach ($_categories['value'] as $category) {
            $child = wp_create_category(strtoupper(DWContetPilotPrefix) .'_'. $category, $parent);

            if ($child) {
                $categories[$category] = $child;
            } else {
                array_push($category_error, $category);
            }
        }

        if ($category_error) {
            $this -> store -> set('_CATEGORY_ERROR', $category_error);
        }

        $this -> store -> set('_categories', $categories);

        return true;
    }

    protected function createPostType()
    {
        $this -> store -> debug(get_class($this).':createPostType()', '{STARTED}');

        $_post_type = $this -> store -> get('post_type');
        $name = $this -> store -> get('name');

        if (!$_post_type) {
            $this -> store -> set('_POST_TYPE_ERROR', 'post_type');
            return false;
        }

        $post_type = register_post_type(strtoupper(DWContetPilotPrefix) .'_'. $name, $_post_type);

        if (!$post_type) {
            $this -> store -> set('_POST_TYPE_ERROR', $_post_type);
            return false;
        }

        $this -> store -> set('_post_type', $post_type);

        return true;
    }

    protected function parseRequest(string $method = '')
    {
        $this -> store -> debug(get_class($this).':requestGET()', '{STARTED}');

        $slug = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this -> store -> set('_REQUEST_URI', $slug);

        if (strtolower($method) == 'get') {
            $this -> store -> set('_PARAMS', $_GET);
        } elseif (strtolower($method) == 'post') {
            $this -> store -> set('_PARAMS', $_POST);
        } else {
            $this -> store -> set('_PARAMS', $_REQUEST);
        }
    }

    public function getURI(array $params = array())
    {
        $slug = $this -> store -> get('_REQUEST_URI') . '?';

        $_params = $this -> store -> get('_PARAMS');

        $keys = array('page');

        foreach ($keys as $key) {
            if (isset($_params[$key])) {
                $slug .= $key. '=' . $_params[$key] . '&';
            }
        }

        if ($params) {
            foreach ($params as $key => $value) {
                $slug .= $key. '=' . $value . '&';
            }
        }

        return $slug;
    }

    public function register()
    {
        
        $this -> store -> debug(get_class($this).':register()', '{STARTED}');

        if ($this -> store -> get('_ERROR')) {
            return false;
        }

        add_action(DWContetPilotPrefix.'register_actions', [$this, 'registerActions']);
    }

    public function registerActions()
    {
            
        $this -> store -> debug(get_class($this).':registerActions()', '{STARTED}');

        add_action(DWContetPilotPrefix.'register_post_types', [$this, 'registerPostTypes']);

        add_action(DWContetPilotPrefix.'register_filters', array($this, 'registerFilters'));

        return $this;
    }

    public function registerPostTypes()
    {
        $this -> store -> debug(get_class($this).':registerPostTypes()', '{STARTED}');

        $this -> createPostType();
        $this -> createCategories();
    }

    public function registerFilters()
    {
        $this -> store -> debug(get_class($this).':registerFilters()', '{STARTED}');
    }

    public function fetchSecrets()
    {
        global $wpdb;

        $this -> store -> debug(get_class($this).':fetchSecrets()', '{STARTED}');

        $table_prefix = $wpdb -> base_prefix;
        $query_1 = "SELECT distinct id, post_title as name FROM ".$table_prefix."posts where post_type = 'dw_cp_secrets' and post_author = '".get_current_user_id()."' and post_status = 'publish'";

        $query = "select sm.meta_value as service, s.* from ".$table_prefix."postmeta sm join (".$query_1.") s on s.id = sm.post_id and lower(sm.meta_key) like 'service'";

        $result = $wpdb -> get_results("$query", 'ARRAY_A');

        return $result;
    }

    public static function getSecret($id)
    {
        global $wpdb;

        $table_prefix = $wpdb -> base_prefix;
        $query = "SELECT * FROM ".$table_prefix."posts where post_type = 'dw_cp_secrets' and post_author = '".get_current_user_id()."' and id='".$id."' and post_status = 'publish'";

        $result = $wpdb -> get_results("$query", 'ARRAY_A');

        return $result;
    }

    public function fetchIntervals()
    {
        global $wpdb;

        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);
        $query = 'SELECT id, type FROM '.$table_prefix.'_triggers where disabled <> 1 and deleted <> 1';

        $result = $wpdb -> get_results("$query", 'ARRAY_A');

        return $result;
    }
}
