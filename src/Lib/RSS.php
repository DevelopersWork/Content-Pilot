<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

use DW\ContentPilot\Core\Store;
use DW\ContentPilot\Lib\API;

use \Exception as Exception;

// https://news.google.com/rss/search?q=gaming
// http://www.google.com/search?q=gaming

class RSS
{

    private $store;
    private $service;

    public function __construct()
    {

        $this -> store = new Store();

        $this -> store -> set('xml', '');

        $this -> store -> debug(get_class($this).':__construct()', '{STARTED}');
    }

    public static function run($params) {

        global $wpdb;
        $rss = new self();
        $store = new Store();

        $store -> log(__METHOD__, '{STARTED}');
        
        $query = "select max(meta_value) as published from ".$wpdb -> base_prefix."postmeta where meta_key like '".$params[0]['post_id']."_rss_published_after'";
        $_result = $wpdb -> get_results("$query", 'ARRAY_A');

        $last_insert = "";
        if (count($_result) > 1) {
            $last_insert = $_result[0]['published'];
        }

        $meta = array(
            'secret' => '',
            'feed_url' => '',
            'rss_published_after' => $last_insert ? $last_insert : '1970-01-01T00:00:00Z',
            'maxResults' => 10
        );
        foreach ($params as $_ => $row) {
            $meta[$row['meta_key']] = $row['meta_value'];
        }
        
        $results = $rss -> getFeed ($meta['feed_url'], $meta);

        foreach($results as $item){
            $rss -> makePost($item['title'], $item['content']);
        }
    }

    public function makePost($title, $content) {

        $post_content = IO:: readAssetFile('youtube_live_post.html');
        
        if ($title) {
            $post_content = str_replace("%video_id%", $title, $post_content);
        }

        if ($content) {
            
            $desc = str_replace('\n', '<br/>', $content);
            $post_content = str_replace("%description%", $desc, $post_content);

        }

        $data = array(
            'post_title' => $title,
            'post_name' => str_replace('%', '', urlencode($title)),
            'post_content' => $post_content,
            'post_category' => array(),
            'tags_input' => array(),
            'post_status' => 'publish',
            'post_type' => 'post'
        );

        $post_id = wp_insert_post($data);
    }

    public function getFeed(string $url, array $queryParams = array())
    {
        
        $this -> store -> debug(get_class($this).':getFeed()', '{STARTED}');

        $xml = $this -> xml($url);
        
        $this -> store -> log(get_class($this).':getFeed()', json_encode($queryParams));

        $total = 0;
        if(isset($queryParams['maxResults'])) {
            $total = $queryParams['maxResults'];
        }

        $items = [];        

        foreach($xml -> channel -> item as $item) {

            if(!$item -> link) continue;

            if(isset($queryParams['rss_published_after']) && strtotime($queryParams['rss_published_after']) >= strtotime($item -> pubDate)){
                continue;
            }

            $content = "";

            $response = file_get_contents($item -> link);
            if(!$response) continue;

            $PHPHtmlParser = dw_cp_classes['PHPHtmlParser'];
            $dom = new $PHPHtmlParser();
            $dom->loadStr($response);

            $articles = $dom -> find('artice');
            
            foreach($articles as $article){
                $content .= $article -> innertext;
            }

            if(!$content) $content = $item -> description;

            array_push($items, [
                'title' => $item -> title,
                'link' => $item -> link,
                'description' => $item -> description,
                'pubDate' => $item -> pubDate,
                'guid' => $item -> guid,
                'content' => $content
            ]);

            $total -= 1;

            if($total < 1) break;

            break;
        }

        return $items;
    }

    private function xml(string $url = "")
    {

        $this -> store -> debug(get_class($this).':xml()', '{STARTED}');

        if(!preg_match("/^http:\/\//i", $url) && !preg_match("/^https:\/\//i", $url))
            return this;

        $xml = file_get_contents($url);

        try{
            
            $xml = simplexml_load_string($xml);

            $this -> store -> set('xml', $xml);

            return $xml;

        } catch (Exception $ex) {
            $this -> store -> set('_ERROR', $ex);
            $this -> store -> error(get_class($this).':xml()', $ex -> getMessage());
        }
        
        return false;
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