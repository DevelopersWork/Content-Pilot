<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core;

use DW\ContentPilot\Core\Store;
use DW\ContentPilot\Features\{ 
    Dashboard, Secrets, Settings, Jobs
};

class Service
{

    private $store;
    private $services;

    public function __construct()
    {

        $this -> store = new Store();

        $this -> store -> debug(get_class($this).':__construct()', '{STARTED}');

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
                // Settings::class
            )
        );
    }

    public function register()
    {

        add_action(DWContetPilotPrefix.'register_actions', array( $this, 'register_actions'));

        foreach ($this -> features as $type => $classes) {
            $this -> store -> debug(get_class($this).':register()', '{REGISTERING} '.$type);
            
            foreach ($classes as $FeatureClass) {
                $feature = $this -> instantiate($FeatureClass);

                if (method_exists($feature, 'register')) {
                    $feature -> register();

                    $this -> services[get_class($feature)] = $feature;
                }
            }
        }

        return $this;
    }

    public function register_actions()
    {
        add_action(DWContetPilotPrefix.'register_scripts', array($this, 'register_scripts'));
        add_action(DWContetPilotPrefix.'register_styles', array($this, 'register_styles'));
    }

    public function register_scripts()
    {

        $this -> store -> debug(get_class($this).':register_scripts()', '{STARTED}');

        // jQuery v3.3.1
        wp_register_script(DWContetPilotPrefix . '-jquery3', 'https://code.jquery.com/jquery-3.3.1.min.js', array(), '3.3.1', true);
        wp_script_add_data(DWContetPilotPrefix . '-jquery3', array( 'integrity', 'crossorigin' ), array( 'sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=', 'anonymous' ));
        wp_enqueue_script(DWContetPilotPrefix . '-jquery3');
        // Bootstrap v5.1.3
        wp_register_script(DWContetPilotPrefix . '-bootstrap.bundle.min', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array(), '5.1.3', true);
        wp_script_add_data(DWContetPilotPrefix . '-bootstrap.bundle.min', array( 'integrity', 'crossorigin' ), array( ));
        wp_enqueue_script(DWContetPilotPrefix . '-bootstrap.bundle.min');
        // Admin Script
        wp_enqueue_script(DWContetPilotPrefix . '-script.admin', dw_cp_plugin_dir_url . 'assets/js/script.admin.js', array(), dw_cp_plugin_version, true);

        return $this;
    }

    public function register_styles()
    {

        $this -> store -> debug(get_class($this).':register_styles()', '{STARTED}');

        // Bootstrap v5.1.3
        wp_enqueue_style(DWContetPilotPrefix . '-bootstrap.min', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', array(), '5.1.3', 'all');
        // Admin Style
        wp_enqueue_style(DWContetPilotPrefix . '-style.admin', dw_cp_plugin_dir_url . 'assets/css/style.admin.css', array(), dw_cp_plugin_version, 'all');

        return $this;
    }

    private function instantiate($class)
    {
        return new $class();
    }

    public function list()
    {
        $s_list = array();

        foreach ($this -> services as $key => $val) {
            array_push($s_list, $key);
        }

        return $s_list;
    }

    public function get(string $name)
    {

        return $this->services[$name];
    }
}
