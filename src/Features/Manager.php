<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

class Manager {

    protected $store, $title;
    private $class;

    private $page, $settings, $sections, $fields;

    private $tabs;
    
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
            'settings' => ( $parent == null ) ? array() : $this -> sections[$parent]['settings']
        );

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

        array_push( $this -> sections[$group]['settings'], $name );

        print_r($this -> sections[$group]['settings']);
        print('<hr>');

        return $this;
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

        print($title);
        print_r($this -> settings[$setting]['fields']);
        print('<hr>');

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
            $tab['id'] = $name;

            if( $section['issection'] == True )
                array_push($tabs, $tab);
        }

        $this -> tabs = array_merge($tabs, array());

        return $tabs;

    }

    public function getFields($_fields) {

        $fields = array();

        foreach ( $_fields as $_field ) {

            $f = $this -> fields[$_field];

            $field = array();
            $field['name'] = $f['title'];

            array_push($fields, $field);

        }

        return $fields;
    }

    public function generateTabs($tabs = null) {
        $html = '';

        if( $tabs == null ) $tabs = $this -> getTabs();

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

    public function generateFields($tabs = null) {
        $html = '';

        if( $tabs == null ) $tabs = $this -> getTabs();

        $i = 0;
        foreach($tabs as $tab) {

            if($i != 0) $html .= '<div id="tab-'. ($i + 1) .'" class="tab-pane">';
            else $html .= '<div id="tab-'. ($i + 1) .'" class="tab-pane active">';

            $html .= '<h3>' . $tab['name'] . '</h3>';

            $settings = $this -> sections[$tab['id']]['settings'];

            print_r($settings);
            print('<br>');

            foreach($settings as $setting) {
                
                $fields = $this -> getFields($this -> settings[$setting]['fields']);

                print_r($fields);
                print('<hr>');

                foreach($fields as $field) {

                    $html .= $field['name'] . '<br/>';

                }
            }

            $html .= '</div>';

            $i += 1;
        }

        return $html;
    }

}

