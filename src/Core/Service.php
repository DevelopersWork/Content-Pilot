<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core;

use DW\ContentPilot\Core\{ Store };

class Service {

    private $store, $services;

    function __construct($__FILE__) {

        $this -> store = new Store();

        $this -> store -> log( get_class($this).':__construct()', '{STARTED}' );

        /*
        * 3-categories of services:
        * 1. Public
        * 2. Private
        * 3. Protected
        */

        $this -> services = $this -> fetchServices($__FILE__);

    }
    

    private function fetchServices($__FILE__) {

        $this -> store -> log( get_class($this).':fetchServices()', '{STARTED}' );

        $plugin_path = plugin_dir_path( $__FILE__ );

        $file = $plugin_path . 'assets/reference/services.json';
        
        $content = file_get_contents( $file );
        
        foreach(json_decode($content) as $item){
            $this -> store -> set($item -> id, $item);
        }

        return count(json_decode($content));
    }

    public function register() {

        foreach($this -> services as $service_class) {
            
            $service = $this -> instantiate($service_class);

            if(method_exists($service, 'register')){
                $service -> register();
            }
        }

    }

    private function instantiate($class){

        return new $class($this -> store);
    }

    public function list() {
        $s_list = array();

        foreach($dic as $key => $val) {
            array_push($s_list, $key);
        }

        return $s_list;
    }

    public function get(string $name) {
        return $this->services[$name];
    }
}
