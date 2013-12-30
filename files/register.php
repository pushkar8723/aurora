<?php
if ($judge['value'] != "Lockdown" || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')) {
    if (isset($_SESSION['loggedin'])) {
        redirectTo(SITE_URL);
    }
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
    <h1>Register</h1>
    <form method='post' class='form-horizontal' role='form' action='<?php echo SITE_URL; ?>/process.php'>
        <div class='col-lg-12'>
            <div class='form-group'>
                <label class="col-lg-2 control-label" for='teamname'>Team Name</label>
                <div class="col-lg-4">
                    <input class='form-control' type='text' name='teamname' id='teamname' required data-placement='right' title='Only alphabets numbers _ and @ are allowed' <?php
                    if (isset($_SESSION['reg']['teamname'])) {
                        echo "value='" . $_SESSION['reg']['teamname'] . "' ";
                        unset($_SESSION['reg']['teamname']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class="col-lg-2 control-label" for='pass1'>Password</label>
                <div class="col-lg-4">
                    <input class='form-control' type='password' name='password' id='pass1' required />
                </div>
            </div>
            <div class='form-group'>
                <label class="col-lg-2 control-label" for='pass2'>Re-Password</label>
                <div class='col-lg-4'><input class='form-control' type='password' name='repassword' id='pass2' required/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-2 control-label' for='pass2'>Group</label>
                <div class='col-lg-4'>
                    <select name="group" class='form-control'>
                        <?php
                        $query = 'select * from groups';
                        $result = DB::findAllFromQuery($query);
                        foreach ($result as $row) {
                            echo "<option value='$row[gid]'>$row[groupname]</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <h3>Team Member 1 (compulsory)</h3>
            <div class='form-group'>
                <label class='col-lg-2 control-label' for='name1'>Full Name</label>
                <div class='col-lg-4'><input class='form-control' type='text' name='name1' id='name1' required <?php
                    if (isset($_SESSION['reg']['name1'])) {
                        echo "value='" . $_SESSION['reg']['name1'] . "' ";
                        unset($_SESSION['reg']['name1']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-2 control-label' for='roll1'>Roll No</label>
                <div class='col-lg-4'><input class='form-control' type='text' name='roll1' id='roll1' required <?php
                    if (isset($_SESSION['reg']['roll1'])) {
                        echo "value='" . $_SESSION['reg']['roll1'] . "' ";
                        unset($_SESSION['reg']['roll1']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-2 control-label' for='branch1'>Branch</label>
                <div class='col-lg-4'><input class='form-control' type='text' name='branch1' id='branch1' required <?php
                    if (isset($_SESSION['reg']['branch1'])) {
                        echo "value='" . $_SESSION['reg']['branch1'] . "' ";
                        unset($_SESSION['reg']['branch1']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-2 control-label' for='email1'>E-mail</label>
                <div class='col-lg-4'><input class='form-control' type='text' name='email1' id='email1' required <?php
                    if (isset($_SESSION['reg']['email1'])) {
                        echo "value='" . $_SESSION['reg']['email1'] . "' ";
                        unset($_SESSION['reg']['email1']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-2 control-label' for='phno1'>Phone No</label>
                <div class='col-lg-4'><input class='form-control' type='text' name='phno1' id='phno1' required <?php
                    if (isset($_SESSION['reg']['phno1'])) {
                        echo "value='" . $_SESSION['reg']['phno1'] . "' ";
                        unset($_SESSION['reg']['phno1']);
                    }
                    ?>/></div>
            </div>
        </div>
        <div class='col-lg-6'>
            <h3>Team Member 2 (optional)</h3>
            <div class='form-group'>
                <label class='col-lg-4 control-label' for='name2'>Full Name</label>
                <div class='col-lg-8'><input class='form-control' type='text' name='name2' id='name2' <?php
                    if (isset($_SESSION['reg']['name2'])) {
                        echo "value='" . $_SESSION['reg']['name2'] . "' ";
                        unset($_SESSION['reg']['name2']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-4 control-label' for='roll2'>Roll No</label>
                <div class='col-lg-8'><input class='form-control' type='text' name='roll2' id='roll2' <?php
                    if (isset($_SESSION['reg']['roll2'])) {
                        echo "value='" . $_SESSION['reg']['roll2'] . "' ";
                        unset($_SESSION['reg']['roll2']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-4 control-label' for='branch2'>Branch</label>
                <div class='col-lg-8'><input class='form-control' type='text' name='branch2' id='branch2' <?php
                    if (isset($_SESSION['reg']['branch2'])) {
                        echo "value='" . $_SESSION['reg']['branch2'] . "' ";
                        unset($_SESSION['reg']['branch2']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-4 control-label' for='email2'>E-mail</label>
                <div class='col-lg-8'><input class='form-control' type='text' name='email2' id='email2' <?php
                    if (isset($_SESSION['reg']['email2'])) {
                        echo "value='" . $_SESSION['reg']['email2'] . "' ";
                        unset($_SESSION['reg']['email2']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-4 control-label' for='phno2'>Phone No</label>
                <div class='col-lg-8'><input class='form-control' type='text' name='phno2' id='phno2' <?php
                    if (isset($_SESSION['reg']['phno2'])) {
                        echo "value='" . $_SESSION['reg']['phno2'] . "' ";
                        unset($_SESSION['reg']['phno2']);
                    }
                    ?>/></div>
            </div>
        </div>
        <div class='col-lg-6'>
            <h3>Team Member 3 (optional)</h3>
            <div class='form-group'>
                <label class='col-lg-4 control-label' for='name3'>Full Name</label>
                <div class='col-lg-8'><input class='form-control' type='text' name='name3' id='name3' <?php
                    if (isset($_SESSION['reg']['name3'])) {
                        echo "value='" . $_SESSION['reg']['name3'] . "' ";
                        unset($_SESSION['reg']['name3']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-4 control-label' for='roll3'>Roll No</label>
                <div class='col-lg-8'><input class='form-control' type='text' name='roll3' id='roll3' <?php
                    if (isset($_SESSION['reg']['roll3'])) {
                        echo "value='" . $_SESSION['reg']['roll3'] . "' ";
                        unset($_SESSION['reg']['roll3']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-4 control-label' for='branch3'>Branch</label>
                <div class='col-lg-8'><input class='form-control' type='text' name='branch3' id='branch3' <?php
                    if (isset($_SESSION['reg']['branch3'])) {
                        echo "value='" . $_SESSION['reg']['branch3'] . "' ";
                        unset($_SESSION['reg']['branch3']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-4 control-label' for='email3'>E-mail</label>
                <div class='col-lg-8'><input class='form-control' type='text' name='email3' id='email3' <?php
                    if (isset($_SESSION['reg']['email3'])) {
                        echo "value='" . $_SESSION['reg']['email3'] . "' ";
                        unset($_SESSION['reg']['email3']);
                    }
                    ?>/></div>
            </div>
            <div class='form-group'>
                <label class='col-lg-4 control-label' for='phno3'>Phone No</label>
                <div class='col-lg-8'><input class='form-control' type='text' name='phno3' id='phno3' <?php
                    if (isset($_SESSION['reg']['phno3'])) {
                        echo "value='" . $_SESSION['reg']['phno3'] . "' ";
                        unset($_SESSION['reg']['phno3']);
                    }
                    ?>/></div>
            </div>
        </div>
        <div class='col-lg-12'>
            <div class='form-group'>
                <label class='col-lg-2 control-label'></label>
                <div class='col-lg-4'><input type='submit' name='register' value='Submit' class='btn btn-primary btn-large'/></div>
            </div>
        </div>
    </form>
    <?php
} else {
    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Lockdown Mode :(</h1>This feature is now offline as Judge is in Lockdown mode.</div><br/><br/><br/>";
}
?>