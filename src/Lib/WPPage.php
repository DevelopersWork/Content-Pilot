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
    protected $auth_key = "";

    public function __construct($page = array())
    {
        parent::__construct();

        $this -> page = $page;

        if ($this -> page) {
            add_action(DWContetPilotPrefix.'register_menus', [$this, 'register_page']);
        }
        
    }

    protected function addPage(array $_page)
    {
        
        $this -> page = $this -> createPage($_page);

        if (!$this -> page) {
            return $this -> store -> set('_ERROR', true);
        }

        return $this;
    }

    protected function addSubPage(array $_page)
    {

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

    public function register_page()
    {

        $this -> auth_key = md5(
            $this -> store -> get('_AUTH_KEY') . '_' . $this -> get('menu_slug')
        );

        $page = $this -> page;

        if (!array_key_exists('parent_slug', $page)) {
            add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['function'], $page['icon_url'], $page['position']);
        } else {
            add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['function']);
        }

        $this -> createCategories();
        $this -> createPostType();
        $this -> parseRequest();

        return $this;
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

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">'.$this -> store -> get('name').'</h1>';
        echo '<hr class="wp-header-end">';
        echo settings_errors();
        echo '</div>';

    }
}
