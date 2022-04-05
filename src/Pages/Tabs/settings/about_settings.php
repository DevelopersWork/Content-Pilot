<h2 class="nav-tab-wrapper">
    <a href="<?php echo $this -> getURI(); ?>&amp;tab=general" target="" class="nav-tab">General</a>
    <span class="nav-tab nav-tab-active">About</span>
    <a href="<?php echo $this -> getURI(); ?>&amp;tab=licenses" target="" class="nav-tab">License</a>
</h2>

<div id="poststuff">

    <div id="post-body" class="metabox-holder columns-12">

        <div id="postbox-container-1" class="postbox-container">

            <div id="normal-sortables" class="meta-box-sortables ui-sortable ui-sortable-disabled">
                <div class="postbox">

                    <div class="postbox-header">
                        <h2 class="hndle ui-sortable-handle is-non-sortable">Plugin Information</h2>
                    </div>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>

                                <tr>
                                    <th scope="row">
                                        <label>Name</label>
                                    </th>
                                    <td>
                                        <?php echo dw_cp_plugin_name; ?>
                                        <p class="description"></p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label>Version</label>
                                    </th>
                                    <td>
                                        <?php echo dw_cp_plugin_version; ?>
                                        <p class="description"></p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label>Path</label>
                                    </th>
                                    <td>
                                        <?php echo dw_cp_plugin_dir_path; ?>
                                        <p class="description"></p>
                                    </td>
                                </tr>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="postbox-container-2" class="postbox-container">

            <div id="normal-sortables" class="meta-box-sortables ui-sortable ui-sortable-disabled">
                <div class="postbox">

                    <div class="postbox-header">
                        <h2 class="hndle ui-sortable-handle is-non-sortable">Wordpress Information</h2>
                    </div>
                    <div class="inside">

                        <table class="form-table">
                            <tbody>

                                <tr>
                                    <th scope="row">
                                        <label>Version</label>
                                    </th>
                                    <td>
                                        <?php global $wp_version; echo $wp_version; ?>
                                        <p class="description"></p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label>Required PHP</label>
                                    </th>
                                    <td>
                                        <?php global $required_php_version; echo $required_php_version; ?>
                                        <p class="description"></p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label>Required MySQL</label>
                                    </th>
                                    <td>
                                        <?php global $required_mysql_version; echo $required_mysql_version; ?>
                                        <p class="description"></p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label>Plugins</label>
                                    </th>
                                    <td>
                                        <?php
                                foreach (get_plugins() as $plugin) 
                                    echo $plugin['Name'] . ' ' . $plugin['Version'] . '<br>';
                            ?>
                                        <p class="description"></p>
                                    </td>
                                </tr>


                            </tbody>

                        </table>


                    </div>
                </div>
            </div>
        </div>

        <div id="postbox-container-3" class="postbox-container">

            <div id="normal-sortables" class="meta-box-sortables ui-sortable ui-sortable-disabled">
                <div class="postbox">

                    <div class="postbox-header">
                        <h2 class="hndle ui-sortable-handle is-non-sortable">PHP Information</h2>
                    </div>
                    <div class="inside">

                        <table class="form-table">
                            <tbody>

                                <tr>
                                    <th scope="row">
                                        <label>PHP Version</label>
                                    </th>
                                    <td>
                                        <?php echo PHP_VERSION; ?>
                                        <p class="description"></p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label>Extensions</label>
                                    </th>
                                    <td>
                                        <?php
                                            echo join(", ", get_loaded_extensions());
                                        ?>
                                        <p class="description"></p>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>