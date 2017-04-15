<?php
/** @author: utk
 */
include_once(dirname(__FILE__) . '/../functions.php');
include_once 'SSE_Util.php';

function liveContestRanking($contestCode, $limit) {
	$query = "SELECT ranktable FROM contest WHERE code = '$contestCode'";
	$table = '<table class="table table-hover">' ;
	$result = DB::findOneFromQuery($query);
	$rankTable = json_decode($result['ranktable'], true);
	$rank = 1;
	foreach ($rankTable as $row) {
		$table .= '<tr>';
		$table .= '<td align = "center">'.$rank.'</td><td align="center"><a href="'.SITE_URL.'/teams/'.$row['teamname'].'">'.$row['teamname'].'</a></td><td align="center">'.$row['score'].'</td>';
		$table .= '</tr>';
		if($rank >= $limit)
			break;
		$rank ++;
		
	}
    $table .= '</table>';
	return $table;
}
//SSE_Util::sendMessageToClient($printTable);
