<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Features\Manager;

class Configuration extends Manager {

    function __construct( $store ) {

		parent::__construct( $store, 'Configuration' );

		$this -> setPage ( 'manage_options', array( $this, 'renderPage' ), PLUGIN_SLUG, PLUGIN_SLUG );

        $section_id = $this -> createSection ( 'Settings', array( $this, 'renderSection' ) );
		$section_id2 = $this -> createSection ( 'ABC', array( $this, 'renderSection' ), $section_id );
		
        $this -> setSetting ( $section_id, 'Setting Name', array( $this, 'renderSetting' ) );
		$this -> setSetting ( $section_id, 'Setting Name2', array( $this, 'renderSetting' ) );
        $this -> setField ( 'Field Title', 'Setting Name', $section_id, array( $this, 'renderField' ) );
		$this -> setField ( 'Field Title5', 'Setting Name', $section_id, array( $this, 'renderField' ) );
		$this -> setField ( 'Field Title2', 'Setting Name2', $section_id, array( $this, 'renderField' ) );

		
		$this -> setSetting ( $section_id2, 'Setting Name4', array( $this, 'renderSetting' ) );
		$this -> setField ( 'Field Title4', 'Setting Name4', $section_id2, array( $this, 'renderField' ) );

		$section_id3 = $this -> createSection ( 'About', array( $this, 'renderSection' ) );
		$this -> setSetting ( $section_id3, 'Setting Name3', array( $this, 'renderSetting' ) );
		$this -> setField ( 'Field Title3', 'Setting Name3', $section_id3, array( $this, 'renderField' ) );
    }

    public function renderPage() {

		$tabs = $this -> generateTabs();

		$fields = $this -> generateFields();

		parent::renderPage();

        return include_once PLUGIN_PATH . "/src/Pages/Configuration.php";
    }

}

