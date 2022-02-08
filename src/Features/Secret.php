<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Core\{Manager, Tag};

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Secret extends Manager {

    function __construct( $store ) {

		parent::__construct( $store, 'Key Management' );

		$this -> setPage ( 'manage_options', array( $this, 'renderPage' ), dw_cp_PLUGIN_SLUG, dw_cp_PLUGIN_SLUG );

        $overview_section = $this -> createSection ( 'Overview', array( $this, 'renderSection' ), null, FALSE );
        $overview_setting_table = $this -> setSetting ( $overview_section, 'table');
        $this -> setField ( '', $overview_setting_table, $overview_section, array( $this, 'renderOverViewTable' ) );

		$create_section = $this -> createSection ( 'Add', array( $this, 'renderSection' ), null, TRUE );
        $this -> setField ( 'Name', $this -> setSetting ( $create_section, 'secret_name'), $create_section, array( $this, 'renderField' ), array('placeholder' => 'Type here...', 'col' => ' col-8 ') );
        $this -> setField ( 'API Key', $this -> setSetting ( $create_section, 'secret_value'), $create_section, array( $this, 'renderField' ), array('placeholder' => 'Type here...', 'type' => 'password', 'col' => ' col-8 ') );
        $this -> setField ( 'Service', $this -> setSetting ( $create_section, 'service_id'), $create_section, array( $this, 'renderServiceIdField' ) );

    }

    public function submit() {
        global $alert_show, $wpdb;
        
        if(isset($_POST['form_name'])) {
            if($_POST['form_name'] == strtolower($this -> getPage()['menu_title']) .'_'. strtolower('Add')) {
                
                $response = $this -> addAPIKey ($_POST);

                if( ! $response ) {
                    $alert_show = $this -> renderAlert( array(
                        'type' => 'alert-danger',
                        'description' => 'API KEY ADDING FAILED:: ' . $wpdb -> last_error
                    ) );
                } else {
                    $alert_show = $this -> renderAlert( array(
                        'type' => 'alert-success',
                        'description' => '<strong>API KEY ADDED</strong>'
                    ) );
                }
                
            }
            
        }
    }

    private function addAPIKey( array $args ) {
        global $wpdb;
        
        $names = array('secret_name', 'service_id', 'secret_value');
        $flag = 1;
        foreach($names as $name){
            if( ! isset($args[$name]) || ! $args[$name] ){
                $flag = 0;
                break;
            }
        }

        if($flag == 1) {
            
            $table = dw_cp_PLUGIN_PREFIX . '_secrets';

            $data = array(
                'name' => $_POST['secret_name'], 
                'value' => $_POST['secret_value'], 
                'service_id' => $_POST['service_id']
            );
            
            $st = '';
            foreach($data as $key => $value) $st .= md5($key . $value). '_';
            $data['hash'] = md5($st);
            
            $format = array('%s','%s', '%s', '%s');
            
            $wpdb -> insert($table, $data, $format);
            $response = $wpdb->insert_id;
            
            return $response;
        }
    }

    public function renderServiceIdField( array $args ){
        global $wpdb;
    
        $query = "
            SELECT 
                id, name 
            FROM 
                " . dw_cp_PLUGIN_PREFIX . "_services AS services 
            WHERE disabled = 0
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
                <th scope="col">Name</th><th scope="col">Service</th><th scope="col">Key</th><th scope="col"></th>
                </tr></thead>
                <tbody>
        ';
      
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        global $wpdb;
      
        $query = "
            SELECT 
                secrets.name AS secret_name, 
                services.name AS service_name, 
                secrets.value AS _key,
                secrets.disabled
            FROM 
                " . dw_cp_PLUGIN_PREFIX . "_secrets AS secrets
            JOIN 
                " . dw_cp_PLUGIN_PREFIX . "_services AS services ON services.id = secrets.service_id
            WHERE services.disabled = 0 AND secrets.deleted = 0
        ";

        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        foreach($_result as $_ => $row) {
            $html .= "<tr>";
                $html .= "<td>".$row['secret_name']."</td>";    
                $html .= "<td>".$row['service_name']."</td>";
                $html .= "<td>".$row['_key']."</td>";
                $html .= "<td>".($row['disabled'] == '1' ? "<b>DISABLED</b>" : "")."</td>";
            $html .= "</tr>";
        }
          
        $html .= '</tbody></table>';
        return $html;
    }

}

