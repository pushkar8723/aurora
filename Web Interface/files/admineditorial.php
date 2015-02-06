<?php
if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateEditorial'])){
        $_POST['code'] = addslashes($_POST['code']);
        $_POST['statement'] = addslashes($_POST['statement']);
        $result = DB::findOneFromQuery("select pid from problems where code = '$_POST[code]'");
        if($result == NULL){
            $_SESSION['msg'] = "Problem with code $_POST[code] not found!";
            $cache['code'] = $_POST[code];
            $cache['statement'] = $_POST[statement];
        }
        $pid = $result['pid'];
        $result = DB::findOneFromQuery("select * from editorials where pid = '$pid'");
        if($result == NULL){
            $data['pid'] = $pid;
            $data['content'] = $_POST['statement'];
            DB::query("INSERT into editorials VALUES ( $pid, '$_POST[statement]' )");
        }else {
            DB::query("update editorials set content = '$_POST[statement]' where pid = '$pid'");
        }
//        $_SESSION['msg'] = 'Editorial Updated';
    }
    if (isset($_GET['code'])) {
        $_GET['code'] = addslashes($_GET['code']);
        $query = "select pid, code from problems where code = '$_GET[code]'";
        $res = DB::findOneFromQuery($query);
        if($res != NULL){
            $result = DB::findOneFromQuery("select content from editorials where pid = '$res[pid]'");
        }
        ?>

        <center><h1>Editorial Settings - <?php echo "<a class='btn btn-primary' href='" . SITE_URL . "/editorial/$_GET[code]'>$_GET[code] Editorial</a>"; ?></h1></center>
        <form class='form-horizontal' role='form' method='post' action='<?php echo SITE_URL . "/admineditorial/$_GET[code]'"; ?> enctype='multipart/form-data'>
            <div class='form-group'>
                <label class='control-label col-sm-2' for='code'>Code</label>
                <div class='col-sm-4'>
                    <input class='form-control' type='text' name='code' id='code' value='<?php echo $res['code']; ?>' required/>
                </div>
            </div>
            <div class='form-group'>
                <label class='control-label col-sm-2'for='statement'>Problem Editorial</label>
                <div class='col-sm-10'>
                    <textarea class='form-control' name='statement' style='width: 99%; height: 500px;'><?php echo $result['content']; ?></textarea>
                </div>
            </div>
            <div class='col-md-10'>
                <div class='form-group'>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4'></label>
                    <div class='col-lg-8'>
                        <input type='submit' class='btn btn-primary btn-large' value='Submit' name='updateEditorial' />
                    </div>
                </div>
            </div>
        </form><br/>
        <?php
    } else {
        echo "<br/><br/><br/><div style='padding: 10px;'><h1>No problem specified</h1></div><br/><br/><br/>";
    }
} else {
    $_SESSION['msg'] = "Access Denied: You need to be administrator to access that page.";
    redirectTo(SITE_URL);
}
?>
<script type="text/javascript" src="<?php echo JS_URL; ?>/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
    selector:'textarea',
    plugins: "link",
    forced_root_block : false,
});
</script>
