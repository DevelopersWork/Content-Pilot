<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

class Validations {

    public static function validate_php_version() {

        if( !defined('PHP_VERSION') ) {
            return false;
        }

        if ( version_compare( PHP_VERSION, '7.4', '<=' ) ) {
            return false;
        }

        return PHP_VERSION;
    }

    public static function validate_wp_version() {

        if( !isset($GLOBALS['wp_version']) ) {
            return false;
        }

        if ( version_compare( $GLOBALS['wp_version'], '5.9', '<' ) ) {
            return false;
        }

        return $GLOBALS['wp_version'];
    }

}