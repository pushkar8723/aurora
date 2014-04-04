<?php
require 'config.php';
if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
    ?>
    <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
            <link type="text/css" rel="stylesheet" href="<?php echo CSS_URL; ?>/bootstrap.css" media="screen" />
            <link type="text/css" rel="stylesheet" href="<?php echo CSS_URL; ?>/bootstrap-responsive.css" media="screen" />
            <link type="text/css" rel="stylesheet" href="<?php echo CSS_URL; ?>/style.css" media="screen" />
            <script type="text/javascript" src="<?php echo JS_URL; ?>/jquery.js"></script>
            <script type="text/javascript" src="<?php echo JS_URL; ?>/bootstrap.js"></script> 
            <script type="text/javascript" src="<?php echo JS_URL; ?>/plugin.js"></script>
        </head>
        <body>
            <div class='container'>
                <?php
                $pgroup = addslashes($_GET['code']);
                $res = DB::findAllFromQuery("Select * from problems where pgroup = '$pgroup'");
                $i=1;
                foreach ($res as $result) {
                    echo "<div style='page-break-after: ".(($i++ == count($res))?("auto"):("always"))."; text-align: justify;'><center><h3>$result[name]</h3></center>";
                    $statement = stripslashes($result["statement"]);
                    $statement = preg_replace("/\n/", "<br>", $statement);
                    $statement = preg_replace("/<image \/>/", "<img src='data:image/jpeg;base64,$result[image]' />", $statement);
                    echo " $statement<br/>
                   <b>Time Limit :</b> $result[timelimit] Second(s)<br/><b>Score :</b> $result[score] Point(s)<br/><b>Input File Limit :</b> $result[maxfilesize] Bytes<br/><b>Languages Allowed :</b> $result[languages]</div>";
                }
                ?>
            </div>
        </body>
    </html>
    <?php
} else {
    $_SESSION['msg'] = "Access Denied: You need to be administrator to access that page.";
    redirectTo(SITE_URL);
}
?>
