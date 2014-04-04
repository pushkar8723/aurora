<?php

if ($judge['value'] != "Lockdown" || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')) {
    ?>
    <center><h1>Rankings</h1></center>
    <?php
    if(isset($_GET['code'])){
        $tab = addslashes($_GET['code']);
    }
    $query = "select * from groups";
    $result = DB::findAllFromQuery($query);
    $group = Array();
    $groupval = Array();
    foreach ($result as $row) {
        $group[$row['gid']] = $row['groupname'];
        $groupval[$row['groupname']] = $row['gid'];
    }
    echo "<ul class='nav nav-tabs'>
            <li " . ((!isset($_GET['code']) || $_GET['code'] == "")? ("class='active'") : ("")) . "><a href='" . SITE_URL . "/rankings'>Everyone</a></li>";
    foreach ($group as $gr){
        echo "<li " . ((isset($tab) && $tab == $gr) ? ("class='active'") : ("")) . "><a href='" . SITE_URL . "/rankings/$gr'>$gr</a></li>";
    }
    echo "</ul>";
    if (isset($_GET['page']))
        $page = $_GET['page'];
    else
        $page = 1;
    $select = "SELECT * ";
    $body = " FROM teams WHERE status='Normal'";
    if (isset($tab)){
        $body .= " and gid=$groupval[$tab]";
    }
    $body .= " ORDER BY score DESC, penalty ASC";
    $result = DB::findAllWithCount($select, $body, $page, 20);
    $data = $result['data'];
    $i = 20 * ($page - 1) + 1;
//    $ac = array();
//    $query = "SELECT tid, count(distinct(runs.pid)) as count FROM runs,problems WHERE runs.result='AC' and problems.status!='Deleted' and runs.pid = problems.pid and problems.contest='contest' group by tid";
//    $res = DB::findAllFromQuery($query);
//    foreach ($res as $row)
//        $ac[$row['tid']] = $row['count'];
//    $tot = $result;
//    $query = "SELECT tid, count(distinct(runs.pid)) as count FROM runs,problems WHERE problems.status!='Deleted' and runs.pid = problems.pid and problems.contest='contest' group by tid";
//    $res = DB::findAllFromQuery($query);
//    foreach ($res as $row)
//        $tot[$row['tid']] = $row['count'];
    echo "<table class='table table-hover'><tr><th>Rank</th><th>Team Name</th><th>Team Group</th><th>Score</th></tr>";
    foreach ($data as $row) {
        //$subs = ((isset($ac[$row['tid']])) ? ($ac[$row['tid']]) : ("0")) . "/" . ((isset($tot[$row['tid']])) ? ($tot[$row['tid']]) : ("0"));<td>$subs</td>
        echo "<tr><td>" . $i++ . "</td><td><a href='" . SITE_URL . "/teams/$row[teamname]'>$row[teamname]</a></td><td>" . $group[$row['gid']] . "</td><td>$row[score]</td></tr>";
    }
    echo "</table>";
    pagination($result['noofpages'], SITE_URL . "/rankings".((isset($tab)?("/$tab"):(""))), $page, 10);
} else {
    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Lockdown Mode :(</h1>This feature is now offline as Judge is in Lockdown mode.</div><br/><br/><br/>";
}
?>
