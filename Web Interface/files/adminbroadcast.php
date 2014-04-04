<?php
if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
    ?>
<h1>Broadcast <small>(Works only during live contest)</small></h1>
<h3>Add broadcast msg</h3>
<form class='form-horizontal' action='<?php echo SITE_URL; ?>/process.php' method='post'>
    <div class='col-md-12'>
        <div class='form-group'>
            <label class='control-label col-lg-2' for='btitle'>Title</label>
            <div class='col-md-4'>
                <input class='form-control' id='btitle' name='btitle' />
            </div>
        </div>
    </div>
    <div class='col-md-12'>
        <div class='form-group'>
            <label class='control-label col-lg-2' for='bmsg'>Message</label>
            <div class='col-md-4'>
                <textarea class='form-control' style='width: 500px; height: 200px;' name='bmsg' id='bmsg'></textarea>
            </div>
        </div>
    </div>
    <div class='col-md-12'>
        <div class='form-group'>
            <label class='control-label col-lg-2'></label>
            <div class='col-md-4'>
                <input type='submit' class='btn btn-primary' name='addbmsg' value='Send' />
            </div>
        </div>
    </div>
</form>
<h4>Messages</h4>
<table class='table table-hover'>
    <tr><th>Title</th><th>Message</th><th>Delete</th></tr>    
<?php
    $query = "select * from broadcast where deleted = 0";
    $result = DB::findAllFromQuery($query);
    foreach($result as $row){
        echo "<tr><td>$row[title]</td><td>$row[msg]</td><td>"
                . "<form action='".SITE_URL."/process.php' method='post'>"
                . "<input type='hidden' name='id' value='$row[id]' />"
                . "<input type='submit' name='delbmsg' class='btn btn-danger' value='Delete'/>"
                . "</form>"
                . "</td></tr>";
    }
    echo "</table>";
} else {
    $_SESSION['msg'] = "Access Denied: You need to be administrator to access that page.";
    redirectTo(SITE_URL);
}
?>