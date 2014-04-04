<?php
if ($judge['value'] != "Lockdown" || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')) {
    if (isset($_GET['page']))
        $page = $_GET['page'];
    else
        $page = 1;
    $options = explode(',', addslashes($_GET['code']));
    $query = "select pid from problems where code='$options[0]'";
    $push = DB::findOneFromQuery($query);
    $pid = $push['pid'];
    if (sizeof($options) == 1) {
        echo "<h1>All Submissions</h1>";
        if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
            echo "<center><form method='post' action='" . SITE_URL . "/process.php'>
            <input type='hidden' name='pid' value='$pid' />";
            if(isset($_GET['filter'])){
                echo "<input type='hidden' name='filter' value='$_GET[filter]' />";
            }
            echo "<input type='submit' name='rejudge' class='btn btn-danger' value='Rejudge All Selected Submisssions'/>
            </form></center><br/>";
        }
        $query = "from runs where pid=$pid and access !='deleted'".((isset($_GET['filter']))?(" and result='$_GET[filter]' "):(""))." order by rid desc";
    } else {
        $query = "select tid from teams where teamname = '$options[1]'";
        $push = DB::findOneFromQuery($query);
        $tid = $push['tid'];
        echo "<h1>$options[1]'s Submissions</h1>";
        if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
            echo "<center><form method='post' action='" . SITE_URL . "/process.php'>
                <input type='hidden' name='pid' value='$pid' />
                <input type='hidden' name='tid' value='$tid' />";
            if(isset($_GET['filter'])){
                echo "<input type='hidden' name='filter' value='$_GET[filter]' />";
            }
            echo "<input type='submit' name='rejudge' class='btn btn-danger' value='Rejudge All Selected Submisssions'/>
            </form></center><br/>";
        }
        $query = "from runs where pid=$pid and tid=$tid and access !='deleted'".((isset($_GET['filter']))?(" and result='$_GET[filter]' "):(""))." order by rid desc";
    }
    $resopt = array('AC', 'RTE', 'WA', 'TLE', 'CE', 'DQ', 'PE');
    echo "<div class='breadcrumb' align='center'>";
    echo "Filter : <a class='label label-primary' href='".SITE_URL."/status/$_GET[code]'>ALL</a> ";
    foreach ($resopt as $val){
        echo "<a class='label label-primary' href='".SITE_URL."/status/$_GET[code]&filter=$val'>$val</a> ";
    }
    echo "</div>";
    $result = DB::findAllWithCount("select *", $query, $page, 20);
    $res = $result['data'];
    echo "<table class='table table-hover'><tr><th>Run ID</ht><th>Team</th><th>Problem</th><th>Language</th><th>Time</th><th>Result</th><th>Options</th></tr>";
    foreach ($res as $row) {
        $team = DB::findOneFromQuery("select teamname from teams where tid = $row[tid]");
        $prob = DB::findOneFromQuery("Select name, code from problems where pid = $row[pid]");
        echo "<tr" . (($row['result'] == "AC") ? (" class='success'>") : (">")) . "<td>" . (($row['access'] == 'public' || (isset($_SESSION['loggedin']) && ($_SESSION['team']['status'] == "Admin" || $_SESSION['team']['id'] == $row['tid']))) ? ("<a href='" . SITE_URL . "/viewsolution/$row[rid]'>$row[rid]</a>") : ("$row[rid]")) . "</td><td><a href='" . SITE_URL . "/teams/$team[teamname]'>$team[teamname]</a></td><td><a href='" . SITE_URL . "/problems/$prob[code]'>$prob[name]</a></td><td>$row[language]</td><td>$row[time]</td><td>$row[result]</td><td>" . (($row['access'] == 'public' || (isset($_SESSION['loggedin']) && ($_SESSION['team']['status'] == "Admin" || $_SESSION['team']['id'] == $row['tid']))) ? ("<a class='btn btn-primary' href='" . SITE_URL . "/viewsolution/$row[rid]'>Code</a>") : ("")) . "</td></tr>";
    }
    echo "</table>";
    pagination($result['noofpages'], SITE_URL."/status/$_GET[code]".((isset($_GET['filter']))?("&filter=$_GET[filter]"):("")), $page, 10);
} else {
    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Lockdown Mode :(</h1>This feature is now offline as Judge is in Lockdown mode.</div><br/><br/><br/>";
}
?>
