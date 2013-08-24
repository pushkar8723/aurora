<?php
if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
    echo "<h1>Groups Settings</h1>
        <form method='post' action='".SITE_URL."/process.php'>
        <input type='text' name='groupname' />
        <input class='btn btn-primary' style='margin-top: -10px;' type='submit' name='addgroup' value='Add Group' />
        </form>";
    $query = "select * from groups";
    $result = DB::findAllFromQuery($query);
    echo "<h3>List of Groups</h3>
        <table class='table table-hover'><tr><th>Name</th><th>Option</th></tr>";
    foreach ($result as $row){
        echo "<tr>
            <td>
            <form method='post' action='".SITE_URL."/process.php'>
            <input type='hidden' name='gid' value='$row[gid]' />
            <input type='text' name='groupname' value='$row[groupname]' />
            <input class='btn btn-primary' style='margin-top: -10px;' type='submit' name='updategroup' value='Update Group' />
            </form>
            </td>
            <td>
            <form method='post' action='".SITE_URL."/process.php'>
            <input type='hidden' name='gid' value='$row[gid]' />
            <input class='btn btn-danger' type='submit' name='deletegroup' value='Delete Group' />
            </form>
            </td></tr>";        
    }
    echo "</table>";
} else {
    $_SESSION['msg'] = "Access Denied: You need to be administrator to access that page.";
    redirectTo(SITE_URL);
}
?>
