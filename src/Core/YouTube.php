<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class YouTube {

    public static $_key = "";

    public static function search($search_string = null) {

        $client = new Google_Client();
        $client->setApplicationName('API code samples');
        $client->setScopes([
            'https://www.googleapis.com/auth/youtube.force-ssl',
        ]);
        
        $service = new Google_Service_YouTube($client);
        
        $queryParams = [
            'maxResults' => 25,
            'q' => $search_string ? $search_string : 'DevelopersWork'
        ];

        $response = $service->search->listSearch('snippet', $queryParams);
        
        return $response;

    }

}