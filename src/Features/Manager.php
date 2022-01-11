<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Features\Tag;

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

    public function setPage($capability, $callback, $slug, $parent = null, $icon = null, $position = null, $asSubPage = null, $title = null) {

        $this -> page = array(
            'page_title' => ( $title == null ? $this -> title : $title ) . ' ‹ ' . PLUGIN_NAME,
            'menu_title' => ( $title == null ? $this -> title : $title ), 
            'capability' => $capability, 
            'menu_slug' => $slug . ($parent == null ? '' : '-' . strtolower(( $title == null ? $this -> title : $title ))), 
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

        if ( $asSubPage != null ) 
            $this -> page['asSubPage'] = $asSubPage;


        return $this -> syncStore();
    }

    public function createSection($title, $callback, $parent = null, $is_form = null) {

        $slug = $this -> page['menu_slug'];
        $menu = $this -> page['menu_title'];

        $id = md5($slug . '_' . $menu . '_' . $title . '_' . $this -> class);
        
        $this -> sections[$id] = array(
            'id' => 'section' . '-' . $id,
            'title' => $title,
            'page' => $slug . '#' . ( $parent == null ) ? $id : $parent,
            'callback' => $callback,
            'issection' => ( $parent == null ) ? True : False,
            'parent' => $parent,
            'settings' => array(),
            'is_form' => $is_form == null ? False : $is_form
        );

        array_push( $this -> page['sections'], $id );

        $this -> syncStore();

        return $id;
    }

    public function setSetting($group, $name, $callback = null) {

        $slug = $this -> page['menu_slug'];
        $menu = $this -> page['menu_title'];

        if ( ! array_key_exists( $group, $this -> sections ) ) 
            return $this;

        $id = md5($slug . '_' . $menu . '_' . $group . '_' . $this -> class . '_' . $name);
        
        $this -> settings[$id] = array(
            'option_group' => $group,
            'option_name' => $name,
            'callback' => $callback,
            'fields' => array(),
            'id' => $id
        );

        if( $this -> sections[$group]['parent'] != null ) {

            $parent = $this -> sections[$group]['parent'];
            array_push( $this -> sections[$parent]['settings'], $id );

        }

        array_push( $this -> sections[$group]['settings'], $id );

        $this -> syncStore();

        return $id;
    }

    public function setField($title, $setting, $section, $callback, $args=null) {

        $menu = $this -> page['menu_title'];

        if ( ! array_key_exists($section, $this -> sections) ) return $this;
        $_section = $this -> sections[$section];

        $slug = $_section['page'];

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
                'group' => $_setting['option_group'],
                'label_for' => $_setting['option_name'],
                'type' => ( isset($args) &&  array_key_exists('type', $args) ) ? $args['type'] : 'text',
                'class' => ( isset($args) &&  array_key_exists('class', $args) ) ? $args['class'] : 'regular-text',
                'placeholder' => ( isset($args) &&  array_key_exists('placeholder', $args) ) ? $args['placeholder'] : 'Type here...',
                'col' => ( isset($args) &&  array_key_exists('col', $args) ) ? $args['col'] : ' col-6 ',
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
        $settings = array_values( $this -> settings );
        $sections = array_values( $this -> sections );
        $fields = array_values( $this -> fields );

        if (array_key_exists('asSubPage', $this -> page))
            $api -> addPages($page) -> asSubPage($this -> title);
        else 
            $api -> addSubPages($page); 
        
        $api ->addSettings($settings) -> addSections($sections) -> addFields($fields) -> register();

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

        $sections_header = '';
        $sections_content = '';
        $section_count = 0;

        foreach($page['sections'] as $s) {

            $section = $this -> getSection($s);

            if( $section['issection'] != True ) continue;

            $section['order'] = isset($_GET['tab']) ? 1 : $section_count;
            if(isset($_GET['tab']) && $_GET['tab'] == strtolower($section['title'])) $section['order'] = 0;
            $section_count += 1;

            $_section = $section['callback']($section);

            $sections_header .= $_section['head'];
            $sections_content .= $_section['content'];

            $fields = '';

            foreach($section['settings'] as $s) { 

                $setting = $this -> getSetting($s);

                foreach($setting['fields'] as $f) { 

                    $field = $this -> getField($f);
    
                    $fields .= $field['callback']($field);
    
                }
            }

            if($fields != '')
                $sections_content = str_replace('<h1>%'.$section['title'].'%</h1>', $fields, $sections_content);
            
        }

        $this -> data['sections_header'] = $sections_header;
        $this -> data['sections_content'] = $sections_content;

        return $this;
    }

    public function renderPage() {
        
        $page_title = $this -> page['menu_title'];

        $section_header = $this -> data['sections_header'];

        $section_content = $this -> data['sections_content'];

        $submit = array( $this, 'submit');

        return include_once PLUGIN_PATH . "/src/Pages/Manager.php";
    }

    public function renderSetting( $input ) {
        return $input;
    }

    public function renderSection( array $args ) {

        $head = '';

        $head .= '<li class="nav-item" role="presentation">';
            $head .= '<button class="nav-link'.($args['order'] == 0 ? ' active' : '').'" id="'.$args['id'].'-tab" data-bs-toggle="tab" data-bs-target="#'.$args['id'].'" type="button" role="tab" aria-controls="'.$args['id'].'" aria-selected="'.($args['order'] == 0 ? 'true' : 'false').'">';
                $head .= $args['title'];
            $head .= '</button>';
        $head .= '</li>';

        $content = '';

        $content .= '<div class="tab-pane fade show'.($args['order'] == 0 ? ' active' : '').'" id="'.$args['id'].'" role="tabpanel" aria-labelledby="'.$args['id'].'-tab">';
        
            $content .= '<div class="row mt-3"><div class="col-1"></div><div class="card '.($args['is_form'] ? '' : 'text-center').' col-10">';
                $content .= '<div class="card-body">';
                    $get = "";
                    if(isset($_GET))
                        foreach ($_GET as $key => $value)
                            if($key != 'tab')
                                $get .= $key.'='.$value.'&';
                    if($args['is_form']) $content .= '<form method="POST" action="?'.$get.'tab='.strtolower($args['title']).'">';
                        $content .= '<input type="hidden" name="form_name" value="'.strtolower($this -> page['menu_title']) .'_'. strtolower($args['title']).'"/>';
                        $content .= '<div class="row"><h1>%'.$args['title'].'%</h1></div>';
                        if($args['is_form'])
                            $content .= '<div class="row mt-3"><div class="col-1"><button type="submit" class="btn btn-primary">_'.strtoupper($args['title']).'_</button></div></div>';
                    if($args['is_form']) $content .= '</form>';
                $content .= '</div>';
            $content .= '</div><div class="col-1"></div></div>';
        
        $content .= '</div>';

        $html = array('head' => $head, 'content' => $content);

        return $html;
    }

    public function renderField( array $args ) {

        if ( $args['args']['type'] == 'checkbox' ) $field = Tag:: inputCheckboxTag( $args );
        else $field = Tag:: inputTag( $args );

        return $field;
    }

    public function submit() {
        return $this;
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

