<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class YouTube {

    private $store;
    private $client, $service, $lastResponse;
    private $key;

    public function __construct($store) {

        $this -> store = $store;

    }

    public function fetchVideoIds(string $query = null, array $queryParams = array()) {

        $this -> createClient() -> createService();

        $queryParams['maxResults'] = 1000;
        $queryParams['order'] = 'date';
        $queryParams['type'] = 'video';
        $queryParams['q'] = $query ? $query : 'DevelopersWork';

        return $this -> service -> search -> listSearch('id', $queryParams);
    }

    public function getVideoById(string $id = '', array $queryParams = array()) {

        $this -> createClient() -> createService();

        $queryParams['id'] = $id;

        return $this -> service -> videos -> listVideos('snippet, contentDetails, statistics', $queryParams);
    }

    public function search($search_string = null) {
        
        $queryParams = [
            'maxResults' => 5,
            'type' => 'video',
            'eventType' => 'live',
            'order' => 'date',
            'q' => $search_string ? $search_string : 'DevelopersWork'
        ];

        $this -> lastResponse = $this -> service -> search -> listSearch('snippet', $queryParams);
        $this -> lastResponse = $this -> service -> videos -> listVideos('snippet,contentDetails,statistics', $queryParams);
        
        return $this -> lastResponse;

    }

    public function makePost($key = "", $search = "") {

        if( $key == "" ) {
            return FALSE;
        }

        $this -> key = $key;

        $search = $search == "" ? 'valorant' : $search;

        $response = $this -> fetchVideoIds($search, array('eventType' => 'live'))['items'];
        $videoID = $response[rand(0, count($response) - 1)]['id']['videoId'];

        $response = $this -> getVideoById($videoID)['items'][0];

        $path = PLUGIN_PATH . 'assets/html/';
        $post_content = file_get_contents( $path . 'youtube_live_post.html' );

        $post_content = str_replace( "%video_id%", $videoID, $post_content );

        $desc = str_replace('\n', '<br/>', $response['snippet']['description']);
        $desc = str_replace(' ', '&nbsp;', $desc);
        $post_content = str_replace( "%description%", $desc, $post_content );
        
        $post_content = str_replace( "%channel_id%", $response['snippet']['channelId'], $post_content );
        $post_content = str_replace( "%channel_name%", $response['snippet']['channelTitle'], $post_content );

        $post_content = str_replace( "%viewCount%", $response['statistics']['viewCount'], $post_content );
        $post_content = str_replace( "%likeCount%", $response['statistics']['likeCount'], $post_content );
        $post_content = str_replace( "%commentCount%", $response['statistics']['commentCount'], $post_content );

        $data = array(
			'post_title' => $response['snippet']['title'],
			'post_content' => $post_content,
			'post_category' => array($search),
			'tags_input' => $response['snippet']['tags'],
			'post_status' => 'publish',
			'post_type' => 'post'
		);

        $result = wp_insert_post( $data );

        if ( $result && ! is_wp_error( $result ) ) {
			$thenewpostID = $result;

			//add the youtube meta data
			add_post_meta( $thenewpostID, 'videoID', $videoID);
			add_post_meta( $thenewpostID, 'publishedAt',  $response['snippet']['publishedAt']);
			add_post_meta( $thenewpostID, 'channelId', $response['snippet']['channelId']);
            add_post_meta( $thenewpostID, 'channelTitle', $response['snippet']['channelTitle']);
			add_post_meta( $thenewpostID, 'ytitle', $response['snippet']['title']);
			add_post_meta( $thenewpostID, 'ydescription', $response['snippet']['description']);
            if ($response['snippet']['thumbnails']['maxres']){
                add_post_meta( $thenewpostID, 'imageresmed', $response['snippet']['thumbnails']['high']['url']);
			    add_post_meta( $thenewpostID, 'imagereshigh', $response['snippet']['thumbnails']['maxres']['url']);

                $this -> generateFeaturedImage($response['snippet']['thumbnails']['maxres']['url'], $thenewpostID, $videoID);
            }else {

                add_post_meta( $thenewpostID, 'imageresmed', $response['snippet']['thumbnails']['medium']['url']);
			    add_post_meta( $thenewpostID, 'imagereshigh', $response['snippet']['thumbnails']['high']['url']);

                $this -> generateFeaturedImage($response['snippet']['thumbnails']['high']['url'], $thenewpostID, $videoID);
            
            }

            return $thenewpostID;
	
		}

        return FALSE;
    }

    private function generateFeaturedImage( $image_url, $post_id, $custom_filename = ''  ){
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);
        $filename = $custom_filename . '_' . basename($image_url);
        if(wp_mkdir_p($upload_dir['path']))
          $file = $upload_dir['path'] . '/' . $filename;
        else
          $file = $upload_dir['basedir'] . '/' . $filename;
        file_put_contents($file, $image_data);
    
        $wp_filetype = wp_check_filetype($filename, null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
        $res2= set_post_thumbnail( $post_id, $attach_id );

    }

    private function createClient() {
        global $wpdb;

        $key = $this -> key;

        if ( ! $key ) {
            // Fetch API key from the database
            $query = "SELECT 
                secrets.value AS _key 
            FROM " . PLUGIN_PREFIX . "_services AS services 
            JOIN " . PLUGIN_PREFIX . "_secrets AS secrets 
                ON services.id = secrets.service_id 
            WHERE 
                lower(services.name) = 'youtube' AND 
                secrets.disabled = 0 AND secrets.deleted = 0 AND 
                services.disabled = 0
            ";

            $_result = $wpdb->get_results( $query, 'ARRAY_A' );

            $key = $_result[rand(0, count($_result) - 1)]['_key'];
        }

        $Google_Client = $this -> store -> get('Google_Client');

        $client = new $Google_Client();
        $client->setApplicationName(PLUGIN_NAME);
        $client->setDeveloperKey($key);
        $client->setScopes([
            'https://www.googleapis.com/auth/youtube.force-ssl',
        ]);

        $this -> client = $client;

        return $this;
    }

    private function createService() {

        if ( ! $this -> client )
            $this -> createClient();

        $Google_Service_YouTube = $this -> store -> get('Google_Service_YouTube');

        $this -> service = new $Google_Service_YouTube($this -> client);

        return $this;
    }

}