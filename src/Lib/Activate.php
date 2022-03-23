<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

use DW\ContentPilot\Core\Store;
use DW\ContentPilot\Lib\Validations;

class Activate
{

    private $store;
    private $name;

    public function __construct(string $__FILE__)
    {
        $this -> store = new Store();
        $this -> __FILE__ = $__FILE__;
    }

    public function activate()
    {
        $this -> store -> log(get_class($this).':activate()', '{STARTED}');

        $this -> compatibilityCheck();
        $this -> createSQLTables();
        $this -> loadReferenceData();

        flush_rewrite_rules();
    }

    private function createSQLTables()
    {
        global $wpdb;

        $this -> store -> log(get_class($this).':createSQLTables()', '{STARTED}');

        $charset_collate = $wpdb->get_charset_collate();
        $plugin_path = plugin_dir_path($this -> __FILE__);

        $ddl_path = $plugin_path . 'assets/ddl/';
        $ddls = array_diff(scandir($ddl_path), array('.', '..'));

        $regex = "/^.*\.(sql)$/i";

        $tables = array('triggers');

        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);


        for ($i = 0; $i < count($tables); $i++) {
            $table = $tables[$i];
            $ddl = $table . '.sql';

            if (! in_array($ddl, $ddls, true)) {
                continue;
            }

            $sql = file_get_contents($ddl_path . $ddl);
            $sql = str_replace("%table_prefix%", $table_prefix, $sql);
            $sql = str_replace("%charset_collate%", $charset_collate, $sql);

            dbDelta($sql);
        }
    }

    private function compatibilityCheck()
    {

        $this -> store -> log(get_class($this).':compatibilityCheck()', '{STARTED}');

        $php_version_check = Validations::validate_php_version($this -> store, $this -> __FILE__);

        if (!$php_version_check) {
            return $php_version_check;
        }

        $wp_version_check = Validations::validate_wp_version($this -> store, $this -> __FILE__);

        if (!$wp_version_check) {
            return $wp_version_check;
        }

        return $this -> store -> log(get_class($this).':compatibilityCheck()', 'PHP v'.$php_version_check.', Wordpress v'.$wp_version_check);
    }

    private function loadReferenceData()
    {
        global $wpdb;

        $this -> store -> log(get_class($this).':loadReferenceData()', '{STARTED}');

        $charset_collate = $wpdb->get_charset_collate();
        $plugin_path = plugin_dir_path($this -> __FILE__);

        $path = $plugin_path . 'assets/dml/';
        $dmls = array_diff(scandir($path), array('.', '..'));

        $regex = "/^.*\.(sql)$/i";

        $tables = array('triggers');

        $table_prefix = $wpdb -> base_prefix . esc_attr(DWContetPilotPrefix);
        

        for ($i = 0; $i < count($tables); $i++) {
            $table = $tables[$i];
            $dml = $table . '.sql';

            if (! preg_match($regex, $dml)) {
                continue;
            }

            $queries = file_get_contents($path . $dml);
            $queries = str_replace("%table_prefix%", $table_prefix, $queries);
            
            foreach (explode('\n', $queries) as $query) {
                dbDelta($query);
            }
        }
    }
}
