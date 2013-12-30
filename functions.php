<?php

/**
 * @author: Siddhartha Sahu <sh.siddhartha@gmail.com>
 * @license: GNU GPLv3
 * 
 */
function processDirPath($dir) {
  $dir = preg_replace('/\.\./', '', $dir);
  return preg_replace('/\//', '', $dir);
}

function getAllFilesfromDirectory($dir, $sort = true, $sortby = "name", $sortorder = SORT_ASC, $ignore = array()) {
  $files = array();
  if (!is_dir($dir))
    return array();
  $handle = opendir($dir);
  if ($handle) {
    $files = array();
    while (false !== ($entry = readdir($handle))) {
      if ($entry != "." && $entry != ".." && !in_array($entry, $ignore)) {
        $files[$entry] = filectime($dir . "/" . $entry);
      }
    }
    closedir($handle);
  }
  if (!$sort) {
    return array_keys($files);
  }
  else {
    if ($sortby == "date") {
      asort($files);
      if ($sortorder == SORT_DESC) {
        return array_reverse(array_keys($files));
      }
      return array_keys($files);
    }
    else if ($sortby == "name") {
      $files = array_keys($files);
      natsort($files);
      if ($sortorder == SORT_DESC) {
        return array_reverse(array_values($files));
      }
      return array_values($files);
    }
  }
  return false;
}

function getGeneralCoverImage($category) {
  foreach (getCoverNames() as $cover) {
    if (file_exists(GENERAL_PATH . "/" . $category . "/" . $cover)) {
      return GENERAL_URL . "/" . $category . "/" . $cover;
    }
  }
  return NOTFOUND_URL;
}

function getCoverNames() {
  global $EXTNS;
  $e = array();
  foreach ($EXTNS as $extn) {
    $e[] = "cover." . $extn;
  }
  return $e;
}

function saveOrderedImages($uid, $type, $category, $name) {
  $query = "select * from orders where deleted = 0 and fk_user_id = :uid and type=:type and category = :category and name = :name";
  $res = DB::findOneFromQuery($query, array(":uid" => $uid, ":type" => $type, ":category" => $category, ":name" => $name));
  if (!($res && count($res) > 0)) {
    return DB::insert("orders", array("fk_user_id" => $uid, "type" => $type, "category" => $category, "name" => $name));
  }
  return -1;
}

function getOrderedImages($uid) {
  $query = "select * from orders where deleted=0 and fk_user_id = $uid";
  $res = DB::findAllFromQuery($query);
  $img = array();
  foreach ($res as $value) {
    if ($value['type'] == "general") {
      $path = GENERAL_PATH;
      $url = GENERAL_URL;
    }
    else if ($value['type'] == "premium") {
      $path = PREMIUM_PATH;
      $url = PREMIUM_URL;
    }
    if (file_exists($path . "/" . $value['category'] . "/" . $value['name'])) {
      $value['url'] = $url . "/" . $value['category'] . "/" . $value['name'];
    }
    else {
      $value['url'] = NOTFOUND_URL;
    }
    $img[] = $value;
  }
  return $img;
}

function getOrderedImageUrls($uid) {
  $query = "select * from orders where deleted = 0 and fk_user_id = $uid";
  $res = DB::findAllFromQuery($query);
  $img = array();
  foreach ($res as $index => $value) {
    if ($value['type'] == "general") {
      $path = GENERAL_PATH;
      $url = GENERAL_URL;
    }
    else if ($value['type'] == "premium") {
      $path = PREMIUM_PATH;
      $url = PREMIUM_URL;
    }
    if (file_exists($path . "/" . $value['category'] . "/" . $value['name'])) {
      $img[$index] = $url . "/" . $value['category'] . "/" . $value['name'];
    }
    else {
      $img[$index] = NOTFOUND_URL;
    }
  }
  return $img;
}

function removeFromOrders($uid, $type, $category, $name) {
  return DB::update("orders", array("deleted" => 1), "fk_user_id = :uid and type=:type and category = :category and name = :name", array(":uid" => $uid, ":type" => $type, ":category" => $category, ":name" => $name));
}

