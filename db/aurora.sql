-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 02, 2013 at 12:06 AM
-- Server version: 5.5.31-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `aurora_codejam`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `variable` tinytext,
  `value` longtext
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
  `query` text,
  `reply` text,
  `access` tinytext,
  `createtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contest`
--

CREATE TABLE IF NOT EXISTS `contest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` tinytext NOT NULL,
  `name` text NOT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `announcement` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `groupname` tinytext,
  `statusx` int(11) DEFAULT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `time` int(11) NOT NULL,
  `ip` tinytext,
  `tid` text DEFAULT NULL,
  `request` text,
  PRIMARY KEY (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `problems`
--

CREATE TABLE IF NOT EXISTS `problems` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `code` tinytext,
  `name` tinytext,
  `type` tinytext,
  `contest` tinytext NOT NULL,
  `status` tinytext,
  `pgroup` tinytext,
  `statement` longtext,
  `image` longblob,
  `imgext` tinytext,
  `input` longtext,
  `output` longtext,
  `timelimit` float DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `languages` tinytext,
  `options` tinytext,
  `displayio` tinyint(1) NOT NULL DEFAULT '0',
  `maxfilesize` int(11) NOT NULL DEFAULT '50000',
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

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
  `language` tinytext,
  `name` tinytext,
  `code` longtext,
  `time` tinytext,
  `result` tinytext,
  `error` text,
  `access` tinytext,
  `submittime` int(11) DEFAULT NULL,
  `output` longtext,
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=399 ;

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

--
-- Dumping data for table `runs`
--

INSERT INTO `runs` (`rid`, `pid`, `tid`, `language`, `name`, `code`, `time`, `result`, `error`, `access`, `submittime`, `output`) VALUES
(1, 1, 1, 'C', 'code', '#include<stdio.h>\r\nint main(){\n	int i;\n	while(scanf("%d",&i)!=EOF)\r\n		printf("%d\\n",i*i);\n	return 0;\n	}\r\n', '', NULL, '', 'public', NULL, ''),
(2, 1, 1, 'C++', 'code', '#include<iostream>\r\nusing namespace std;\r\nint main(){\n	int i;\n	while(cin>>i)\r\n		cout<<(i*i)<<endl;\n	return 0;\n	}\r\n', '', NULL, '', 'public', NULL, ''),
(3, 1, 1, 'C#', 'code', 'using System;\r\nclass Program {\r\n  static void Main(string[] args){\r\n    int i; string s;\r\n    while ((s = Console.ReadLine()) != null){\r\n      i = Int16.Parse(s);\r\n      Console.WriteLine(i * i);\r\n      }\r\n    }\r\n  }', '', NULL, '', 'public', NULL, ''),
(4, 1, 1, 'Java', 'code', 'import java.io.*;\r\npublic class code {\r\n	public static void main(String args[])throws IOException{\r\n		BufferedReader in = new BufferedReader(new InputStreamReader(System.in));\r\n		int n;\r\n		String str;\r\n		while((str=in.readLine())!=null){\r\n			n = Integer.parseInt(str);\r\n			n = n*n;\r\n			System.out.println(n);\r\n			} // while\r\n		}\r\n	}', '', NULL, '', 'public', NULL, ''),
(5, 1, 1, 'JavaScript', 'code', 'importPackage(java.io);\r\nimportPackage(java.lang);\r\nvar reader = new BufferedReader( new InputStreamReader(System[''in'']) );\r\nwhile (true){\r\n    var line = reader.readLine();\r\n    if (line==null) break;\r\n    else {\r\n        i = parseInt(line);\r\n        System.out.println((i*i)+'''');\r\n        }\r\n    }', '', NULL, '', 'public', NULL, ''),
(6, 1, 1, 'Pascal', 'code', 'program code;\nvar\n	i: integer;\nbegin\n	while not eof do begin\n		readln(i);\n		writeln(i*i);\n	end\nend. { code }', '', NULL, '', 'public', NULL, ''),
(7, 1, 1, 'Perl', 'code', 'while($n = <STDIN>){\r\n	print ($n*$n);\r\n	print "\\n";\r\n	}', '', NULL, '', 'public', NULL, ''),
(8, 1, 1, 'PHP', 'code', '<?php\r\n$stdin = fopen("php://stdin","r");\r\nwhile($i = trim(fgets($stdin))){\r\n	echo ($i*$i)."\\n";\r\n	}\r\nfclose($stdin);\r\n?>', '', NULL, '', 'public', NULL, ''),
(9, 1, 1, 'Python', 'code', 'try:\n	while 1:\n		i = int(raw_input())\n		print i*i\nexcept:\n	pass\n', '', NULL, '', 'public', NULL, ''),
(10, 1, 1, 'Ruby', 'code', 'while n = gets\n	n = n.chomp.to_i\n	puts (n*n).to_s\nend', '', NULL, '', 'public', NULL, '');
-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE IF NOT EXISTS `teams` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `teamname` tinytext,
  `teamname2` tinytext,
  `pass` tinytext,
  `status` tinytext,
  `score` int(11) DEFAULT NULL,
  `penalty` bigint(20) DEFAULT NULL,
  `name1` tinytext,
  `roll1` tinytext,
  `branch1` tinytext,
  `email1` tinytext,
  `phone1` tinytext,
  `name2` tinytext,
  `roll2` tinytext,
  `branch2` tinytext,
  `email2` tinytext,
  `phone2` tinytext,
  `name3` tinytext,
  `roll3` tinytext,
  `branch3` tinytext,
  `email3` tinytext,
  `phone3` tinytext,
  `platform` text,
  `ip` text,
  `session` tinytext,
  `gid` int(11) NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=196 ;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`tid`, `teamname`, `teamname2`, `pass`, `status`, `score`, `penalty`, `name1`, `roll1`, `branch1`, `email1`, `phone1`, `name2`, `roll2`, `branch2`, `email2`, `phone2`, `name3`, `roll3`, `branch3`, `email3`, `phone3`, `platform`, `ip`, `session`, `gid`) VALUES
(1, 'judge', NULL, '99c8ef576f385bc322564d5694df6fc2', 'Admin', '', '', '', '', '', 'pushkar8723@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '[]', '', 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
