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
    <form role='form' class="form-horizontal" method="post" action="<?php echo SITE_URL; ?>/process.php">
        <div class="form-group">
            <label class='col-lg-2 control-label' for='judgemode'>Mode</label>
            <div class='col-lg-4'>
                <select class='form-control' name='mode' id='judgemode'>
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
        <div class='form-group'>
            <label class='control-label col-lg-2' for='endtime'>End Time</label>
            <div class='col-lg-4'>
                <div class="input-group">
                    <input class='form-control' type='text' id='endtime' name='endtime' data-placement='right' title='Sets the timers equal to no of minutes.' <?php
                    if ($admin['mode'] == 'Active') {
                        echo "value='" . ((int) (($admin['endtime'] - time()) / 60)) . "'";
                    }
                    ?>/>
                    <span class="input-group-addon">minute(s)</span>
                </div>
            </div>
        </div>
        <div class='form-group'>
            <label class='control-label col-lg-2' for='penalty'>Penalty</label>
            <div class='col-lg-4'>
                <div class="input-group">
                    <input class='form-control' type='text' id='penalty' name='penalty' value='<?php echo $admin['penalty']; ?>'/>
                    <span class="input-group-addon">minute(s)</span>
                </div>
            </div>
        </div>
        <div class='form-group'>
            <label class='control-label col-lg-2'></label>
            <div class='col-lg-4'>
                <input type='submit' class='btn btn-primary' value='update' name='judgeupdate'/> 
            </div>
        </div>
    </form>
    <h3>Socket Settings</h3>
    <form class="form-horizontal" role='form' method="post" action="<?php echo SITE_URL; ?>/process.php">
        <div class="form-group">
            <label class='control-label col-lg-2' for='ip'>IP</label>
            <div class='col-lg-4'><input class='form-control' type="text" name="ip" id="ip" value="<?php echo $admin['ip'] ?>" /></div>
        </div>
        <div class="form-group">
            <label class='control-label col-lg-2' for='port'>Port</label>
            <div class='col-lg-4'><input class='form-control' type="text" name="port" id="port" value="<?php echo $admin['port'] ?>" /></div>
        </div>
        <div class="form-group">
            <label class='control-label col-lg-2'></label>
            <div class='col-lg-4'><input type="submit" name="judgesocket" value="Update Socket" class="btn btn-primary"/></div>
        </div>
    </form>
    <h3>Notice</h3>
    <form class="form-horizontal" role='form' method="post" action="<?php echo SITE_URL; ?>/process.php">
        <div class="form-group">
            <label class='control-label col-lg-2' for='notice'>Notice</label>
            <div class='col-lg-8'><textarea class='form-control' style="width: 550px; height: 400px;" name="notice" id="notice"><?php echo $admin['notice']; ?></textarea></div>
        </div>
        <div class="form-group">
            <label class='control-label col-lg-2'></label>
            <div class='col-lg-4'><input type="submit" name="judgenotice" value="Update Notice" class="btn btn-primary"/></div>
        </div>
    </form>
    <?php
} else {
    $_SESSION['msg'] = "Access Denied: You need to be administrator to access that page.";
    redirectTo(SITE_URL);
}
?>