function checkOrdered($img) {
  if (isset($_SESSION['loggedin'])) {
    $images = getOrderedImageUrls($_SESSION['user']['id']);
    if (in_array($img, $images))
      return true;
    return false;
  }
  return false;
}

function redirectTo($url, $exit = true) {
  header("Location:" . $url);
  if ($exit) {
    exit;
  }
}

function redirectAfter($url) {
  if (isset($_SESSION['RedirectUrl'])) {
    $url = $_SESSION['RedirectUrl'];
    unset($_SESSION['RedirectUrl']);
  }
  redirectTo($url);
}

function checkLogin() {
  if (!isset($_SESSION["loggedin"])) {
    $_SESSION['RedirectUrl'] = $_SERVER['REQUEST_URI'];
    header("Location:" . SITE_URL);
    exit;
  }
}

function getCacheNumber() {
  return DEBUG ? date("YmdHis") : date("YmdH");
}

function writeFile($filename, $data) {
  $file = fopen($filename, "a+");
  if ($file) {
    fputs($file, date("[Y-m-d H:i:s]\n") . $data
            . "\n======================================================================\n");
    fclose($file);
  }
}

function writeError($data) {
  writeFile(ERROR_LOG, $data);
}

function isValidEmail($email) {
  $pattern = "^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$";
  if (eregi($pattern, $email)) {
    return true;
  }
  return false;
}

function sendMail($subject, $body, $to, $from = "ACM BIT Mesra <team@bitmesra.acm.org>") {
  require_once MAIL_PATH;
  $headers = array('From' => $from,
      'To' => $to,
      'Subject' => $subject,
      'Date' => date("Y-m-d H:i:s") . " +0530",
      'Content-Type' => 'text/html',
      'charset' => 'UTF-8'
  );
  $smtp = Mail::factory('smtp', array('host' => MAIL_HOST,
              'port' => MAIL_PORT,
              'auth' => true,
              'username' => MAIL_USER,
              'password' => MAIL_PASS));
  $mail = $smtp->send($to, $headers, $body);
  $data = "Mail:\n" . print_r(array("To" => $to, "From" => $from, "Subject" => $subject, "Body" => $body), true);
  if (PEAR::isError($mail)) {
    writeError($data);
    return false;
  }
  writeFile(COMMENTS_LOG, $data);
  return true;
}

function prettyPrint($data, $withType = false) {
  echo "<pre>";
  $withType ? var_dump($data) : print_r($data);
  echo "</pre>";
}

function printPageNos($total) {
  if ($total > 1) {
    parse_str($_SERVER['QUERY_STRING'], $query_string);
    echo "<p class='pagenos'>Page: ";
    for ($i = 1; $i <= $total; $i++) {
      $query_string['page'] = $i;
      echo "<a style='text-decoration:none' href='?" . http_build_query($query_string) . "'>$i</a> ";
    }
    echo "</p>";
  }
}

function removeSlashes($data) {
  return str_replace("\\", "", $data);
}

function getSessionMessage($name) {
  if (isset($_SESSION[$name])) {
    $data = $_SESSION[$name];
    unset($_SESSION[$name]);
  }
  else {
    $data = "";
  }
  return $data;
}

class DB {

  public static $connection = null;

  public static function initialize() {
    if (self::$connection != null)
      return true;
    try {
      self::$connection = new PDO("mysql:dbname=" . SQL_DB . ";host=" . SQL_HOST . ";port=" . SQL_PORT . "", SQL_USER, SQL_PASS, array(
                  PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                  PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
              ));
      self::$connection->exec("SET CHARACTER SET utf8");
    }
    catch (PDOException $error) {
      self::$connection = null;
      writeError('DB Connection failed:\n' . $error->getMessage());
      die("Error creating database connection (error log)!");
      return false;
    }
    return true;
  }

  public static function closeConnection() {
    self::$connection = null;
    return true;
  }

  private static function handleError($e = null, $data = "") {
    if ($e != null) {
      $data .= "\nError: " . $e->getMessage() . "\n" . $e->getFile();
    }
    writeError("Query error:\n" . $data);
  }

