<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

class WPPage {

    private $page = array();

    WPPage($page = array()) {

        $this -> page = $page;

        add_action(DWContetPilotPrefix.'register_menus', [$this, 'register_page']);

    }

    protected function addPage( array $_page ) {
        
        $this -> page = $this -> createPage($_page);

        return $this;
    }

    protected function addSubPage( array $_page ) {

        if ( !array_key_exists('parent_slug', $_page) ) 
            return array();

        $this -> addPage($_page);

        $this -> page['parent_slug'] = $_page['parent_slug'];

        return $this;
    }

    private function createPage( array $_page ) {

        $page = array();

        $required = array('page_title', 'menu_title', 'capability', 'menu_slug');

        foreach( $required as $key ) {

            if ( !array_key_exists($key, $_page) ) 
                return array();
            else 
                $page[$key] = $_page[$key];
        
        }

        $optional = array('function', 'icon_url', 'position');

        foreach( $optional as $key ) {
            if ( !array_key_exists($key, $_page) ) 
                $page[$key] = NULL;
            else 
                $page[$key] = $_page[$key];
        }

        return $page;
    }

    public function register_page() {
        $page = $this -> page;

        if ( !array_key_exists('parent_slug', $_page) ) 
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['function'], $page['icon'], $page['position'] );

		else
			add_submenu_page( $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['function'] );

        return $this;
    }

}
