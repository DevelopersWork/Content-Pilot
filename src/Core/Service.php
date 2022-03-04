<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core;

use DW\ContentPilot\Core\{ Store };

use DW\ContentPilot\Features\{ Dashboard, Secrets, Settings };

class Service {

    private $store, $services, $__FILE__;

    function __construct($__FILE__) {

        $this -> __FILE__ = $__FILE__;

        $this -> store = new Store();

        $this -> store -> log( get_class($this).':__construct()', '{STARTED}' );

        /*
        * 4-categories of services:
        * 1. Public -> Should be reloaded whenever page loading is done
        * 2. Private -> Only when logged in user access
        * 3. Protected -> Only logged in user has the certain access
        * 4. System -> Run by the system hooks
        */

        $this -> features = array(
            'system' => array(),
            'public' => array(),
            'private' => array(
                Dashboard::class,
                Secrets::class
            ),
            'protected' => array(
                Settings::class
            )
        );

        // $this -> services = $this -> fetchServices($__FILE__);
        $this -> services = array();

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

        foreach($this -> features as $type => $classes){
            
            $this -> store -> log( get_class($this).':register()', '{REGISTERING} '.$type);
            
            foreach($classes as $FeatureClass) {

                $feature = $this -> instantiate($FeatureClass);

                if(method_exists($feature, 'register')) {
                    
                    $feature -> register();

                    $this -> services[get_class($feature)] = $feature;
                }
            }
        }

        return $this;

    }

    private function instantiate($class){
        return new $class($this -> __FILE__);
    }

    public function list() {
        $s_list = array();

        foreach($this -> services as $key => $val) {
            array_push($s_list, $key);
        }

        return $s_list;
    }

    public function get(string $name) {

        return $this->services[$name];
    
    }
}
