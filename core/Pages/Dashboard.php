<?php

/**
 * @package DWContentPilot
 */

namespace DW\ContentPilot\Core\Pages;

use DW\ContentPilot\Lib\{WPPage};

class Dashboard extends WPPage
{

    public function __construct($parent_slug)
    {
        parent::__construct();

        $this->page = [
            ...$this->page,
            'parent_slug' => $parent_slug,
            'menu_slug' => $parent_slug,
            'callback' => array($this, 'menu_page_callback')
        ];
    }
}
