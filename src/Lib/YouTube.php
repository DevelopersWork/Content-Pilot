<?php
/**
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Lib;

use DW\ContentPilot\Core\Store;

class YouTube
{

    private $store;

    public function __construct()
    {

        $this -> store = new Store();

        add_action('init', [$this, 'init']);
    }

}
