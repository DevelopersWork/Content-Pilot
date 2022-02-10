<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Core\Manager;

class Dashboard extends Manager {

    function __construct( $store ) {

        parent::__construct( $store, 'Dashboard' );

        $this -> setPage ( 'manage_options', array( $this, 'renderPage' ), dw_cp_PLUGIN_SLUG, null, 'dashicons-hammer', 110, TRUE, dw_cp_PLUGIN_NAME);

        $overview_section = $this -> createSection ( 'Overview', array( $this, 'renderSection' ), null, FALSE );
        $overview_setting_table = $this -> setSetting ( $overview_section, 'table');
        $this -> setField ( '', $overview_setting_table, $overview_section, array( $this, 'renderOverViewTable' ) );
    
    }

    public function renderOverViewTable( array $args ){
        
        $html = '
            <thead><tr>
                <th scope="col">Job</th><th scope="col">Service</th><th scope="col">Trigger</th><th scope="col">Meta</th><th scope="col">API</th>
                <th scope="col">Status</th><th scope="col">Runs</th>
            </tr></thead>
            <tbody>
        ';

        global $wpdb;
      
        $query = "
            SELECT 
                jobs.name AS job_name, jobs.hash as job_hash,
                services.name AS service_name,
                triggers.type AS trigger_name,
                metas.name AS meta_name, metas.data AS meta_data,
                secrets.name AS secret_name,
                case when ref.is_success is null then 3 else ref.is_success end as is_success, count(*) as count_is_success
            FROM 
                " . dw_cp_PLUGIN_PREFIX . "_jobs_services_secrets_map AS ref
            LEFT JOIN 
                " . dw_cp_PLUGIN_PREFIX . "_jobs AS jobs ON jobs.id = ref.job_id
            LEFT JOIN 
                " . dw_cp_PLUGIN_PREFIX . "_services AS services ON services.id = ref.service_id
            LEFT JOIN 
                " . dw_cp_PLUGIN_PREFIX . "_triggers AS triggers ON triggers.id = ref.trigger_id
            LEFT JOIN 
                " . dw_cp_PLUGIN_PREFIX . "_metas AS metas ON metas.id = ref.meta_id
            LEFT JOIN 
                " . dw_cp_PLUGIN_PREFIX . "_secrets AS secrets ON secrets.id = ref.secret_id
            GROUP BY jobs.name, jobs.hash, services.name, triggers.type, metas.name, metas.data, ref.is_success, secrets.name
        ";

        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        foreach($_result as $_ => $row) {
            $html .= "<tr>";
            $html .= "<td>".$row['job_name']."</td>";
            $html .= "<td>".$row['service_name']."</td>";
            $html .= "<td>".str_replace("_", ' ', $row['trigger_name'])."</td>";
            $html .= "<td>".$row['meta_data']."</td>";
            $html .= "<td>".$row['secret_name']."</td>";
            $html .= "<td>".($row['is_success'] == 1 ? 'SUCCESS' : ($row['is_success'] == 0 ? 'FAILED' : 'NA'))."</td>";
            $html .= "<td>".(($row['is_success'] == 1 || $row['is_success'] == 0) ? $row['count_is_success'] : '0')."</td>";
            // $html .= "<td>";
            //     $html .= "<form method='POST'>";
            //         $html .= "<input type='hidden' name='form_name' value='job_run'/>";
            //         $html .= "<input type='hidden' name='job_hash' value='".$row['job_hash']."'/>";
            //         $html .= "<button type='submit' class='btn btn-primary'>RUN NOW</button>";
            //     $html .= "</form>";
            // $html .= "</td>";
            $html .= "</tr>";
        }
          
        $html .= '</tbody>';
        return $html;
    }

}

