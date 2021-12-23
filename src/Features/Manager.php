<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

class Manager {

    protected $store, $title;
    private $class;

    private $page, $settings, $sections, $fields;

    public $data = array();
    
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

        $this -> syncStore();
    
    }

    public function __init__() {

        $this -> setPage ( 'manage_options', array( $this, 'renderPage' ), PLUGIN_SLUG, PLUGIN_SLUG );

        $section_id = $this -> createSection ( 'Section Title', array( $this, 'renderSection' ) );

        $setting_id = $this -> setSetting ( $section_id, 'Setting Name', array( $this, 'renderSetting' ) );

        $this -> setField ( 'Field Title', 'Setting Group', 'Section Title', array( $this, 'renderField' ) );
    
    }

    public function setPage($capability, $callback, $slug, $parent = null, $icon = null, $position = null) {

        $this -> page = array(
            'page_title' => $this -> title . ' ‹ ' . PLUGIN_NAME,
            'menu_title' => $this -> title, 
            'capability' => $capability, 
            'menu_slug' => $slug . '-' . strtolower($this -> title), 
            'callback' => $callback,

            'sections' => array(),
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

        return $this -> syncStore();
    }

    public function createSection($title, $callback, $parent = null) {

        $slug = $this -> page['menu_slug'];
        $menu = $this -> page['menu_title'];

        $id = md5($slug . '_' . $menu . '_' . $title . '_' . $this -> class);
        
        $this -> sections[$id] = array(
            'id' => $id,
            'title' => $title,
            'page' => $slug,
            'callback' => $callback,
            'issection' => ( $parent == null ) ? True : False,
            'parent' => $parent,
            'settings' => array()
        );

        array_push( $this -> page['sections'], $id );

        $this -> syncStore();

        return $id;
    }

    public function setSetting($group, $name, $callback) {

        $slug = $this -> page['menu_slug'];
        $menu = $this -> page['menu_title'];

        if ( ! array_key_exists( $group, $this -> sections ) ) 
            return $this;
        
        $this -> settings[$name] = array(
            'option_group' => $group,
            'option_name' => $name,
            'callback' => $callback,
            'fields' => array()
        );

        if( $this -> sections[$group]['parent'] != null ) {

            $parent = $this -> sections[$group]['parent'];
            array_push( $this -> sections[$parent]['settings'], $name );

        }

        array_push( $this -> sections[$group]['settings'], $name );

        $this -> syncStore();

        return $this -> syncStore();
    }

    public function setField($title, $setting, $section, $callback, $args=null) {

        $slug = $this -> page['menu_slug'];
        $menu = $this -> page['menu_title'];

        if ( ! array_key_exists($section, $this -> sections) ) return $this;
        $_section = $this -> sections[$section];

        if ( ! in_array($setting, $_section['settings']) ) return $this;
        if ( $this -> settings[$setting]['option_group'] != $section ) return $this;
        $_setting = $this -> settings[$setting];

        $id = md5($slug . '_' . $menu . '_' . $title . '_' . $setting . '_' . $section);

        $this -> fields[$id] = array(
            'id' => $_setting['option_name'],
            'title' => $title,
            'page' => $slug,
            'section' => $_section['id'],
            'args' => array(
                'label_for' => $_setting['option_name'],
                'class' => ( isset($args) &&  array_key_exists('class', $args) ) ? $args['class'] : ''
            ),
            'callback' => $callback,
            'key' => $id
        );

        array_push( $this -> settings[$setting]['fields'], $id );

        return $this -> syncStore();
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

        return $this -> render();
    }

    public function syncStore() {

        $this -> store -> get( $this -> class )['page'] = $this -> page;

        $this -> store -> get( $this -> class )['settings'] = $this -> settings;
        $this -> store -> get( $this -> class )['sections'] = $this -> sections;
        $this -> store -> get( $this -> class )['fields'] = $this -> fields;

        return $this;

    }

    public function render() {

        $page = $this -> getPage();

        $tabs = array();

        foreach($page['sections'] as $s) {

            $section = $this -> getSection($s);

            if( $section['issection'] != True ) continue;

            $fields = array();

            foreach($section['settings'] as $se) {

                $setting = $this -> getSetting($se);

                $_fields = array();

                foreach($setting['fields'] as $f) {

                    $field = $this -> getField($f);

                    $_field = array(
                        'id' => $field['id'],
                        'title' => $field['title'],
                        'page' => $field['page']
                    );

                    array_push($_fields, $_field);
                }

                array_push($fields, $_fields);

            }

            $tab = array(
                'id' => $section['id'],
                'title' => $section['title'],
                'issection' => $section['issection'],
                'parent' => $section['parent'],
                'fields' => $fields
            );

            array_push($tabs, $tab);
            
        }

        $this -> data['page'] = array(
            'menu_title' => $page['menu_title']
        );

        $this -> data['tabs'] = $tabs;

        return $this;
    }

    public function renderPage() {
        
        echo '<h1 class="wp-heading-inline">' . $this -> page['menu_title'] . '</h1>';
        
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
    
        echo "<input type='$type' value='$value' name='$name' id='$id' class='regular-text code' /> <span class='description'>$desc</span>";
            
    }

    public function getPage(){
        return $this -> page;
    }

    public function getSection($id) {
        return $this->sections[$id];
    }

    public function getSetting($name) {
        return $this -> settings[$name];
    }

    public function getField($id) {
        return $this -> fields[$id];
    }

}
