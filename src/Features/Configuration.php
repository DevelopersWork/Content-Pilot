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
				'page_title' => PLUGIN_NAME, 
				'menu_title' => 'Settings', 
				'capability' => 'manage_options', 
				'menu_slug' => PLUGIN_SLUG . '_configuration', 
				'callback' => array( $this, 'render' ), 
                'parent_slug' => PLUGIN_SLUG
			)
		);

        $settings = array(
			array(
				'option_group' => PLUGIN_SLUG . 'option_group_configuration_text_field',
				'option_name' => 'simple_text_field',
				'callback' => array( $this, 'textFieldProcessing' )
			)
		);

        $sections = array(
			array(
				'id' => PLUGIN_SLUG . 'option_group_configuration_id1',
				'title' => 'Configuration',
				'callback' => array( $this, 'adminSectionManager' ),
				'page' => $page[0]['menu_slug']
			)
		);

        $fields = array(
            array(
				'id' => $settings[0]['option_name'],
				'title' => "Simple Text Field",
				'callback' => array( $this, 'textField' ),
				'page' => $page[0]['menu_slug'],
				'section' => $sections[0]['id'],
				'args' => array(
					'label_for' => $settings[0]['option_name'],
					'class' => 'example-class'
				)
			)
        );

        $API = $this -> store -> get('SetupAPI');
        $api = new $API();
        $api -> addSubPages($page) -> addSettings($settings) -> addSections($sections) -> addFields($fields) -> register();
    }

    public function render() {
        require_once PLUGIN_PATH . "/src/Pages/Configuration.php";
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

