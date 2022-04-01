<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

use DW\ContentPilot\Core\Store;

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

    public function fetchLatestVideoIds(string $key, string $query = null, array $queryParams = array())
    {
        $this -> store -> debug(get_class($this).':fetchVideoIds()', '{STARTED}');

        $service = $this -> createYouTubeService($key);

        $queryParams['order'] = 'date';
        $queryParams['type'] = 'video';

        if(!array_key_exists('maxResults', $queryParams) || !$queryParams['maxResults']) 
            $queryParams['maxResults'] = 25;

        if(!array_key_exists('q', $queryParams) || !$queryParams['q']) 
            $queryParams['q'] = 'DevelopersWork';

        $this -> store -> log(get_class($this).':search()', json_encode($queryParams));

        return $service -> search -> listSearch('id', $queryParams);
    }

    public function search(string $q, array $queryParams = array(), string $key) {
        
        $this -> store -> debug(get_class($this).':search()', '{STARTED}');

        $service = $this -> createYouTubeService($key);

        $queryParams['q'] = $q;

        $this -> store -> log(get_class($this).':search()', json_encode($queryParams));

        try {
	        $result = $service -> search -> listSearch('id, snippet', $queryParams);

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

        if (! $this -> store -> get('client') ) {
            $this -> createClient($_api_key);
        }        

        /*
        * Google_Service_YouTube:: Class to make requests to the YouTube Data API
        */
        $Google_Service_YouTube = dw_cp_classes['Google_Service_YouTube'];
        $service = new $Google_Service_YouTube($this -> store -> get('client'));

        return $service;

    }

    public function setKey(string $key) {
        $this -> store -> set('key', $key);
    }

}