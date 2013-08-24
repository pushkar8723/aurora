<?php
if(isset($_SESSION['loggedin'])){ 
    $query = "select * from teams where tid='".$_SESSION['team']['id']."'";
    $team = DB::findOneFromQuery($query);
?>
<h1>Account Settings</h1>
<form class="form-horizontal" method="post" action="<?php echo SITE_URL; ?>/process.php">
    <div class='control-group'>
        <div class='control-label'><label for='pass1'>Old Password</label></div>
        <div class='controls'><input type='password' name='oldpass' id='oldpass' /></div>
    </div>
    <div class='control-group'>
        <div class='control-label'><label for='pass1'>New Password</label></div>
        <div class='controls'><input type='password' name='pass1' id='pass1' /></div>
    </div>
    <div class='control-group'>
        <div class='control-label'><label for='pass1'>Retype Password</label></div>
        <div class='controls'><input type='password' name='repass' id='repass' /></div>
    </div>
    <div class='control-group'>
        <div class='control-label'><label for='pass1'>Group</label></div>
        <div class='controls'>
            <select name="group">
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
    <div class='control-group'>
        <div class='control-label'></div>
        <div class='controls'>
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
