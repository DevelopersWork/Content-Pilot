<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

use DW\ContentPilot\Core\Store;
use DW\ContentPilot\Lib\API;

use \Exception as Exception;

class YouTube
{

    private $store;
    private $service;

    public function __construct()
    {

        $this -> store = new Store();

        $this -> store -> debug(get_class($this).':__construct()', '{STARTED}');
    }

    public static function getVideos(array $params)
    {
        $yt = new YouTube();
        if (isset($params['secret']) && isset($params['author'])) {
            $yt -> setKey($params['secret'], $params['author']);
        }

        $queryParams = array();

        $queryParams['type'] = 'video';
        $queryParams['order'] = 'date';
        $queryParams['eventType'] = 'live';
        $queryParams['maxResults'] = '50';

        if ($params['yt_channel']) {
            $queryParams['channelId'] = $params['yt_channel'];
        }
        if ($params['yt_video']) {
            $queryParams['relatedToVideoId'] = $params['yt_video'];
        }

        if ($params['yt_published_after']) {
            $queryParams['publishedAfter'] = $params['yt_published_after'];
        }

        $events = array('live', 'completed', 'upcoming');
        $eventType = $params['yt_video_type'];
        if ($eventType) {
            $queryParams['eventType'] = in_array($eventType, $events) ? $eventType : 'live';
        }

        return $yt -> search($params['yt_keyword'], $queryParams);
    }

    public static function fetchVideo(array $ids, array $params = array())
    {
        $yt = new YouTube();

        if (isset($params['secret']) && isset($params['author'])) {
            $yt -> setKey($params['secret'], $params['author']);
        }

        $queryParams = array();

        return $yt -> video($ids, $queryParams);
    }

    public static function makePost($id = array(), $snippet = array(), $statistics = array(), $category = array())
    {
        $post_content = IO:: readAssetFile('youtube_live_post.html');

        $title = '';
        
        if ($id) {
            $post_content = str_replace("%video_id%", $id, $post_content);
            
            $title = isset($id) ? $id : '';
        }

        if ($snippet) {
            $title = isset($snippet['title']) ? $snippet['title'] : '';
            $tags = isset($snippet['tags']) ? $snippet['tags'] : '';
            
            $desc = str_replace('\n', '<br/>', $snippet['description']);
            $post_content = str_replace("%description%", $desc, $post_content);
            
            $post_content = str_replace("%channel_id%", $snippet['channelId'], $post_content);
            
            $post_content = str_replace("%channel_name%", $snippet['channelTitle'], $post_content);
        }

        if ($statistics) {
            $post_content = str_replace("%viewCount%", $statistics['viewCount'] || 0, $post_content);
            
            $post_content = str_replace("%likeCount%", $statistics['likeCount'] || 0, $post_content);
            
            $post_content = str_replace("%commentCount%", $statistics['commentCount'] || 0, $post_content);
        }

        $data = array(
            'post_title' => $title,
            'post_name' => str_replace('%', '', urlencode($title)),
            'post_content' => $post_content,
            'post_category' => $category ? $category : array(),
            'tags_input' => $tags,
            'post_status' => 'publish',
            'post_type' => 'post'
        );

        $post_id = wp_insert_post($data);

        if ($post_id && !is_wp_error($post_id)) {
            // add_post_meta($post_id, 'service', $_POST['job_service']);
            // add_post_meta($post_id, 'interval', $_POST['job_interval']);
        }
    }

    public function search(string $q, array $queryParams = array(), string $scope = "", string $key = "")
    {
        
        $this -> store -> debug(get_class($this).':search()', '{STARTED}');

        $service = $this -> createYouTubeService($key);

        if ($q) {
            $queryParams['q'] = $q;
        }
        
        $this -> store -> log(get_class($this).':search()', json_encode($queryParams));

        try {
            if ($scope) {
                $result = $service -> search -> listSearch($scope, $queryParams);
            } else {
                $result = $service -> search -> listSearch('id, snippet', $queryParams);
            }

            return $result;
        } catch (Exception $ex) {
            $this -> store -> set('_ERROR', $ex);
            $this -> store -> error(get_class($this).':search()', $ex -> getMessage());
        }

        return false;
    }

    public function video(array $ids, array $queryParams = array(), string $scope = "", string $key = "")
    {
        
        $this -> store -> debug(get_class($this).':video()', '{STARTED}');

        $service = $this -> createYouTubeService($key);

        if ($ids) {
            $queryParams['id'] = join(",", $ids);
        }
        
        $this -> store -> log(get_class($this).':video()', json_encode($queryParams));

        try {
            if ($scope) {
                $result = $service -> videos -> listSearch($scope, $queryParams);
            } else {
                $result = $service -> videos -> listVideos(
                    'id,snippet,contentDetails,statistics,liveStreamingDetails,recordingDetails,status,topicDetails',
                    $queryParams
                );
            }

            return $result;
        } catch (Exception $ex) {
            $this -> store -> set('_ERROR', $ex);
            $this -> store -> error(get_class($this).':search()', $ex -> getMessage());
        }

        return false;
    }

    private function createClient(string $_api_key = "")
    {

        $this -> store -> debug(get_class($this).':createClient()', '{STARTED}');

        $key = $_api_key ? $_api_key : $this -> store -> get('key');

        /*
        * Google_Client:: Class imported from the Google API Client library.
        */
        $Google_Client = dw_cp_classes['Google_Client'];
        $client = new $Google_Client();
        
        $client->setApplicationName(dw_cp_plugin_name);
        $client->setDeveloperKey($key);
        $client->setScopes([
            'https://www.googleapis.com/auth/youtube.force-ssl',
        ]);

        $this -> store -> set('client', $client);
        
        return $this;
    }

    private function createYouTubeService(string $_api_key = "")
    {

        $this -> store -> debug(get_class($this).':createYouTubeService()', '{STARTED}');

        if (! $this -> store -> get('client')) {
            $this -> createClient($_api_key);
        }

        /*
        * Google_Service_YouTube:: Class to make requests to the YouTube Data API
        */
        $Google_Service_YouTube = dw_cp_classes['Google_Service_YouTube'];
        $service = new $Google_Service_YouTube($this -> store -> get('client'));

        return $service;
    }

    public function setKey(string $key, string $author)
    {
        
        $result = API:: getSecret($key, $author);

        if (count($result) < 1) {
            return null;
        }

        $row = $result[0];

        if (isset($row['post_content'])) {
            $this -> store -> set('key', $row['post_content']);
        } else {
            $this -> store -> set('key', '');
        }
    }
}
