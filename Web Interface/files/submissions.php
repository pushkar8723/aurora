<?php

if ($judge['value'] != "Lockdown" || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')) {
    if (isset($_GET['code'])) {
        $_GET['code'] = addslashes($_GET['code']);
        ?>
        <center><h1>Submissions</h1></center>
        <?php

        if (isset($_GET['page']))
            $page = $_GET['page'];
        else
            $page = 1;
        if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
            $query = "select tid from teams where teamname = '$_GET[code]'";
            $push = DB::findOneFromQuery($query);
            $tid = $push['tid'];
            echo "<center><form method='post' action='" . SITE_URL . "/process.php'>
            <input type='hidden' name='tid' value='$tid' />";
            if(isset($_GET['filter'])){
                echo "<input type='hidden' name='filter' value='$_GET[filter]' />";
            }
            echo "<input type='submit' name='rejudge' class='btn btn-danger' value='Rejudge All Selected Submisssions'/>
            </form></center>";
        }
        $resopt = array('AC', 'RTE', 'WA', 'TLE', 'CE', 'DQ', 'PE');
        echo "<div class='breadcrumb' align='center'>";
        echo "Filter : <a class='label label-primary' href='" . SITE_URL . "/submissions/$_GET[code]'>ALL</a> ";
        foreach ($resopt as $val) {
            echo "<a class='label label-primary' href='" . SITE_URL . "/submissions/$_GET[code]&filter=$val'>$val</a> ";
        }
        echo "</div>";
        $select = "Select *";
        $query = "from runs where access!='deleted' and tid in (SELECT tid FROM teams WHERE teamname='$_GET[code]') AND pid in (SELECT pid FROM problems WHERE status='Active' or status='Inactive')".((isset($_GET['filter']))?(" and result='$_GET[filter]' "):(""))." order by rid desc";
        $result = DB::findAllWithCount($select, $query, $page, 25);
        $data = $result['data'];
        echo "<table class='table table-hover'><tr><th>Run ID</ht><th>Team</th><th>Problem</th><th>Language</th><th>Time</th><th>Result</th><th>Options</th></tr>";
        foreach ($data as $row) {
            $prob = DB::findOneFromQuery("Select name, code from problems where pid = $row[pid]");
            echo "<tr" . (($row['result'] == "AC") ? (" class='success'>") : (">")) . "<td>" . (($row['access'] == 'public' || (isset($_SESSION['loggedin']) && ($_SESSION['team']['status'] == "Admin" || $_SESSION['team']['id'] == $row['tid']))) ? ("<a href='" . SITE_URL . "/viewsolution/$row[rid]'>$row[rid]</a>") : ("$row[rid]")) . "</td><td><a href='" . SITE_URL . "/teams/$_GET[code]'>$_GET[code]</a></td><td><a href='" . SITE_URL . "/problems/$prob[code]'>$prob[name]</a></td><td>$row[language]</td><td>$row[time]</td><td>$row[result]</td><td>" . (($row['access'] == 'public' || (isset($_SESSION['loggedin']) && ($_SESSION['team']['status'] == "Admin" || $_SESSION['team']['id'] == $row['tid']))) ? ("<a class='btn btn-primary' href='" . SITE_URL . "/viewsolution/$row[rid]'>Code</a>") : ("")) . "</td></tr>";
        }
        echo "</table>";
        pagination($result['noofpages'], SITE_URL . "/submissions/$_GET[code]".((isset($_GET['filter']))?("&filter=$_GET[filter]"):("")), $page, 10);
    } else {
        ?>
        <center><h1>Submissions</h1></center>
        <?php

        if (isset($_GET['page']))
            $page = $_GET['page'];
        else
            $page = 1;
        $select = "Select *";
        if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
            $query = "from runs where pid not in (SELECT pid FROM problems WHERE status='Deleted') order by rid desc";
        } else {
            $query = "from runs where access!='deleted' AND pid not in (SELECT pid FROM problems WHERE status='Deleted') order by rid desc";
        }
        $result = DB::findAllWithCount($select, $query, $page, 25);
        $data = $result['data'];
        $team = array();
        $t = DB::findAllFromQuery("select tid, teamname from teams");
        foreach ($t as $row) {
            $team[$row['tid']] = $row['teamname'];
        }
        $probname = array();
        $probcode = array();
        $p = DB::findAllFromQuery("Select pid, name, code from problems");
        foreach ($p as $row) {
            $probname[$row['pid']] = $row['name'];
            $probcode[$row['pid']] = $row['code'];
        }
        echo "<table class='table table-hover'><tr><th>Run ID</ht><th>Team</th><th>Problem</th><th>Language</th><th>Time</th><th>Result</th><th>Options</th></tr>";
        foreach ($data as $row) {

            echo "<tr" . (($row['result'] == "AC") ? (" class='success'>") : (">")) . "<td>" . (($row['access'] == 'public' || (isset($_SESSION['loggedin']) && ($_SESSION['team']['status'] == "Admin" || $_SESSION['team']['id'] == $row['tid']))) ? ("<a href='" . SITE_URL . "/viewsolution/$row[rid]'>$row[rid]</a>") : ("$row[rid]")) . "</td><td><a href='" . SITE_URL . "/teams/" . $team[$row['tid']] . "'>" . $team[$row['tid']] . "</a></td><td><a href='" . SITE_URL . "/problems/" . $probcode[$row['pid']] . "'>" . $probname[$row['pid']] . "</a></td><td>$row[language]</td><td>$row[time]</td><td>$row[result]</td><td>" . (($row['access'] == 'public' || (isset($_SESSION['loggedin']) && ($_SESSION['team']['status'] == "Admin" || $_SESSION['team']['id'] == $row['tid']))) ? ("<a class='btn btn-primary' href='" . SITE_URL . "/viewsolution/$row[rid]'>Code</a>") : ("")) . "</td></tr>";
        }
        echo "</table>";
        pagination($result['noofpages'], SITE_URL . "/submissions", $page, 10);
    }
} else {
    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Lockdown Mode :(</h1>This feature is now offline as Judge is in Lockdown mode.</div><br/><br/><br/>";
}
?>
