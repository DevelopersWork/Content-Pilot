<div class="warp">

    <ul class="nav nav-tabs">
        <?php 
            if( ! isset( $tabs ) ) die();
            
            echo $tabs;
        
        ?>
    </ul>

    <div class="tab-content">

        <div id="tab-1" class="tab-pane active">
            <form method="post" action="options.php">
                <?php 

                    function tab1( $e ) {
                        $e -> renderTab_1();
                    }
                    
                ?>
            </form>
        </div>

        <div id="tab-2" class="tab-pane">
            <h1> Hello tab 2</h1>
        </div>

        <div id="tab-3" class="tab-pane">
            <h1> Hello tab 3</h1>
        </div>
    </div>

    </div>

