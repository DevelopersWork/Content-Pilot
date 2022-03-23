<?php
/**
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class SetupAPI
{

    private $pages = array();
    private $sub_pages = array();
    private $settings = array();
    private $sections = array();
    private $fields = array();

    function register()
    {
        if (! empty($this->pages) || ! empty($this->sub_pages)) {
            add_action('admin_menu', array( $this, 'addAdminMenu' ));
        }

        if (! empty($this->settings)) {
            add_action('admin_init', array( $this, 'registerCustomFields' ));
        }
    }

    public function addAdminMenu()
    {
        foreach ($this->pages as $page) {
            add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon'], $page['position']);
        }

        foreach ($this->sub_pages as $page) {
            add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback']);
        }
    }

    public function registerCustomFields()
    {

        // register setting
        foreach ($this->settings as $setting) {
            register_setting($setting["option_group"], $setting["option_name"], ( isset($setting["callback"]) ? $setting["callback"] : '' ));
        }

        // add settings section
        foreach ($this->sections as $section) {
            add_settings_section($section["id"], $section["title"], ( isset($section["callback"]) ? $section["callback"] : '' ), $section["page"]);
        }

        // add settings field
        foreach ($this->fields as $field) {
            add_settings_field($field["id"], $field["title"], ( isset($field["callback"]) ? $field["callback"] : '' ), $field["page"], $field["section"], ( isset($field["args"]) ? $field["args"] : '' ));
        }
    }

    public function addPages(array $_pages)
    {
        
        $pages = array();
        foreach ($_pages as $_page) {
            $page = $this -> createPage($_page);
            if (! empty($page)) {
                array_push($pages, $page);
            }
        }
        $this -> pages = $pages;

        return $this;
    }

    public function asSubPage(string $title)
    {
        
        if (empty($this->pages)) {
            return $this;
        }

        if (!$title) {
            return $this;
        }

        $admin_page = array();
        foreach ($this->pages[0] as $key => $value) {
            $admin_page[$key] = $value;
        }
        
        $admin_page['menu_title'] = $title;
        $admin_page['parent_slug'] = $admin_page['menu_slug'];
        
        $subpage = $this -> createPage($admin_page);
        if (! empty($subpage)) {
            $this->sub_pages = array($subpage);
        }

        return $this;
    }

    public function addSubPages(array $_sub_pages)
    {

        $pages = array();
        foreach ($_sub_pages as $_page) {
            $page = $this -> createPage($_page);
            if (! empty($page)) {
                array_push($pages, $page);
            }
        }
        $this -> sub_pages = $pages;

        return $this;
    }

    public function addSettings(array $_settings)
    {

        $settings = array();
        
        foreach ($_settings as $_setting) {
            $setting = $this -> createSetting($_setting);
            if (! empty($setting)) {
                array_push($settings, $setting);
            }
        }
        
        $this -> settings = $settings;

        return $this;
    }

    public function addSections(array $_sections)
    {

        $sections = array();
        
        foreach ($_sections as $_section) {
            $section = $this -> createSection($_section);
            if (! empty($section)) {
                array_push($sections, $section);
            }
        }
        
        $this -> sections = $sections;

        return $this;
    }

    public function addFields(array $_fields)
    {

        $fields = array();
        
        foreach ($_fields as $_field) {
            $field = $this -> createField($_field);
            if (! empty($field)) {
                array_push($fields, $field);
            }
        }
        
        $this -> fields = $fields;

        return $this;
    }

    public static function isKeyExists($key, $array)
    {
        return array_key_exists($key, $array);
    }

    public function createPage($_page)
    {
        $page = array();

        if (! $this->isKeyExists('page_title', $_page)) {
            return array();
        }
        $page['page_title'] = $_page['page_title'];
        if (! $this->isKeyExists('menu_title', $_page)) {
            return array();
        }
        $page['menu_title'] = $_page['menu_title'];
        if (! $this->isKeyExists('capability', $_page)) {
            return array();
        }
        $page['capability'] = $_page['capability'];
        if (! $this->isKeyExists('menu_slug', $_page)) {
            return array();
        }
        $page['menu_slug'] = $_page['menu_slug'];
        if (! $this->isKeyExists('callback', $_page)) {
            return array();
        }
        $page['callback'] = $_page['callback'];

        if (! $this->isKeyExists('parent_slug', $_page)) {
            if (! $this->isKeyExists('icon', $_page)) {
                return array();
            }
            $page['icon'] = $_page['icon'];
            if (! $this->isKeyExists('position', $_page)) {
                return array();
            }
            $page['position'] = $_page['position'];
        } else {
            $page['parent_slug'] = $_page['parent_slug'];
        }

        return $page;
    }

    public function createSetting($_setting)
    {
        $setting = array();

        if (! $this->isKeyExists('option_group', $_setting)) {
            return array();
        }
        $setting['option_group'] = $_setting['option_group'];
        
        if (! $this->isKeyExists('option_name', $_setting)) {
            return array();
        }
        $setting['option_name'] = $_setting['option_name'];
        
        if ($this->isKeyExists('callback', $_setting) && $_setting['callback'] != null) {
            $setting['callback'] = $_setting['callback'];
        }
        
        return $setting;
    }

    public function createSection($_section)
    {
        $section = array();

        if (! $this->isKeyExists('id', $_section)) {
            return array();
        }
        $section['id'] = $_section['id'];
        if (! $this->isKeyExists('title', $_section)) {
            return array();
        }
        $section['title'] = $_section['title'];
        if (! $this->isKeyExists('callback', $_section)) {
            return array();
        }
        $section['callback'] = $_section['callback'];
        if (! $this->isKeyExists('page', $_section)) {
            return array();
        }
        $section['page'] = $_section['page'];

        return $section;
    }

    public function createField($_field)
    {
        $field = array();

        if (! $this->isKeyExists('id', $_field)) {
            return array();
        }
        $field['id'] = $_field['id'];
        
        if (! $this->isKeyExists('title', $_field)) {
            return array();
        }
        $field['title'] = $_field['title'];
        
        if (! $this->isKeyExists('page', $_field)) {
            return array();
        }
        $field['page'] = $_field['page'];

        if (! $this->isKeyExists('callback', $_field)) {
            return array();
        }
        $field['callback'] = $_field['callback'];

        if (! $this->isKeyExists('section', $_field)) {
            $field['section'] = '';
        } else {
            $field['section'] = $_field['section'];
        }
        
        if (! $this->isKeyExists('args', $_field)) {
            $field['args'] = '';
        } else {
            $field['args'] = $_field['args'];
        }

        return $field;
    }

    public function get($key)
    {

        if ($key == 'pages') {
            return $this -> pages;
        }
        
        if ($key == 'sub_pages') {
            return $this -> sub_pages;
        }
        
        if ($key == 'settings') {
            return $this -> settings;
        }
        
        if ($key == 'sections') {
            return $this -> sections;
        }
        
        if ($key == 'fields') {
            return $this -> fields;
        }

        return null;
    }
}
