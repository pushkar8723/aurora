<?php

if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
    if (isset($_GET['page']))
        $page = $_GET['page'];
    else
        $page = 1;
    $query = 'from clar';
    if (!isset($_GET['group']) || $_GET['group'] == '' || $_GET['group'] == "pending") {
        $tab = 'pending';
        $query .= " where (reply = '' or reply is NULL) and access != 'deleted'";
    } else if ($_GET['group'] == "replied") {
        $tab = 'replied';
        $query .= " where reply != '' and access != 'deleted'";
    } else {
        $tab = 'deleted';
        $query .= " where access = 'deleted'";
    }
    $query .= " order by time desc";
    echo "<h1>Clarifications</h1>
        <ul class='nav nav-tabs'>
            <li " . (($tab == 'pending') ? ("class='active'") : ("")) . "><a href='" . SITE_URL . "/adminclar&group=pending'>Pending</a></li>
            <li " . (($tab == 'replied') ? ("class='active'") : ("")) . "><a href='" . SITE_URL . "/adminclar&group=replied'>Replied</a></li>
            <li " . (($tab == 'deleted') ? ("class='active'") : ("")) . "><a href='" . SITE_URL . "/adminclar&group=deleted'>Deleted</a></li>
        </ul>";
    $data = DB::findAllWithCount("select * ", $query, $page, 10);
    $result = $data['data'];
    foreach ($result as $row) {
        $save = $row['reply'];
        $row['query'] = preg_replace("/\n/", "<br>", $row['query']);
        $row['reply'] = preg_replace("/\n/", "<br>", $row['reply']);
        $query = "select teamname from teams where tid='$row[tid]'";
        $team = DB::findOneFromQuery($query);
        if ($row['pid'] != '0') {
            $query = "select name, code from problems where pid='$row[pid]'";
            $prob = DB::findOneFromQuery($query);
            $prob['code'] = "problems/" . $prob['code'];
        } else {
            $prob['name'] = 'Feedback';
            $prob['code'] = 'contact';
        }
        echo "<a href='" . SITE_URL . "/teams/$team[teamname]'>$team[teamname]</a> (<a href='" . SITE_URL . "/$prob[code]'>$prob[name]</a>):<br/>
                <b>Q. $row[query]</b><br/>";
        if ($row['reply'] != "") {
            echo "A. $row[reply]<br/><br/>";
        }
        echo "<form class='form-inline' role='form' method='post' action='" . SITE_URL . "/process.php'>";
        echo "<div class='form-group'>Access:</div><div class='form-group'><select class='form-control'  name='access'><option value='public' " . (($row['access'] == "public") ? ("selected='selected' ") : ("")) . ">Public</option><option value='deleted' " . (($row['access'] == "deleted") ? ("selected='selected' ") : ("")) . ">Deleted</option></select></div><br/><br/>";
        echo "<input type='hidden' name='tid' value='$row[tid]' /><input type='hidden' name='pid' value='$row[pid]' /><input type='hidden' name='time' value='$row[time]' />
<textarea class='form-control' name='reply' style='width: 450px; height: 100px;'>$save</textarea><br/><br/>
<input type='submit' class='btn btn-primary' name='clarreply' value='Reply / Change Reply'/>
</form><hr/>";
    }
    pagination($data['noofpages'], SITE_URL . "/adminclar"."&group=$tab", $page, 10);
} else {
    $_SESSION['msg'] = "Access Denied: You need to be administrator to access that page.";
    redirectTo(SITE_URL);
}
?>
