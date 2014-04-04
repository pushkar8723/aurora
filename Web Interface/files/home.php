<?php 
if(isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin'){
    echo "<a class='btn btn-primary pull-right' style='margin-top: 10px;' href='".SITE_URL."/adminjudge'><i class='glyphicon glyphicon-edit'></i> Edit</a>";
}
echo "<center><h1>Notice</h1></center>";
$query = "select value from admin where variable='notice'";
$result = DB::findOneFromQuery($query);
$data = $result['value'];
$data = str_replace("\r", "", $data);
$data = preg_replace("/\n\n\n*/", "\n\n", $data);
$data = preg_replace("/[\s\n]*$/", "", $data);
$data = explode("\n\n", $data);
foreach ($data as $x) {
    $y = explode("\n", $x);
    if (!isset($y[0]))
        continue;
    if (isset($y[0][0]) and $y[0][0] == "~" and $_SESSION["status"] != "Admin")
        continue;
    if (isset($y[0][0]) and $y[0][0] == "~")
        $y[0] = substr($y[0], 1);
    echo "<br><table class='table table-striped'><tr><th>" . stripslashes($y[0]) . "</th></tr><tr><td style='text-align: justify'><ul>";
    for ($i = 1; $i < count($y); $i++)
        echo "<li>" . stripslashes($y[$i]) . "</li>";
    echo "</ul></td></tr></table>";
}
?>