<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//var_dump(doCompetitionCheck());
$date = new DateTime('08-08-2016 00:00:00');
$startdate = $date->getTimestamp();
echo $startdate;
var_dump(Leaderboard::getStaticRankTableInJSON('test-1'));
?>