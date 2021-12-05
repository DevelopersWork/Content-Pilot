<h1>
    <?php echo PLUGIN_NAME . " Dashboard";?>
</h1>

<?php

use Dev\WpContentAutopilot\Core\YouTube;

$response = YouTube::search();

print_r($response);