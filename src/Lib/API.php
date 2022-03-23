<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

use DW\ContentPilot\Core\Store;

class API
{

    private $store;
    
    public function setStore(Store $store)
    {
        $this -> store = $store;
    }

    public function createCategories()
    {
        $this -> store -> debug(get_class($this).':createCategory()', '{STARTED}');
        $categories = [];

        $_categories = $this -> store -> get('categories');

        if ($_categories['name'] == '') {
            $this -> store -> set('_ERROR', true);
        }

        $parent = wp_create_category(strtoupper(DWContetPilotPrefix) .'_'. $_categories['name']);

        if (!$parent) {
            $this -> store -> set('_ERROR', true);
        } else {
            $categories[$_categories['name']] = $parent;
        }

        foreach ($_categories['value'] as $category) {
            $child = wp_create_category(strtoupper(DWContetPilotPrefix) .'_'. $category, $parent);

            if ($child) {
                $categories[$category] = $child;
            } else {
                $this -> store -> set('_ERROR', true);
            }
        }

        $this -> store -> set('_categories', $categories);

        return true;
    }

    public function createPostTypes()
    {
    }
}
