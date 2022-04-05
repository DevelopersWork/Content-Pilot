<form action="" method="POST">
    <input type="hidden" name="f_submit" value="<?php echo md5(DWContetPilotPrefix . '_add_secret');?>_secret">
    <input type="hidden" name="f_key" value="<?php echo $this -> auth_key; ?>">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-12">

            <div id="postbox-container-1" class="postbox-container">

                <div id="normal-sortables" class="meta-box-sortables ui-sortable ui-sortable-disabled">
                    <div class="postbox">

                        <div class="postbox-header">
                            <h2 class="hndle ui-sortable-handle is-non-sortable" style="text-transform: uppercase;">
                                Creating new Secret key
                            </h2>
                        </div>
                        <div class="inside">
                            <table class="form-table">

                                <tbody>
                                    <tr id="row_secret_name">
                                        <th scope="row">
                                            <label for="secret_name">Name</label>
                                        </th>
                                        <td>
                                            <input type="text" class="code widefat" name="secret_name" id="secret_name"
                                                value="">
                                            <p class="description">
                                                A unqiue name for the key
                                            </p>
                                        </td>
                                    </tr>
                                    <tr id="row_secret_key">
                                        <th scope="row">
                                            <label for="secret_key">Key</label>
                                        </th>
                                        <td>
                                            <input type="text" class="code widefat" name="secret_key" id="secret_key"
                                                value="">
                                            <p class="description">
                                                The secret key
                                            </p>
                                        </td>
                                    </tr>
                                    <tr id="row_secret_service">
                                        <th scope="row">
                                            <label for="secret_service">Service</label>
                                        </th>
                                        <td>
                                            <select name="secret_service" id="secret_service">
                                                <option value="" selected>None</option>
                                                <option value="YouTube">YouTube</option>
                                                <option value="RSS">RSS Feed</option>
                                            </select>
                                            <p class="description">Which service key can use this key!</p>
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
    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    <a href="<?php echo $this -> getURI(); ?>&amp" target="" class="button">Cancel</a>
</form>