<?php

function loginbox() {
    if (!isset($_SESSION['loggedin'])) {
        ?>
        <h4>Login</h4>
        <center>
            <form action="<?php echo SITE_URL; ?>/process.php" method="post" role="form">
                <div class="input-group" style="margin-bottom: -1px;">
                    <span class="input-group-addon" style="border-bottom-left-radius: 0;"><i class="glyphicon glyphicon-user"></i></span>
                    <input class="form-control" style="border-bottom-right-radius: 0;" type="text" name="teamname" placeholder="Teamname" required/>
                </div>
                <div class="input-group">
                    <span style="border-top-left-radius: 0;" class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <input style="border-top-right-radius: 0;" class="form-control" type="password" name="password" placeholder="Password" required/>
                </div><br/>
                <input type="submit" name="login" value="Log In" class="btn btn-primary btn-block"/>
            </form>
            <a href='<?php echo SITE_URL; ?>/register'>New Team? Register Here.</a>
        </center>
        <?php
    } else {
        ?>
        <h4>Team</h4> 
        <table class='table table-condensed'>
            <tr><th>Team Name</th><th>Score</th><th>Overall Rank</th></tr>
            <?php
            $query = "SELECT count(*)+1 as rank, (select score from teams where tid = " . $_SESSION['team']['id'] . ") as sco FROM `teams` WHERE (score > (select score from teams where tid = " . $_SESSION['team']['id'] . ") and status = 'Normal') or (score = (select score from teams where tid = " . $_SESSION['team']['id'] . ") and penalty < (select penalty from teams where tid = " . $_SESSION['team']['id'] . ") and status='Normal') ";
            $res = DB::findOneFromQuery($query);
            echo "<tr><td><a href='" . SITE_URL . "/teams/" . $_SESSION['team']['name'] . "'>" . $_SESSION['team']['name'] . "</a></td><td>$res[sco]</td><td style='text-align: center'>$res[rank]</td></tr>";
            ?>
        </table>
        <?php
    }
}

function mysubs() {
    ?>
    <h4>My Submissions</h4>
    <table class='table table-condensed'>
        <tr><th>RID</th><th>Problem</th><th>Result</th></tr>
        <?php
        $query = "SELECT rid, (select name from problems where pid = runs.pid) as pname,(select code from problems where pid = runs.pid) as pcode, result FROM runs WHERE tid = " . $_SESSION['team']['id'] . " order by rid desc limit 0, 5";
        $res = DB::findAllFromQuery($query);
        foreach ($res as $row)
            echo "<tr><td><a href='" . SITE_URL . "/viewsolution/$row[rid]'>$row[rid]</a></td><td><a href='" . SITE_URL . "/problems/$row[pcode]'>$row[pname]</a></td><td>$row[result]</td></tr>";
        ?>
    </table>
    <?php
}

function contest_status() {
    $status = Array();
    $query = "select * from admin where variable = 'endtime' or variable = 'mode' or variable='ip' or variable='port'";
    DB::query($query);
    $result = DB::findAllFromQuery($query);
    foreach ($result as $row) {
        $status[$row['variable']] = $row['value'];
    }
    ?>
    <table class='table table-condensed'>
        <tr><th>Mode</th><th>Judge</th><th>End Time</th></tr>
        <tr><td><div id="ajax-contest-mode">
                    <?php
                    if ($status['mode'] == "Active" && $status['endtime'] < time())
                        echo "Disabled";
                    else
                        echo $status['mode'];
                    ?></div>
            </td>
            <td>
                <?php
                $client = stream_socket_client($status['ip'] . ":" . $status['port'], $errno, $errorMessage);
                if ($client === false)
                    echo "Offline";
                else
                    echo "Online";
                fclose($client);
                ?>
            </td>
            <td>
                <?php
                if ($status['mode'] == "Active") {
                    echo "<div id='ajax-contest-time'></div>";
                    ?>
                    <script type='text/javascript'>
                        var countdown = <?php echo $status['endtime'] - time(); ?>;
                        function step() {
                            if (countdown > 0) {
                                $("div#ajax-contest-time").html(parseInt(countdown / 3600) + ":" + parseInt((countdown / 60)) % 60 + ":" + (countdown % 60));
                            } else {
                                $("div#ajax-contest-time").html("NA");
                                $("div#ajax-contest-mode").html("Disabled");
                            }
                            if (countdown >= 0)
                                countdown--;
                            window.setTimeout("step();", 1000);
                        }
                        step();
                    </script>
                    <?php
                } else
                    echo "NA";
                ?>
            </td>
        </tr>
    </table>
    <?php
}

