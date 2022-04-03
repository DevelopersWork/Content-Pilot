<h2 class="nav-tab-wrapper">
    <a href="<?php echo $this -> getURI(); ?>&amp;tab=general" target="" class="nav-tab">General</a>
    <a href="<?php echo $this -> getURI(); ?>&amp;tab=about" target="" class="nav-tab">About</a>
    <span class="nav-tab nav-tab-active">License</span>
</h2>

<div id="poststuff">
    <form action="" method="POST">
        <div id="dw_cp_metabox_general_settings" class="postbox">
            <h3 class="hndle"><span>License Information</span></h3>
            <div class="inside" style="">
                <table class="form-table">
                    
                    <tbody><tr id="row_dw_cp[version]">
                        <th scope="row">
                            <label for="dw_cp[version]">Version</label>
                        </th>
                        <td>
                            <?php echo dw_cp_plugin_version; ?>
                            <p class="description"></p>
                        </td>
                    </tr></tbody>
                
                </table>
            </div>
        </div>

        <input type="hidden" name="" value="">
        <input type="hidden" name="<?php echo DWContetPilotPrefix . '_' . $class_name;?>">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    </form>
</div>
