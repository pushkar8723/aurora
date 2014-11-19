<?php
include_once '../functions.php';

//change the status of all the problems from disabled to active.
function activateContestProblems($contestID) {
	DB::query("UPDATE problems SET status = 'Active' where pgroup = '$contestID'");
	DB::query("UPDATE problems SET total = 0 where pgroup = '$contestID'");
	DB::query("UPDATE problems SET solved = 0 where pgroup = '$contestID'");
	echo 'Activated all problems.<br>';
}


function getArrayOfRIDsToBeDeleted($contestID) {
	$query = "SELECT rid from runs WHERE pid IN(SELECT pid FROM problems WHERE pgroup='$contestID')";
	return DB::findAllFromQuery($query);	
}

function deleteRIDsFromRunsAndSubsCode($ridArray) {
	foreach($ridArray as $rid) {
		DB::query('delete from runs where rid = '.$rid['rid']);
		DB::query('delete from subs_code where rid = '.$rid['rid']);
		echo 'Deleted RID : '.$rid['rid'].' <br>';
	}
}

$contestID = $_GET['code'];
$query = "SELECT starttime FROM contest WHERE code = '$contestID'";
$result = DB::findOneFromQuery($query);
$currentTime = time();
if($currentTime > $result['starttime'] )
    die('Access denied');
$query = "UPDATE admin set value='$contestID' where variable='currentContest'";
DB::query($query);
echo 'Current Contest set to $contestID';
activateContestProblems($contestID);
deleteRIDsFromRunsAndSubsCode(getArrayOfRIDsToBeDeleted($contestID));
echo 'Ready !!!';
