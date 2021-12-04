<h1>
    <?php echo PLUGIN_NAME . " Configuration";?>
</h1>

<form method="post" action="options.php">
    <?php 
        settings_fields(  PLUGIN_SLUG . 'option_group_configuration_text_field' );
        do_settings_sections( PLUGIN_SLUG . '_configuration' );
        submit_button();
    ?>
</form>