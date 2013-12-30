<?php
if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
    if (isset($_GET['code'])) {
        $_GET['code'] = addslashes($_GET['code']);
        $query = "select * from contest where code = '$_GET[code]'";
        $res = DB::findOneFromQuery($query);
        ?>
        <center><h1>Contest Settings - <?php echo $_GET['code']; ?></h1></center>
        <form class="form-horizontal" action="<?php echo SITE_URL; ?>/process.php" method="post">
            <input type='hidden' value='<?php echo $res['id']; ?>' name='id'/>
            <div class='col-md-12'>
                <div class="form-group">
                    <label class='control-label col-lg-2' for="name">Name</label>
                    <div class="col-lg-4"><input class='form-control' type='text' id='name' name='name' value='<?php echo $res['name']; ?>' required/></div>
                </div>
            </div>
            <div class='col-md-12'>
                <div class="form-group">
                    <label class='control-label col-lg-2' for="code">Code</label>
                    <div class="col-lg-4"><input class='form-control' type='text' id='code' name='code' value='<?php echo $res['code']; ?>' required/></div>
                </div>
            </div>
            <div class='col-md-12'>
                <div class="form-group">
                    <label class='col-lg-2 control-label' for="starttime">Start Time</label>
                    <div class="col-lg-4"><input class='form-control' type='text' id='starttime' name='starttime' value='<?php echo date("d-m-Y H:i:s", $res['starttime']); ?>' required/> Format : DD-MM-YYYY h:m:s</div>
                </div>
            </div>
            <div class='col-md-12'>
                <div class="form-group">
                    <label class='col-lg-2 control-label' for="endtime">End Time</label>
                    <div class="col-lg-4"><input class='form-control' type='text' id='endtime' name='endtime' value='<?php echo date("d-m-Y H:i:s", $res['endtime']); ?>' required/> Format : DD-MM-YYYY h:m:s</div>
                </div>
            </div>
            <div class="form-group">
                <label class='control-label col-lg-2' for="announcement">Announcements</label>
                <div class="col-lg-10"><textarea class='form-control' id='announcement' name='announcement' style='width: 450px; height: 300px;'><?php echo $res['announcement']; ?></textarea></div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2"></label>
                <div class="col-lg-4"><input type='submit' class='btn btn-primary btn-large' value='Update Contest' name='updatecontest'/></div>
            </div>
        </form>
        <?php
    } else {
        ?>
        <center><h1>Contest Settings</h1></center>
        <form class="form-horizontal" action="<?php echo SITE_URL; ?>/process.php" method="post">
            <div class='col-md-12'>
                <div class="form-group">
                    <label class='col-lg-2 control-label' for="name">Name</label>
                    <div class="col-lg-4"><input class='form-control' type='text' id='name' name='name' required/></div>
                </div>
            </div>
            <div class='col-md-12'>
                <div class="form-group">
                    <label class='col-lg-2 control-label' for="code">Code</label>
                    <div class="col-lg-4"><input class='form-control' type='text' id='code' name='code' required/></div>
                </div>
            </div>
            <div class='col-md-12'>
                <div class="form-group">
                    <label class='col-lg-2 control-label' for="starttime">Start Time</label>
                    <div class="col-lg-4"><input class='form-control' type='text' id='starttime' name='starttime' required/> Format : DD-MM-YYYY h:m:s</div>
                </div>
            </div>
            <div class='col-md-12'>
                <div class="form-group">
                    <label class='col-lg-2 control-label' for="endtime">End Time</label>
                    <div class="col-lg-4"><input class='form-control' type='text' id='endtime' name='endtime' required/> Format : DD-MM-YYYY h:m:s</div>
                </div>
            </div>
            <div class="form-group">
                <label class='control-label col-lg-2' for="announcement">Announcements</label>
                <div class="col-lg-10"><textarea class='form-control' id='announcement' name='announcement' style='width: 450px; height: 300px;'></textarea></div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2"></label>
                <div class="col-lg-4"><input type='submit' class='btn btn-primary btn-large' value='Add Contest' name='addcontest'/></div>
            </div>
        </form>
        <?php
    }
} else {
    $_SESSION['msg'] = "Access Denied: You need to be administrator to access that page.";
    redirectTo(SITE_URL);
}
?>
