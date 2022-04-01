<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Features;

use DW\ContentPilot\Lib\{ WPPage, IO, YouTube };

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

        if ( isset($_POST['f_submit']) && preg_match("/job$/i", $_POST['f_submit']) ) {
            
            $this -> store -> debug(get_class($this).':handleRequest()', '{STARTING}');

            if ($_POST['f_submit'] == (md5(DWContetPilotPrefix . '_add_job') . '_job')) {

                if (isset($_POST['f_key']) && $_POST['f_key'] == $this -> auth_key) 
                    return $this -> add();

            } else if ($_POST['f_submit'] == (md5(DWContetPilotPrefix . '_edit_job') . '_job')) {

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

        $this -> store -> debug(get_class($this).':add()', '{STARTING}');

        $notice = array(
            'msg' => 'Adding Job Failed!!!',
            'type' => 'error',
            'domain' => 'add-secrets-dw-content-pilot'
        );

        $keys = array('job_name', 'job_interval', 'job_service');

        foreach ($keys as $key) {
            if (!isset($_POST[$key]) || !$_POST[$key]) {
                $this -> store -> log(get_class($this).':add()', json_encode($_POST));
                $this -> store -> append('notices', $notice);
                return add_action('admin_notices', array( $this -> store, 'adminNotice'));
            }
        }
        
        $secret = array();
        if(isset($_POST['job_secret']) && $_POST['job_secret'] != ""){
            $secret = $this -> getSecret($_POST['job_secret']);
            if(count($secret) < 1){
                $this -> store -> log(get_class($this).':add()', json_encode($_POST));
                $this -> store -> append('notices', $notice);
                return add_action('admin_notices', array( $this -> store, 'adminNotice'));
            }
            $secret = $secret[0];
        }

        if($_POST['job_service'] == 'YouTube') {
            
            $keys = array('job_secret', 'yt_channel', 'yt_keyword');

            foreach ($keys as $key)
                if (!isset($_POST[$key]) || !$_POST[$key]) {
                    $this -> store -> log(get_class($this).':add()', json_encode($_POST));
                    $this -> store -> append('notices', $notice);
                    return add_action('admin_notices', array( $this -> store, 'adminNotice'));
                }
        }

        $content = 'Service='.$_POST['job_service'];
        $content .= md5(DWContetPilotPrefix);
        $content .= 'Interval='.$_POST['job_interval'];

        $data = array(
            'post_title' => $_POST['job_name'],
            'post_name' => str_replace('%', '', urlencode($_POST['job_name'])),
            'post_content' => $content,
            'post_status' => 'publish',
            'post_type' => $this -> get('menu_slug')
        );

        $post_id = wp_insert_post($data);

        if ($post_id && !is_wp_error($post_id)) {

            add_post_meta($post_id, 'service', $_POST['job_service']);
            add_post_meta($post_id, 'interval', $_POST['job_interval']);

            if($_POST['job_service'] == 'YouTube') {

                $yt = new YouTube();

                $channel = trim($_POST['yt_channel']);
                $channel = explode(' ', $channel)[0];
                $channel = explode(',', $channel)[0];

                $channel = $yt -> search( 
                    $channel, 
                    array('type' => 'channel'),
                    $secret['post_content']
                );

                if(!$channel || !isset($channel['items']) || !is_array($channel['items']) || count($channel['items']) < 1){
                    $notice['msg'] = 'YouTube Channel -> "'.$_POST['yt_channel'].'"/API Key is invalid';
                    $this -> store -> append('notices', $notice);
                    return add_action('admin_notices', array( $this -> store, 'adminNotice'));
                }

                $yt_channel = $channel['items'][0];

                add_post_meta($post_id, 'secret', $secret['id']);
                add_post_meta($post_id, 'yt_channel', $yt_channel['id']['channelId']);
                add_post_meta($post_id, 'yt_keyword', $_POST['yt_keyword']);
            
            }

            $notice['type'] = 'success';
            $notice['msg'] = 'Job was successfully created!';
        }

        $this -> store -> append('notices', $notice);
        return add_action('admin_notices', array( $this -> store, 'adminNotice'));
    }

    public function view()
    {

        $results = $this -> fetchPosts(array('service', 'interval', 'secret', 'yt_channel', 'yt_keyword'));
        $posts = $results['posts'];
        $args = $results['args'];

        $html_template = IO:: read_asset_file('table_view/jobs_row.html');

        $posts_html = "";

        foreach ($posts as $_post) {
            $_post_html = $html_template;

            $_post_html = str_replace('$post_id', $_post['ID'], $_post_html);
            $_post_html = str_replace('$post_title', $_post['post_title'], $_post_html);

            $_post_html = str_replace('$post_author_id', $_post['post_author'], $_post_html);
            $_post_html = str_replace('$post_modified_gmt', $_post['post_modified_gmt'], $_post_html);
            $_post_html = str_replace('$post_author', get_the_author_meta('display_name', $_post['post_author']), $_post_html);

            $posts_html .= $_post_html;
        }

        return array('tbody' => $posts_html, 'args' => $args);
    }

    public function fetchIntervals(){
        global $wpdb;

        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);
        $query = 'SELECT id, type FROM '.$table_prefix.'_triggers where disabled <> 1 and deleted <> 1';

        $result = $wpdb -> get_results("$query", 'ARRAY_A' );

        return $result;
    }

    public function fetchSecrets(){
        global $wpdb;

        $table_prefix = $wpdb -> base_prefix;
        $query = "SELECT distinct id, post_title as name FROM ".$table_prefix."posts where post_type = 'dw_cp_secrets' and post_author = '".get_current_user_id()."'";

        $result = $wpdb -> get_results("$query", 'ARRAY_A' );

        return $result;
    }

    private function getSecret($id) {
        global $wpdb;

        $table_prefix = $wpdb -> base_prefix;
        $query = "SELECT * FROM ".$table_prefix."posts where post_type = 'dw_cp_secrets' and post_author = '".get_current_user_id()."' and id='".$id."'";

        $result = $wpdb -> get_results("$query", 'ARRAY_A' );

        return $result;
    }
}