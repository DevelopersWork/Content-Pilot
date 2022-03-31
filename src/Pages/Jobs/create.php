<div id="poststuff">
    <form action="" method="POST">
        <input type="hidden" name="f_submit" value="<?php echo md5(DWContetPilotPrefix . '_add_job');?>_job">
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
                                <input placeholder="" type="text" class="code widefat" name="job_name" id="job_name"
                                    value="">
                                <p class="description">
                                    A unqiue name for the job
                                </p>
                            </td>
                        </tr>
                        <tr id="row_job_interval">
                            <th scope="row">
                                <label for="job_interval">Interval</label>
                            </th>
                            <td>
                                <select name="job_interval" id="job_interval">
                                    <option value="" selected>None</option>
                                    <?php
                                        $result = $this -> fetchIntervals();

                                        foreach($result as $row){
                                            echo '<option value="'.$row['id'].'">';
                                            echo str_replace('_', ' ', $row['type']);
                                            echo '</option>';
                                        }
                                    ?>
                                </select>
                                <p class="description">How frequent the job should be triggered!</p>
                            </td>
                        </tr>
                        <tr id="row_job_service">
                            <th scope="row">
                                <label for="job_service">Service</label>
                            </th>
                            <td>
                                <select name="job_service" id="job_service" onChange="serviceUpdate(this)">
                                    <option value="" selected>None</option>
                                    <option value="YouTube">YouTube</option>
                                    <!-- <option value="RSS">RSS Feed</option> -->
                                </select>
                                <p class="description">What kind of Service Job will perform!</p>
                            </td>
                        </tr>
                        <script>
                        function serviceUpdate(event) {
                            if (event.value == 'YouTube') {
                                document.getElementById('row_job_secret').hidden = false;
                                document.getElementById('row_yt_channel').hidden = false;
                                document.getElementById('row_yt_keyword').hidden = false;
                            }
                        }
                        </script>

                        <tr id="row_job_secret" hidden>
                            <th scope="row">
                                <label for="job_secret">Secret Key</label>
                            </th>
                            <td>
                                <select name="job_secret" id="job_secret">
                                    <option value="" selected>None</option>
                                    <?php
                                        $result = $this -> fetchSecrets();

                                        foreach($result as $row){
                                            echo '<option value="'.$row['id'].'">';
                                            echo str_replace('_', ' ', $row['name']);
                                            echo '</option>';
                                        }
                                    ?>
                                </select>
                                <p class="description">Select the Secret key for the Job</p>
                            </td>
                        </tr>
                        <tr id="row_yt_channel" hidden>
                            <th scope="row">
                                <label for="yt_channel">YouTube Channel (Optional)</label>
                            </th>
                            <td>
                                <textarea placeholder="" type="text" class="code widefat" name="yt_channel"
                                    id="yt_channel"></textarea>
                                <p class="description">Example: UCNLm0XtW8zWuzmhD5BqXagw, Intrests,
                                    UCUEhqlSd2qvU2_HFMV7nRnQ, ENGILIPISU</p>
                            </td>
                        </tr>
                        <tr id="row_yt_keyword" hidden>
                            <th scope="row">
                                <label for="yt_keyword">YouTube Keyword (Optional)</label>
                            </th>
                            <td>
                                <textarea placeholder="" type="text" class="code widefat" name="yt_keyword"
                                    id="yt_keyword"></textarea>
                                <p class="description">Example: developerswork, gaming, valorant, hypixel skyblock</p>
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