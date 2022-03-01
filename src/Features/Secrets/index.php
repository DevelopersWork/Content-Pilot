<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Features\Secrets;

class Secrets extends \DW\ContentPilot\Core\Store {

    public function __construct() {
        parent::__construct();
    }

    public function get_secret( $secret_name ) {
        $secret = get_option( $secret_name );
        return $secret;
    }

    public function set_secret( $secret_name, $secret_value ) {
        update_option( $secret_name, $secret_value );
    }

}