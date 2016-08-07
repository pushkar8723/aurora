<?php
include_once(dirname(__FILE__) . '/../functions.php');

class Leaderboard {
	private static function cmp($a, $b) {
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
	
	public static function updateContestRankings($code) {
		$query = "select * from contest where code = '$code'";
		$contest = DB::findOneFromQuery($query);
		$query = "select runs.tid as tid, teamname, problems.score, submittime as time,
			(select count(rid) from runs r where tid = runs.tid and pid = runs.pid and result != 'AC'
			and result is not NULL and submittime < runs.submittime) as penalty
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
		usort($rank, "self::cmp");
		$rankTableJSON = json_encode($rank);
		DB::query("UPDATE contest SET ranktable = '$rankTableJSON' WHERE code = '$code'");
	}

	public static function getStaticRankTableInJSON($contestCode) {
		$query = "SELECT ranktable FROM contest WHERE code = '$contestCode'";
		return DB::findOneFromQuery($query);				
	}
}






