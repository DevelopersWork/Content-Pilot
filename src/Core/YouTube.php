<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class YouTube {

    private $store;
    private $client, $service, $lastResponse;

    public function __construct($store) {

        $this -> store = $store;

    }

    public function createClient( string $key = null ) {

        if ( ! $key ) {
            // Fetch API key from the database
            $query = "SELECT 
                secrets.value AS _key 
            FROM " . PLUGIN_PREFIX . "_services AS services 
            JOIN " . PLUGIN_PREFIX . "_secrets AS secrets 
                ON services.id = secrets.service_id 
            WHERE lower(services.name) = 'youtube' AND secrets.disabled = 0";

            global $wpdb;
            $_result = $wpdb->get_results( $query, 'ARRAY_A' );

            $key = $_result[0]['_key'];
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

    public function createService() {

        if ( ! $this -> client )
            $this -> createClient();

        $Google_Service_YouTube = $this -> store -> get('Google_Service_YouTube');

        $this -> service = new $Google_Service_YouTube($this -> client);

        return $this;
    }

    public function search($search_string = null) {
        
        $queryParams = [
            'maxResults' => 25,
            'q' => $search_string ? $search_string : 'DevelopersWork'
        ];

        $this -> lastResponse = $this -> service -> search -> listSearch('snippet', $queryParams);
        
        return $this -> lastResponse;

    }

    public function makePost(int $index) {

        if ( ! $this -> lastResponse )
            $this -> search();

        $data = array(
			'post_title' => '$item->snippet->title',
			'post_content' => '$item->snippet->description',
			'post_category' => array('$_POST[\'uncategorized\']'),
			'tags_input' => array('$tags'),
			'post_status' => 'publish',
			'post_type' => 'wp10yvids'
		);

        $result = wp_insert_post( $data );

        if ( $result && ! is_wp_error( $result ) ) {
			$thenewpostID = $result;

			//add the youtube meta data
			add_post_meta( $thenewpostID, 'videoID', '$item->id');
			add_post_meta( $thenewpostID, 'publishedAt', '$item->snippet->publishedAt');
			add_post_meta( $thenewpostID, 'channelId', '$item->snippet->channelId');
			add_post_meta( $thenewpostID, 'ytitle', '$item->snippet->title');
			add_post_meta( $thenewpostID, 'ydescription', '$item->snippet->description');
			add_post_meta( $thenewpostID, 'imageresmed', '$item->snippet->thumbnails->medium->url');
			add_post_meta( $thenewpostID, 'imagereshigh', '$item->snippet->thumbnails->high->url');
	
		}

        return $this;
    }

}