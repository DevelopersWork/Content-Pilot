<?php
/** 
 * @package DevWPContentAutopilot
 */
namespace Dev\WpContentAutopilot\Features;

use Dev\WpContentAutopilot\Features\{Manager, Tag};

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Meta extends Manager {

    function __construct( $store ) {

		parent::__construct( $store, 'Meta Management' );

		$this -> setPage ( 'manage_options', array( $this, 'renderPage' ), PLUGIN_SLUG, PLUGIN_SLUG );

        $overview_section = $this -> createSection ( 'Overview', array( $this, 'renderSection' ), null, FALSE );
        $overview_setting_table = $this -> setSetting ( $overview_section, 'table');
        $this -> setField ( '', $overview_setting_table, $overview_section, array( $this, 'renderOverViewTable' ) );

		$create_section = $this -> createSection ( 'Create', array( $this, 'renderSection' ), null, TRUE );
        $this -> setField ( 'Name', $this -> setSetting ( $create_section, 'meta_name'), $create_section, array( $this, 'renderField' ), array('placeholder' => 'Type here...', 'col' => ' col-12 ') );
        $this -> setField ( 'Service', $this -> setSetting ( $create_section, 'service_id'), $create_section, array( $this, 'renderServiceIdField' ), array('col' => ' col-6 ') );
        $this -> setField ( 'Data', $this -> setSetting ( $create_section, 'data'), $create_section, array( $this, 'renderField' ), array('placeholder' => 'Type here...', 'col' => ' col-12 ', 'type' => 'textarea') );
        $this -> setField ( 'API Key', $this -> setSetting ( $create_section, 'secret_id'), $create_section, array( $this, 'renderSecretIdField' ), array('col' => ' col-6 ') );
        $this -> setField ( 'Key Required', $this -> setSetting ( $create_section, 'key_required'), $create_section, array( $this, 'renderField' ), array('type' => 'checkbox', 'col' => ' col-2 ')  );
        

    }

    public function submit() {
        global $alert_show, $wpdb;
        
        if(isset($_POST['form_name'])) {
            if($_POST['form_name'] == strtolower($this -> getPage()['menu_title']) .'_'. strtolower('Create')) {
                
                $response = $this -> createMeta ($_POST);

                if( ! $response ) {
                    $alert_show = $this -> renderAlert( array(
                        'type' => 'alert-danger',
                        'description' => 'META CREATION FAILED:: ' . $wpdb -> last_error
                    ) );
                } else {
                    $alert_show = $this -> renderAlert( array(
                        'type' => 'alert-success',
                        'description' => '<strong>META DATA ADDED</strong>'
                    ) );
                }
                
            }
            
        }
    }

    private function createMeta( array $args ) {
        global $wpdb;

        $names = array('meta_name', 'service_id', 'data');
        $flag = 1;
        foreach($names as $name){
            if( ! isset($args[$name]) || ! $args[$name] ){
                $flag = 0;
                break;
            }
        }

        if($flag == 1) {
            
            $table = PLUGIN_PREFIX . '_metas';

            $data = array(
                'name' => $_POST['meta_name'], 
                'secret_id' => isset($_POST['secret_id']) && $_POST['secret_id'] != '' ? $_POST['secret_id'] : NULL,
                'service_id' => $_POST['service_id'],
                'key_required' => isset($_POST['key_required']) ? 1 : 0,
                'data' => $_POST['data']
            );
            
            $st = '';
            foreach($data as $key => $value) $st .= md5($key . $value). '_';
            $data['hash'] = md5($st);
            
            $format = array('%s','%s', '%s', '%d', '%s', '%s');
            
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
                " . PLUGIN_PREFIX . "_services AS services 
            WHERE disabled = 0
        ";
        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        $options = array();
        foreach($_result as $_ => $row) $options[$row['id']] = $row['name'];
        $args['options'] = $options;
            
        $field = Tag:: selectTag( $args );

        return $field;
    }

    public function renderSecretIdField( array $args ){
        global $wpdb;
    
        $query = "
            SELECT 
                id, name 
            FROM 
                " . PLUGIN_PREFIX . "_secrets AS _secrets 
            WHERE deleted = 0 AND disabled = 0
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
                <th scope="col">Name</th><th scope="col">Service</th><th scope="col">Key</th><th scope="col">Data</th>
                </tr></thead>
                <tbody>
        ';
      
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        global $wpdb;
      
        $query = "
            SELECT 
                metas.name AS meta_name, 
                metas.data,
                services.name AS service_name, 
                CASE 
                    WHEN secrets.name IS NOT NULL AND metas.key_required = 1 THEN secrets.name
                    WHEN metas.key_required = 1 THEN '*ANY*'
                    ELSE ''
                END AS _key
            FROM 
                " . PLUGIN_PREFIX . "_metas AS metas
            JOIN 
                " . PLUGIN_PREFIX . "_services AS services ON metas.service_id = services.id
            LEFT JOIN 
                " . PLUGIN_PREFIX . "_secrets AS secrets ON metas.secret_id = secrets.id
            WHERE services.disabled = 0 AND metas.deleted = 0
        ";

        $_result = $wpdb->get_results( $query, 'ARRAY_A' );

        foreach($_result as $_ => $row) {
            $html .= "<tr>";
                $html .= "<td>".$row['meta_name']."</td>";    
                $html .= "<td>".$row['service_name']."</td>";
                $html .= "<td>".$row['_key']."</td>";
                $html .= "<td>".$row['data']."</td>";
            $html .= "</tr>";
        }
          
        $html .= '</tbody></table>';
        return $html;
    }

}

