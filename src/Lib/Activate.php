<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

use DW\ContentPilot\Core\{ Store };
use DW\ContentPilot\Lib\{ Validations };

class Activate {

    public static function activate() {

        flush_rewrite_rules();

    }


}