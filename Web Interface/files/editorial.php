<?php
if ($judge['value'] != "Lockdown" || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')) {
    if (isset($_GET['code'])) {
        $_GET['code'] = addslashes($_GET['code']);
        if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == "Admin") {
            $query = "select * from problems where code = '$_GET[code]'";
            $result = DB::findOneFromQuery($query);
            if($result != NULL)
            echo "<a class='btn btn-primary pull-right' style='margin-top: 10px;' href='" . SITE_URL . "/admineditorial/$_GET[code]'><i class='glyphicon glyphicon-edit'></i> Edit</a>";
        } else {
            $query = "select * from problems where code = '$_GET[code]' and status != 'Deleted'";
            $result = DB::findOneFromQuery($query);
        }
        if ($result == NULL) {
            echo "<br/><br/><br/><div style='padding: 10px;'><h1>Problem not Found :(</h1>The problem you are looking for doesn't exsits.</div><br/><br/><br/>";
        } else {
            $contentRes = DB::findOneFromQuery("select content from editorials where pid = " . $result['pid']);
            if($contentRes == NULL){
               echo "<br/><br/><br/><div style='padding: 10px;'><h1>Editorial not Found :(</h1>The editorial you are looking for doesn't exsits.</div><br/><br/><br/>";
            }else{
                $content = stripslashes($contentRes["content"]);
                echo "<div class='text-center page-header'><h1>$result[name] - Editorial</h1></div><div class='btn-group'><a class='btn btn-default' href='" . SITE_URL . "/problems/$result[code]'>Problem</a></div>
            <br/><br/>" . $content . "<br/>";
            }
        }
    } else {
        echo "<br/><br/><br/><div style='padding: 10px;'><h1>Problem not Found :(</h1>The problem you are looking for doesn't exsits.</div><br/><br/><br/>";
    }
} else {
    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Lockdown Mode :(</h1>This feature is now offline as Judge is in Lockdown mode.</div><br/><br/><br/>";
}
?>
