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

    public function log(string $trace, string $message) {

        $line = time() . ': ' . $trace . ':: ' . $message . PHP_EOL;
        
        file_put_contents('php://stdout', print_r($line, TRUE));
    }

    public function error(string $trace, string $message) {

        $line = time() . ': ' . $trace . '>> ' . $message . PHP_EOL;
        
        file_put_contents('php://stderr', print_r($line, TRUE));
    }

}