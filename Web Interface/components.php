<?php
include_once 'functions.php';
include_once 'files/Leaderboard.php';
include_once 'files/LiveContestRanking.php';
function loginbox() {
    if (!isset($_SESSION['loggedin'])) {
        ?>
        <div class="panel-heading text-center">
            <h3 class="panel-title">Login</h3>
        </div>
        <div class="panel-body text-center">

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
        </div>
        <?php
    } else {
        ?>
        <div class="panel-heading text-center">
            <h3 class="panel-title">Team</h3>
        </div>
        <div class="panel-body text-center">

            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Score</th>
                        <th>Overall Rank</th>
                    </tr>
                </thead>
                <?php
                $query = "SELECT count(*)+1 as rank, (select score from teams where tid = " . $_SESSION['team']['id'] . ") as sco FROM `teams` WHERE (score > (select score from teams where tid = " . $_SESSION['team']['id'] . ") and status = 'Normal') or (score = (select score from teams where tid = " . $_SESSION['team']['id'] . ") and penalty < (select penalty from teams where tid = " . $_SESSION['team']['id'] . ") and status='Normal') ";
                $res = DB::findOneFromQuery($query);
                echo "<tr><td><a href='" . SITE_URL . "/teams/" . $_SESSION['team']['name'] . "'>" . $_SESSION['team']['name'] . "</a></td><td>$res[sco]</td><td style='text-align: center'>$res[rank]</td></tr>";
                ?>
            </table>
        </div>
        <?php
    }
}

function mysubs() { ?>
    <div class="panel panel-default">
        <div class="panel-heading text-center">
            <h3 class="panel-title">My Submissions</h3>
        </div>
        <div class="panel-body text-center">
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th>RID</th>
                        <th>Problem</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <?php
                $query = "SELECT rid, (select name from problems where pid = runs.pid) as pname,(select code from problems where pid = runs.pid) as pcode, result FROM runs WHERE tid = " . $_SESSION['team']['id'] . " order by rid desc limit 0, 5";
                $res = DB::findAllFromQuery($query);
                $resAttr = array('AC' => 'success', 'RTE' => 'warning', 'WA' => 'danger', 'TLE' => 'warning', 'CE' => 'warning', 'DQ' => 'danger', 'PE' => 'info', '...' => 'default', '' => 'default'); //Defines label attributes
                foreach ($res as $row)
                    echo "<tr><td><a href='" . SITE_URL . "/viewsolution/$row[rid]'>$row[rid]</a></td><td><a href='" . SITE_URL . "/problems/$row[pcode]'>$row[pname]</a></td><td><span class='label label-".$resAttr[$row['result']]."'>$row[result]</span></td></tr>";
                ?>
            </table>
        </div>
    </div>
<?php }

