<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class Activate {

    public static function activate() {
        flush_rewrite_rules();
    }
}