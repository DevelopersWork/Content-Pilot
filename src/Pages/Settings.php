<div class="wrap">
    <h1><?php echo $this -> store -> get('name'); ?></h1>
    <hr class="wp-header-end" />
    <?php settings_errors(); ?>

    <?php
    if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    } else {
        $active_tab = 'general';
    }

    if ($active_tab == 'general') {
        include_once 'Tabs/settings/general_settings.php';
    } elseif ($active_tab == 'about') {
        include_once 'Tabs/settings/about_settings.php';
    } elseif ($active_tab == 'licenses') {
        include_once 'Tabs/settings/licenses_settings.php';
    }
    ?>
</div>
