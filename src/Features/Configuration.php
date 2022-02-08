<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

class Configuration {

    private $store;

    function __construct( $store ) {

        $this -> store = $store;
    
    }

    public function register() {

        if ( ! $this -> store ) return $this;

        $page = array(
			array(
				'page_title' => dw_cp_PLUGIN_NAME, 
				'menu_title' => 'Settings', 
				'capability' => 'manage_options', 
				'menu_slug' => dw_cp_PLUGIN_SLUG . '_configuration', 
				'callback' => array( $this, 'render' ), 
                'parent_slug' => dw_cp_PLUGIN_SLUG
			)
		);

        // $section_id = $this -> createSection ( 'Settings', array( $this, 'renderSection' ) );
		// $section_id2 = $this -> createSection ( 'ABC', array( $this, 'renderSection' ), $section_id );
		
        // $this -> setSetting ( $section_id, 'Setting Name');
		// $this -> setSetting ( $section_id, 'Setting Name2');
		// $this -> setSetting ( $section_id, 'Setting Name5');
        // $this -> setField ( 'Field Title', 'Setting Name', $section_id, array( $this, 'renderField' ) );
		// $this -> setField ( 'Field Title5', 'Setting Name5', $section_id, array( $this, 'renderField' ) );
		// $this -> setField ( 'Field Title2', 'Setting Name2', $section_id, array( $this, 'renderField' ) );

		
		// $this -> setSetting ( $section_id2, 'Setting Name4');
		// $this -> setField ( 'Field Title4', 'Setting Name4', $section_id2, array( $this, 'renderField' ) );

		// $section_id3 = $this -> createSection ( 'About', array( $this, 'renderSection' ) );
		// $this -> setSetting ( $section_id3, 'Setting Name3');
		// $this -> setField ( 'Field Title3', 'Setting Name3', $section_id3, array( $this, 'renderField' ) );
    }

	public function renderPage(){
        require_once dw_cp_PLUGIN_PATH . "/src/Pages/Configuration.php";

        // $yt = new YouTube($this -> store);
        // $yt -> makePost();

    }

    public function render() {
        require_once dw_cp_PLUGIN_PATH . "/src/Pages/Configuration.php";
    }

    public function textFieldProcessing( $input ) {
        return $input;
    }

    public function adminSectionManager() {
		echo 'Manage the Sections and Features of this Plugin by activating the checkboxes from the following list.';
	}

    public function textField() {
        $value = esc_attr( get_option('simple_text_field') );
        echo '<input type="text" class="regular-text" name="simple_text_field" value="' . $value . '" placeholder="Waahhh!!!"/>';
    }

    public function checkboxField( $args ) {
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checkbox = get_option( $option_name );
		$checked = isset($checkbox[$name]) ? ($checkbox[$name] ? true : false) : false;

		echo '<div class="' . $classes . '"><input type="text" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ( $checked ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
	}

    public function checkboxSanitize( $input ) {
		$output = array();

		// foreach ( $this->managers as $key => $value ) {
		// 	$output[$key] = isset( $input[$key] ) ? true : false;
		// }

		return $output;
	}


}

