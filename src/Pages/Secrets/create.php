<div id="poststuff">
    <form action="" method="POST">
        <input type="hidden" name="f_submit" value="<?php echo md5(DWContetPilotPrefix . '_add_secret');?>">
        <input type="hidden" name="f_key" value="<?php echo $this -> auth_key; ?>">

        <div id="dw_cp_metabox_add_secrets" class="postbox">
            <h3 class="hndle"><span>Adding new key</span></h3>
            <div class="inside">
                <table class="form-table">

                    <tbody>
                        <tr id="row_secret_name">
                            <th scope="row">
                                <label for="secret_name">Name</label>
                            </th>
                            <td>
                                <input type="text" class="code widefat" name="secret_name" id="secret_name" value="">
                                <p class="description">
                                    A unqiue name for the key
                                </p>
                            </td>
                        </tr>
                    </tbody>

                    <tbody>
                        <tr id="row_secret_key">
                            <th scope="row">
                                <label for="secret_key">Key</label>
                            </th>
                            <td>
                                <input type="text" class="code widefat" name="secret_key" id="secret_key" value="">
                                <p class="description">
                                    The secret key
                                </p>
                            </td>
                        </tr>
                    </tbody>

                    <tbody hidden>
                        <tr id="row_secret_service">
                            <th scope="row">
                                <label for="secret_service">Service</label>
                            </th>
                            <td>
                                <select name="secret_service" id="secret_service">
                                    <option value="">None</option>
                                    <option value="YouTube" selected="selected">YouTube</option>
                                </select>
                                <p class="description">Which service key can be used!</p>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>


        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        <a href="<?php echo $this -> getURI(); ?>&amp" target="" class="button">Cancel</a>
    </form>
</div>