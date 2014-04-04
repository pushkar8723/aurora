<?php
if ($judge['value'] != "Lockdown" || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')) {
    if (isset($_GET['code'])) {
        $_GET['code'] = addslashes($_GET['code']);
        if (!isset($_SESSION['loggedin']) || $_SESSION['team']['status'] == 'Normal')
            $query = "select runs.rid as rid, pid, tid, runs.language as language, time, result, access, submittime, name, code, error, output from runs, subs_code where runs.rid = subs_code.rid and runs.rid = $_GET[code] and (access = 'public'" . ((isset($_SESSION['team']['id'])) ? (" or tid=" . $_SESSION['team']['id']) : ("")) . ")";
        elseif (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')
            $query = "select runs.rid as rid, pid, tid, runs.language as language, time, result, access, submittime, name, code, error, output from runs, subs_code where runs.rid = subs_code.rid and runs.rid = $_GET[code]";
        else
            $query = "select * from runs where 1 = 2";
        $res = DB::findOneFromQuery($query);
        if ($res) {
            ?>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shCore.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shBrushBash.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shBrushCpp.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shBrushCSharp.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shBrushJava.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shBrushJScript.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shBrushPascal.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shBrushPerl.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shBrushPhp.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shBrushPlain.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shBrushPython.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL; ?>/syntax-highlighter/shBrushRuby.js"></script>
            <link type="text/css" rel="stylesheet" href="<?php echo SITE_URL; ?>/syntax-highlighter/shCoreDefault.css"/>
            <script type="text/javascript">
                SyntaxHighlighter.all();
            </script> 
            <?php
            $query = "select code, name, displayio, problems.status as status, contest, input, output, teamname from problems, teams where pid = $res[pid] and tid = $res[tid]";
            $prob = DB::findOneFromQuery($query);
            $btn = "";
            if ($prob['status'] == 'Active' || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin' && $prob['contest'] == 'practice')) {
                $btn = "<a class='btn btn-danger' href='" . SITE_URL . "/submit&edit=$res[rid]'><i class='glyphicon glyphicon-edit'></i> Edit</a>";
            }
            echo "<h1>Solution<div class='pull-right btn-group'>$btn<a class='btn btn-danger' href='" . SITE_URL . "/process.php?rid=$res[rid]&file=code'><i class='glyphicon glyphicon-download'></i> Download</a></div></h1>
                <table class='table'><tr><th>Run ID</th><th>Problem</th><th>Team Name</th><th>Result</th><th>Run time</th><th>Language</th><th>Submission Time</th></tr>
                <tr><td>$res[rid]</td><td><a href='" . SITE_URL . "/problems/$prob[code]'>$prob[name]</a></td><td><a href='" . SITE_URL . "/teams/$prob[teamname]'>$prob[teamname]</a></td><td>$res[result]</td><td>$res[time]</td><td>$res[language]</td><td>" . date("d F Y, l, H:i:s", $res['submittime']) . "</td></tr>
                </table>";
            if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
                ?>
                <table class="table">
                    <tr><th>Rejudge</th><th>Disqualify</th><th>Access</th></tr>
                    <tr>
                        <td>                    
                            <form class="form-inline" method="post" action="<?php echo SITE_URL; ?>/process.php">
                                <input type="hidden" value="<?php echo $res['rid']; ?>" name="rid" />
                                <input type="submit" name="rejudge" value="Rejudge" class="btn btn-danger"/>
                            </form>
                        </td>
                        <td>
                            <form class="form-inline" method="post" action="<?php echo SITE_URL; ?>/process.php">
                                <input type="hidden" value="<?php echo $res['rid']; ?>" name="rid" />
                                <input type="submit" name="dq" value="Disqualify" class="btn btn-danger"/>
                            </form>
                        </td>
                        <td>
                            <form class="form-inline" method="post" action="<?php echo SITE_URL; ?>/process.php">
                                <input type="hidden" value="<?php echo $res['rid']; ?>" name="rid" />
                                <div class="col-md-6">
                                    <select class="form-control" name="access">
                                        <option value="public" <?php if ($res['access'] == "public") echo "selected='selected' "; ?>>Public</option>
                                        <option value="private" <?php if ($res['access'] == "private") echo "selected='selected' "; ?>>Private</option>
                                        <option value="deleted" <?php if ($res['access'] == "deleted") echo "selected='selected' "; ?>>Deleted</option>
                                    </select>
                                </div>
                                <input type="submit" name="runaccess" value="Update" class="btn btn-danger"/>
                            </form>
                        </td>
                    </tr>
                </table>
                <?php
            }
            echo "<h4>Code</h4><div style='overflow-x: auto;' class='limit'><pre class='brush: " . $brush[$res['language']] . "'>" . htmlspecialchars($res['code']) . "</pre></div>";
            if (strlen($res['error']) != 0) {
                $error = explode("||", $res['error']);
                echo "<h4>Error</h4><div style='overflow-x: auto;' class='limit'><pre class='brush: text'>" . ((isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == "Admin") ? ($res['error']) : ($error[0])) . "</pre></div>";
            }
            if (($prob['displayio'] == 1 && ($res['result'] == 'AC' || $res['result'] == 'WA' || $res['result'] == 'PE')) || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')) {
                echo "<div class='col-md-4' style='overflow-x: auto;'><a class='btn btn-primary' target='_blank' href='" . SITE_URL . "/process.php?rid=$_GET[code]&file=input'>Download Input File</a></div>";
                echo "<a class='btn btn-primary' target='_blank' href='" . SITE_URL . "/process.php?rid=$_GET[code]&file=correct'>Download Correct Output File</a>
                            <a class='btn btn-primary' target='_blank' href='" . SITE_URL . "/process.php?rid=$_GET[code]&file=output'>Download Output File</a>";
                if (strlen($prob['input']) <= 102400) {
                    ?>
                    <div class='col-md-4' style='overflow-x: auto;'><h4>Input</h4><div class='limit'><pre class='brush: text'><?php echo $prob['input']; ?></pre></div></div>
                    <?php
                }
                if (strlen($prob['output']) <= 102400 && strlen($res['output']) <= 102400) {
                    $correct = preg_split("/((\r?\n)|(\r\n?))/", $prob['output']);
                    $output = preg_split("/((\r?\n)|(\r\n?))/", $res['output']);
                    $lines = array();
                    for ($i = 0; $i < count($correct) || $i < count($output);) {
                        //echo bin2hex($correct[$i])." ".bin2hex($output[$i])."<br/>";
                        if ($i < count($correct) && $i < count($output) && $correct[$i] != $output[$i]) {
                            array_push($lines, $i + 1);
                        } else if ($i > count($correct) && $i < count($output)) {
                            array_push($lines, $i + 1);
                        }
                        $i++;
                    }
                    //print_r($lines);
                    $lines = "[" . implode(',', $lines) . "]";
                    ?>
                    <div class='col-md-4' style='overflow-x: auto;'><h4>Correct Output</h4><div class='limit'><pre class='brush: text'><?php echo $prob['output']; ?></pre></div></div>
                    <div class='col-md-4' style='overflow-x: auto;'><h4>Actual Output</h4><div class='limit'><pre class='brush: text;<?php if ($res['result'] != 'AC') echo " highlight: " . $lines; ?>;' id="output"><?php echo ($res['result'] != 'AC') ? (stripslashes($res['output'])) : ($prob['output']); ?></pre></div></div>                
                            <?php
                        } else {

                            // First 10 mismatch
                            if (in_array($res['result'], array('WA', 'PE'))) {
                                echo "<div style='padding: 10px; background: #f7f7f2; overflow-x:auto; margin: 10px;'><h3>First 10 Mismatch</h3>";
                                ini_set('memory_limit', '-1');
                                $correct = preg_split("/((\r?\n)|(\r\n?))/", $prob['output']);
                                $output = preg_split("/((\r?\n)|(\r\n?))/", $res['output']);
                                echo "<table class='mismatch'><th colspan='2'>Correct Output</th><th colspan='2'>Actual Output</th>";
                                for ($i = 0, $count = 0; ($i < count($correct) || $i < count($output)) && $count < 10; $i++) {

                                    if ($i < count($correct) && $i < count($output) && $correct[$i] != $output[$i]) {
                                        $count++;
                                        echo "<tr><td class='line'>" . ($i + 1) . "</td><td>$correct[$i]</td><td class='line'>" . ($i + 1) . "</td><td>$output[$i]</td></tr>";
                                    } else if ($i > count($correct) && $i < count($output)) {
                                        $count++;
                                        echo "<tr><td class='line'>" . ($i + 1) . "</td><td>$correct[$i]</td></tr><td class='line'>" . ($i + 1) . "</td><td></td></tr>";
                                    } else if ($i < count($correct) && $i > count($output)) {
                                        $count++;
                                        echo "<tr><td class='line'>" . ($i + 1) . "</td><td></td></tr><td class='line'>" . ($i + 1) . "</td><td>$output[$i]</td></tr>";
                                    }
                                }
                                echo "</table></div>";
                            }
                        }
                        ?>
                        <?php
                        // } else {
//                    
                        // }
                    }
                } else {
                    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Solution not Found :(</h1>The solution you are looking for doesn't exsits or you are not authorized to view.</div><br/><br/><br/>";
                }
            } else {
                ?>
        <script type='text/javascript'>
            $(document).ready(function() {
                $('#submit').click(function() {
                    $(location).attr('href', '<?php echo SITE_URL; ?>/viewsolution/' + $('#rid').val());
                });
            });
        </script>
        <?php
        echo "<center><h1>Solution</h1></center>Run ID : <input id='rid' type='text' /> <input style='margin-top: -10px;' id='submit' value='Search' type='button' class='btn btn-primary' />";
    }
} else {
    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Lockdown Mode :(</h1>This feature is now offline as Judge is in Lockdown mode.</div><br/><br/><br/>";
}
?>
