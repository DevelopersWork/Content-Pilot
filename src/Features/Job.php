<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Features\{Manager, Tag};
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Job extends Manager {

    function __construct( $store ) {

		parent::__construct( $store, 'Job' );

		$this -> setPage ( 'manage_options', array( $this, 'renderPage' ), PLUGIN_SLUG, PLUGIN_SLUG );

        $overview_section = $this -> createSection ( 'Overview', array( $this, 'renderSection' ), null, FALSE );
        $overview_setting_table = $this -> setSetting ( $overview_section, 'table');
        $this -> setField ( '', $overview_setting_table, $overview_section, array( $this, 'renderOverViewTable' ) );

		$create_section = $this -> createSection ( 'Create', array( $this, 'renderSection' ), null, TRUE );
        $this -> setField ( 'Job Name', $this -> setSetting ( $create_section, 'job_name'), $create_section, array( $this, 'renderField' ), array('placeholder' => 'Type here...', 'col' => ' col-8 ') );
        $this -> setField ( 'Service', $this -> setSetting ( $create_section, 'service_id'), $create_section, array( $this, 'renderServiceIdField' ) );
        $this -> setField ( 'Trigger', $this -> setSetting ( $create_section, 'trigger_id'), $create_section, array( $this, 'renderTriggerIdField' ) );


        $modify_section = $this -> createSection ( 'Modify', array( $this, 'renderSection' ), null, TRUE );
        $this -> setField ( 'Job Name', $this -> setSetting ( $modify_section, 'job_name'), $modify_section, array( $this, 'renderJobNameField' ), array('placeholder' => 'Type here...', 'col' => ' col-8 ') );
        $this -> setField ( 'Disabled', $this -> setSetting ( $modify_section, 'disabled'), $modify_section, array( $this, 'renderField' ), array('type' => 'checkbox', 'col' => ' col-2 ') );
        $this -> setField ( 'Key', $this -> setSetting ( $modify_section, 'key_required'), $modify_section, array( $this, 'renderField' ), array('type' => 'checkbox', 'col' => ' col-2 ') );
        $this -> setField ( 'Service', $this -> setSetting ( $modify_section, 'service_id'), $modify_section, array( $this, 'renderServiceIdField' ) );
        $this -> setField ( 'Trigger', $this -> setSetting ( $modify_section, 'trigger_id'), $modify_section, array( $this, 'renderTriggerIdField' ) );

        $section_id_4 = $this -> createSection ( 'Delete', array( $this, 'renderSection' ) );

    }

    public function submit() {
        global $alert_show;
        
        if(isset($_POST['form_name'])) {
            if($_POST['form_name'] == 'job_create') {
                
                $response = $this -> createJob ($_POST);

                if( ! $response ) {
                    $alert_show = $this -> renderAlert( array(
                        'type' => 'alert-danger',
                        'description' => 'JOB CREATION FAILED'
                    ) );
                }
                
            }
            else if($_POST['form_name'] == 'job_run') {}
        }
    }

    private function createJob( array $args ) {
        global $wpdb;
        
        $names = array('service_id', 'trigger_id', 'job_name');
        $flag = 1;
        foreach($names as $name){
            if( ! isset($args[$name]) ){
                $flag = 0;
                break;
            }
        }

        if($flag == 1) {
            
            $table = PLUGIN_PREFIX . '_jobs';

            $data = array(
                'name' => $_POST['job_name'], 
                'service_id' => $_POST['service_id'], 
                'trigger_id' => $_POST['trigger_id'], 
                'key_required' => 1 
            );
            
            $st = '';
            foreach($data as $key => $value) $st .= md5($key . $value). '_';
            $data['hash'] = md5($st);
            
            $format = array('%s','%s', '%s', '%d', '%s');
            
            $wpdb->insert($table, $data, $format);
            $response = $wpdb->insert_id;
            
            return $response;
        }
    }

    public function renderJobNameField( array $args ) {
            
        global $wpdb;
    
        $query = "
            SELECT 
                id, name, service_id, trigger_id, key_required, disabled 
            FROM 
                " . PLUGIN_PREFIX . "_jobs AS triggers
            WHERE deleted = 0
        ";
        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        $options = array();
        foreach($_result as $_ => $row) $options[$row['id']] = $row['name'];
        $args['options'] = $options;
            
        $field = Tag:: selectTag( $args );

        return $field;
    }

    public function renderDisabledField( array $args ) {
            
        global $wpdb;
    
        $query = "
            SELECT 
                id, name, service_id, trigger_id, key_required, disabled 
            FROM 
                " . PLUGIN_PREFIX . "_jobs AS triggers
            WHERE deleted = 0
        ";
        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        $options = array();
        foreach($_result as $_ => $row) $options[$row['id']] = $row['name'];
        $args['options'] = $options;
            
        $field = Tag:: selectTag( $args );

        return $field;
    }

    public function renderTriggerIdField( array $args ){
        global $wpdb;
            
        $query = "
            SELECT 
                id, type 
            FROM 
                " . PLUGIN_PREFIX . "_triggers AS triggers
            WHERE disabled = 0 
        ";
        $_result = $wpdb->get_results( $query, 'ARRAY_A' );        
        
        $options = array();
        foreach($_result as $_ => $row) $options[$row['id']] = str_replace("_", ' ', $row['type']);
        $args['options'] = $options;
    
        $field = Tag:: selectTag( $args );

        return $field;
    }

    public function renderServiceIdField( array $args ){
        global $wpdb;
    
        $query = "
            SELECT 
                id, name 
            FROM 
                " . PLUGIN_PREFIX . "_services AS services 
        ";
        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        $options = array();
        foreach($_result as $_ => $row) $options[$row['id']] = $row['name'];
        $args['options'] = $options;
            
        $field = Tag:: selectTag( $args );

        return $field;
    }

    public function renderOverViewTable( array $args ){
        
        $html = '
            <table class="table table-striped border">
                <thead><tr>
                    <th scope="col">Job</th><th scope="col">Service</th><th scope="col">Trigger</th><th scope="col"></th>
                </tr></thead>
                <tbody>
        ';
      
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        global $wpdb;
      
        $query = "
            SELECT 
                jobs.name AS job_name, jobs.hash as job_hash,
                services.name AS service_name,
                triggers.type
            FROM 
                " . PLUGIN_PREFIX . "_jobs_services_secrets_map AS ref
            JOIN 
                " . PLUGIN_PREFIX . "_jobs AS jobs ON jobs.id = ref.job_id
            JOIN 
                " . PLUGIN_PREFIX . "_services AS services ON services.id = ref.service_id
            JOIN 
                " . PLUGIN_PREFIX . "_triggers AS triggers ON triggers.id = ref.trigger_id
        ";

        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        foreach($_result as $_ => $row) {
            $html .= "<tr>";
            $html .= "<td>".$row['job_name']."</td>";
            $html .= "<td>".$row['service_name']."</td>";
            $html .= "<td>".str_replace("_", ' ', $row['type'])."</td>";
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

	// public function renderPage(){
    //     require_once PLUGIN_PATH . "/src/Pages/Job.php";
    // }

}

