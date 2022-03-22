<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

use DW\ContentPilot\Core\{ Store };
use DW\ContentPilot\Lib\{ API };

class WPPage {

    private $page = array();
    protected $auth_key = "";
    protected $store;
    protected $api = new API();

    public function __construct($page = array()) {

        $this -> store = new Store();

        $this -> api -> setStore($this -> store);

        $this -> page = $page;

        $this -> auth_key = wp_get_session_token();

        if($this -> page) add_action(DWContetPilotPrefix.'register_menus', [$this, 'register_page']);

        $this -> store -> set(
            '_ERROR': False,
            '_AUTH_KEY': wp_get_session_token(),
            'categories': [
                'name': '',
                'value': []
            ]
        );

    }

    protected function addPage( array $_page ) {
        
        $this -> page = $this -> createPage($_page);

        if(!$this -> page) return false;

        return $this;
    }

    protected function addSubPage( array $_page ) {

        if ( !array_key_exists('parent_slug', $_page) ) 
            return false;

        if(!$this -> addPage($_page)) return false;

        $this -> page['parent_slug'] = sanitize_key($_page['parent_slug']);

        return $this;
    }

    private function createPage( array $_page ) {

        $page = array();

        $required = array('page_title', 'menu_title', 'capability', 'menu_slug');

        foreach( $required as $key ) {

            if ( !array_key_exists($key, $_page) ) 
                return array();
            else if($key == 'menu_slug') 
                $page[$key] = sanitize_key($_page[$key]);
            else 
                $page[$key] = $_page[$key];
        }

        $optional = array('function', 'icon_url', 'position');

        foreach( $optional as $key ) {
            if ( !array_key_exists($key, $_page) ) 
                $page[$key] = '';
            else 
                $page[$key] = $_page[$key];
        }

        return $page;
    }

    public function register_page() {
        $page = $this -> page;

        if ( !array_key_exists('parent_slug', $page) ) 
			add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['function'], $page['icon_url'], $page['position']);

		else
			add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['function']);

        return $this;
    }

    public function get_slug() {
        
        if(array_key_exists('menu_slug', $this -> page)) 
            return $this -> page['menu_slug'];
        
        return '';
    }

    public function get($name) {
        if(array_key_exists($name, $this -> page)) 
            return $this -> page[$name];
        
        return '';
    }

    public function render_page(){

        $path = plugin_dir_path($this -> __FILE__);

        $class_name = explode('\\', get_class($this));
        $class_name = array_pop($class_name);

        $slug = explode('?', $_SERVER['REQUEST_URI'])[0] . '?';

        if(isset($_GET)) foreach($_GET as $key => $value) {
            if($key != 'tab') $slug .= '&'.$key.'='.$value;
        }

        return include_once $path . "/src/Pages/".$class_name.".php";
    }

}
