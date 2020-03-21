<?php

require_once 'config.php';

function customhash($str) {
    return md5($str); // To help change the hashing for password saving if needed.
}

$query = "select value from admin where variable='mode'";

$judge = DB::findOneFromQuery($query);
$query = "insert into logs value ('" . time() . "', '$_SERVER[REMOTE_ADDR]', '" . addslashes(print_r($_SESSION, TRUE)) . "', '" . addslashes(print_r($_REQUEST, TRUE)) . "' )";
DB::query($query);
$_SESSION['msg'] = '';
if ($judge['value'] != "Lockdown" || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') || isset($_POST['login'])) {
// ------------------ LOGIN ------------------- //
    if (isset($_POST['login'])) {
        if (!isset($_POST['teamname']) || $_POST['teamname'] == '') {
            $_SESSION['msg'] = "Teamname missing";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (!isset($_POST['password']) || $_POST['password'] == '') {
            $_SESSION['msg'] = "Teamname missing";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else {
            $_POST['password'] = customhash($_POST['password']);
            $query = "select * from teams where teamname  = '$_POST[teamname]' and pass = '$_POST[password]'";
            $res = DB::findOneFromQuery($query);
            if ($res && ($res['status'] == 'Normal' || $res['status'] == 'Admin')) {
                $save = $_SESSION;
                session_regenerate_id(true);
                $_SESSION = $save;
                $_SESSION['team']['id'] = $res['tid'];
                $_SESSION['team']['name'] = $res['teamname'];
                $_SESSION['loggedin'] = "true";
                $_SESSION['team']['status'] = $res['status'];
                $_SESSION['team']['time'] = time();
                redirectTo(SITE_URL . $_SESSION['url']);
            } else if ($res) {
                $_SESSION['msg'] = "You can not log in as your current status is : $res[status]";
                redirectTo(SITE_URL . $_SESSION['url']);
            } else {
                $_SESSION['msg'] = "Incorrect Username/Password";
                redirectTo(SITE_URL . $_SESSION['url']);
            }
        }
// ---------------------- LOG OUT -------------------------- //
    } else if (isset($_GET['logout'])) {
        session_destroy();
        redirectTo(SITE_URL . "/");
    } else if (isset($_GET['rid'])) {
        $query = "select displayio, access, runs.tid, runs.pid, runs.rid, result, runs.language as language, subs_code.code as code, subs_code.output as output, problems.output as correct, input from runs, subs_code, problems where runs.rid = $_GET[rid] and runs.rid = subs_code.rid and problems.pid = runs.pid";
        $runs = DB::findOneFromQuery($query);
        if ($_GET['file'] == "code" && ($runs['access'] == "public" || (isset($_SESSION['loggedin']) && $runs['tid'] == $_SESSION['team']['id']) || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == "Admin"))) {
            $ext = $valtoext[$runs['language']];
            header("Content-type: text/plain");
            header("Content-Disposition: attachment; filename=code-$_GET[rid].$ext");
            print $runs['code'];
        } else if (($runs['displayio'] == 1 && $runs['access'] == "public") || ($runs['displayio'] == 1 && isset($_SESSION['loggedin']) && $runs['tid'] == $_SESSION['team']['id']) || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == "Admin")) {
            header("Content-type: text/plain");
            header("Content-Disposition: attachment; filename=$_GET[file].txt");
            if ($_GET['file'] == "input") {
                print $runs['input'];
            } else if ($_GET['file'] == "correct") {
                print $runs['correct'];
            } else if ($_GET['file'] == "output") {
                if ($runs['result'] == "WA" || $runs['result'] == "PE") {
                    print $runs['output'];
                } else if ($runs['result'] == "AC") {
                    print $runs['correct'];
                }
            }
        }
// ------------------ CODE SUBMISSION --------------------- //
    } else if (isset($_POST['submitcode'])) {
        $_SESSION['subcode'] = addslashes($_POST['sub']);
        /*
         * 
         * Different levels of check ... (quite a job!)
         * Lvl 0 - User is logged in
         * Lvl 1 - Judge Active or Passive
         * Lvl 2 - All the POST value are set
         * Lvl 3 - Max Input file size
         * Lvl 4 - Problem and language combination is allowed
         * Lvl 5 - Only CQM's submissions are accepted in Active mode
         * Lvl 6 - Only Practice submissions are accepted in Passive mode
         * Lvl 7 - Only Active problem are accepted
         * Also Admin can by-pass lvl 1 and lvl 7 checks.
         * 
         */
        if (isset($_SESSION['loggedin'])) {     // Lvl 0
            $query = "select * from admin where variable ='mode' or variable ='endtime' or variable='ip' or variable ='port'";
            $check = DB::findAllFromQuery($query);
            $admin = Array();
            foreach ($check as $row) {
                $admin[$row['variable']] = $row['value'];
            }
            if ($admin['mode'] == 'Passive' || ($admin['mode'] == 'Active' && $admin['endtime'] >= time()) || $_SESSION['team']['status'] == 'Admin') { // Lvl 1
                $allowed = Array('application/octet-stream', 'text/x-csrc', 'text/x-c++src', 'text/x-csharp', 'text/x-java', 'text/javascript', 'text/x-pascal', 'text/x-perl', 'text/x-php', 'text/x-python', 'text/x-ruby', 'text/plain');
                if ((isset($_POST['lang']) && $_POST['lang'] != "") && ($_FILES['code_file']['size'] > 0 || (isset($_POST['sub']) && $_POST['sub'] != "")) && (isset($_POST['probcode']) && $_POST['probcode'] != "")) {  // Lvl 2
                    if ($_FILES['code_file']['size'] > 0 && $_FILES['code']['error'] == 0 && in_array($_FILES['code_file']['type'], $allowed)) {
                        $sourcecode = addslashes(file_get_contents($_FILES['code_file']['tmp_name']));
                    } else {
                        $sourcecode = $_POST['sub'];
                    }
                    $query = "select pid, status, contest, maxfilesize, total from problems where languages like '%$_POST[lang]%' and code = '$_POST[probcode]'";
                    $res = DB::findOneFromQuery($query);
                    if (strlen(stripcslashes($sourcecode)) <= $res['maxfilesize']) { // Lvl 3
                        if ($res) { // Lvl 4
                            if ($admin['mode'] == 'Active' && $admin['endtime'] >= time() && $res['contest'] == 'practice') { // Lvl 5
                                $_SESSION['msg'] = "Submissions are only accepted for CQM question right now. Come back later.";
                                redirectTo(SITE_URL . $_SESSION['url']);
                            } else if ($admin['mode'] == 'Passive' && $res['contest'] == 'contest' && $_SESSION['team']['status'] != 'Admin') { // Lvl 6
                                $_SESSION['msg'] = "Submissions are only accepted for Practice question right now.";
                                redirectTo(SITE_URL . $_SESSION['url']);
                            } else if ($res['status'] == 'Active' || $_SESSION['team']['status'] == 'Admin') { // Lvl 7
                                $submittime = time();
                                $query = "INSERT INTO runs (pid,tid,language,access,submittime) VALUES ('$res[pid]', '" . $_SESSION['team']['id'] . "', '$_POST[lang]', 'private', '" . $submittime . "')";
                                $res2 = DB::query($query);
                                DB::query("update problems set total=".($res['total']+1)." where pid = $res[pid]");
                                $query = "select rid from runs where tid = " . $_SESSION['team']['id'] . " and pid = $res[pid] and submittime = $submittime";
                                $result = DB::findOneFromQuery($query);
                                if ($result) {
                                    $rid = $result['rid'];
                                    $query = "INSERT INTO subs_code (rid, name, code) VALUES ('$rid', 'Main', '$sourcecode')";
                                    $result = DB::query($query);
                                    $query = "select rid from subs_code where rid = $rid";
                                    $result = DB::findOneFromQuery($query);
                                    if ($result) {
                                        unset($_SESSION['subcode']);
                                        $_SESSION['msg'] = "Problem submitted successfully. If your problem is not judged then contact admin.";
                                        $client = stream_socket_client($admin['ip'] . ":" . $admin['port'], $errno, $errorMessage);
                                        if ($client === false) {
                                            $_SESSION["msg"] .= "<br/>Cannot connect to Judge: Contact Admin";
                                        }
                                        fwrite($client, $rid);
                                        fclose($client);
                                        redirectTo(SITE_URL . "/viewsolution/" . $rid);
                                    } else {
                                        DB::query("Delete from runs where rid = $rid");
                                        $_SESSION['msg'] = "Some error occured during submission. If the problem continues contact Admin";
                                        redirectTo(SITE_URL . $_SESSION['url']);
                                    }
                                } else {
                                    $_SESSION['msg'] = "Some error occured during submission. If the problem continues contact Admin";
                                    redirectTo(SITE_URL . $_SESSION['url']);
                                }
                            } else {
                                $_SESSION['msg'] = "Problem is not active for submissions.";
                                redirectTo(SITE_URL . $_SESSION['url']);
                            }
                        } else {
                            $_SESSION['msg'] = "Either the problem does not exsits or the language is not allowed.";
                            redirectTo(SITE_URL . $_SESSION['url']);
                        }
                    } else {
                        $_SESSION['msg'] = "Submitted code exceeds size limits.";
                        redirectTo(SITE_URL . $_SESSION['url']);
                    }
                } else {
                    $_SESSION['msg'] = "You missed some necessary values.";
                    redirectTo(SITE_URL . $_SESSION['url']);
                }
            } else {
                $_SESSION['msg'] = "You cannot submit at this time.";
                redirectTo(SITE_URL . $_SESSION['url']);
            }
        } else {
            $_SESSION['msg'] = "You should be logged in to make a submission.";
            redirectTo(SITE_URL . $_SESSION['url']);
        }
// -------------------------------- REGISTER ---------------------------- //
    } else if (isset($_POST['register'])) {
        if ((isset($_POST['teamname']) && $_POST['teamname'] != "") && (isset($_POST['password']) && $_POST['password'] != "") && (isset($_POST['repassword']) && $_POST['repassword'] != "") && (isset($_POST['name1']) && $_POST['name1'] != "") && (isset($_POST['roll1']) && $_POST['roll1'] != "") && (isset($_POST['branch1']) && $_POST['branch1'] != "") && (isset($_POST['email1']) && $_POST['email1'] != "") && (isset($_POST['phno1']) && $_POST['phno1'] != "")) {
            if (preg_match("/^[a-zA-Z0-9_@]+$/", $_POST['teamname'], $match) && $match[0] == $_POST['teamname']) {
                if ($_POST['password'] == $_POST['repassword']) {
                    $query = "select * from teams where teamname='" . $_POST['teamname'] . "'";
                    $res = DB::findOneFromQuery($query);
                    if ($res == NULL) {
                        $query = "Insert into teams (teamname, pass, status, name1, roll1, branch1, email1, phone1, name2, roll2, branch2, email2, phone2, name3, roll3, branch3, email3, phone3, score, penalty, gid) 
                        values ('" . $_POST['teamname']. "', '" . customhash($_POST['password']) . "', 'Normal', '" . $_POST['name1'] . "', '" . $_POST['roll1'] . "','" . $_POST['branch1'] . "','" . $_POST['email1'] . "','" . $_POST['phno1'] . "','" . $_POST['name2'] . "', '" . $_POST['roll2'] . "', '" . $_POST['branch2'] . "', '" . $_POST['email2'] . "', '" . $_POST['phno2'] . "', '" . $_POST['name3'] . "', '" . $_POST['roll3'] . "', '" . $_POST['branch3'] . "','" . $_POST['email3'] . "','" . $_POST['phno3'] . "','0','0','" . $_POST['group'] . "')";
                        $res = DB::query($query);
                        $query = "select * from teams where teamname='" . $_POST['teamname'] . "'";
                        $res = DB::findOneFromQuery($query);
                        if ($res) {
                            $_SESSION['msg'] = "Team successfully registered.";
                            redirectTo(SITE_URL . "/");
                        } else {
                            $_SESSION['reg'] = $_POST;
                            $_SESSION['msg'] = "Some error occured. Try again. If the problem continues contact admin.";
                            redirectTo(SITE_URL . $_SESSION['url']);
                        }
                    } else {
                        $_SESSION['reg'] = $_POST;
                        $_SESSION['msg'] = "Teamname already registered.";
                        redirectTo(SITE_URL . $_SESSION['url']);
                    }
                } else {
                    $_SESSION['reg'] = $_POST;
                    $_SESSION['msg'] = "Password mismatch.";
                    redirectTo(SITE_URL . $_SESSION['url']);
                }
            } else {
                $_SESSION['reg'] = $_POST;
                $_SESSION['msg'] = "Team name should contain only alphabets numbers @ and _.";
                redirectTo(SITE_URL . $_SESSION['url']);
            }
        }
// ------------------------------- ACCOUNT UPDATE ---------------------------- //
    } else if (isset($_POST['update'])) {
        if (isset($_SESSION['loggedin'])) {
            if (isset($_POST['oldpass']) && isset($_POST['pass1']) && isset($_POST['repass']) && $_POST['pass1'] != "") {
                if ($_POST['pass1'] == $_POST['repass']) {
                    $query = "select * from teams where tid ='" . $_SESSION['team']['id'] . "' and pass ='" . customhash($_POST['oldpass']) . "'";
                    $res = DB::findOneFromQuery($query);
                    if ($res) {
                        $query = "update teams set pass = '" . customhash($_POST['pass1']) . "' " . ((isset($_POST['group']) && $_POST['group'] != "") ? (", gid='" . $_POST['group'] . "' ") : ("")) . "where tid='" . $_SESSION['team']['id'] . "'";
                        $res = DB::query($query);
                        $_SESSION['msg'] = "Password Updated";
                        redirectTo(SITE_URL . $_SESSION['url']);
                    } else {
                        $_SESSION['msg'] = "Old password incorrect.";
                        redirectTo(SITE_URL . $_SESSION['url']);
                    }
                } else {
                    $_SESSION['msg'] = "Password do not match or password is empty.";
                    redirectTo(SITE_URL . $_SESSION['url']);
                }
            } else if (isset($_POST['group'])) {
                if ($_POST['group'] != '') {
                    $query = "update teams set gid='" . $_POST['group'] . "' where tid='" . $_SESSION['team']['id'] . "'";
                    $res = DB::query($query);
                    $_SESSION['msg'] = "Group Updated";
                    redirectTo(SITE_URL . $_SESSION['url']);
                } else {
                    $_SESSION['msg'] = "Incorrect group.";
                    redirectTo(SITE_URL . $_SESSION['url']);
                }
            } else {
                $_SESSION['msg'] = "Not enough values.";
                redirectTo(SITE_URL . $_SESSION['url']);
            }
        } else {
            $_SESSION['msg'] = "You should be logged in.";
            redirectTo(SITE_URL . $_SESSION['url']);
        }
    } else if (isset($_POST['clar'])) {
        if (isset($_SESSION['loggedin'])) {
            if (isset($_POST['query']) && $_POST['query'] != "") {
                $query = "Insert into clar (time, pid, tid, query, access) 
                values ('" . time() . "', '" . $_POST['pid'] . "', '" . $_SESSION['team']['id'] . "', '" . $_POST['query'] . "', 'public')";
                $res = DB::query($query);
                $_SESSION['msg'] = "Clarification posted... we will reply soon.";
                redirectTo(SITE_URL . $_SESSION['url']);
            } else {
                $_SESSION['msg'] = "Empty Query :(";
                redirectTo(SITE_URL . $_SESSION['url']);
            }
        } else {
            $_SESSION['msg'] = "You should be logged in.";
            redirectTo(SITE_URL . $_SESSION['url']);
        }
    } else if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {  // request only admin can make
        if (isset($_POST['judgeupdate'])) {
            $admin = Array();
            $admin['mode'] = $_POST['mode'];
            $admin['endtime'] = $_POST['endtime'];
            $curTime = time(); //so both start and endtime use same time
            $admin['starttime'] = $curTime;
            if ($_POST['mode'] == 'Active') {
                if (!isset($admin['endtime']) || $admin['endtime'] == "") {
                    $admin['endtime'] = ($curTime + 180 * 60);
                } else {
                    $admin['endtime'] = ($curTime + $_POST['endtime'] * 60);
                }
            } else {
                $admin['endtime'] = $curTime;
            }
            $admin['penalty'] = $_POST['penalty'];
            foreach ($admin as $key => $val) {
                $query = "update admin set value = '$val' where variable = '$key'";
                DB::query($query);
            }
            $_SESSION['msg'] = "Judge Updated.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['addproblem'])) {
            $prob = Array();
            $prob['name'] = $_POST['name'];
            $prob['code'] = $_POST['code'];
            $prob['score'] = $_POST['score'];
            $prob['type'] = $_POST['type'];
            $prob['pgroup'] = $_POST['pgroup'];
            $prob['contest'] = $_POST['contest'];
            $prob['timelimit'] = $_POST['timelimit'];
            $prob['status'] = $_POST['status'];
            $prob['languages'] = $_POST['languages'];
            $prob['displayio'] = $_POST['displayio'];
            $prob['maxfilesize'] = $_POST['maxfilesize'];
            $prob['statement'] = addslashes(file_get_contents($_FILES['statement']['tmp_name']));
            $prob['input'] = addslashes(file_get_contents($_FILES['input']['tmp_name']));
            $prob['output'] = addslashes(addslashes(file_get_contents($_FILES['output']['tmp_name'])));
            $prob['sampleinput'] = addslashes(file_get_contents($_FILES['sampleinput']['tmp_name']));
            $prob['sampleoutput'] = addslashes(addslashes(file_get_contents($_FILES['sampleoutput']['tmp_name'])));
            if ($_FILES['image']['size'] > 0) {
                $prob['image'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
            }
            $query = "insert into problems (" . implode(array_keys($prob), ",") . ") values ('" . implode(array_values($prob), "','") . "')";
            DB::query($query);
            $_SESSION['msg'] = "Problem Added.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['updateproblem'])) {
            $query = "select * from admin where variable='ip' or variable ='port'";
            $check = DB::findAllFromQuery($query);
            $admin = Array();
            foreach ($check as $row) {
                $admin[$row['variable']] = $row['value'];
            }
            $prob = Array();
            $pid = $_POST['pid'];
            $_POST['newpid'] = $_POST['newpid'];
            $prob['pid'] = $_POST['newpid'];
            $prob['name'] = $_POST['name'];
            $prob['code'] = $_POST['code'];
            $prob['score'] = $_POST['score'];
            $prob['type'] = $_POST['type'];
            $prob['pgroup'] = $_POST['pgroup'];
            $prob['contest'] = $_POST['contest'];
            $prob['timelimit'] = $_POST['timelimit'];
            $prob['status'] = $_POST['status'];
            $prob['languages'] = $_POST['languages'];
            $prob['displayio'] = $_POST['displayio'];
            $prob['maxfilesize'] = $_POST['maxfilesize'];
            $prob['statement'] = $_POST['statement'];
            if ($_FILES['input']['size'] > 0) {
                $prob['input'] = addslashes(file_get_contents($_FILES['input']['tmp_name']));
                $client = stream_socket_client($admin['ip'] . ":" . $admin['port'], $errno, $errorMessage);
                if ($client === false) {
                    $_SESSION["msg"] .= "<br/>Cannot connect to Judge: Contact Admin";
                }
                fwrite($client, "del$pid");
                fclose($client);
            }
            if ($_FILES['output']['size'] > 0) {
                $prob['output'] = addslashes(addslashes(file_get_contents($_FILES['output']['tmp_name'])));
                $client = stream_socket_client($admin['ip'] . ":" . $admin['port'], $errno, $errorMessage);
                if ($client === false) {
                    $_SESSION["msg"] .= "<br/>Cannot connect to Judge: Contact Admin";
                }
                fwrite($client, "del$pid");
                fclose($client);
            }
            if ($_FILES['sampleinput']['size'] > 0) {
                $prob['sampleinput'] = addslashes(file_get_contents($_FILES['sampleinput']['tmp_name']));
            }
            if ($_FILES['sampleoutput']['size'] > 0) {
                $prob['sampleoutput'] = addslashes(file_get_contents($_FILES['sampleoutput']['tmp_name']));
            }
            if ($_FILES['image']['size'] > 0) {
                $prob['image'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
            }
            foreach ($prob as $key => $val) {
                $query = "update problems set $key = '$val' where pid=$pid";
                DB::query($query);
            }
            if ($_POST['pid'] != $_POST['newpid']) {
                DB::query("Update runs set pid = $_POST[newpid] where pid = $pid");
                DB::query("Update clar set pid = $_POST[newpid] where pid = $pid");
                $client = stream_socket_client($admin['ip'] . ":" . $admin['port'], $errno, $errorMessage);
                if ($client === false) {
                    $_SESSION["msg"] .= "<br/>Cannot connect to Judge: Contact Admin";
                }
                fwrite($client, "del$pid");
                fclose($client);
            }
            $_SESSION['msg'] .= "Problem Updated.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['addcontest'])) {
            $newcontest['code'] = $_POST['code'];
            $newcontest['name'] = $_POST['name'];
            $date = new DateTime($_POST['starttime']);
            $newcontest['starttime'] = $date->getTimestamp();
            $date = new DateTime($_POST['endtime']);
            $newcontest['endtime'] = $date->getTimestamp();
            $newcontest['announcement'] = $_POST['announcement'];
            $query = "insert into contest (" . implode(array_keys($newcontest), ",") . ") values ('" . implode(array_values($newcontest), "','") . "')";
            DB::query($query);
            $_SESSION['msg'] = "Contest Added.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['updatecontest'])) {
            $id = $_POST['id'];
            $newcontest['code'] = $_POST['code'];
            $newcontest['name'] = $_POST['name'];
            $date = new DateTime($_POST['starttime']);
            $newcontest['starttime'] = $date->getTimestamp();
            $date = new DateTime($_POST['endtime']);
            $newcontest['endtime'] = $date->getTimestamp();
            $newcontest['announcement'] = $_POST['announcement'];
            foreach ($newcontest as $key => $val) {
                $query = "update contest set $key = '$val' where id=$id";
                DB::query($query);
            }
            $_SESSION['msg'] = "Contest Updated.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['updateteam'])) {
            $tid = $_POST['tid'];
            $team['teamname'] = $_POST['teamname'];
            if (preg_match("/^[a-zA-Z0-9_@]+$/", $team['teamname'], $match) && $match[0] == $team['teamname']) {
                $team['pass'] = $_POST['password'];
                $team['status'] = $_POST['status'];
                $team['name1'] = $_POST['name1'];
                $team['roll1'] = $_POST['roll1'];
                $team['branch1'] = $_POST['branch1'];
                $team['email1'] = $_POST['email1'];
                $team['phone1'] = $_POST['phone1'];
                $team['name2'] = $_POST['name2'];
                $team['roll2'] = $_POST['roll2'];
                $team['branch2'] = $_POST['branch2'];
                $team['email2'] = $_POST['email2'];
                $team['phone2'] = $_POST['phone2'];
                $team['name3'] = $_POST['name3'];
                $team['roll3'] = $_POST['roll3'];
                $team['branch3'] = $_POST['branch3'];
                $team['email3'] = $_POST['email3'];
                $team['phone3'] = $_POST['phone3'];
                $team['gid'] = $_POST['group'];
                foreach ($team as $key => $val) {
                    $query = "update teams set $key = '$val' where tid=$tid";
                    DB::query($query);
                }
                $_SESSION['msg'] = "Team Updated.";
                redirectTo(SITE_URL . $_SESSION['url']);
            } else {
                $_SESSION['msg'] = "Teamname didn't satisfy the criteria.";
                redirectTo(SITE_URL . $_SESSION['url']);
            }
        } else if (isset($_POST['rejudge'])) {
            $query = "select * from admin where variable ='mode' or variable ='endtime' or variable='ip' or variable ='port'";
            $check = DB::findAllFromQuery($query);
            $admin = Array();
            foreach ($check as $row) {
                $admin[$row['variable']] = $row['value'];
            }
            if (isset($_POST['filter'])) {
                $sql = "select rid from runs where result='$_POST[filter]' and access != 'deleted'";
                if (isset($_POST['tid'])) {
                    $sql .= "and tid=$_POST[tid]";
                } else {
                    $sql .= "and pid=$_POST[pid]";
                }
                $res = DB::findAllFromQuery($sql);
                $rids = array();
                foreach ($res as $row) {
                    array_push($rids, $row['rid']);
                }
                $query = "update runs set result = NULL, time = NULL where rid in (" . implode(',', $rids) . ")";
            } else {
                $query = "update runs set result = NULL, time = NULL where ";
                if (isset($_POST['rid'])) {
                    $query .= "rid = " . $_POST['rid'];
                }
                if (isset($_POST['tid']) && isset($_POST['rid'])) {
                    $query .= " and tid=" . $_POST['tid'];
                } else if (isset($_POST['tid'])) {
                    $query .= "tid=" . $_POST['tid'];
                }
                if (isset($_POST['pid']) && (isset($_POST['rid']) || isset($_POST['tid']))) {
                    $query .= " and pid=" . $_POST['pid'];
                } else if (isset($_POST['pid'])) {
                    $query .= "pid=" . $_POST['pid'];
                }
            }
            DB::query($query);
            $client = stream_socket_client($admin['ip'] . ":" . $admin['port'], $errno, $errorMessage);
            if ($client === false) {
                $_SESSION["msg"] .= "<br/>Cannot connect to Judge: Contact Admin";
            }
            fwrite($client, 'rejudge');
            fclose($client);
            $_SESSION['msg'] .= "Problem(s) set to rejudge.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['dq'])) {
            $query = "update runs set result = 'DQ' where rid = " . $_POST['rid'];
            DB::query($query);
            $_SESSION['msg'] = "Solution Disqualified.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['runaccess'])) {
            $query = "update runs set access = '" . $_POST['access'] . "' where rid = " . $_POST['rid'];
            DB::query($query);
            $_SESSION['msg'] = "Access Updated.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['judgesocket'])) {
            $_POST['ip'] = $_POST['ip'];
            $query = "update admin set value='$_POST[ip]' where variable='ip'";
            DB::query($query);
            $_POST['port'] = $_POST['port'];
            $query = "update admin set value='$_POST[port]' where variable='port'";
            DB::query($query);
            $_SESSION['msg'] = "Socket Updated.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['judgenotice'])) {
            $_POST['notice'] = $_POST['notice'];
            $query = "update admin set value='$_POST[notice]' where variable='notice'";
            DB::query($query);
            $_SESSION['msg'] = "Notice Updated.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['clarreply'])) {
            $tid = $_POST['tid'];
            $pid = $_POST['pid'];
            $time = $_POST['time'];
            $reply = $_POST['reply'];
            $access = $_POST['access'];
            $query = "update clar set reply='$reply', access='$access' where tid=$tid and pid=$pid and time=$time";
            DB::query($query);
            $_SESSION['msg'] = "Reply Updated.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['addgroup'])) {
            $groupname = $_POST['groupname'];
            $query = "insert into groups (groupname) values ('$groupname')";
            DB::query($query);
            $_SESSION['msg'] = "Group Created.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['updategroup'])) {
            $gid = $_POST['gid'];
            $groupname = $_POST['groupname'];
            $query = "update groups set groupname ='$groupname' where gid=$gid";
            DB::query($query);
            $_SESSION['msg'] = "Group Updated.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['deletegroup'])) {
            $gid = $_POST['gid'];
            $query = "delete from groups where gid=$gid";
            DB::query($query);
            $_SESSION['msg'] = "Group Deleted.";
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['statusupdate'])) {
            $pid = $_POST['pid'];
            $status = $_POST['status'];
            $res = DB::query("update problems set status = '$status' where pid = '$pid'");
            echo ($res) ? ("1") : ("0");
        } else if (isset($_POST['addbmsg'])) {
            $bcast['title'] = $_POST['btitle'];
            $bcast['msg'] = $_POST['bmsg'];
            DB::insert('broadcast', $bcast);
            $_SESSION['msg'] = 'Message queued for delievery.';
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_POST['delbmsg'])) {
            $id = $_POST['id'];
            DB::delete('broadcast', "id=$id");
            $_SESSION['msg'] = 'Message deleted.';
            redirectTo(SITE_URL . $_SESSION['url']);
        } else if (isset($_GET['freeze'])) {
            require_once 'components.php';
            if (!in_array(strtolower($_GET['freeze']), array("admin", "broadcast", "clar", "contest", "groups", "logs", "problems", "teams", "runs", "subs_code"))) {
                DB::query("Drop table if exists $_GET[freeze]");
                DB::query("CREATE TABLE $_GET[freeze] (
  rank int(11) NOT NULL,
  teamname text NOT NULL,
  time int(11) NOT NULL,
  penalty int(11) NOT NULL,
  score int(11) NOT NULL,
  solved int(11) NOT NULL,
  PRIMARY KEY (rank)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
                $rank = getrankings($_GET['freeze']);
                $i = 1;
                $sql = "INSERT INTO $_GET[freeze] (rank, teamname, time, penalty, score, solved) VALUES";
                $row = array();
                foreach ($rank as $val) {
                    array_push($row, "($i, '$val[teamname]', $val[time], $val[penalty], $val[score], $val[solved])");
                    $i++;
                }
                $row = implode(",", $row);
                DB::query($sql . $row);
                DB::query("update contest set ranktable='$_GET[freeze]' where code = '$_GET[freeze]'");
            } else {
                $_SERVER['msg'] = "Contest code not allowed. Please change contest's code";
            }
            redirectTo(SITE_URL . $_SESSION['url']);
        }
    }
} else {
    $_SESSION['msg'] = "Judge is in Lockdown mode and so no requests are being processed.";
}
?>
