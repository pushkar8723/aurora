<?php
include_once '../files/Leaderboard.php';
include_once '../functions.php';

$query = "SELECT value FROM admin WHERE variable = 'mode'";
$judge = DB::findOneFromQuery($query);
if($judge['value'] == 'Active') {
	//assuming that the current contest is the last one added in the contest table.
	$query = "SELECT code FROM contest where id = (SELECT max(id) FROM contest) ";
	$result = DB::findOneFromQuery($query);
	$contestCode = $result['code'];
	Leaderboard::updateContestRankings($contestCode);
	echo 'Updated at '.time().PHP_EOL;
}
