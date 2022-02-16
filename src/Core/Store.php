<?php
/** 
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core;

class Store {

    private $MEMORY;

    public function __construct() {
        
        $this->MEMORY = array(
            'STORE' => 'Lol',
            'admin_notice' => null
        );

    }

    public function set(string $name, $value){
        $this->MEMORY[$name] = $value;

        return $this;
    }

    public function get(string $name){
        return $this->MEMORY[$name];
    }

    public function log(string $trace, string $message) {

        $line = time() . ': ' . $trace . ':: ' . $message . PHP_EOL;
        
        file_put_contents('php://stdout', print_r($line, TRUE));
    }

    public function error(string $trace, string $message) {

        $line = time() . ': ' . $trace . '>> ' . $message . PHP_EOL;
        
        file_put_contents('php://stderr', print_r($line, TRUE));
    }

    public function admin_notice() {

        // $this -> store -> set('admin_notice', array('msg' => 'uWWW.com', 'type' => 'error', 'domain' => 'dw-content-pilot'));

        // add_action( 'admin_notices', array( $this -> store, 'admin_notice') );


        if ( !$this -> MEMORY['admin_notice'] ) {
            return;
        }

        $admin_notice = $this -> MEMORY['admin_notice'];

        $type = isset($admin_notice['type']) ? $admin_notice['type'] : 'info';
        $msg = isset($admin_notice['msg']) ? $admin_notice['msg'] : 'No message';
        $domain = isset($admin_notice['domain']) ? $admin_notice['domain'] : 'dw-content-pilot';
        $dismissible = isset($admin_notice['dismissible']) ? $admin_notice['dismissible'] : false;
        
        $class = 'notice notice-' . $type . ($dismissible ? ' is-dismissible' : '');
        $message = __( $msg, $domain );
 
        printf( '<div class="%1$s"><p><strong>%2$s: </strong>%3$s</p></div>', esc_attr( $class ), esc_attr( ucfirst($type) ), esc_html( $message ) ); 
        
        $this -> MEMORY['admin_notice'] = null;
    }

}