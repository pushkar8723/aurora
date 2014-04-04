<?php

function cmp($a, $b) {
    if ($a['score'] > $b['score'])
        return -1;
    else if ($a['score'] < $b['score'])
        return 1;
    else {
        if ($a['solved'] > $b['solved'])
            return -1;
        else if ($a['solved'] < $b['solved'])
            return 1;
        else {
            if ($a['time'] + $a['penalty'] * 20 * 60 < $b['time'] + $b['penalty'] * 20 * 60)
                return -1;
            else if ($a['time'] + $a['penalty'] * 20 * 60 > $b['time'] + $b['penalty'] * 20 * 60)
                return 1;
            else
                return 0;
        }
    }
}

function timeformating($a) {
    $sec = $a % 60;
    $a -= $sec;
    $a = $a / 60;
    $min = $a % 60;
    $a -= $min;
    $hr = $a / 60;
    $str = "";
    if ($hr > 0)
        $str .= $hr . ":";
    if ($min > 0)
        $str .= $min . ":";
    $str .= $sec;
    return $str;
}

if (isset($_GET['code'])) {
    $_GET['code'] = addslashes($_GET['code']);
    $query = "select * from contest where code = '$_GET[code]'";
    $contest = DB::findOneFromQuery($query);
    ?>
    <center><h1>Rankings - <?php echo $contest['name'] ?></h1></center>
    <?php
    $query = "select teams.tid, teamname, group_concat(distinct(problems.pid) SEPARATOR ',') as pids
from problems, teams, runs 
where 
runs.result = 'AC' and
problems.pid = runs.pid and 
problems.contest = 'contest' and
runs.access != 'deleted' and
teams.tid = runs.tid and 
problems.pgroup = '$_GET[code]' and 
(problems.status = 'Active' or problems.status = 'Inactive' or problems.status ='deleted') and
teams.status != 'Admin'
group by teams.tid";
    $res = DB::findAllFromQuery($query);
    foreach ($res as $row) {
        $rank[$row['tid']]['teamname'] = $row['teamname'];
        $rank[$row['tid']]['time'] = 0;
        $rank[$row['tid']]['score'] = 0;
        $rank[$row['tid']]['penalty'] = 0;
        $rank[$row['tid']]['solved'] = count(explode(',', $row['pids']));
        $query = "select 
                r.pid as pid, 
                min(submittime) as time, 
                score, 
                (select count(pid) from runs where tid = $row[tid] and pid = problems.pid and submittime < r.submittime and runs.access != 'deleted' and result != 'AC') as penalty 
                from runs r, problems where 
                r.access != 'deleted' and 
                tid = $row[tid] and 
                problems.pid in ($row[pids]) and 
                result = 'AC' and 
                r.pid = problems.pid 
                group by pid";
        $result = DB::findAllFromQuery($query);
        foreach ($result as $val) {
            if ($val['time'] > $rank[$row['tid']]['time'])
                $rank[$row['tid']]['time'] = $val['time'];
            $rank[$row['tid']]['score'] += $val['score'];
            $rank[$row['tid']]['penalty'] += $val['penalty'];
        }
    }
    usort($rank, "cmp");
    $i = 1;
    echo "<table class='table table-hover'><tr><th>Rank</th><th>Temaname</th><th>Time</th><th>Penalty</th><th>Score</th><th>Solved</th><th>Final Time</th></tr>";
    foreach ($rank as $val) {
        $val['time'] -= $contest['starttime'];
        $finaltime = $val['time'] + $val['penalty'] * 20 * 60;
        $val['time'] = timeformating($val['time']);
        $finaltime = timeformating($finaltime);
        echo "<tr><td>$i</td><td><a href='" . SITE_URL . "/teams/$val[teamname]'>$val[teamname]</a></td><td>$val[time]</td><td>$val[penalty]</td><td>$val[score]</td><td>$val[solved]</td><td>$finaltime</td></tr>";
        $i++;
    }
    echo "</table>";
} else {
    echo "<h1>Select a Contest</h1>
        <div class='row'>";
    $query = "select * from contest";
    $res = DB::findAllFromQuery($query);
    foreach ($res as $row) {
        echo "<div class='span2'><a href='" . SITE_URL . "/rank/$row[code]'>$row[name]</a></div>";
    }
    echo "</div>";
}
?>