function contest_status() {
    $status = Array();
    $query = "select * from admin where variable = 'endtime' or variable = 'starttime' or variable = 'mode' or variable='ip' or variable='port'";
    DB::query($query);
    $result = DB::findAllFromQuery($query);
    foreach ($result as $row) {
        $status[$row['variable']] = $row['value'];
    }
    ?>

    <table class='table'>
        <thead>
            <tr>
                <th class="text-center">Mode</th>
                <th class="text-center">Judge</th>
            </tr>
        </thead>
        <tr>
            <td>
                <div id="ajax-contest-mode">
                    <h4>
                        <?php
                        if ($status['mode'] == "Active" && $status['endtime'] < time())
                            echo "<span class=\"label label-danger\">Disabled</span>";
                        else {
                            $attributes = array(
                                "Active" => "success",
                                "Passive" => "primary",
                                "Disabled" => "default",
                                "Lockdown" => "danger"
                            );
                            echo "<span class=\"label label-" . $attributes[$status['mode']] . "\">" . $status['mode'] . "</span>";
                        }
                        ?>
                    </h4>
                </div>
            </td>
            <td>
                <h4>
                    <?php
                    $client = stream_socket_client($status['ip'] . ":" . $status['port'], $errno, $errorMessage);
                    if ($client === false)
                        echo "<span class=\"label label-danger\">Offline</span>";
                    else
                        echo "<span class=\"label label-success\">Online</span>";
                    fclose($client);
                    ?>
                </h4>
            </td>
        </tr>
    </table>
    <?php
    if ($status['mode'] == "Active") { ?>
        <div id='ajax-contest-time'></div>
        <script type='text/javascript'>
            var totaltime = <?php echo ($status['endtime'] - $status['starttime']); ?>;
            var countdown = <?php echo $status['endtime'] - time(); ?>;
            function step() {
                if (countdown > 0) {
                    var currentPercent = (totaltime-countdown)/totaltime*100;
                    var attribute = currentPercent < 70 ? "success" : currentPercent < 90 ? "warning" : "danger"; //0-70% green, 70-90% orange, 90-100% red
                    $("div#ajax-contest-time").html("<p>Time Elapsed</p><div class=\"progress\"><div class=\"progress-bar progress-bar-" + attribute + "\" role=\"progressbar\" aria-valuenow=\""+countdown+"\" aria-valuemin=\"0\" aria-valuemax=\""+totaltime+"\" style=\"width: "+ currentPercent +"%\"><span class=\"sr-only\">"+currentPercent+"% Elapsed</span></div></div>"+
                        "<h2><span class=\"label label-info\">" + parseInt(countdown / 3600) + ":" + parseInt((countdown / 60)) % 60 + ":" + (countdown % 60)+"</span></h2>"
                    );
                } else {
                    $("div#ajax-contest-time").html();
                    $("div#ajax-contest-mode").html("<h4><span class=\"label label-danger\">Disabled</span></h4>");
                    //TODO: (During code review) Better disabling mechanism
                }
                if (countdown >= 0)
                    countdown--;
                window.setTimeout("step();", 1000);
            }
            step();
        </script>
<?php
    }
}

function latestsubs() {?>
    <div class="panel panel-default">
        <div class="panel-heading text-center">
            <h3 class="panel-title">Latest Submissions</h3>
        </div>
        <div class="panel-body text-center">
            <table class='table table-hover'>
                <thead>
                <tr>
                    <th>Team</th>
                    <th>Problem</th>
                    <th>Result</th>
                </tr>
                </thead>
                <?php
                $query = "SELECT rid, (select teamname from teams where tid = runs.tid) as tname, (select name from problems where pid = runs.pid) as pname,(select code from problems where pid = runs.pid) as pcode, result FROM runs order by rid desc limit 0, 5";
                $res = DB::findAllFromQuery($query);
                $resAttr = array('AC' => 'success', 'RTE' => 'warning', 'WA' => 'danger', 'TLE' => 'warning', 'CE' => 'warning', 'DQ' => 'danger', 'PE' => 'info', '...' => 'default', '' => 'default'); //Defines label attributes
                foreach ($res as $row)
                    echo "<tr><td><a href='" . SITE_URL . "/teams/$row[tname]'>$row[tname]</a></td><td><a href='" . SITE_URL . "/problems/$row[pcode]'>$row[pname]</a></td><td><span class='label label-".$resAttr[$row['result']]."'>$row[result]</span></td></tr>";
                ?>
            </table>
        </div>
    </div>
<?php }

function rankings() {
    $select = "SELECT * ";
    $body = " FROM teams WHERE status='Normal' ORDER BY score DESC, penalty ASC";
    $result = DB::findAllWithCount($select, $body, 1, 10);
    $data = $result['data'];
    $i = 1;
    echo "<table class='table table-hover'><thead><tr><th>Rank</th><th>Name</th><th>Score</th></tr></thead>";
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

function getrankings($code) {
    $result =  Leaderboard::getStaticRankTableInJSON($code);
    return json_decode($result['ranktable'], true);	
}

function getCurrentContest() {
    $result = DB::findOneFromQuery("SELECT value from admin where variable = 'currentContest'");
    $contestCode = $result['value'];
    return $contestCode;
}

function getCurrentContestRanking(){
    $contestCode = getCurrentContest();
    $printTable = liveContestRanking($contestCode, 10);
    echo $printTable;
}

function errorMessageHTML($msg){
    return '<br /><div class="alert alert-danger" role="alert">'.$msg.'</div>';
}
?>
