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
    if(isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == "Admin"){
        $query = "select time from clar where reply is NULL and access='public' and time > ".$_SESSION['team']['time'];
        $result = DB::findAllFromQuery($query);
        if($result)
            if($i != 0)
                $msg .= ",{'title':'$row[title]', 'msg':'$row[msg]'}";
            else
                $msg .= "{'title':'Clarification', 'msg':'New Clarification. Pending Reply!'}";
    }
    $msg .= "]}";
    if(isset($_GET['updatetime'])){
        echo "done";
        $_SESSION['team']['time'] = time();
    }
    echo $msg;
?>
