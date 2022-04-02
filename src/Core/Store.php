<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Core;

class Store
{

    private $MEMORY;
    private $debug = false;

    public function __construct()
    {
        
        $this->MEMORY = array(
            'notices' => array()
        );
    }

    public function set(string $name, $value)
    {
        $this->MEMORY[$name] = $value;

        return $this;
    }

    public function get(string $name)
    {
        if (array_key_exists($name, $this->MEMORY)) {
            return $this->MEMORY[$name];
        }
        return null;
    }

    public function append(string $name, $value)
    {
        $this->MEMORY[$name][] = $value;
        
        return $this;
    }

    public function log(string $trace, string $message)
    {

        $line = $trace . ':: ' . $message . PHP_EOL;
        
        file_put_contents('php://stdout', print_r($line, true));
    }

    public function debug(string $trace, string $message)
    {

        if (!$this -> debug) {
            return null;
        }

        $line = $trace . '<:> ' . $message . PHP_EOL;
        
        file_put_contents('php://stdout', print_r($line, true));
    }

    public function info(string $trace, string $message)
    {

        $line = $trace . ':: ' . $message . PHP_EOL;
        
        file_put_contents('php://stdout', print_r($line, true));
    }

    public function error(string $trace, string $message)
    {

        $line = $trace . '>> ' . $message . PHP_EOL;
        
        file_put_contents('php://stderr', print_r($line, true));
    }

    /*
    * @usage add_action( 'admin_notices', array( $this -> store, 'adminNotice') );
    */
    public function adminNotice()
    {

        if (!$this -> MEMORY['notices']) {
            return;
        }

        $NOTICE = '<div class="%1$s"><p><strong>[Content Pilot] %2$s: </strong>%3$s</p></div>';

        $notices = $this -> MEMORY['notices'];

        foreach ($notices as $notice) {
            $type = isset($notice['type']) ? $notice['type'] : 'info';
            $msg = isset($notice['msg']) ? $notice['msg'] : 'No message';
            $domain = isset($notice['domain']) ? $notice['domain'] : 'dw-content-pilot';
            $dismissible = isset($notice['dismissible']) ? $notice['dismissible'] : false;

            $class = 'notice notice-' . $type . ($dismissible ? ' is-dismissible' : '');
            $message = __($msg, $domain);

            if ($type == 'error') {
                $this -> error($domain, $message);
            } elseif ($type == 'warning') {
                $this -> log($domain, $message);
            } elseif ($type == 'success') {
                $this -> debug($domain, $message);
            }

            printf($NOTICE, esc_attr($class), esc_attr(ucfirst($type)), esc_html($message));
        }
        
        $this -> MEMORY['notices'] = null;
    }
}
