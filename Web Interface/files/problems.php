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
        } else {
            if(isAdmin()){
                echo "<a class='btn btn-default pull-right' style='margin-top: 10px;' href='" . SITE_URL . "/adminproblem/$_GET[code]'><i class='glyphicon glyphicon-edit'></i> Edit</a>";
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
                echo "<div class='page-header text-center'><h1>$result[name]</h1></div>";
                echo "<div style='text-align:left'>";
                if($result['contest'] == 'contest'){
                    echo "<span class='btn-group'>".
                        "<a class='btn btn-default' href='". SITE_URL . "/contests/$result[pgroup]'><span class='glyphicon glyphicon-chevron-left'></span> Problems</a>".
                        "</span>";
                }
                echo "<span class='btn-group' style='float:right;'>" . ((isset($_SESSION['loggedin'])) ? ("<a class='btn btn-default' href='" . SITE_URL . "/status/$_GET[code]," . $_SESSION['team']['name'] . "'>My Submissions</a>") : ("")) . "<a class='btn btn-default' href='" . SITE_URL . "/status/$_GET[code]'>All Submissions</a></span></div>
            <br/><br/>" . $statement . "<br/>
            <div class='row'>
                <div class='col-md-6' style='overflow-x: auto;'><h4>Sample Input</h4><div class='limit'><pre class='brush: text'>$result[sampleinput]</pre></div></div>
                <div class='col-md-6' style='overflow-x: auto;'><h4>Sample Output</h4><div class='limit'><pre class='brush: text'>$result[sampleoutput]</pre></div></div>
            </div>
            <div class='row'>
                <div class='col-md-4'>
                    <div class='panel panel-default'>
                        <div class='panel-body'>
                            <strong>Problem ID: </strong>$result[code]<br/>
                            <strong>Time Limit: </strong>$result[timelimit] Second(s)<br/>
                            <strong>Score: </strong>$result[score] Point(s)<br/>
                            <strong>Input File Limit: </strong>$result[maxfilesize] Bytes<br/><br/>
                            ". (($result['status'] == 'Active' || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == "Admin")) ? ("<a class='btn btn-block btn-success' href='" . SITE_URL . "/submit/$_GET[code]'>Submit <span class='glyphicon glyphicon-cloud-upload'></span></a>") : ('')) . "
                        </div>
                    </div>
                </div>
                <div class='col-md-8'>
                    <div class='panel panel-default'>
                        <div class='panel-body text-center'>";

                if (isset($_SESSION['loggedin'])) {
                    ?>
                            <form action="<?php echo SITE_URL; ?>/process.php" role='form' method="post">
                                <input type="hidden" value="<?php echo $result['pid']; ?>" name="pid" />
                                <textarea class='form-control' style="" name="query" placeholder="Post clarification..."></textarea><br/>
                                <input name="clar" type="submit" class="btn btn-default btn-block" value="Send" />
                            </form>
                    <?php
                }else{
                    echo "<h3 class='text-center'>Login to post clarification.</h3>";
                }?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                                    <?php
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
                                        echo "<input type='hidden' name='tid' value='$row[tid]' /><input type='hidden' name='pid' value='$row[pid]' /><input type='hidden' name='time' value='$row[time]' />
                                                <textarea class='form-control' name='reply' placeholder='Enter response...'>$row[reply]</textarea><br/>
                                                    <div class='form-inline'><select style='width: 250px;' class='form-control' name='access'><option value='public' " . (($row['access'] == "public") ? ("selected='selected' ") : ("")) . ">Public</option><option value='deleted' " . (($row['access'] == "deleted") ? ("selected='selected' ") : ("")) . ">Deleted</option></select>  <input type='submit' class='btn btn-success' name='clarreply' value='Reply / Change Reply'/></div>
                                            </form>";
                                    }
                                }
                            }
                            else
                                echo "<h3 class='text-center'>No Clarifications.</h3>";?>
                        </div>
                    </div>
                </div>
            </div>
<?php
            }
        }
    } else {
        $button_drop="<div class=\"btn-group\">

            <button data-toggle=\"dropdown\" class=\"btn btn-default dropdown-toggle\"><span class=\"caret\"></span></button>
            <ul class=\"dropdown-menu\">
                <li id=\"prob_tag\"><a>Hide/Show Tag</a></li>
            </ul>
        </div>";
        echo '<script src="'.SITE_URL.'/js/custom.js" type="text/javascript"></script>';
        echo '<div class="page-header text-center"><h1>Practice Problems&nbsp;'.$button_drop.'</h1></div>';

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
        $counter=0;
        if(empty($res)){
            echo "<h3 class='text-center'>No problems available :( Check back later</h3>";
        }
        foreach ($res as $row) {
            if ($row['pgroup'] != $lastgroup){
                if($lastgroup!="") echo "</table></div>";
                echo "<div class=\"text-center\"><h3>$row[pgroup]</h3></div>";
                echo '<div id="table_prob_tag">';
                echo "<table  class='table table-hover'>";
                echo "<thead><tr><th>Name</th><th class='tabletaghidden'>Type</th><th>ID</th><th>Submissions</th><th>Score</th><th>Editorial</th></tr></thead>";
                $counter=$counter++;
                $lastgroup = $row['pgroup'];
            }
            echo "<tr ".((isset($solved[$row['pid']]))?("class='success'"):(""))."><td><a href='" . SITE_URL . "/problems/$row[code]'>$row[name]</a></td><td class='tabletaghidden' >$row[type]</td><td><a href='" . SITE_URL . "/submit/$row[code]'>$row[code]</a></td><td><a href='" . SITE_URL . "/status/$row[code]'>$row[solved]/$row[total]</a></td><td><span class='badge'>$row[score] pt</span></td><td>" . (isset($editorial[$row['pid']])?("<a href='" . SITE_URL . "/editorial/$row[code]'>Link</a>"):(($_SESSION['team']['status'] == 'Admin')?"<a href='". SITE_URL . "/admineditorial/$row[code]'>Add</a>":"None")) . "</td></tr>";
            $counter=$counter+1;
        }
        if(!empty($res)) echo "</table></div>"; //in the rare event there is NOTHING, need to remove divs to avoid fucking up layout
    }
} else {
    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Lockdown Mode :(</h1>This feature is now offline as Judge is in Lockdown mode.</div><br/><br/><br/>";
}
?>
