<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class Deactivate {

    public static function deactivate() {
        flush_rewrite_rules();
    }
}