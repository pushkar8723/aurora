<?php
if (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin') {
    if (isset($_GET['code'])) {
        $_GET['code'] = addslashes($_GET['code']);
        $query = "select * from problems where code = '$_GET[code]'";
        $res = DB::findOneFromQuery($query);
        ?>
        <center><h1>Problem Settings - <?php echo "<a class='btn btn-primary' href='" . SITE_URL . "/problems/$_GET[code]'>$_GET[code]</a>"; ?></h1></center>
        <form class='form-horizontal' role='form' method='post' action='<?php echo SITE_URL; ?>/process.php' enctype='multipart/form-data'>
            <input type='hidden' name='pid' value='<?php echo $res['pid']; ?>' />
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='New PID'>New PID</label>
                    <div class='col-lg-8'>
                        <input class='form-control' type='text' name='newpid' id='newpid' value='<?php echo $res['pid']; ?>' required/>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='name'>Name</label>
                    <div class='col-lg-8'>
                        <input class='form-control' type='text' name='name' id='name' value='<?php echo $res['name']; ?>' required/>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='code'>Code</label>
                    <div class='col-lg-8'>
                        <input class='form-control' type='text' name='code' id='code' value='<?php echo $res['code']; ?>' required/>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='pgroup'>Problem Group</label>
                    <div class='col-lg-8'>
                        <input class='form-control' type='text' name='pgroup' id='pgroup' value='<?php echo $res['pgroup']; ?>' required/>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='score'>Score</label>
                    <div class='col-lg-8'>
                        <input class='form-control' type='text' name='score' id='score' value='<?php echo $res['score']; ?>' required/>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='type'>Type</label>
                    <div class='col-lg-8'>
                        <input class='form-control' type='text' name='type' id='type' value='<?php echo $res['type']; ?>' />
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='timelimit'>Time Limit</label>
                    <div class='col-lg-8'>
                        <input class='form-control' type='text' name='timelimit' id='timelimit' value='<?php echo $res['timelimit']; ?>' required/>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='contest'>Contest type</label>
                    <div class='col-lg-8'>
                        <select class='form-control' name='contest' id='contest'>
                            <option value='contest' <?php if ($res['contest'] == "contest") echo "selected='selected'"; ?> >Contest</option>
                            <option value='practice' <?php if ($res['contest'] == "practice") echo "selected='selected'"; ?>>Practice</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='maxfilesize'>Max File size</label>
                    <div class='col-lg-8'>
                        <input class='form-control' type='text' name='maxfilesize' id='maxfilesize' value='<?php echo $res['maxfilesize']; ?>'/>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='col-lg-4 control-label' for='displayio'>Display IO</label>
                    <div class='col-lg-8'>
                        <select class='form-control' name='displayio' id='displayio'>
                            <option value='1' <?php if ($res['displayio'] == "1") echo "selected='selected'"; ?>>Yes</option>
                            <option value='0' <?php if ($res['displayio'] == "0") echo "selected='selected'"; ?>>No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='image'>Image File</label>
                    <div class='col-lg-8'>
                        <input type='file' name='image' id='image'/>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='status'>Status</label>
                    <div class='col-lg-8'>
                        <select class='form-control' name='status' id='status'>
                            <option value='Active' <?php if ($res['status'] == "Active") echo "selected='selected'"; ?> >Active</option>
                            <option value='Inactive' <?php if ($res['status'] == "Inactive") echo "selected='selected'"; ?>>Inactive</option>
                            <option value='Deleted' <?php if ($res['status'] == "Deleted") echo "selected='selected'"; ?>>Disabled</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='languages'>Languages Allowed</label>
                    <div class='col-lg-8'>
                        <input type='hidden' name='languages' value='<?php echo $res['languages']; ?>' id='languages'>
                        <select class='form-control' onChange="str = '';
                                        for (i = 0; i < this.options.length; i++)
                                            if (this.options[i].selected)
                                                str += ((str != '') ? ',' : '') + this.options[i].value;
                                        document.getElementById('languages').value = str;" multiple='multiple' >
                                    <?php
                                    $language = split(',', $res[languages]);
                                    foreach($valtoname as $key => $value)
                                        echo "<option value='$key' ".((in_array($key, $language)?("selected='selected'"):(""))).">$value</option>";
                                    ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='input'>Input File</label>
                    <div class='col-lg-8'>
                        <input type='file' name='input' id='input'/>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4' for='output'>Output File</label>
                    <div class='col-lg-8'>
                        <input type='file' name='output' id='output'/>
                    </div>
                </div>
                <br/>
            </div>
            <div class='col-md-2'>
                <div class='form-group'>
                    <label class='control-label col-lg-12'for='statement'>Problem Statement</label>
                </div>
            </div>
            <div class='col-md-10'>
                <div class='form-group'>
                    <textarea class='form-control' name='statement' style='width: 99%; height: 500px;'><?php echo $res['statement']; ?></textarea>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='form-group'>
                    <label class='control-label col-lg-4'></label>
                    <div class='col-lg-8'>
                        <input type='submit' class='btn btn-primary btn-large' value='Submit' name='updateproblem' />
                    </div>
                </div>
            </div>
        </form><br/>
        <?php
    } else {
        $langopt = implode(',',array_keys($valtoname));
        ?>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.statusupdate').click(function(event) {
                    pid = event.target.id;
                    pid = pid.replace('prob_', '');
                    $(this).html('Processing...');
                    $.post("<?php echo SITE_URL; ?>/process.php", {
                        "statusupdate": "",
                        "pid": pid,
                        "status": $('#status_' + pid).val()
                    }, function(result) {
                        if (result === '1') {
                            $('#prob_' + pid).html('Update');
                        }
                        else {
                            $('#prob_' + pid).html(result);
                        }
                    });
                });
            });
            function update() {
                str = '';
                for (i = 0; i < this.options.length; i++)
                    if (this.options[i].selected)
                        str += ((str != '') ? ',' : '') + this.options[i].value;
                console.log($("#languages").val());
                //document.getElementById('languages').value = str;
            }
        </script>
        <center><h1>Problem Settings</h1></center>
        <div id='probadd'>
            <form class='form-horizontal' role='form' method='post' action='<?php echo SITE_URL; ?>/process.php' enctype='multipart/form-data'>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class='control-label col-lg-4' for='name'>Name</label>
                        <div class='col-lg-8'>
                            <input class="form-control" type='text' name='name' id='name' required/>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class="control-label col-lg-4" for='statement'>Problem Statement</label>
                        <div class='col-lg-8'>
                            <input style='width:100%; padding: 6px 12px;' type='file' name='statement' id='statement' required/>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class="control-label col-lg-4" for='code'>Code</label>
                        <div class='col-lg-8'>
                            <input class="form-control" type='text' name='code' id='code' required/>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class="control-label col-lg-4" for='image'>Image File</label>
                        <div class='col-lg-8'>
                            <input style='width:100%; padding: 6px 12px;' type='file' name='image' id='image'/>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class="control-label col-lg-4" for='score'>Score</label>
                        <div class='col-lg-8'>
                            <input class="form-control" type='text' name='score' id='score' required/>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class="control-label col-lg-4" for='input'>Input File</label>
                        <div class='col-lg-8'>
                            <input style='width:100%; padding: 6px 12px;' type='file' name='input' id='input' required/>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class="control-label col-lg-4" for='type'>Type</label>
                        <div class='col-lg-8'>
                            <input class="form-control" type='text' name='type' id='type'/>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class="control-label col-lg-4" for='output'>Output File</label>
                        <div class='col-lg-8'>
                            <input style='width:100%; padding: 6px 12px;' type='file' name='output' id='output' required/>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class="control-label col-lg-4" for='pgroup'>Problem Group</label>
                        <div class='col-lg-8'>
                            <input class="form-control" type='text' name='pgroup' id='pgroup' required/>
                            <small>If it is a contest question then its group is same as contest code.</small>
                        </div>
                    </div>
                </div>

                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class='control-label col-lg-4' for='contest'>Contest type</label>
                        <div class='col-lg-8'>
                            <select class='form-control' name='contest' id='contest'>
                                <option value='contest'>Contest</option>
                                <option selected='selected' value='practice'>Practice</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class='control-label col-lg-4' for='timelimit'>Time Limit</label>
                        <div class='col-lg-8'>
                            <input class='form-control' type='text' name='timelimit' id='timelimit' required/>
                        </div>
                    </div>
                </div>

                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class='control-label col-lg-4' for='languages'>Languages Allowed</label>
                        <div class='col-lg-8'>
                            <input type='hidden' name='languages' value='<?php echo $langopt; ?>' id='languages'>
                            <select class='form-control' multiple='multiple' >
                                <?php
                                        foreach ($valtoname as $key => $value) {
                                            echo "<option value='$key' selected='selected'>$value</option>";
                                        }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class='control-label col-lg-4' for='status'>Status</label>
                        <div class='col-lg-8'>
                            <select class='form-control' name='status' id='status'>
                                <option value='Active'>Active</option>
                                <option value='Inactive'>Inactive</option>
                                <option value='Deleted' selected='selected'>Disabled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class='col-md-6'>
                    <div class='form-group'>
                        <label class='control-label col-lg-4' for='maxfilesize'>Max File size</label>
                        <div class='col-lg-8'>
                            <input class='form-control' type='text' name='maxfilesize' id='maxfilesize' value='50000'/>
                        </div>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label class='control-label col-lg-2' for='displayio'>Display IO</label>
                        <div class='col-lg-4'>
                            <select class='form-control' name='displayio' id='displayio'>
                                <option value='1'>Yes</option>
                                <option value='0' selected='selected'>No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label class='control-label col-lg-2'></label>
                        <div class='col-lg-4'>
                            <input type='submit' class='btn btn-primary' value='Submit' name='addproblem' />
                        </div>
                    </div>
                </div>
                <div>
                    <small>The values of all text fields must be a combination of upto 30 characters (single and double quotes are not allowed).
                        <br>Please do not put the name of the problem in the Problem Statement File. You may use the following HTML tags in the Statement File: &lt;b&gt;, &lt;i&gt;, &lt;u&gt;, &lt;ol&gt;, &lt;ul&gt;, &lt;li&gt; & &lt;code&gt; (all others will be deactivated). &lt;br&gt tags will be inserted at line breaks automatically (replacing '\n').
                        <br>If you have uploaded an image (only one jpeg/gif/png, max 3MB, allowed per problem), you must specify its position by inserting the (custom) "&lt;image /&gt;" tag somewhere in your code. It will be replaced by the (proper) &lt;img&gt; tag with the src attribute set appropriately.
                        <br>The Problem Statement, Input and Output Files must be of text format and can have a maximum size of 3MB.</small>
                </div>
            </form>
            <center><a class='btn btn-primary' onclick="$('#probadd').slideUp();
                            $('#list').slideDown();">Display List of Problem</a></center><br/>
        </div>
        <?php
        $query = "select pid, name, score, code, status from problems order by pid desc";
        $res = DB::findAllFromQuery($query);
        echo "<div id='list' style='display:none;'><h3>List of Problems</h3><table class='table table-hover'><tr><th>ID</th><th>Name</th><th>Score</th><th>Code</th><th>Status</th><th>Options</th></tr>";
        foreach ($res as $row) {
            $diffstatus = array('Active', 'Inactive', 'Deleted');
            $statusstr = "<div class='col-lg-8'><select class='form-control' id='status_$row[pid]'>";
            foreach ($diffstatus as $val) {
                $statusstr .="<option value='$val' " . (($row['status'] == $val) ? ("selected='selected'") : ("")) . " >$val</option>";
            }
            $statusstr .= "</select></div> <a href='#' class='btn btn-danger statusupdate' id='prob_$row[pid]'>Update</a>";
            echo "<tr><td>$row[pid]</td><td>$row[name]</td><td>$row[score]</td><td>$row[code]</td><td>$statusstr</td><td><a class='btn btn-primary' href='" . SITE_URL . "/adminproblem/$row[code]'><i class='glyphicon glyphicon-edit'></i> Edit</a></td></tr>";
        }
        echo "</table><center><a class='btn btn-primary' onclick=\"$('#probadd').slideDown(); $('#list').slideUp();\">Add New Problem</a></center></div><br/>";
    }
} else {
    $_SESSION['msg'] = "Access Denied: You need to be administrator to access that page.";
    redirectTo(SITE_URL);
}
?>
