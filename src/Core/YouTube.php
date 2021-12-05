<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class YouTube {

    private $store;
    private $client, $service;

    public function __construct($store) {

        $this -> store = $store;

        // $this -> createClient();
        // $this -> createService();
    }

    public function createClient( string $key = null ) {

        if ( ! $key ) {
            // Fetch API key from the database
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

        $response = $this -> service->search->listSearch('snippet', $queryParams);
        
        return $response;

    }

}