<?php
/**
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

use Dev\WpContentAutopilot\Core\Tag;

class Manager
{

    protected $store, $title, $alert_show;
    private $class;

    private $page, $settings, $sections, $fields;

    public $data = array();
    
    function __construct($store, $class = null)
    {

        $name = ( $class == null || $class == '' ) ? md5(uniqid()) : md5($class);

        $store -> set($name, array());
        
        $this -> store = $store;
        $this -> class = $name;
        $this -> title = $class;

        $this -> settings = array();
        $this -> sections = array();
        $this -> fields = array();

        $this -> page = array();

        $this -> store -> log($this -> title . ':__construct()', '{STARTED}');

        $this -> syncStore();

        $this -> __init__();
    }

    public function loadScript()
    {
        wp_enqueue_script($this -> class, dw_cp_PLUGIN_URL . 'assets/js/' . str_replace(' ', '', $this -> title) . '.js', array(), dw_cp_PLUGIN_VERSION, true);
    }

    public function loadStyle()
    {
        wp_enqueue_style($this -> class, dw_cp_PLUGIN_URL . 'assets/css/' . str_replace(' ', '', $this -> title) . '.css', array(), dw_cp_PLUGIN_VERSION, 'all');
    }

    public function __init__()
    {

        // creating menu
        $this -> setPage('manage_options', array( $this, 'renderPage' ), dw_cp_PLUGIN_SLUG, null, 'dashicons-hammer', 110, true, dw_cp_PLUGIN_NAME);
        
        // creating submenu
        $this -> setPage('manage_options', array( $this, 'renderPage' ), dw_cp_PLUGIN_SLUG, dw_cp_PLUGIN_SLUG);

        // creating section
        $section_id = $this -> createSection('Section Title', array( $this, 'renderSection' ), null, true);

        // creating setting
        $setting_id = $this -> setSetting($section_id, 'Setting Name', array( $this, 'renderSetting' ));

        // creating field
        $this -> setField('Field Title', $setting_id, $section_id, array( $this, 'renderField' ));
    }

    public function setPage($capability, $callback, $slug, $parent = null, $icon = null, $position = null, $asSubPage = null, $title = null)
    {

        $this -> page = array(
            'page_title' => ( $title == null ? $this -> title : $title ) . ' ‹ ' . dw_cp_PLUGIN_NAME,
            'menu_title' => ( $title == null ? $this -> title : $title ),
            'capability' => $capability,
            'menu_slug' => $slug . ($parent == null ? '' : '-' . strtolower(( $title == null ? $this -> title : $title ))),
            'callback' => $callback,

            'sections' => array(),
        );

        $API = $this -> store -> get('SetupAPI');
        if ($parent != null) {
            $this -> page['parent_slug'] = $slug;
        } else {
            if ($icon != null) {
                $this -> page['icon'] = $icon;
            }
            if ($position != null) {
                $this -> page['position'] = $position;
            }
        }

        if ($asSubPage != null) {
            $this -> page['asSubPage'] = $asSubPage;
        }


        return $this -> syncStore();
    }

    public function createSection($title, $callback, $parent = null, $is_form = null)
    {

        $slug = $this -> page['menu_slug'];
        $menu = $this -> page['menu_title'];

        $id = md5($slug . '_' . $menu . '_' . $title . '_' . $this -> class);
        
        $this -> sections[$id] = array(
            'id' => 'section-' . $id,
            'title' => $title,
            'page' => $slug . '#' . ( $parent == null ) ? $id : $parent,
            'callback' => $callback,
            'issection' => ( $parent == null ) ? true : false,
            'parent' => $parent,
            'settings' => array(),
            'is_form' => $is_form == null ? false : $is_form
        );

        array_push($this -> page['sections'], $id);

        $this -> syncStore();

        return $id;
    }

    public function setSetting($group, $name, $callback = null)
    {

        $slug = $this -> page['menu_slug'];
        $menu = $this -> page['menu_title'];

        if (! array_key_exists($group, $this -> sections)) {
            return $this;
        }

        $id = md5($slug . '_' . $menu . '_' . $group . '_' . $this -> class . '_' . $name);
        
        $this -> settings[$id] = array(
            'option_group' => $group,
            'option_name' => $name,
            'callback' => $callback,
            'fields' => array(),
            'id' => $id
        );

        if ($this -> sections[$group]['parent'] != null) {
            $parent = $this -> sections[$group]['parent'];
            array_push($this -> sections[$parent]['settings'], $id);
        }

        array_push($this -> sections[$group]['settings'], $id);

        $this -> syncStore();

        return $id;
    }

    public function setField($title, $setting, $section, $callback, $args = null)
    {

        $menu = $this -> page['menu_title'];

        if (! array_key_exists($section, $this -> sections)) {
            return $this;
        }
        $_section = $this -> sections[$section];

        $slug = $_section['page'];

        if (! in_array($setting, $_section['settings'])) {
            return $this;
        }
        if ($this -> settings[$setting]['option_group'] != $section) {
            return $this;
        }
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

        array_push($this -> settings[$setting]['fields'], $id);

        return $this -> syncStore();
    }

    public function register()
    {

        if (! $this -> store) {
            return $this;
        }

        $this -> store -> log($this -> title . ':register()', '{STARTED}');

        $API = $this -> store -> get('SetupAPI');
        $api = new $API();

        $page = array($this -> page);
        $settings = array_values($this -> settings);
        $sections = array_values($this -> sections);
        $fields = array_values($this -> fields);

        if (array_key_exists('asSubPage', $this -> page)) {
            $api -> addPages($page) -> asSubPage($this -> title);
        } else {
            $api -> addSubPages($page);
        }
        
        $api ->addSettings($settings) -> addSections($sections) -> addFields($fields) -> register();

        return add_action('wp_loaded', array($this, 'render'));
    }

    public function syncStore()
    {

        $this -> store -> get($this -> class)['page'] = $this -> page;

        $this -> store -> get($this -> class)['settings'] = $this -> settings;
        $this -> store -> get($this -> class)['sections'] = $this -> sections;
        $this -> store -> get($this -> class)['fields'] = $this -> fields;

        return $this;
    }

    public function render()
    {

        $this -> store -> log($this -> title . ':render()', '{STARTED}');

        $page = $this -> getPage();

        $sections_header = '';
        $sections_content = '';
        $section_count = 0;

        if ($page) {
            foreach ($page['sections'] as $s) {
                $section = $this -> getSection($s);

                if ($section['issection'] != true) {
                    continue;
                }

                $section['order'] = isset($_GET['tab']) ? 1 : $section_count;
                if (isset($_GET['tab']) && $_GET['tab'] == strtolower($section['title'])) {
                    $section['order'] = 0;
                }
                $section_count += 1;

                $_section = $section['callback']($section);

                $sections_header .= $_section['head'];
                $sections_content .= $_section['content'];

                $fields = '';

                foreach ($section['settings'] as $s) {
                    $setting = $this -> getSetting($s);

                    foreach ($setting['fields'] as $f) {
                        $field = $this -> getField($f);
    
                        $fields .= $field['callback']($field);
                    }
                }

                if ($fields != '') {
                    $sections_content = str_replace('<h1>%'.$section['title'].'%</h1>', $fields, $sections_content);
                }
            }
        }

        $this -> data['sections_header'] = $sections_header;
        $this -> data['sections_content'] = $sections_content;

        return $this;
    }

    public function renderAlert(array $args = array())
    {
        $html = '';
        $html .= '<div class="alert '.(isset($args['type']) ? $args['type'] : 'alert-warning').' alert-dismissible" role="alert">';
            $html .= '<div>';
                $html .= isset($args['description']) ? $args['description'] : 'Oops, something was broken...';
            $html .= '</div>';
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html .= '</div>';

        return $html;
    }

    public function renderPage()
    {
        
        $page_title = $this -> title;

        $section_header = $this -> data['sections_header'];

        $section_content = $this -> data['sections_content'];

        $submit = array( $this, 'submit');

        $alert = array( $this, 'renderAlert');

        global $alert_show;
        $alert_show = $this -> alert_show;

        return include_once dw_cp_PLUGIN_PATH . "/src/Pages/Manager.php";
    }

    public function renderSetting($input)
    {
        return $input;
    }

    public function renderSection(array $args)
    {
        $head = '';

        $get = "";
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key != 'tab') {
                    $get .= $key.'='.$value.'&';
                }
            }
        }

        $head .= '<li class="'.strtolower($args['title']).'">';
            $head .= '<a href="?'.$get.'tab='.strtolower($args['title']).'" class="'.($args['order'] == 0 ? 'current' : '').'" aria-current="'.($args['order'] == 0 ? 'page' : '').'" aria-selected="'.($args['order'] == 0 ? 'true' : 'false').'">';
                $head .= $args['title'] . ' ';
        if ($args['is_form']) {
            $head .= '<span class="count">(';
            $head .= '<span class="'.strtolower($args['title']).'-count">0</span>';
            $head .= ')</span>';
        }
            $head .= '</a>';
            $head .= ' |';
        $head .= '</li>';

        $content = '';

        if (isset($_GET['page']) && $_GET['page'] == $this -> page['menu_slug']) {
            if (( isset($_GET['tab']) && $_GET['tab'] == strtolower($args['title']) ) ||
            ( !isset($_GET['tab']) && strtolower($args['title']) == 'overview' )
            ) {
                if ($args['is_form']) {
                    $content .= '<form method="POST" action="?'.$get.'tab='.strtolower($args['title']).'">';
                    $content .= '<input type="hidden" name="form_name" value="'.strtolower($this -> page['menu_title']) .'_'. strtolower($args['title']).'"/>';
                }
                $content .= '<table class="wp-list-table widefat fixed striped table-view-list comments '.($args['order'] == 0 ? ' active' : '').'" id="'.$args['id'].'" role="tabpanel" aria-labelledby="'.$args['id'].'-tab">';
            
                $content .= '<tbody id="the-comment-list" data-wp-lists="list:comment">';
                    $content .= '<tr class="row"><h1>%'.$args['title'].'%</h1></tr>';
                $content .= '</tbody>';
            
                $content .= '</table>';
                if ($args['is_form']) {
                    $content .= '<p class="submit">';
                    $content .= '<input type="submit" name="submit" id="submit" class="button button-primary" value="_'.strtoupper($args['title']).'_"/>';
                    $content .= '</p>';
                    $content .= '</form>';
                }
            }
        }

        $html = array('head' => $head, 'content' => $content);

        return $html;
    }

    public function renderField(array $args)
    {

        if ($args['args']['type'] == 'checkbox') {
            $field = Tag:: inputCheckboxTag($args);
        } elseif ($args['args']['type'] == 'textarea') {
            $field = Tag:: textAreaTag($args);
        } else {
            $field = Tag:: inputTag($args);
        }

        return $field;
    }

    public function submit()
    {
        return $this;
    }

    public function getPage()
    {
        return $this -> page;
    }

    public function getSection($id)
    {
        return $this->sections[$id];
    }

    public function getSetting($name)
    {
        return $this -> settings[$name];
    }

    public function getField($id)
    {
        return $this -> fields[$id];
    }
}
