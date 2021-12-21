<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

class Manager {

    protected $store, $title;
    private $class;

    private $page, $settings, $sections, $fields;
    
    function __construct( $store, $class = null ) {

        $name = ( $class == null || $class == '' ) ? md5( uniqid() ) : md5( $class );

        $store -> set( $name, array() );
        
        $this -> store = $store;
        $this -> class = $name;
        $this -> title = $class;

        $this -> settings = array();
        $this -> sections = array();
        $this -> fields = array();

        $this -> page = array();

        $store -> get( $name )['page'] = $this -> page;
        $store -> get( $name )['settings'] = $this -> settings;
        $store -> get( $name )['sections'] = $this -> sections;
        $store -> get( $name )['fields'] = $this -> fields;
    
    }

    public function __init__() {
        $this -> setPage ( 'manage_options', array( $this, 'renderPage' ), PLUGIN_SLUG, PLUGIN_SLUG );

        $this -> setSetting ( 'Setting Group', 'Setting Name', array( $this, 'renderSetting' ) );

        $this -> setSection ( 'Section Title', array( $this, 'renderSection' ) );

        $this -> setField ( 'Field Title', 'Setting Group', 'Section Title', array( $this, 'renderField' ) );
    }

    public function setPage($capability, $callback, $slug, $parent = null, $icon = null, $position = null) {

        $this -> page = array(
            'page_title' => $this -> title . ' ‹ ' . PLUGIN_NAME,
            'menu_title' => $this -> title, 
            'capability' => $capability, 
            'menu_slug' => $slug . '-' . strtolower($this -> title), 
            'callback' => $callback,

            'settings' =>  $this -> settings,
            'sections' => $this -> sections,
            'fields' => $this -> fields
        );

        $API = $this -> store -> get('SetupAPI');
        if ( $parent != null ) 
            $this -> page['parent_slug'] = $slug;
        else {
            if ( $icon != null ) 
                $this -> page['icon'] = $icon;
            if ( $position != null ) 
                $this -> page['position'] = $position;
        }

        return $this;
    }

    public function setSetting($group, $name, $callback) {

        $slug = $this -> page['menu_slug'];
        $menu = $this -> page['menu_title'];
        
        $this -> settings[$name] = array(
            'option_group' => md5($slug . '_' . $menu . '_' . $group . '_' . $this -> class),
            'option_name' => $name,
            'callback' => $callback,
            'fields' => array()
        );

        $this -> page['settings'][$name] = $this -> settings[$name];

        return $this;
    }

    public function setSection($title, $callback) {

        $slug = $this -> page['menu_slug'];
        $menu = $this -> page['menu_title'];
        
        $this -> sections[$title] = array(
            'id' => md5($slug . '_' . $menu . '_' . $title . '_' . $this -> class),
            'title' => $title,
            'page' => $slug,
            'callback' => $callback,
            'fields' => array()
        );

        $this -> page['sections'][$title] = $this -> sections[$title];

        return $this;
    }

    public function setField($title, $setting, $section, $callback, $args=null) {

        $slug = $this -> page['menu_slug'];
        $menu = $this -> page['menu_title'];

        $API = $this -> store -> get('SetupAPI');

        if ( ! $API:: isKeyExists($setting, $this -> settings) ) 
            return $this;
        $_setting = $this -> settings[$setting];

        if ( ! $API:: isKeyExists($section, $this -> sections) ) 
            return $this;
        $_section = $this -> sections[$section];

        $this -> fields[$title] = array(
            'id' => $_setting['option_name'],
            'title' => $title,
            'page' => $slug,
            'section' => $_section['id'],
            'args' => array(
                'label_for' => $_setting['option_name'],
                'class' => ( isset($args) &&  $API:: isKeyExists('class', $args) ) ? $args['class'] : ''
            ),
            'callback' => $callback,
            'setting' => $_setting,
            'section' => $_section
        );

        $this -> settings[$setting]['fields'][$title] = $this -> fields[$title];
        $this -> sections[$section]['fields'][$title] = $this -> fields[$title];

        return $this;
    } 

    public function register() {

        if ( ! $this -> store ) return $this;

        $API = $this -> store -> get('SetupAPI');
        $api = new $API();

        $page = array($this -> page);
        $settings = array( array_values( $this -> settings ) );
        $sections = array( array_values( $this -> sections ) );
        $fields = array( array_values( $this -> fields ) );

        $api -> addSubPages($page) -> addSettings($settings) -> addSections($sections) -> addFields($fields) -> register();

        return $this;
    }

    public function renderPage() {
        
        echo "<h1>" . $this -> page['menu_title'] . "</h1>";
        
        return $this;
    }

    public function renderSetting( $input ) {
        return $input;
    }

    public function renderSection( $arg ) {

        echo "<br/><hr/><br/>";
        echo '<h3>id: ' . $arg['id'] . '</h3>';
        echo '<h3>title: ' . $arg['title'] . '</h3>';
        echo "<br/><hr/><br/>";

        return $this;
    }

    public function renderField( array $args ) {

        $type   = $args['type'];
        $id     = $args['label_for'];
        $data   = get_option( $id, array() );
        $value  = $data[ $type ];
    
        $value  = esc_attr( $value );
        $name   = $id . '[' . $type . ']';
        $desc   = $this->get_shortcode_help( $type );
    
        print "<input type='$type' value='$value' name='$name' id='$id'
            class='regular-text code' /> <span class='description'>$desc</span>";
            
    }

    public function getTabs() {
        $tabs = array();
        foreach ( $this -> sections as $name => $section ) {

            $tab = array();
            $tab['name'] = $section['title'];
            $tab['fields'] = $this -> getFields($section['fields']);

            array_push($tabs, $tab);
        }

        return $tabs;

    }

    public function getFields($_fields) {
        $fields = array();
        foreach ( $_fields as $name => $_field ) {

            $field = array();
            $field['name'] = $_field['title'];

            array_push($fields, $field);

        }

        return $fields;
    }

    public function generateTabs() {
        $html = '';

        $tabs = $this -> getTabs();

        $i = 0;
        foreach($tabs as $tab) {
            if($i != 0) $html .= '<li>';
            else $html .= '<li class="active">';

            $html .= '<a href="#tab-'. ($i + 1) .'">';
            $html .= $tab['name'];
            $html .= '</a>';

            $html .= '</li>';

            $i += 1;
        }

        return $html;
    }

}

