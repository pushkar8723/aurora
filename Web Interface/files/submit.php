<?php
if ($judge['value'] != "Lockdown" || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')) {
    ?>
    <script type='text/javascript' src='<?php echo SITE_URL; ?>/codemirror/lib/codemirror.js'></script>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/codemirror/lib/codemirror.css">
    <script src="<?php echo SITE_URL; ?>/codemirror/mode/javascript/javascript.js"></script>
    <script src="<?php echo SITE_URL; ?>/codemirror/mode/clike/clike.js"></script>
    <script src="<?php echo SITE_URL; ?>/codemirror/mode/pascal/pascal.js"></script>
    <script src="<?php echo SITE_URL; ?>/codemirror/mode/perl/perl.js"></script>
    <script src="<?php echo SITE_URL; ?>/codemirror/mode/php/php.js"></script>
    <script src="<?php echo SITE_URL; ?>/codemirror/mode/python/python.js"></script>
    <script src="<?php echo SITE_URL; ?>/codemirror/mode/ruby/ruby.js"></script>
    <script src="<?php echo SITE_URL; ?>/codemirror/addon/edit/closebrackets.js"></script>
    <script src="<?php echo SITE_URL; ?>/codemirror/addon/edit/matchbrackets.js"></script>
    <script type='text/javascript'>
        $(document).ready(function() {
            var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('sub'), {'matchBrackets': true, 'autoCloseBrackets': true, 'lineWrapping': true, 'mode': 'text/x-c++src', 'lineNumbers': true, 'indentUnit': 4});
            var cmmode = {<?php echo $cmmode; ?>};
            $('#lang').change(function() {
                myCodeMirror.setOption('mode', cmmode[$('#lang').val()]);
            });
        });
    </script>
    <?php
    if (!isset($_SESSION['loggedin'])) {
        echo "<br/><br/><br/><div style='padding: 10px;'><h1>You are not logged in! :(</h1>You need to be logged in to submit a solution.</div><br/><br/><br/>";
    } else {
        if (isset($_GET['code']) || isset($_GET['edit'])) {
            if(isset($_GET['edit'])){
                $query = "select runs.rid, pid, tid, access, language, code from runs, subs_code where runs.rid = $_GET[edit] and subs_code.rid = $_GET[edit]";
                $runs = DB::findOneFromQuery($query);
                if($runs['access'] == "public" || (isset($_SESSION['loggedin']) && $runs['tid'] == $_SESSION['team']['id']) || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == "Admin")){
                    $query = "select languages, code from problems where pid = $runs[pid]".(($_SESSION['team']['status'] == "Admin")?"":" and status = 'Active' ");
                    $_SESSION['subcode'] = addslashes($runs['code']);
                } else {
                    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Permission Denied :(</h1>You don't have permission to edit this solution.</div><br/><br/><br/>";
                    return ;
                }
            } else if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
                $_GET['code'] = addslashes($_GET['code']);
                $query = "select languages from problems where code ='$_GET[code]'";
            } else {
                $_GET['code'] = addslashes($_GET['code']);
                $query = "select languages from problems where code ='$_GET[code]' and status = 'Active'";
            }
            $prob = DB::findOneFromQuery($query);
            if ($prob) {
                echo "<h1>Submit Solution" . ((isset($_GET['code']) && $_GET['code'] != "") ? (" - $_GET[code]</h1>") : (" - $prob[code]</h1>"));
                ?>
                <form id='form' action='<?php echo SITE_URL; ?>/process.php' method='post' enctype='multipart/form-data'>
                    <table class='table table-striped'>
                        <tr><th>Language : </th>
                            <td>
                                <select class="form-control" id='lang' name='lang'>
                                    <?php
                                    $lang = split(',', $prob['languages']);
                                    foreach ($lang as $row) {
                                        if ($row == 'Brain')
                                            echo "<option value='$row'".((isset ($_GET['edit']) && $row == $runs['language'])?"selected='selected'":"").">Brainf**k</option>";
                                        else if (isset ($_GET['edit']) && $row == $runs['language'])
                                            echo "<option value='$row' selected='selected'>$row</option>";
                                        else if ($row == 'C++' && !isset ($_GET['edit']))
                                            echo "<option value='$row' selected='selected'>$row</option>";
                                        else
                                            echo "<option value='$row'>$row</option>";
                                    }
                                    ?>
                                </select>    
                            </td><th>File : </th><td><input type='file' name='code_file'/></td></tr>
                        <tr><td colspan='4' style='padding: 0;'><textarea id='sub' name='sub'><?php
                                    if (isset($_SESSION['subcode'])) {
                                        echo stripslashes($_SESSION['subcode']);
                                        unset($_SESSION['subcode']);
                                    }
                                    ?></textarea></td></tr>
                    </table>
                    <input type="hidden" value="<?php echo ((isset($_GET['code']) && $_GET['code'] != "")?($_GET['code']):($prob['code'])); ?>" name="probcode"/>
                    <input type='submit' value='Submit' class='btn btn-large btn-primary' name='submitcode'/>
                </form> 
                <?php
            } else {
                echo "<br/><br/><br/><div style='padding: 10px;'><h1>Problem Inactive :(</h1>You cannot submit you solution at this time.</div><br/><br/><br/>";
            }
        } else {
            echo "<br/><br/><br/><div style='padding: 10px;'><h1>Page not Found :(</h1>The page you are searching for is not on this site.</div><br/><br/><br/>";
        }
    }
} else {
    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Lockdown Mode :(</h1>This feature is now offline as Judge is in Lockdown mode.</div><br/><br/><br/>";
}
?>
