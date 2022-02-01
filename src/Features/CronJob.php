<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Core\YouTube;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class CronJob {

    private $store;

    function __construct( $store ) {

        $this -> store = $store;

        $this -> store -> set('CronJob', $this);
    
    }

    public function register() {
        global $wpdb;
        
        $query = "
            SELECT 
                DISTINCT jobs.id AS job_id,
                triggers.name AS trigger_name, 
                triggers.type AS trigger_type, 
                jobs.hash AS job_hash
            FROM 
                ".PLUGIN_PREFIX."_jobs_services_secrets_map AS ref
            JOIN 
                ".PLUGIN_PREFIX."_jobs AS jobs ON jobs.id = ref.job_id
            JOIN 
                ".PLUGIN_PREFIX."_triggers AS triggers ON triggers.id = ref.trigger_id
        ";

        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        foreach($_result as $_ => $row) {

            $name = PLUGIN_SLUG . '_' . $row['trigger_name'] . '#' . $row['job_id'];

            $args = array ( $row['job_hash'] );

            add_action( $name, array($this, 'run') );
            
            if ( ! wp_next_scheduled( $name, $args ) ) {

                wp_schedule_event( time() + 3, $row['trigger_type'], $name, $args );

            } 

        }

    }

    public function run( string $job_hash ) {
        global $wpdb;

        if( $job_hash == "" ) {

            $table = PLUGIN_PREFIX . '_audits';

            $data = array(
                'job_id' => $request['job_id'], 
                'post_id' => isset($response) ? $response : NULL,
                'is_success' => 0
            );
            
            $st = '';
            foreach($data as $key => $value) $st .= md5($key . $value). '_';
            $st .= md5('insert_timestamp' . microtime(true)). '_';
            $data['hash'] = md5($st);
            
            $format = array('%s', '%d', '%d', '%s');
            
            return $wpdb -> insert($table, $data, $format);
        }

        $query = "
            SELECT 
                jobs.id as job_id,
                metas.data, metas.key_required, 
                secrets.value AS _key, secrets.id AS secret_id, 
                services.name as service_name, 
                seconds, minutes, hours, days, triggers.type AS trigger_type, triggers.name AS trigger_name 
            FROM 
                ".PLUGIN_PREFIX."_jobs_services_secrets_map AS ref
            JOIN 
                ".PLUGIN_PREFIX."_jobs AS jobs ON jobs.id = ref.job_id AND jobs.hash = '".$job_hash."'
            JOIN 
                ".PLUGIN_PREFIX."_metas AS metas ON metas.id = ref.meta_id
            JOIN 
                ".PLUGIN_PREFIX."_services AS services ON services.id = ref.service_id
            JOIN 
                ".PLUGIN_PREFIX."_triggers AS triggers ON triggers.id = ref.trigger_id
            LEFT JOIN 
                ".PLUGIN_PREFIX."_secrets AS secrets ON secrets.id = ref.secret_id
        ";

        $_result = $wpdb->get_results( $query, 'ARRAY_A' );
        $index = rand(0, count($_result) - 1);

        $request = $_result[$index];
        
        if( strtolower($request['service_name']) == 'youtube' &&  $request['key_required'] == 1 ) {

            $response = CronJob:: youtube($request['_key'], $request['data']);

        }

        $table = PLUGIN_PREFIX . '_audits';

        $data = array(
            'job_id' => $request['job_id'], 
            'post_id' => isset($response) ? $response : NULL,
            'is_success' => isset($response) ? 1 : 0,
            'secret_id' => $request['secret_id']
        );
        
        $st = '';
        foreach($data as $key => $value) $st .= md5($key . $value). '_';
        $st .= md5('insert_timestamp' . microtime(true)). '_';
        $data['hash'] = md5($st);
        
        $format = array('%s', '%d', '%d', '%s', '%s');
        
        return $wpdb -> insert($table, $data, $format);

        return FALSE;

    }

    public function youtube($key, $q = "") {

        $yt = new YouTube($this -> store);

        return $yt -> makePost($key, $q);
        
    }

}

