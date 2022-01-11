<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Features\Manager;

class Dashboard extends Manager {

    function __construct( $store ) {

        parent::__construct( $store, 'Dashboard' );

        $this -> setPage ( 'manage_options', array( $this, 'renderPage' ), PLUGIN_SLUG, null, 'dashicons-hammer', 110, TRUE, PLUGIN_NAME);
    
    }

    public function renderPage(){
        require_once PLUGIN_PATH . "/src/Pages/Dashboard.php";
    }

}

