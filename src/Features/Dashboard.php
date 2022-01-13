<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Features\Manager;

class Dashboard extends Manager {

    function __construct( $store ) {

        parent::__construct( $store, 'Dashboard' );

        $this -> setPage ( 'manage_options', array( $this, 'renderPage' ), PLUGIN_SLUG, null, 'dashicons-hammer', 110, TRUE, PLUGIN_NAME);

        $overview_section = $this -> createSection ( 'Overview', array( $this, 'renderSection' ), null, FALSE );
        $overview_setting_table = $this -> setSetting ( $overview_section, 'table');
        $this -> setField ( '', $overview_setting_table, $overview_section, array( $this, 'renderOverViewTable' ) );
    
    }

    public function renderOverViewTable( array $args ){
        
        $html = '
            <table class="table table-striped border">
                <thead><tr>
                    <th scope="col">Job</th><th scope="col">Server</th><th scope="col">Trigger</th><th scope="col">Meta</th><th scope="col">API</th>
                </tr></thead>
                <tbody>
        ';

        global $wpdb;
      
        $query = "
            SELECT 
                jobs.name AS job_name, jobs.hash as job_hash,
                services.name AS service_name,
                triggers.type AS trigger_name,
                meta.name AS meta_name, meta.data AS meta_data,
                GROUP_CONCAT('\"', secrets.name, '\"') AS secret_name
            FROM 
                " . PLUGIN_PREFIX . "_jobs_services_secrets_map AS ref
            LEFT JOIN 
                " . PLUGIN_PREFIX . "_jobs AS jobs ON jobs.id = ref.job_id
            LEFT JOIN 
                " . PLUGIN_PREFIX . "_services AS services ON services.id = ref.service_id
            LEFT JOIN 
                " . PLUGIN_PREFIX . "_triggers AS triggers ON triggers.id = ref.trigger_id
            LEFT JOIN 
                " . PLUGIN_PREFIX . "_meta AS meta ON meta.id = ref.meta_id
            LEFT JOIN 
                " . PLUGIN_PREFIX . "_secrets AS secrets ON secrets.id = ref.secret_id
            GROUP BY jobs.name, jobs.hash, services.name, triggers.type, meta.name, meta.data
        ";

        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        foreach($_result as $_ => $row) {
            $html .= "<tr>";
            $html .= "<td>".$row['job_name']."</td>";
            $html .= "<td>".$row['service_name']."</td>";
            $html .= "<td>".str_replace("_", ' ', $row['trigger_name'])."</td>";
            $html .= "<td>".$row['meta_data']."</td>";
            $html .= "<td>".$row['secret_name']."</td>";
            $html .= "<td>";
                $html .= "<form method='POST'>";
                    $html .= "<input type='hidden' name='form_name' value='job_run'/>";
                    $html .= "<input type='hidden' name='job_hash' value='".$row['job_hash']."'/>";
                    $html .= "<button type='submit' class='btn btn-primary'>RUN NOW</button>";
                $html .= "</form>";
            $html .= "</td>";
            $html .= "</tr>";
        }
          
        $html .= '</tbody></table>';
        return $html;
    }

}

