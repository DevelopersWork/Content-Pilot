<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Features\Manager;

class Configuration extends Manager {

    function __construct( $store ) {

		parent::__construct( $store, 'Configuration' );

		parent::__init__();

		$this -> setSection ( 'Section Title2', array( $this, 'renderSection' ) );
		$this -> setSection ( 'Section Title3', array( $this, 'renderSection' ) );
    }

    public function renderPage() {

		$tabs = $this -> generateTabs();

		parent::renderPage();

        return include_once PLUGIN_PATH . "/src/Pages/Configuration.php";
    }

	public function renderTab_1 () {
		
	}

}

