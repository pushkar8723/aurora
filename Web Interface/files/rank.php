<?php
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
    $query = "select value from admin where variable = 'penalty'";
    $admin = DB::findOneFromQuery($query);
    ?>
<center><?php if(isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == "Admin"){ ?><a class="pull-right btn btn-danger" href="<?php echo SITE_URL; ?>/process.php?freeze=<?php echo $contest['code']; ?>"><?php echo ($contest['ranktable'] == "")?"Freeze Board":"Refresh" ?></a><?php } ?><h1>Rankings - <?php echo $contest['name'] ?></h1></center>
    <?php
    if($contest['ranktable'] == ""){
        $rank = getrankings($contest['code']);
    } else {
        $rank = DB::findAllFromQuery("select * from $_GET[code]");
    }
    $i = 1;
    echo "<table class='table table-hover'><tr><th>Rank</th><th>Temaname</th><th>Time</th><th>Penalty</th><th>Score</th><th>Solved</th><th>Final Time</th></tr>";
    foreach ($rank as $val) {
        $finaltime = $val['time'] + $val['penalty'] * $admin['value'] * 60;
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
