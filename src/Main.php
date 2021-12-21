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

        add_action('admin_enqueue_scripts', array($this, 'enqueue'));

    }

    function enqueue() {

        wp_enqueue_script(PLUGIN_NAME . ' Script', PLUGIN_URL . 'assets/js/script.js');
        wp_enqueue_style(PLUGIN_NAME . ' Style', PLUGIN_URL . 'assets/css/style.css');

    }
    
}