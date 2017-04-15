<?php
// Change the following parameters according to the instructions beside them
define("SITE_URL", "http://" . $_SERVER['HTTP_HOST'] . "/aurora");      // path to directory
define("SQL_USER", "aurora");           // Database username    
define("SQL_PASS", "aurora");           // Database password
define("SQL_DB", "aurora_main");        // Database name
define("SQL_HOST", "127.0.0.1");        // Database host
define("SQL_PORT", "3306");             // Database port
displayErrors(FALSE);                   // Display PHP errors or not.
date_default_timezone_set("Asia/Kolkata"); //Set your timezone, resolves most timer errors
// Language specific variables 
$brush = array("AWK" => "text", "Bash" =>"sh", "Brain" => "text","C" => "c", "C++" => "cpp", "C#" => "csharp", "Java" => "java", "Java", "JavaScript" => "js", "Pascal" => "pascal", "Perl" => "perl", "PHP" => "php", "Python" => "python", "Python3" => "python", "Ruby" => "ruby", "Text" => "text");
$cmmode = "'C': 'text/x-csrc', 'C++': 'text/x-c++src', 'C#': 'text/x-csharp', 'Java': 'text/x-java', 'JavaScript': 'javascript', 'Pascal': 'text/x-pascal', 'Perl': 'text/x-perl', 'PHP': 'text/x-php', 'Python': 'text/x-python', 'Python3': 'text/x-python', 'Ruby': 'text/x-ruby'";
$valtoname = array("AWK"=>"AWK", "Bash"=>"Bash", "Brain" => "Brainf**k", "C" => "C", "C++" => "C++", "Java" => "Java", "C#" => "C#", "JavaScript" => "JavaScript", "Pascal" => "Pascal", "Perl" => "Perl", "PHP" => "PHP", "Python" => "Python", "Python3" => "Python3", "Ruby" => "Ruby", "Text" => "Text"); 
$valtoext = array("AWK"=>"awk", "Bash"=>"sh", "Brain" => "b", "C" => "c", "C++" => "cpp", "Java" => "java", "C#" => "cs", "JavaScript" => "js", "Pascal" => "pas", "Perl" => "pl", "PHP" => "php", "Python" => "py", "Python3" => "py", "Ruby" => "rb", "Text" => "txt");

/* 
 * 
 * NO NEED TO CHANGE THE CODE BELOW
 * 
 * 
 */

session_start();
session_set_cookie_params (0, substr(SITE_URL, strlen("http://" . $_SERVER['HTTP_HOST'])));
ini_set("session.gc_maxlifetime", 86400);
function displayErrors($option = true) {
  if ($option) {
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', '1');
  }
  else {
    error_reporting(0);
    ini_set('display_errors', '0');
  }
}

define("DEBUG", true);

clearstatcache();

define("JS_URL", SITE_URL . "/js");
define("CSS_URL", SITE_URL . "/css");
define("IMAGE_URL", SITE_URL . "/img");
define("ACCOUNT_URL", SITE_URL . "/account");

define("PHPSCRIPTS_PATH", dirname(__FILE__) . "/php_scripts");

   

define("MAIL_PATH", "Mail.php");
//define("MAIL_USER", "");
//define("MAIL_PASS", "");
//define("MAIL_HOST", "");    // ssl://smtp.gmail.com
//define("MAIL_PORT", "");    // 465

define("ERROR_LOG", dirname(__FILE__) . "/errors.txt");

require_once 'functions.php';

