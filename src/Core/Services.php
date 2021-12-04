<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Core;

class Services {

    private $servicesList, $store;

    function __construct($store, array $servicesList) {

        $this -> store = $store;
        
        $this -> store -> set('SetupAPI', SetupAPI:: class) -> set('SERVICES', $this);

        $this -> servicesList = $servicesList;

    }

    public function register() {

        foreach($this -> servicesList as $service_class) {
            
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