  public static function query($query, $values = null) {
    if (!self::initialize())
      return false;
    try {
      if (is_array($values)) {
        $stmt = self::$connection->prepare($query);
        $stmt->execute($values);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
      else {
        return self::$connection->query($query);
      }
    }
    catch (PDOException $e) {
      self::handleError($e, $query);
      return false;
    }
  }

  public static function findAllWithCount($select, $body, $page, $limit) {
    if (!self::initialize())
      return false;
    $countselect = "SELECT count(*) as count ";
    $limitquery = " LIMIT " . ($page - 1) * $limit . "," . $limit;
    $query = $countselect . $body;
    $count = self::findOneFromQuery($query);
    $res['total'] = $count['count'];
    $res['noofpages'] = ceil($count['count'] * 1.0 / $limit);
    $query = $select . " " . $body . $limitquery;
    $res['data'] = self::findAllFromQuery($query);
    return $res;
  }

  public static function insert($table, $data) {
    if (!self::initialize())
      return false;
    $data['createdOn'] = date("Y-m-d H:i:s");
    $data['updatedOn'] = date("Y-m-d H:i:s");
    $keys = array();
    $values = array();
    foreach ($data as $key => $value) {
      $keys[] = $key;
      $values[] = self::$connection->quote($value);
    }
    $query = 'INSERT INTO ' . $table . ' (' . join(', ', $keys) . ') VALUES (' . join(', ', $values) . ')';
    try {
      return self::$connection->exec($query);
    }
    catch (PDOException $e) {
      self::handleError($e, $query);
      return false;
    }
  }

  public static function update($table, $data, $where, $values = null) {
    if (!self::initialize())
      return false;
    $data['updatedOn'] = date("Y-m-d H:i:s");
    $setters = array();
    foreach ($data as $key => $value) {
      $setters[] = $key . '=' . self::$connection->quote($value);
    }
    $query = 'UPDATE ' . $table . ' SET ' . join(', ', $setters) . ' WHERE ' . $where;
    try {
      if (is_array($values)) {
        $stmt = self::$connection->prepare($query);
        $stmt->execute($values);
      }
      else {
        $stmt = self::$connection->exec($query);
      }
      return $stmt;
    }
    catch (PDOException $e) {
      self::handleError($e, $query);
      return false;
    }
  }

  public static function delete($table, $where) {
    return self::update($table, array("deleted" => 1), $where);
  }

  public static function findAllFromQuery($query, $values = null) {
    if (!self::initialize())
      return false;
    try {
      if (is_array($values)) {
        $stmt = self::$connection->prepare($query);
        $stmt->execute($values);
      }
      else {
        $stmt = self::$connection->query($query);
      }
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
      self::handleError($e, $query);
      return false;
    }
  }

  public static function findOneFromQuery($query, $values = null) {
    if (!self::initialize())
      return false;
    try {
      if (is_array($values)) {
        $stmt = self::$connection->prepare($query);
        $stmt->execute($values);
      }
      else {
        $stmt = self::$connection->query($query);
      }
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
      self::handleError($e, $query);
      return false;
    }
  }

  public static function logActivity($activity, $message, $result) {
    if (!self::initialize()) {
      writeError("No connection error:\n" . $activity . "\n" . $message . "\n" . $result);
      return false;
    }
    $createTable = "CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity` text NOT NULL,
  `message` text NOT NULL,
  `result` text NOT NULL,
  `session` text NOT NULL,
  `createdOn` datetime NOT NULL,
  `updatedOn` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    self::query($createTable);
    $data = array("activity" => $activity, "message" => $message, "result" => $result);
    $table = "activity_log";
    $data['session'] = print_r($_SESSION, true);
    $data['updatedOn'] = date("Y-m-d H:i:s");
    $data['createdOn'] = date("Y-m-d H:i:s");
    return self::insert($table, $data);
  }

  public static function escape($value) {
    if (!self::initialize())
      return false;
    return self::$connection->quote($value);
  }

  public static function lastInsertId() {
    if (!self::initialize())
      return false;
    return self::$connection->lastInsertId();
  }

}

?>
