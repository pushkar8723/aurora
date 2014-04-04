<?php
if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
    if (isset($_GET['code'])) {
        $_GET['code'] = addslashes($_GET['code']);
        $query = "select * from teams where teamname='$_GET[code]'";
        $res = DB::findOneFromQuery($query);
        ?>
        <script type='text/javascript'>
            $(document).ready(function() {
                $('#teamname').focus(function() {
                    $('#teamname').tooltip('show');
                });
                $('#teamname').blur(function() {
                    $('#teamname').tooltip('hide');
                });
            });
        </script>
        <h1>Team Settings - <?php echo $_GET['code']; ?></h1>
        <form method='post' class='form-horizontal' action='<?php echo SITE_URL; ?>/process.php'>
            <input type="hidden" value="<?php echo $res['tid']; ?>" name="tid" />
            <div class='form-group'>
                <label for='teamname' class="control-label col-lg-2">Team Name</label>
                <div class='col-md-4'><input class="form-control" type='text' name='teamname' id='teamname' data-placement='right' title='Only alphabets numbers _ and @ are allowed' value="<?php echo $res['teamname']; ?>"/></div>
            </div>
            <div class='form-group'>
                <label class="control-label col-lg-2" for='password'>Password</label>
                <div class='col-md-4'><input class="form-control" type='text' name='password' id='password' value='<?php echo $res['pass']; ?>'/></div>
            </div>
            <div class='form-group'>
                <label class="control-label col-lg-2" for='status'>Status</label>
                <div class='col-md-4'>
                    <select class="form-control" name='status' id='status'>
                        <option value="Normal" <?php if ($res['status'] == "Normal") echo "selected='selected'"; ?> >Normal</option>
                        <option value="Waiting" <?php if ($res['status'] == "Waiting") echo "selected='selected'"; ?> >Waiting</option>
                        <option value="Suspended" <?php if ($res['status'] == "Suspended") echo "selected='selected'"; ?> >Suspended</option>
                        <option value="Deleted" <?php if ($res['status'] == "Deleted") echo "selected='selected'"; ?> >Deleted</option>
                        <option value="Admin" <?php if ($res['status'] == "Admin") echo "selected='selected'"; ?> >Admin</option>
                    </select>
                </div>
            </div>
            <div class='form-group'>
                <label class="control-label col-lg-2" for='group'>Group</label>
                <div class='col-md-4'>
                    <select class="form-control" name="group" id="group">
                        <?php
                        $query = 'select * from groups';
                        $result = DB::findAllFromQuery($query);
                        foreach ($result as $row) {
                            echo "<option value='$row[gid]'" . (($res['gid'] == $row['gid']) ? ("selected='selected'") : ("")) . ")>$row[groupname]</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <h3>Team Member 1 (compulsory)</h3>
            <div class='form-group'>
                <label class="control-label col-lg-2" for='name1'>Full Name</label>
                <div class='col-md-4'><input class="form-control" type='text' name='name1' id='name1' value="<?php echo $res['name1']; ?>" /></div>
            </div>
            <div class='form-group'>
                <label class="control-label col-lg-2" for='roll1'>Roll No</label>
                <div class='col-md-4'><input class="form-control" type='text' name='roll1' id='roll1' value="<?php echo $res['roll1']; ?>" /></div>
            </div>
            <div class='form-group'>
                <label class="control-label col-lg-2" for='branch1'>Branch</label>
                <div class='col-md-4'><input class="form-control" type='text' name='branch1' id='branch1' value="<?php echo $res['branch1']; ?>" /></div>
            </div>
            <div class='form-group'>
                <label class="control-label col-lg-2" for='email1'>E-mail</label>
                <div class='col-md-4'><input class="form-control" type='text' name='email1' id='email1' value="<?php echo $res['email1']; ?>" /></div>
            </div>
            <div class='form-group'>
                <label class="control-label col-lg-2" for='phone1'>Phone No</label>
                <div class='col-md-4'><input class="form-control" type='text' name='phone1' id='phone1' value="<?php echo $res['phone1']; ?>" /></div>
            </div>

            <div class='col-md-6'>
                <h3>Team Member 2 (optional)</h3>
                <div class='form-group'>
                    <label class="control-label col-lg-4" for='name2'>Full Name</label>
                    <div class='col-md-8'><input class="form-control" type='text' name='name2' id='name2' value="<?php echo $res['name2']; ?>"/></div>
                </div>
                <div class='form-group'>
                    <label class="control-label col-lg-4" for='roll2'>Roll No</label>
                    <div class='col-md-8'><input class="form-control" type='text' name='roll2' id='roll2' value="<?php echo $res['roll2']; ?>"/></div>
                </div>
                <div class='form-group'>
                    <label class="control-label col-lg-4" for='branch2'>Branch</label>
                    <div class='col-md-8'><input class="form-control" type='text' name='branch2' id='branch2' value="<?php echo $res['branch2']; ?>"/></div>
                </div>
                <div class='form-group'>
                    <label class="control-label col-lg-4" for='email2'>E-mail</label>
                    <div class='col-md-8'><input class="form-control" type='text' name='email2' id='email2' value="<?php echo $res['email2']; ?>"/></div>
                </div>
                <div class='form-group'>
                    <label class="col-lg-4 control-label" for='phone2'>Phone No</label>
                    <div class='col-md-8'><input class="form-control" type='text' name='phone2' id='phone2' value="<?php echo $res['phone2']; ?>"/></div>
                </div>
            </div>
            <div class='col-md-6'>
                <h3>Team Member 3 (optional)</h3>
                <div class='form-group'>
                    <label class="control-label col-lg-4" for='name3'>Full Name</label>
                    <div class='col-md-8'><input class="form-control" type='text' name='name3' id='name3' value="<?php echo $res['name3']; ?>"/></div>
                </div>
                <div class='form-group'>
                    <label class="control-label col-lg-4" for='roll3'>Roll No</label>
                    <div class='col-md-8'><input class="form-control" type='text' name='roll3' id='roll3' value="<?php echo $res['roll3']; ?>"/></div>
                </div>
                <div class='form-group'>
                    <label class="control-label col-lg-4" for='branch3'>Branch</label>
                    <div class='col-md-8'><input class="form-control" type='text' name='branch3' id='branch3' value="<?php echo $res['branch3']; ?>"/></div>
                </div>
                <div class='form-group'>
                    <label class="control-label col-lg-4" for='email3'>E-mail</label>
                    <div class='col-md-8'><input class="form-control" type='text' name='email3' id='email3' value="<?php echo $res['email3']; ?>"/></div>
                </div>
                <div class='form-group'>
                    <label class="control-label col-lg-4" for='phone3'>Phone No</label>
                    <div class='col-md-8'><input class="form-control" type='text' name='phone3' id='phone3' value="<?php echo $res['phone3']; ?>"/></div>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-lg-2'></label>
                <div class='col-md-8'><input type='submit' name='updateteam' value='Update Team' class='btn btn-primary btn-large'/></div>
            </div>
        </form>
        <?php
    } else {
        $query = "select tid, teamname, gid, status from teams";
        $res = DB::findAllFromQuery($query);
        echo "<h3>List of Teams</h3><table class='table table-hover'><tr><th>ID</th><th>Name</th><th>Group ID</th><th>Status</th><th>Options</th></tr>";
        foreach ($res as $row) {
            echo "<tr><td>$row[tid]</td><td>$row[teamname]</td><td>$row[gid]</td><td>$row[status]</td><td><a class='btn btn-primary' href='" . SITE_URL . "/adminteam/$row[teamname]'><i class='glyphicon glyphicon-edit'></i> Edit</a></td></tr>";
        }
        echo "</table>";
    }
} else {
    $_SESSION['msg'] = "Access Denied: You need to be administrator to access that page.";
    redirectTo(SITE_URL);
}
?>
