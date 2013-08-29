-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 29, 2013 at 11:46 PM
-- Server version: 5.5.31-MariaDB-log
-- PHP Version: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `aurora_main`
--
CREATE DATABASE IF NOT EXISTS `aurora_main` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `aurora_main`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `variable` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`variable`, `value`) VALUES
('lastjudge', '0'),
('mode', 'Disabled'),
('penalty', '20'),
('notice', 'Aurora Online Judge\r\nWelcome to Aurora Online Judge'),
('endtime', '0'),
('port', '8723'),
('ip', '127.0.0.1'),
('test', 'test');
-- --------------------------------------------------------

--
-- Table structure for table `clar`
--

CREATE TABLE IF NOT EXISTS `clar` (
  `time` int(11) NOT NULL,
  `tid` int(11) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `query` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `reply` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `access` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `createtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contest`
--

CREATE TABLE IF NOT EXISTS `contest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `announcement` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `groupname` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `statusx` int(11) DEFAULT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `time` int(11) NOT NULL,
  `ip` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `tid` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `request` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `problems`
--

CREATE TABLE IF NOT EXISTS `problems` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `code` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `type` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `contest` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `pgroup` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `statement` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `image` longblob,
  `imgext` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `input` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `output` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `timelimit` float DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `languages` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `options` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `displayio` tinyint(1) NOT NULL DEFAULT '0',
  `maxfilesize` int(11) NOT NULL DEFAULT '50000',
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `problems`
--

INSERT INTO `problems` (`pid`, `code`, `name`, `type`, `contest`, `status`, `pgroup`, `statement`, `image`, `imgext`, `input`, `output`, `timelimit`, `score`, `languages`, `options`, `displayio`, `maxfilesize`) VALUES
(1, 'TEST', 'Squares', 'Ad-Hoc', 'practice', 'Active', 'Test', 'WAP to output the square of an integer.\r\nInput : Read until the end of file. Each line contains a single positive integer less than or equal to 10.\r\nOutput : Output the square of the integer, one in each line.\r\n\r\n<b>SAMPLE INPUT</b>\r\n<code>\r\n1\r\n2\r\n3\r\n5\r\n</code>\r\n\r\n<b>SAMPLE OUTPUT </b>\r\n<code>\r\n1\r\n4\r\n9\r\n25\r\n</code>', NULL, NULL, '1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n', '1\n4\n9\n16\n25\n36\n49\n64\n81\n100\n', 0.5, 0, 'Brain,C,C++,C#,Java,JavaScript,Pascal,Perl,PHP,Python,Ruby,Text', '', 1, 50000);


-- --------------------------------------------------------

--
-- Table structure for table `runs`
--

CREATE TABLE IF NOT EXISTS `runs` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `tid` int(11) DEFAULT NULL,
  `language` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `time` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `result` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `access` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `submittime` int(11) DEFAULT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10111 ;

--
-- Dumping data for table `runs`
--

INSERT INTO `runs` (`rid`, `pid`, `tid`, `language`, `time`, `result`, `access`, `submittime`) VALUES
(1, 1, 1, 'C', '', '', 'public', NULL),
(2, 1, 1, 'C++', '', '', 'public', NULL),
(3, 1, 1, 'C#', '', '', 'public', NULL),
(4, 1, 1, 'Java', '', '', 'public', NULL),
(5, 1, 1, 'JavaScript', '', '', 'public', NULL),
(6, 1, 1, 'Pascal', '', '', 'public', NULL),
(7, 1, 1, 'Perl', '', '', 'public', NULL),
(8, 1, 1, 'PHP', '', '', 'public', NULL),
(9, 1, 1, 'Python', '', '', 'public', NULL),
(10, 1, 1, 'Ruby', '', '', 'public', NULL);

--
-- Triggers `runs`
--
DROP TRIGGER IF EXISTS `scoreupdate`;
DELIMITER //
CREATE TRIGGER `scoreupdate` AFTER UPDATE ON `runs`
 FOR EACH ROW begin
DECLARE done INT DEFAULT FALSE;
DECLARE v_rid, v_submittime, v_incorrect, v_pen, v_score, recpid int(11);
DECLARE v_sco int DEFAULT 0;
DECLARE v_penalty bigint DEFAULT 0;
DECLARE cur1 CURSOR FOR SELECT distinct(runs.pid) as pid,problems.score as score FROM runs,problems WHERE runs.tid= OLD.tid and runs.result='AC' and runs.pid=problems.pid and problems.status!='Deleted' and runs.access!='deleted' and problems.contest = 'contest';
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
OPEN cur1;
read_loop: LOOP
	FETCH cur1 INTO recpid, v_score;
    	IF done THEN
      		LEAVE read_loop;
    	END IF;
	SELECT rid,submittime into v_rid, v_submittime FROM runs WHERE result='AC' and tid=OLD.tid and pid=recpid and access!='deleted' ORDER BY rid ASC LIMIT 0,1;
	SELECT count(*) into v_incorrect FROM runs WHERE result!='AC' and access!='deleted' and rid<v_rid and tid=OLD.tid and pid=recpid;
	SELECT value into v_pen from admin where variable = 'penalty';
	SELECT (v_sco + v_score) into v_sco;
	SELECT (v_penalty + v_submittime + v_incorrect*v_pen*60) into v_penalty;
end loop;
UPDATE teams SET score = v_sco, penalty=v_penalty where tid=OLD.tid;
CLOSE cur1;
end
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `subs_code`
--

CREATE TABLE IF NOT EXISTS `subs_code` (
  `rid` int(11) NOT NULL DEFAULT '0',
  `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `code` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `error` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `output` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subs_code`
--

INSERT INTO `subs_code` (`rid`, `name`, `code`, `error`, `output`) VALUES
(1, 'code', '#include<stdio.h>\r\nint main(){\r\n	int i;\r\n	while(scanf("%d", &i) != EOF)\r\n		printf("%d\\n", i*i);\r\n	return 0;\r\n	}\r\n', '', ''),
(2, 'code', '#include<iostream>\r\nusing namespace std;\r\nint main(){\r\n	int i;\r\n	while(cin>>i)\r\n		cout<<(i*i)<<endl;\r\n	return 0;\r\n	}\r\n', '', ''),
(3, 'code', 'using System;\r\nclass Program {\r\n  static void Main(string[] args){\r\n    int i; string s;\r\n    while ((s = Console.ReadLine()) != null){\r\n      i = Int16.Parse(s);\r\n      Console.WriteLine(i * i);\r\n      }\r\n    }\r\n  }', '', ''),
(4, 'Main', 'import java.io.*;\npublic class Main {\n	public static void main(String args[])throws IOException{\n		BufferedReader in = new BufferedReader(new InputStreamReader(System.in));\n		int n;\n		String str;\n		while((str=in.readLine())!=null){\n			n = Integer.parseInt(str);\n			n = n*n;\n			System.out.println(n);\n			} // while\n		}\n	}', '', ''),
(5, 'code', 'importPackage(java.io);\r\nimportPackage(java.lang);\r\nvar reader = new BufferedReader( new InputStreamReader(System[''in'']) );\r\nwhile (true){\r\n    var line = reader.readLine();\r\n    if (line==null) break;\r\n    else {\r\n        i = parseInt(line);\r\n        System.out.println((i*i)+'''');\r\n        }\r\n    }', '', ''),
(6, 'code', 'program code;\nvar\n	i: integer;\nbegin\n	while not eof do begin\n		readln(i);\n		writeln(i*i);\n	end\nend. { code }', '', ''),
(7, 'code', 'while($n = <STDIN>){\r\n	print ($n*$n);\r\n	print "\\n";\r\n	}', '', ''),
(8, 'code', '<?php\r\n$stdin = fopen("php://stdin","r");\r\nwhile($i = trim(fgets($stdin))){\r\n	echo ($i*$i)."\\n";\r\n	}\r\nfclose($stdin);\r\n?>', '', ''),
(9, 'code', 'try:\n	while 1:\n		i = int(raw_input())\n		print i*i\nexcept:\n	pass\n', '', ''),
(10, 'code', 'while n = gets\n	n = n.chomp.to_i\n	puts (n*n).to_s\nend', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE IF NOT EXISTS `teams` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `teamname` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `teamname2` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `pass` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `status` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `score` int(11) DEFAULT NULL,
  `penalty` bigint(20) DEFAULT NULL,
  `name1` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `roll1` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `branch1` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `email1` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `phone1` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `name2` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `roll2` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `branch2` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `email2` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `phone2` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `name3` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `roll3` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `branch3` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `email3` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `phone3` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `platform` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `ip` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `session` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `gid` int(11) NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=474 ;


--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`tid`, `teamname`, `teamname2`, `pass`, `status`, `score`, `penalty`, `name1`, `roll1`, `branch1`, `email1`, `phone1`, `name2`, `roll2`, `branch2`, `email2`, `phone2`, `name3`, `roll3`, `branch3`, `email3`, `phone3`, `platform`, `ip`, `session`, `gid`) VALUES
(1, 'judge', NULL, '99c8ef576f385bc322564d5694df6fc2', 'Admin', '', '', '', '', '', 'pushkar8723@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '[]', '', 2);
