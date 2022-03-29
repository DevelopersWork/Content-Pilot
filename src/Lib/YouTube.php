<?php
/**
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Lib;

use DW\ContentPilot\Core\Store;

class YouTube
{

    private $store;
    private $service;

    public function __construct(string $key)
    {

        $this -> store = new Store();

        $this -> store -> debug(get_class($this).':__construct()', '{STARTED}');

    }

    public function fetchLatestVideoIds(string $key, string $query = null, array $queryParams = array())
    {
        $this -> store -> log(get_class($this).':fetchVideoIds()', '{STARTED}');

        $service = $this -> createYouTubeService($key);

        if (!$service) {
            return $this -> store -> set('_ERROR', true);
        }

        $queryParams['order'] = 'date';
        $queryParams['type'] = 'video';

        if(!array_key_exists('maxResults', $queryParams) || !$queryParams['maxResults']) 
            $queryParams['maxResults'] = 25;

        if(!array_key_exists('q', $queryParams) || !$queryParams['q']) 
            $queryParams['q'] = 'DevelopersWork';

        return $service -> search -> listSearch('id', $queryParams);
    }

    private function createClient()
    {

        $this -> store -> debug(get_class($this).':createClient()', '{STARTED}');

        $key = $this -> store -> get('key');

        /*
        * Google_Client:: Class imported from the Google API Client library.
        */
        $client = new Google_Client();
        $client->setApplicationName(dw_cp_plugin_name);
        $client->setDeveloperKey($key);
        $client->setScopes([
            'https://www.googleapis.com/auth/youtube.force-ssl',
        ]);

        $this -> store -> set('client', $client);

        return $this;
    }

    private function createYouTubeService()
    { 

        $this -> store -> debug(get_class($this).':createYouTubeService()', '{STARTED}');

        if (! $this -> store -> get('client') ) {
            $this -> createClient();
        }

        /*
        * Google_Service_YouTube:: Class to make requests to the YouTube Data API
        */
        $service = new Google_Service_YouTube($this -> store -> get('client'));

        return $service;
    }

}
