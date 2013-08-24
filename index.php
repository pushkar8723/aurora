<?php
require_once 'config.php';
require_once 'components.php';
$_SESSION['url'] = $_SERVER['REQUEST_URI']; // used by process.php to send to last visited page
$query = "select value from admin where variable='mode'";
$judge = DB::findOneFromQuery($query);
if ($judge['value'] == 'Lockdown' && isset($_SESSION['loggedin']) && $_SESSION['team']['status'] != 'Admin') {
    session_destroy();
    session_regenerate_id(true);
    session_start();
    $_SESSION['msg'] = "Judge is in Lockdown mode and so you have been logged out.";
    redirectTo(SITE_URL);
}
?>
<!DOCTYPE html>
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
        <script type="text/javascript">
            $(window).load(function() {
                if ($('#sidebar').height() < $('#mainbar').height())
                    $('#sidebar').height($('#mainbar').height());
            });
        </script>
        <title>Aurora</title>
        <link rel='shortcut icon' href='<?php echo SITE_URL; ?>/img/favicon.png' />
    </head>
    <body>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<?php echo SITE_URL; ?>">Aurora v2</a>
                    <div class="nav-collapse collapse">
                        <ul class="nav">
                            <li><a href="<?php echo SITE_URL; ?>/home">Home</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/faq">FAQ</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/problems">Problems</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/contests">Contests</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/rankings">Rankings</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/submissions">Submissions</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/contact">Contact Us</a></li>
                        </ul>
                        <?php if (isset($_SESSION['loggedin'])) { ?>
                            <ul class="nav pull-right">
                                <?php if ($_SESSION['team']['status'] == 'Admin') { ?>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            Admin
                                            <b class="caret"></b>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a href='<?php echo SITE_URL; ?>/adminjudge'>Judge Settings</a></li>
                                            <li><a href='<?php echo SITE_URL; ?>/adminproblem'>Problem Settings</a></li>
                                            <li><a href='<?php echo SITE_URL; ?>/admincontest'>Contest Settings</a></li>
                                            <li><a href='<?php echo SITE_URL; ?>/adminteam'>Team Settings</a></li>
                                            <li><a href='<?php echo SITE_URL; ?>/admingroup'>Group Settings</a></li>
                                            <li><a href='<?php echo SITE_URL; ?>/adminclar'>Clarifications</a></li>
                                            <li><a href='<?php echo SITE_URL; ?>/adminlog'>Request Logs</a></li>                                            
                                        </ul>
                                    </li>                              
                                <?php } ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        Account
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href='<?php echo SITE_URL; ?>/edit'>Account Settings</a></li>
                                        <li><a href='<?php echo SITE_URL; ?>/process.php?logout'>Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div> 
        <div class="container bodycont">
            <div class='row'>
                <div class='span9' id='mainbar'>
                    <?php if (isset($_SESSION['msg'])) { ?>
                        <div class="alert alert-error" style="margin-top: 20px;">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <center><?php
                                echo $_SESSION['msg'];
                                unset($_SESSION['msg']);
                                ?></center>
                        </div>
                        <?php
                    }
                    if (!isset($_GET['tab']) || $_GET['tab'] == 'home') {
                        $str = 'files/home.php';
                    } else {
                        $str = 'files/' . $_GET['tab'] . '.php';
                    }
                    if (file_exists($str))
                        require $str;
                    else
                        echo "<br/><br/><br/><div style='padding: 10px;'><h1>Page not Found :(</h1>The page you are searching for is not on this site.</div><br/><br/><br/>";
                    ?>
                </div>
                <div class='span3'>
                    <div id='sidebar'>
                        <?php loginbox(); ?>
                        <hr/>
                        <h4>Contest Status</h4>
                        <?php contest_status($judge); ?>
                        <hr />
                        <?php if (isset($_SESSION['loggedin'])) {
                            mysubs();
                            echo "<hr/>";
                        } ?>
                        <h4>Overall Rankings</h4>
<?php rankings(); ?>
                        <hr/>
<?php if ($judge['value'] == 'Active') {
    latestsubs();
    echo "<hr/>";
} ?>
                    </div>
                </div>
            </div>
            <div class="footer">
                Created by - Kaustubh Karkare<br/>
                Upgraded by - Pushkar Anand
            </div>
        </div>
    </body>
</html>
