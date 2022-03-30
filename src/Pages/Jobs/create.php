<div id="poststuff">
    <form action="" method="POST">
        <input type="hidden" name="f_submit" value="<?php echo md5(DWContetPilotPrefix . '_add_job');?>">
        <input type="hidden" name="f_key" value="<?php echo $this -> auth_key; ?>">

        <div id="dw_cp_metabox_add_secrets" class="postbox">
            <h3 class="hndle"><span>Creating a Job</span></h3>
            <div class="inside">
                <table class="form-table">

                    <tbody>
                        <tr id="row_job_name">
                            <th scope="row">
                                <label for="job_name">Name</label>
                            </th>
                            <td>
                                <input type="text" class="code widefat" name="job_name" id="job_name" value="">
                                <p class="description">
                                    A unqiue name for the job
                                </p>
                            </td>
                        </tr>
                    </tbody>

                    <tbody>
                        <tr id="row_job_interval">
                            <th scope="row">
                                <label for="job_interval">Interval</label>
                            </th>
                            <td>
                                <select name="job_interval" id="job_interval">
                                    <option value="" selected>None</option>
                                </select>
                                <p class="description">How frequent the job should be triggered!</p>
                            </td>
                        </tr>
                    </tbody>

                    <tbody>
                        <tr id="row_job_service">
                            <th scope="row">
                                <label for="job_service">Service</label>
                            </th>
                            <td>
                                <select name="job_service" id="job_service">
                                    <option value="" selected>None</option>
                                    <option value="YouTube">YouTube</option>
                                    <option value="RSS">RSS Feed</option>
                                </select>
                                <p class="description">What service job will do!</p>
                            </td>
                        </tr>
                    </tbody>

                    <tbody>
                        <tr id="row_job_secret">
                            <th scope="row">
                                <label for="job_secret">Secret Key (Optional)</label>
                            </th>
                            <td>
                                <select name="job_secret" id="job_secret">
                                    <option value="" selected>None</option>
                                </select>
                                <p class="description">A Secret key if to be used</p>
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