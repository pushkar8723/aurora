<?php
if ($judge['value'] != "Lockdown" || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')) {
    if (isset($_GET['code'])) {
        $_GET['code'] = addslashes($_GET['code']);
        if (isAdmin()) {
            $query = "select * from problems where code = '$_GET[code]'";
        } else {
            $query = "select * from problems where code = '$_GET[code]' and status != 'Deleted'";
        }
        $result = DB::findOneFromQuery($query);
        if ($result == NULL) {
            echo errorMessageHTML("<b>Problem not found!</b>");
            // echo "<br/><br/><br/><div style='padding: 10px;'><h1>Problem not Found :(</h1>The problem you are looking for doesn't exsits.</div><br/><br/><br/>";
        } else {
            if(isAdmin()){
                echo "<a class='btn btn-primary pull-right' style='margin-top: 10px;' href='" . SITE_URL . "/adminproblem/$_GET[code]'><i class='glyphicon glyphicon-edit'></i> Edit</a>";
            }
            // if($result['contest'] == 'contest'){
            //     echo "<a class='btn btn-primary' style='margin-top: 10px;' href='" . SITE_URL . "/contests/$result[pgroup]'><i class='glyphicon glyphicon-edit'></i> Problems</a>";
            // }
            if ($result['contest'] == 'contest' && !isAdmin()) {
                $query = "select starttime from contest where code = '$result[pgroup]'";
                $check = DB::findOneFromQuery($query);
                if ($check['starttime'] > time()) {
                    echo errorMessageHTML("<b>Contest not yet started!</b>");
                    $flag = 1;
                } else {
                    $flag = 0;
                }
            } else {
                $flag = 0;
            }
            if ($flag == 0) {
                $statement = stripslashes($result["statement"]);
                $statement = preg_replace("/\n/", "<br>", $statement);
                $statement = preg_replace("/<image \/>/", "<img src='data:image/jpeg;base64,$result[image]' />", $statement);
                echo "<center><h1>$result[name]</h1></center>";
                echo "<div style='text-align:left'>";
                if($result['contest'] == 'contest'){
                    echo "<span class='btn-group'>".
                        "<a class='btn btn-primary' href='". SITE_URL . "/contests/$result[pgroup]'><span class='glyphicon glyphicon-chevron-left'></span> Problems</a>".
                        "</span>";
                }
                echo "<span class='btn-group' style='float:right;'>" . ((isset($_SESSION['loggedin'])) ? ("<a class='btn btn-primary' href='" . SITE_URL . "/status/$_GET[code]," . $_SESSION['team']['name'] . "'>My Submissions</a>") : ("")) . "<a class='btn btn-primary' href='" . SITE_URL . "/status/$_GET[code]'>All Submissions</a>" . (($result['status'] == 'Active' || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == "Admin")) ? ("<a class='btn btn-primary' href='" . SITE_URL . "/submit/$_GET[code]'>Submit</a>") : ('')) . "</span></div>
            <br/><br/>" . $statement . "<br/>
                <b>Time Limit :</b> $result[timelimit] Second(s)<br/><b>Score :</b> $result[score] Point(s)<br/><b>Input File Limit :</b> $result[maxfilesize] Bytes<br/><b>Languages Allowed :</b> $result[languages]";
                echo "<hr/><h3>Clarifications</h3>";
                $query = "select * from clar where pid = $result[pid] and access = 'Public'";
                $clar = DB::findAllFromQuery($query);
                if ($clar != NULL) {
                    $id = 0;
                    foreach ($clar as $row) {
                        if($id != 0)
                            echo '<hr>';
                        $id++;
                        $query = "select teamname from teams where tid = $row[tid]";
                        $team = DB::findOneFromQuery($query);
                        $rowquery = preg_replace("/\n/", " ",htmlspecialchars($row['query']));
                        $rowreply = preg_replace("/\n/", " ", htmlspecialchars($row['reply']));
                        echo "<b><a href='" . SITE_URL . "/teams/$team[teamname]'>$team[teamname]</a>:<br/>Q. $rowquery</b><br/>" . (($rowreply != "") ? ("A. $rowreply") : (''));
                        if (isAdmin()) {
                            echo "<form role='form' method='post' action='" . SITE_URL . "/process.php'>";
                            echo "Access: <select style='width: 250px;' class='form-control' name='access'><option value='public' " . (($row['access'] == "public") ? ("selected='selected' ") : ("")) . ">Public</option><option value='deleted' " . (($row['access'] == "deleted") ? ("selected='selected' ") : ("")) . ">Deleted</option></select><br/>";
                            echo "<input type='hidden' name='tid' value='$row[tid]' /><input type='hidden' name='pid' value='$row[pid]' /><input type='hidden' name='time' value='$row[time]' />
<textarea class='form-control' name='reply' style='width: 450px; height: 100px;'>$row[reply]</textarea><br/>
<input type='submit' class='btn btn-primary' name='clarreply' value='Reply / Change Reply'/>
</form>";
                        }
                    }
                }
                else
                    echo "No Clarifications.<br>";
                if (isset($_SESSION['loggedin'])) {
                    ?>
                    <hr/>
                    <h3>Post Clarification</h3>
                    <form action="<?php echo SITE_URL; ?>/process.php" role='form' method="post">
                        <input type="hidden" value="<?php echo $result['pid']; ?>" name="pid" />
                        <textarea class='form-control' style="width: 500px; height: 200px;" name="query"></textarea><br/>
                        <input name="clar" type="submit" class="btn btn-primary" />
                    </form><br/>
                    <?php
                }
            }
        }
    } else {
        $button_drop='<div class="btn-group">
            
            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"><span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li id="prob_tag">Hide/Show Tag</li>
            </ul>
        </div>';
        echo '<script src="'.SITE_URL.'/js/custom.js" type="text/javascript"></script>';
        echo '<center><h1>Practice Problems&nbsp;'.$button_drop.'</h1></center>';

        if (isset($_SESSION['loggedin'])){
            $solved = array();
            $query = "select distinct(pid) as pid from runs where result = 'AC' and tid = ".$_SESSION['team']['id'];
            $res = DB::findAllFromQuery($query);
            foreach($res as $row){
                $solved[$row['pid']] = true;
            }
        }
        $editorial = array();
        $query = "select pid from editorials";
        $res = DB::findAllFromQuery($query);
        foreach($res as $row){
            $editorial[$row['pid']] = 1;
        }
        if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == "Admin") {
            $query = "select pid, name, code, status, pgroup, type, score, solved, total from problems where contest='practice' order by pid desc";
        } else {
            $query = "select pid, name, code, status, pgroup, type, score, solved, total from problems where status != 'Deleted' and contest='practice' order by pid desc";
        }
        $res = DB::findAllFromQuery($query);
        $lastgroup = "";
        echo '<div id="table_prob_tag">';
        echo "<table  class='table table-hover'>";
        $counter=0;
        foreach ($res as $row) {
            if ($row['pgroup'] != $lastgroup){
                echo "<tr><td colspan='6'><center><h3>$row[pgroup]</h3></center></td></tr><tr><th>Name</th><th class='tabletaghidden'>Type</th><th>Score</th><th>Code</th><th>Submissions</th><th>Editorial</th></tr>";
                $counter=$counter++;
                $lastgroup = $row['pgroup'];
            }
            echo "<tr ".((isset($solved[$row['pid']]))?("class='success'"):(""))."><td><a href='" . SITE_URL . "/problems/$row[code]'>$row[name]</a></td><td class='tabletaghidden' ><a href='" . SITE_URL . "/problems/$row[code]'>$row[type]</a></td><td><a href='" . SITE_URL . "/problems/$row[code]'>$row[score]</a></td><td><a href='" . SITE_URL . "/submit/$row[code]'>$row[code]</a></td><td><a href='" . SITE_URL . "/status/$row[code]'>$row[solved]/$row[total]</a></td><td>" . (isset($editorial[$row['pid']])?("<a href='" . SITE_URL . "/editorial/$row[code]'>Link</a>"):(($_SESSION['team']['status'] == 'Admin')?"<a href='". SITE_URL . "/admineditorial/$row[code]'>Add</a>":"None")) . "</td></tr>";
            $counter=$counter+1;
        }
        echo "</table>";
        echo '</div>';

    }
} else {
    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Lockdown Mode :(</h1>This feature is now offline as Judge is in Lockdown mode.</div><br/><br/><br/>";
}
?>
