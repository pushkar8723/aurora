<?php
if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
    echo "<div class=\"page-header text-center\"><h1>Groups Settings</h1></div>
        <form method='post' action='".SITE_URL."/process.php'>
        <div class='col-lg-3'><input class='form-control' type='text' name='groupname' /></div>
        <input class='btn btn-default' type='submit' name='addgroup' value='Add Group' />
        </form>";
    $query = "select * from groups";
    $result = DB::findAllFromQuery($query);
    echo "<h3 class='text-center'>List of Groups</h3>
        <table class='table table-hover'><thead><tr><th>Name</th><th>Actions</th></tr></thead>";
    foreach ($result as $row){
        echo "<tr>
            <td>
            <form role='form' method='post' action='".SITE_URL."/process.php'>
            <input type='hidden' name='gid' value='$row[gid]' />
            <div class='col-lg-6'><input class='form-control' type='text' name='groupname' value='$row[groupname]' /></div>
            <input class='btn btn-primary' type='submit' name='updategroup' value='Update Group' />
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
