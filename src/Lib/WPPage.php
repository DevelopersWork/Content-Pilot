<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

use DW\ContentPilot\Core\Store;
use DW\ContentPilot\Lib\API;

class WPPage extends API
{

    private $page = array();

    public function __construct($page = array())
    {
        parent::__construct();

        $this -> page = $page;

        $posts_per_page = 10;
        if(isset($_GET['posts_per_page']) && is_int($_GET['posts_per_page'])) 
            $posts_per_page = $_GET['posts_per_page'];
        $this -> store -> set('posts_per_page', $posts_per_page);

        $this -> store -> set('tabs', array());
        
    }

    protected function addPage(array $_page)
    {

        $this -> store -> debug(get_class($this).':addPage()', '{STARTED}');
        
        $this -> page = $this -> createPage($_page);

        if (!$this -> page) {
            return $this -> store -> set('_ERROR', true);
        }

        return $this;
    }

    protected function addSubPage(array $_page)
    {

        $this -> store -> debug(get_class($this).':addSubPage()', '{STARTED}');

        if (!array_key_exists('parent_slug', $_page)) {
            return $this -> store -> set('_ERROR', true);
        }

        if (!$this -> addPage($_page)) {
            return $this -> store -> set('_ERROR', true);
        }

        $this -> page['parent_slug'] = sanitize_key($_page['parent_slug']);

        return $this;
    }

    private function createPage(array $_page)
    {

        $this -> store -> debug(get_class($this).':createPage()', '{STARTED}');

        $page = array();

        $required = array('page_title', 'menu_title', 'capability', 'menu_slug');

        foreach ($required as $key) {
            if (!array_key_exists($key, $_page)) {
                return array();
            } elseif ($key == 'menu_slug') {
                $page[$key] = sanitize_key($_page[$key]);
            } else {
                $page[$key] = $_page[$key];
            }
        }

        $optional = array('function', 'icon_url', 'position');

        foreach ($optional as $key) {
            if (!array_key_exists($key, $_page)) {
                $page[$key] = '';
            } else {
                $page[$key] = $_page[$key];
            }
        }

        return $page;
    }

    public function get($name)
    {
        
        if (array_key_exists($name, $this -> page)) {
            return $this -> page[$name];
        }
        
        return '';
    }

    public function render_page()
    {
        $this -> store -> debug(get_class($this).':render_page()', '{STARTED}');

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">'.$this -> store -> get('name').'</h1>';
        echo '<a href="'.$this -> getURI().'tab=create" class="page-title-action">Add New</a>';
        echo '<hr class="wp-header-end">';
        echo settings_errors();

        if (isset($_GET['tab'])) {
            $active_tab = strtolower($_GET['tab']);
        } else {
            $active_tab = 'view';
        }

        if ( in_array( $active_tab, $this -> store -> get('tabs') ) ) {
            $this -> store -> debug(get_class($this).':render_page()', '{RENDERING-'.$active_tab.'}');
            include_once dw_cp_plugin_dir_path.'/src/Pages/'.$this -> store -> get('name').'/'.$active_tab.'.php';
        }
        else
            include_once dw_cp_plugin_dir_path.'/src/Pages/404.php';
        

        echo '</div>';

    }

    public function render_page_path()
    {
        $this -> store -> debug(get_class($this).':render_page_path()', '{STARTED}');

        include_once dw_cp_plugin_dir_path . '/src/Pages/' . $this -> store -> get('name') . '.php';
    }

    public function register_actions()
    {
            
        parent::register_actions();

        add_action(DWContetPilotPrefix.'register_menus', [$this, 'register_menus']);

        add_action(DWContetPilotPrefix.'register_scripts', array($this, 'register_scripts'));
        add_action(DWContetPilotPrefix.'register_styles', array($this, 'register_styles'));

        return $this;
    }

    public function register_menus()
    {

        $this -> store -> debug(get_class($this).':register_menus()', '{STARTED}');

        $this -> auth_key = md5(
            $this -> store -> get('_AUTH_KEY') . '_' . $this -> get('menu_slug')
        );

        $page = $this -> page;

        if (!array_key_exists('parent_slug', $page)) {
            add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['function'], $page['icon_url'], $page['position']);
        } else {
            add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['function']);
        }

        $this -> parseRequest();

        return $this;
    }

    public function register_scripts()
    {
        $this -> store -> debug(get_class($this).':register_scripts()', '{STARTED}');
    }

    public function register_styles()
    {
        $this -> store -> debug(get_class($this).':register_styles()', '{STARTED}');
    }
}