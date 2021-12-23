<div class="warp">
    <h1 class="wp-heading-inline">
        <?php 
            if( isset( $_metadata ) ) echo $_metadata['page']['menu_title'];
        ?>
    </h1>
    <ul class="nav nav-tabs">
        <?php
            if( isset( $_metadata ) ) {
                
                $i = 0;
                foreach( $_metadata['tabs'] as $tab ) {

                    if($i != 0) echo '<li>';
                    else echo '<li class="active">';

                    echo '<a href="#tab-'. ($i + 1) .'">';
                    echo $tab['title'];
                    echo '</a>';

                    echo '</li>';

                    $i += 1;
                }
                
            }
        ?>
    </ul>

    <div class="tab-content">
        <?php 

            if( isset( $_metadata ) ) {

                $i = 0;
                foreach( $_metadata['tabs'] as $tab ) {

                    if($i != 0) echo '<div id="tab-'. ($i + 1) .'" class="tab-pane">';
                    else echo '<div id="tab-'. ($i + 1) .'" class="tab-pane active">';

                    echo '<h3>' . $tab['title'] . '</h3>';

                    echo '<form method="post" action="options.php">';

                    foreach($tab['fields'] as $setting) {
                
                        foreach($setting as $field) {
                
                            echo $field['title'] . '<br/>';

                            get_option( $field['id'] ) ? print('wow its there') : print('oops');

                            // settings_fields( $field['id'] );
					        // do_settings_sections( $field['page'] );
                    
                        }
                    }

                    submit_button();

                    echo '</form>';

                    echo '</div>';

                    $i += 1;
                }
                
            }
        
        ?>
    </div>

</div>

