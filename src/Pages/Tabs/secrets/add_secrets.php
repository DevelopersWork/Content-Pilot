
<!-- <h2 class="nav-tab-wrapper">
    <a href="<?php echo $slug; ?>&amp;tab=view" target="" class="nav-tab">View</a>
    <a href="<?php echo $slug; ?>&amp;tab=modify" target="" class="nav-tab">Modify</a>
    <span class="nav-tab nav-tab-active">Add</span>
</h2> -->
<div id="poststuff">
    <form action="" method="POST">
        <input type="hidden" name="f_submit" value="<?php echo md5(DWContetPilotPrefix . '_add_secrets');?>">
        <input type="hidden" name="f_time" value="<?php echo $this -> auth_key; ?>">

        <div id="dw_cp_metabox_add_secrets" class="postbox">
            <h3 class="hndle"><span>Adding new key</span></h3>
            <div class="inside" style="">
                <table class="form-table">
                    
                    <tbody><tr id="row_secret_name">
                        <th scope="row">
                            <label for="secret_name">Name</label>
                        </th>
                        <td>
                            <input type="text" class="code widefat" name="secret_name" id="secret_name" value="">
                            <p class="description"></p>
                        </td>
                    </tr></tbody>

                     <tbody><tr id="row_secret_key">
                        <th scope="row">
                            <label for="secret_key">Key</label>
                        </th>
                        <td>
                            <input type="text" class="code widefat" name="secret_key" id="secret_key" value="">
                            <p class="description"></p>
                        </td>
                    </tr></tbody>

                    <!-- <tbody><tr id="row_secret_service">
                        <th scope="row">
                            <label for="secret_service">Opinionated Styles</label>
                        </th>
                        <td>
                            <select name="secret_service" id="secret_service">
                                <option value="">None</option>
                                <option value="light" selected="selected">Light</option>
                                <option value="dark">Dark</option>
                            </select>
                            <p class="description">Use default Ninja Forms styling conventions.</p>                                                                    </td>
                    </tr></tbody> -->
                    <input type="hidden" name="secret_service" value="youtube">
                
                </table>
            </div>
        </div>

        
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        <a href="<?php echo $slug; ?>&amp" target="" class="button">Cancel</a>
    </form>
    <!-- <input type="cancel" name="cancel" id="cancel" class="button" value="Cancel"> -->
</div>