function latestsubs() {
    echo "<h4>Latest Submissions</h4><table class='table table-condensed'><tr><th>RID</th><th>Team</th><th>Problem</th><th>Result</th></tr >";
    $query = "SELECT rid, (select teamname from teams where tid = runs.tid) as tname, (select name from problems where pid = runs.pid) as pname,(select code from problems where pid = runs.pid) as pcode, result FROM runs order by rid desc limit 0, 5";
    $res = DB::findAllFromQuery($query);
    //echo $query;
    foreach ($res as $row)
        echo "<tr><td><a href='" . SITE_URL . "/viewsolution/$row[rid]'>$row[rid]</a></td><td><a href='" . SITE_URL . "/teams/$row[tname]'>$row[tname]</a></td><td><a href='" . SITE_URL . "/problems/$row[pcode]'>$row[pname]</a></td><td>$row[result]</td></tr>";
    echo "</table>";
}

function rankings() {
    $select = "SELECT * ";
    $body = " FROM teams WHERE status='Normal' ORDER BY score DESC, penalty ASC";
    $result = DB::findAllWithCount($select, $body, 1, 10);
    $data = $result['data'];
    $i = 1;
    echo "<table class='table table-condensed'><tr><th>Rank</th><th>Name</th><th>Score</th></tr>";
    foreach ($data as $row) {
        echo "<tr><td>" . $i++ . "</td><td><a href='" . SITE_URL . "/teams/$row[teamname]'>" . $row['teamname'] . "</a></td><td>" . $row['score'] . "</td></tr>";
    }
    echo "</table>";
}

function pagination($noofpages, $url, $page, $maxcontent) {
    if ($noofpages > 1) {
        if ($page - ($maxcontent / 2) > 0)
            $start = $page - 5;
        else
            $start = 1;
        if ($noofpages >= $start + $maxcontent)
            $end = $start + $maxcontent;
        else
            $end = $noofpages;
        ?>
        <div align='center'>
            <ul class ="pagination">        
                <?php if ($page > 1) { ?>
                    <li><a href="<?php echo $url . "&page=1"; ?>">First</a></li>
                    <li><a href="<?php echo $url . "&page=" . ($page - 1); ?>">Prev</a></li>
                    <?php
                }
                for ($i = $start; $i <= $end; $i++) {
                    ?>
                    <li <?php echo ($i == $page) ? ("class='disabled'") : (''); ?>><a href="<?php echo ($i != $page) ? ($url . "&page=" . $i) : ("#"); ?>"><?php echo $i; ?></a></li>
                    <?php
                }
                if ($page < $noofpages) {
                    ?>
                    <li><a href="<?php echo $url . "&page=" . ($page + 1); ?>">Next</a></li>
                    <li><a href="<?php echo $url . "&page=" . $noofpages; ?>">Last</a></li>
                <?php } ?>
            </ul>
        </div>
        <?php
    }
}

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

function getrankings($code) {
    $query = "select * from contest where code = '$code'";
    $contest = DB::findOneFromQuery($query);
    $query = "select runs.tid as tid, teamname, problems.score, submittime as time, (select count(rid) from runs r where tid = runs.tid and pid = runs.pid and result != 'AC' and result is not NULL and submittime < runs.submittime) as penalty 
from runs, teams, problems, contest 
where 
teams.status = 'Normal' and runs.tid = teams.tid and problems.pid = runs.pid and
runs.pid in (select pid from problems where pgroup ='$code') and result = 'AC' group by runs.tid, runs.pid";
    $res = DB::findAllFromQuery($query);
    foreach ($res as $row) {
        if (isset($rank[$row['tid']])) {
            $rank[$row['tid']]['time'] += ($row['time'] - $contest['starttime']);
            $rank[$row['tid']]['score'] += $row['score'];
            $rank[$row['tid']]['penalty'] += $row['penalty'];
            $rank[$row['tid']]['solved'] ++;
        } else {
            $rank[$row['tid']]['teamname'] = $row['teamname'];
            $rank[$row['tid']]['time'] = ($row['time'] - $contest['starttime']);
            $rank[$row['tid']]['score'] = $row['score'];
            $rank[$row['tid']]['penalty'] = $row['penalty'];
            $rank[$row['tid']]['solved'] = 1;
        }
    }
    usort($rank, "cmp");
    return $rank;
}
?>
