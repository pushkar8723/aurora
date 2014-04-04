<?php
if(isset($_SESSION['loggedin'])){ 
    $query = "select * from teams where tid='".$_SESSION['team']['id']."'";
    $team = DB::findOneFromQuery($query);
?>
<h1>Account Settings</h1>
<form class="form-horizontal" role='form' method="post" action="<?php echo SITE_URL; ?>/process.php">
    <div class='form-group'>
        <label class='col-lg-2 control-label' for='pass1'>Old Password</label>
        <div class='col-lg-4'><input class='form-control' type='password' name='oldpass' id='oldpass' /></div>
    </div>
    <div class='form-group'>
        <label class='col-lg-2 control-label' for='pass1'>New Password</label>
        <div class='col-lg-4'><input class='form-control' type='password' name='pass1' id='pass1' /></div>
    </div>
    <div class='form-group'>
        <label class='col-lg-2 control-label' for='pass1'>Re-Password</label>
        <div class='col-lg-4'><input class='form-control' type='password' name='repass' id='repass' /></div>
    </div>
    <div class='form-group'>
        <label class='col-lg-2 control-label' for='pass1'>Group</label>
        <div class='col-lg-4'>
            <select name="group" class='form-control'>
                <?php
                    $query = 'select * from groups';
                    $result = DB::findAllFromQuery($query);
                    foreach($result as $row){
                        echo "<option value='$row[gid]'".(($team['gid'] == $row['gid'])?("selected='selected'"):("")).")>$row[groupname]</option>";
                    }
                ?>
            </select>
        </div>
    </div>
    <div class='form-group'>
        <lable class='col-lg-2 control-label'></lable>
        <div class='col-lg-4'>
            <input type="submit" class="btn btn-primary btn-large" value="update" name="update" />
        </div>
    </div>
</form>
For other changes contact admins. 
<?php }
else{
    $_SESSION['msg'] = "You have to be logged in";
    redirectTo("http://" . $_SERVER['HTTP_HOST'] . $_SESSION['url']);
}
?>
