<?php
/** @author: utk
 */
include_once '../functions.php';
include_once 'SSE_Util.php';

function liveContestRanking($contestCode) {
	$query = "SELECT ranktable FROM contest WHERE code = '$contestCode'";
	$table = '<table class="table table-striped table-bordered table-condensed">' ;
	$result = DB::findOneFromQuery($query);
	$rankTable = json_decode($result['ranktable'], true);
	$rank = 1;
	foreach ($rankTable as $row) {
		$table .= '<tr>';
		$table .= '<td align = "center">'.$rank.'</td><td align="center">'.$row['teamname'].'</td><td align="center">'.$row['score'].'</td>';
		$table .= '</tr>';
		if($rank >= 15)
			break;
		$rank ++;
		
	}
	$table .= '</table>';
	return $table;
}

$contestCode = 'CQM-3';
$printTable = liveContestRanking($contestCode);
//echo $printTable;
SSE_Util::sendMessageToClient($printTable);
