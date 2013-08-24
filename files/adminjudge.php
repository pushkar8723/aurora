<?php
if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
    $query = "select * from admin";
    $result = DB::findAllFromQuery($query);
    $admin = Array();
    foreach ($result as $row) {
        $admin[$row['variable']] = $row['value'];
    }
    ?>
    <script type='text/javascript'>
        $(document).ready(function() {
            $('#endtime').focus(function() {
                $('#endtime').tooltip('show');
            });
            $('#endtime').blur(function() {
                $('#endtime').tooltip('hide');
            });
        });
    </script>
    <center><h1>Judge Settings</h1></center>
    <h3>General Settings</h3>
    <form class="form-horizontal" method="post" action="<?php echo SITE_URL; ?>/process.php">
        <div class="control-group">
            <div class='control-label'><label for='judgemode'>Mode</label></div>
            <div class='controls'>
                <select name='mode' id='judgemode'>
                    <option value='Active' <?php
                    if ($admin['mode'] == "Active") {
                        echo "selected='selected'";
                    }
                    ?> >Active</option>
                    <option value='Passive' <?php
                    if ($admin['mode'] == "Passive") {
                        echo "selected='selected'";
                    }
                    ?> >Passive</option>
                    <option value='Disabled' <?php
                    if ($admin['mode'] == "Disabled") {
                        echo "selected='selected'";
                    }
                    ?> >Disabled</option>
                    <option value='Lockdown' <?php
                    if ($admin['mode'] == "Lockdown") {
                        echo "selected='selected'";
                    }
                    ?> >Lockdown</option>
                </select>
            </div>
        </div>
        <div class='control-group'>
            <div class='control-label'><label for='endtime'>Contest End Time</label></div>
            <div class='controls'>
                <div class="input-append">
                    <input type='text' id='endtime' name='endtime' data-placement='right' title='Sets the timers equal to no of minutes.' <?php
                    if ($admin['mode'] == 'Active') {
                        echo "value='" . ((int) (($admin['endtime'] - time()) / 60)) . "'";
                    }
                    ?>/>
                    <span class="add-on">minute(s)</span>
                </div>
            </div>
        </div>
        <div class='control-group'>
            <div class='control-label'><label for='penalty'>Penalty</label></div>
            <div class='controls'>
                <div class="input-append">
                    <input type='text' id='penalty' name='penalty' value='<?php echo $admin['penalty']; ?>'/>
                    <span class="add-on">minute(s)</span>
                </div>
            </div>
        </div>
        <div class='control-group'>
            <div class='control-label'></div>
            <div class='controls'>
                <input type='submit' class='btn btn-primary' value='update' name='judgeupdate'/> 
            </div>
        </div>
    </form>
    <h3>Socket Settings</h3>
    <form class="form-horizontal" method="post" action="<?php echo SITE_URL; ?>/process.php">
        <div class="control-group">
            <div class='control-label'><label for='ip'>IP</label></div>
            <div class='controls'><input type="text" name="ip" id="ip" value="<?php echo $admin['ip'] ?>" /></div>
        </div>
        <div class="control-group">
            <div class='control-label'><label for='port'>Port</label></div>
            <div class='controls'><input type="text" name="port" id="port" value="<?php echo $admin['port'] ?>" /></div>
        </div>
        <div class="control-group">
            <div class='control-label'></div>
            <div class='controls'><input type="submit" name="judgesocket" value="Update Socket" class="btn btn-primary"/></div>
        </div>
    </form>
    <h3>Notice</h3>
    <form class="form-horizontal" method="post" action="<?php echo SITE_URL; ?>/process.php">
        <div class="control-group">
            <div class='control-label'><label for='notice'>Notice</label></div>
            <div class='controls'><textarea style="width: 550px; height: 400px;" name="notice" id="notice"><?php echo $admin['notice']; ?></textarea></div>
        </div>
        <div class="control-group">
            <div class='control-label'></div>
            <div class='controls'><input type="submit" name="judgenotice" value="Update Notice" class="btn btn-primary"/></div>
        </div>
    </form>
    <?php
} else {
    $_SESSION['msg'] = "Access Denied: You need to be administrator to access that page.";
    redirectTo(SITE_URL);
}
?>