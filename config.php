<?php
// Change the following parameters according to the instructions beside them
define("SITE_URL", "http://" . $_SERVER['HTTP_HOST'] . "/aurora");      // patth to directory
define("SQL_USER", "aurora");           // Database username    
define("SQL_PASS", "aurora");           // Database password
define("SQL_DB", "aurora_install");     // Database name  
define("SQL_HOST", "localhost");        // Database host
define("SQL_PORT", "3306");             // Database port
displayErrors(FALSE);                   // Display PHP errors or not.


/*
 * 
 * 
 * NO NEED TO CHANGE THE CODE BELOW
 * 
 * 
 */

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

session_start();
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

date_default_timezone_set("Asia/Kolkata");

require_once 'functions.php';
?>
