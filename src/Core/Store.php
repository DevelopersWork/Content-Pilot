<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class Store {

    private $CONSTANTS;

    public function __construct() {
        
        $this->CONSTANTS = array('STORE' => 'Lol');

    }

    public function set(string $name, $value){
        $this->CONSTANTS[$name] = $value;

        return $this;
    }

    public function get(string $name){
        return $this->CONSTANTS[$name];
    }

}