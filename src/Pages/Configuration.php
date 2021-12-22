<div class="warp">

    <ul class="nav nav-tabs">
        <?php 
            
            if( isset( $tabs ) ) echo $tabs;
            
        ?>
    </ul>

    <div class="tab-content">

        <?php 

            if( isset( $fields ) ) echo $fields;
        
        ?>
    </div>

</div>

