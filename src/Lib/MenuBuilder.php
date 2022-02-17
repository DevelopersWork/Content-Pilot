<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

class MenuBuilder {

    protected $page = array();
    protected $sections = array();

    public function addPage( array $_page ) {
        
        $this -> page = $this -> createPage($_page);

        return $this;
    }

    public function addSubPage( array $_page ) {

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
                continue;
            else 
                $page[$key] = $_page[$key];
        }

        return $page;
    }

    public function addSections( array $_sections ) {

        $sections = array();
        
        foreach ($_sections as $_section) {
            $section = $this -> createSection($_section);
            if ( ! empty($section) ) array_push($sections, $section);
        }
        
        $this -> sections = $sections;

        return $this;

    }

    private function createSection( array $_section ) {

        $section = array();

        $required = array('id', 'title', 'callback', 'page');

        foreach( $required as $key ) {

            if ( !array_key_exists($key, $_section) ) 
                return array();
            else 
                $section[$key] = $_section[$key];
        
        }

        return $section;
    }

    public function get( string $key ) {

        if($key == 'page') return $this -> page;
        
        if($key == 'sections') return $this -> sections;

        return null;
    }

}
