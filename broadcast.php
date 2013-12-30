<?php
    require 'config.php';
    $query = "select * from broadcast where deleted=0 and createdOn >= '".date('Y-m-d H:i:s', $_SESSION['team']['time'])."'";
    $result = DB::findAllFromQuery($query);
    $msg = "{\"broadcast\":[";
    $i = 0;
    foreach($result as $row){
        if($i != 0)
            $msg .= ",";
        $i++;
        $row['msg'] = preg_replace("/\r\n|\r|\n/",'<br/>',$row['msg']);
        $msg .= "{'title':'$row[title]', 'msg':'$row[msg]'}";
    }
    $msg .= "]}";
    $_SESSION['team']['time'] = time();
    echo $msg;
?>
