<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot;

use Dev\WpContentAutopilot\Core\Services;

class Main {

    private $store;

    function __construct($store) {

        $this->store = $store;
    
    }

    public function init() {

        $services = array(
            Features\Dashboard:: class,
            Features\Configuration:: class
        );

        $_service = new Services($this -> store, $services);
        $_service -> register();

    }
    
}