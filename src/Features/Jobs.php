<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Features;

use DW\ContentPilot\Lib\{ WPPage, IO };

class Jobs extends WPPage
{

    function __construct()
    {
        parent::__construct();

        $this -> addSubPage(array(
            'parent_slug' => dw_cp_plugin_name,
            'page_title' => $this -> store -> get('name'),
            'menu_title' => $this -> store -> get('name'),
            'capability' => 'manage_options',
            'menu_slug' => DWContetPilotPrefix .'_'. $this -> store -> get('name'),
            'function' => array( $this, 'render_page' )
        ));

        $post_type = array(
            'description' => 'Job of the Content Pilot plugin',
            'public' => false,
            'has_archive' => true,
            'can_export' => false,
            'delete_with_user' => true,
            'exclude_from_search' =>  false,
            'show_in_rest' => false,
            'capability_type' =>  array( 'post', 'page' ),
            '__name' => $this -> store -> get('name')
        );
        $this -> store -> set('post_type', $post_type);

        $this -> store -> set('tabs', array('create', 'view', 'edit'));

        add_action('admin_init', array($this, 'handleRequest'));
    }

    public function handleRequest()
    {

        if ( isset($_POST['f_submit']) ) {
            
            $this -> store -> log(get_class($this).':handleRequest()', '{STARTING}');

            if ($_POST['f_submit'] == md5(DWContetPilotPrefix . '_add_job')) {
                if (isset($_POST['f_key']) && $_POST['f_key'] == $this -> auth_key) 
                    return $this -> add();
            } else if ($_POST['f_submit'] == md5(DWContetPilotPrefix . '_edit_job')) {
                if (isset($_POST['f_key']) && $_POST['f_key'] == $this -> auth_key) 
                    return null; //$this -> modify_secrets();
            }

            $notice = array(
                'msg' => 'Internal-Error',
                'type' => 'error',
                'domain' => 'jobs-dw-content-pilot'
            );

            $this -> store -> append('notices', $notice);
            add_action('admin_notices', array( $this -> store, 'adminNotice'));
        }
    }

    private function add()
    {

        $notice = array(
            'msg' => 'Adding Job Failed!!!',
            'type' => 'error',
            'domain' => 'add-secrets-dw-content-pilot'
        );

        $keys = array('job_name', 'secret_id', 'job_type', 'job_status', 'job_interval');

        foreach ($keys as $key) {
            if (!isset($_POST[$key]) || !$_POST[$key]) {
                $this -> store -> append('notices', $notice);
                return add_action('admin_notices', array( $this -> store, 'adminNotice'));
            }
        }

        $data = array(
            'post_title' => $_POST['job_name'],
            'post_name' => str_replace('%', '', urlencode($_POST['job_name'])),
            'post_content' => $_POST['secret_key'],
            'post_status' => 'publish',
            'post_type' => $this -> get('menu_slug')
        );

        $post_id = wp_insert_post($data);

        if ($post_id && !is_wp_error($post_id)) {

            $category = $this -> store -> get('categories')['name'];
            $categories = array($this -> store -> get('_categories')[$category]);

            if (in_array($_POST['secret_service'], $this -> store -> get('categories')['value'])) {
                array_push(
                    $categories, 
                    $this -> store -> get('_categories')[$_POST['secret_service']]
                );
            }

            wp_set_post_categories($post_id, $categories);

            $notice['type'] = 'success';
            $notice['msg'] = 'New key was successfully added!';
        }

        $this -> store -> append('notices', $notice);
        return add_action('admin_notices', array( $this -> store, 'adminNotice'));
    }

    public function view()
    {

        $results = $this -> fetchPosts();
        $posts = $results['posts'];
        $args = $results['args'];

        $html_template = IO:: read_asset_file('table_view/secrets_row.html');

        $posts_html = "";

        foreach ($posts as $_post) {
            $_post_html = $html_template;

            $_post_html = str_replace('$post_id', $_post['ID'], $_post_html);
            $_post_html = str_replace('$post_title', $_post['post_title'], $_post_html);

            $category = '<span aria-hidden="true">—</span><span class="screen-reader-text">No categories</span>';
            if (isset($_post['post_category']) && is_array($_post['post_category'])) {
                $category = "";
                foreach ($_post['post_category'] as $_c) {
                    $c = get_cat_name($_c);

                    $category .= ' <a href="#category_name='.$c.'">'.$c.'</a>,';
                }
                
                $_post_html = str_replace('$service', get_cat_name($_post['post_category']), $_post_html);
            }
            $category = trim($category, ',');
            $category = trim($category, ' ');
            $_post_html = str_replace('$post_category', $category, $_post_html);

            $_post_html = str_replace('$post_author_id', $_post['post_author'], $_post_html);
            $_post_html = str_replace('$post_modified_gmt', $_post['post_modified_gmt'], $_post_html);
            $_post_html = str_replace('$post_author', get_the_author_meta('display_name', $_post['post_author']), $_post_html);

            $posts_html .= $_post_html;
        }

        return array('tbody' => $posts_html, 'args' => $args);
    }
}