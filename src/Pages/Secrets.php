<div class="wrap">
    <h1><?php echo $class_name; ?></h1>
    <?php settings_errors(); ?>

    <?php
    if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    } else {
        $active_tab = 'view';
    }

    if ($active_tab == 'view') {
        include_once 'Tabs/secrets/view_secrets.php';
    } elseif ($active_tab == 'modify') {
        include_once 'Tabs/secrets/modify_secrets.php';
    } elseif ($active_tab == 'add') {
        include_once 'Tabs/secrets/add_secrets.php';
    }
    ?>
</div>
