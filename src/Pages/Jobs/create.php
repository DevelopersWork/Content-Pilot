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

                                    foreach ($result as $row) {
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
                                    <option value="RSS">RSS Feed</option>
                                </select>
                                <p class="description">What kind of Service Job will perform!</p>
                            </td>
                        </tr>
                        <script>
                        function serviceUpdate(event) {

                            document.getElementById('row_job_secret').hidden = true;
                            document.getElementById('row_yt_channel').hidden = true;
                            document.getElementById('row_yt_video').hidden = true;
                            document.getElementById('row_yt_keyword').hidden = true;
                            document.getElementById('row_job_hint').hidden = true;
                            document.getElementById('row_yt_video_type').hidden = true;
                            document.getElementById('row_rss_feed_url').hidden = true;

                            if (event.value == 'YouTube') {

                                let secrets = <?php echo json_encode($this -> fetchSecrets())?>;

                                let option = document.createElement('option');
                                option.value = "";
                                option.innerHTML = "None";
                                option.selected = true

                                document.getElementById('job_secret').innerHTML = "";
                                document.getElementById('job_secret').add(option);

                                for (let i in secrets) {
                                    let secret = secrets[i];

                                    if (secret['service'] != event.value)
                                        continue;

                                    let option = document.createElement('option');
                                    option.value = secret['id'];
                                    option.innerHTML = secret['name'];

                                    document.getElementById('job_secret').add(option);
                                }

                                document.getElementById('row_job_secret').hidden = false;
                                document.getElementById('row_yt_channel').hidden = false;
                                document.getElementById('row_yt_video').hidden = false;
                                document.getElementById('row_yt_keyword').hidden = false;
                                document.getElementById('row_job_hint').hidden = false;
                                document.getElementById('row_yt_video_type').hidden = false;
                            }
                            if (event.value == 'RSS') {

                                let secrets = <?php echo json_encode($this -> fetchSecrets())?>;

                                let option = document.createElement('option');
                                option.value = "";
                                option.innerHTML = "None";
                                option.selected = true

                                document.getElementById('job_secret').innerHTML = "";
                                document.getElementById('job_secret').add(option);

                                for (let i in secrets) {
                                    let secret = secrets[i];

                                    if (secret['service'] != event.value)
                                        continue;

                                    let option = document.createElement('option');
                                    option.value = secret['id'];
                                    option.innerHTML = secret['name'];

                                    document.getElementById('job_secret').add(option);
                                }

                                document.getElementById('row_rss_feed_url').hidden = false;
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
                                </select>
                                <p class="description">Select the Secret key for the Job</p>
                            </td>
                        </tr>

                        <tr id="row_rss_feed_url" hidden>
                            <th scope="row">
                                <label for="rss_feed_url">Feed Url</label>
                            </th>
                            <td>
                                <input placeholder="" type="text" class="code widefat" name="rss_feed_url"
                                    id="rss_feed_url" value="">
                                <p class="description">
                                    Example: https://news.google.com/rss/search?q=gaming
                                </p>
                            </td>
                        </tr>

                        <tr id="row_yt_video_type" hidden>
                            <th scope="row">
                                <label for="yt_video_type">Video Type</label>
                            </th>
                            <td>
                                <select name="yt_video_type" id="yt_video_type">
                                    <option value="completed">Archieved Live</option>
                                    <option value="live" selected>Currently Live</option>
                                    <option value="upcoming">Scheduled Live</option>
                                </select>
                                <p class="description"></p>
                            </td>
                        </tr>
                        <tr id="row_job_hint" hidden>
                            <th scope="row">

                            </th>
                            <td>
                                <strong>Provide any one of the below</strong>
                            </td>
                        </tr>
                        <tr id="row_yt_channel" hidden>
                            <th scope="row">
                                <label for="yt_channel">YouTube Channel (Optional) *Only 1*</label>
                            </th>
                            <td>
                                <input placeholder="Channel Id/Name" type="text" class="code widefat" name="yt_channel"
                                    id="yt_channel" />
                                <p class="description">Example: UCNLm0XtW8zWuzmhD5BqXagw, Intrests,
                                    UCUEhqlSd2qvU2_HFMV7nRnQ, ENGILIPISU, UCA99nItLBFj_cOVeOuiT_aQ</p>
                            </td>
                        </tr>
                        <tr id="row_yt_video" hidden>
                            <th scope="row">
                                <label for="yt_video">YouTube Similar VideoID (Optional) *Only 1*</label>
                            </th>
                            <td>
                                <input placeholder="Video Id" type="text" class="code widefat" name="yt_video"
                                    id="yt_video" />
                                <p class="description">Example: oI8sRtEyoOA, WEjCUmCoxSY</p>
                            </td>
                        </tr>
                        <tr id="row_yt_keyword" hidden>
                            <th scope="row">
                                <label for="yt_keyword">YouTube Query (Optional)</label>
                            </th>
                            <td>
                                <textarea placeholder="Make the query simple and generic for better results" type="text"
                                    class="code widefat" name="yt_keyword" id="yt_keyword"></textarea>
                                <p class="description">Ex: Minecraft + Hypixel Skyblock + Dungeons</p>
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
