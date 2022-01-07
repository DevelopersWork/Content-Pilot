<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Core\YouTube;

class CronJob {

    private $store;

    function __construct( $store ) {

        $this -> store = $store;
    
    }

    public function register() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // wordpress database object
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $query = "
            SELECT 
                ref.*,
                jobs.name AS job_name,
                secrets.value AS secret,
                services.name AS service_name,
                triggers.name AS trigger_name, triggers.type AS trigger_type, triggers.seconds, 
                triggers.minutes, triggers.hours, triggers.days
            FROM 
                ".PLUGIN_PREFIX."_jobs_services_secrets_map AS ref
            JOIN 
                ".PLUGIN_PREFIX."_jobs AS jobs ON jobs.id = ref.job_id
            JOIN 
                ".PLUGIN_PREFIX."_secrets AS secrets ON secrets.id = ref.secret_id
            JOIN 
                ".PLUGIN_PREFIX."_services AS services ON services.id = ref.service_id
            JOIN 
                ".PLUGIN_PREFIX."_triggers AS triggers ON triggers.id = ref.trigger_id AND triggers.disabled = 0
        ";

        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        foreach($_result as $_ => $row) {

            $name = PLUGIN_SLUG . '_' . $row['trigger_name'];

            add_action( $name, array($this, strtolower($row['service_name'])) );
        }

    }

    public function youtube() {

        $yt = new YouTube($this -> store);

        $yt -> createService() -> makePost(0);
        
    }

}

