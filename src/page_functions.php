<?php
function List_File($file, $file_url) {//****************************************
    global $_, $DOC_ROOT, $ONESCRIPT, $ICONS, $MESSAGE;

    $file_OS = Convert_encoding($file);
    clearstatcache();
    $ipath = trim($DOC_ROOT,'/').dir_name($file_url);

    $href = $ONESCRIPT.'?i='.$ipath.'&amp;f='.basename($file_url);
    $edit_link = '<a href="'.$href.'&amp;p=edit'.'" id="old_backup">'.hsc(basename($file)).'</a>';

?>

    <tr>
        <td>
            <a href="<?= $href.'&amp;p=deletefile' ?>" class="button" id="del_backup">
                <?= $ICONS['delete'].'&nbsp;'.hsc($_['Delete']) ?>
            </a>
        </td>
        <td class="file_name"><?= $edit_link; ?></td>
        <td class="meta_T file_size">&nbsp;	<?= number_format(filesize($file_OS)); ?> B	</td>
        <td class="meta_T file_time"> &nbsp;
            <script>
                FileTimeStamp(<?= filemtime($file_OS); ?>, 1, 0, 1);
            </script>
        </td>
    </tr>

<?php
}//end List_File() //***********************************************************

function List_Backups_and_Logs() {//********************************************
    global $_, $ONESCRIPT_backup, $ONESCRIPT_file, $ONESCRIPT_file_backup,
        $CONFIG_backup, $CONFIG_FILE_backup, $LOGIN_LOG_url, $LOGIN_LOG_file;

    //Indicate if a login log or backups (from a prior p/w or u/n change) exist.

    $CONFIG_FILE_backup_OS    = Convert_encoding($CONFIG_FILE_backup);
    $ONESCRIPT_file_backup_OS = Convert_encoding($ONESCRIPT_file_backup);
    $LOGIN_LOG_file_OS		  = Convert_encoding($LOGIN_LOG_file);

    clearstatcache();
    $backup_found = $log_found = false;
    if (is_file($ONESCRIPT_file_backup_OS) || is_file($CONFIG_FILE_backup_OS) ) {
        $backup_found = true;
    }
    if (is_file($LOGIN_LOG_file_OS)) {
        $log_found = true;
    }

    if ( $backup_found || $log_found ) {
        echo '<table class="index_T">';
        if ($log_found){
            List_File($LOGIN_LOG_file, $LOGIN_LOG_url);
        }
        if (is_file($ONESCRIPT_file_backup_OS)) {
            List_File($ONESCRIPT_file_backup, $ONESCRIPT_backup);
        }
        if (is_file($CONFIG_FILE_backup_OS)) {
            List_File($CONFIG_FILE_backup, $CONFIG_backup);
        }
        echo '</table>';

        if ($backup_found) {
            echo '<p><b>'.hsc($_['admin_txt_00']).'</b></p>';
            echo '<p>'.hsc($_['admin_txt_01']);
        }
        echo '<hr>';
    }//end of check for backup
}//end List_Backups_and_Logs() //***********************************************

function Admin_Page() {//*******************************************************
    global $_, $DOC_ROOT, $ONESCRIPT, $ipath, $filename, $param1, $param2, $MAIN_TITLE, $MESSAGE;

    // Restore/Preserve $ipath prior to admin page in case OneFileCMS is edited (which would change $ipath).
    if ( $_SESSION['admin_page'] ) {
        $ipath  = $_SESSION['admin_ipath'];
        $param1 = '?i='.URLencode_path($ipath);
    } else {
        $_SESSION['admin_page']  = true;
        $_SESSION['admin_ipath'] = $ipath;
    }

    // [Close] returns to either the index or edit page.
    $params = "";
    if ($filename != "") {
        $params = $param2.'&amp;p=edit';
    }

    $button_attribs = '<button type="button" class="button" onclick="parent.location =\''.$ONESCRIPT;
    $ofcms_ipath = trim($DOC_ROOT,'/').dir_name($ONESCRIPT);
    $edit_params = '?i='.$ofcms_ipath.'&amp;f='.basename($ONESCRIPT).'&amp;p=edit';

    echo '<h2>'.hsc($_['Admin_Options']).'</h2>';

    echo '<span class="admin_buttons">';
    echo $button_attribs.$param1.$params.'\'" id="close">'.hsc($_['Close']).'</button>';
    echo $button_attribs.$param1.'&amp;p=changepw\'">'.hsc($_['pw_change']).'</button>';
    echo $button_attribs.$param1.'&amp;p=changeun\'">'.hsc($_['un_change']).'</button>';
    echo $button_attribs.$param1.'&amp;p=hash\'">'.hsc($_['Generate_Hash']).'</button>';
    echo $button_attribs.$edit_params.'\'">'.hsc($_['View'].' '.$MAIN_TITLE).'</button>';
    echo '</span>';

    echo '<div class="info">';

    List_Backups_and_Logs();

    echo '<p><b>'.hsc($_['Username']).': </b>';
    echo '<span class="meta_T meta_T2">'.get_current_user()."</span><br>\n";

    echo '<p><b>'.hsc($_['admin_txt_03']).': </b>';
    echo '<span class="meta_T meta_T2">'.session_save_path()."</span><br>\n";

    echo '<p><b>'.hsc($_['admin_txt_04']).': </b>';
    echo '<span class="meta_T meta_T2">'.php_uname('n')."</span><hr>\n";

    echo '<p><b>'.hsc($_['admin_txt_02']).'</b>';
    echo '<p>'   .hsc($_['admin_txt_16']);
    echo '<p>'.hsc($_['admin_txt_14']);
    echo '</div>'; //end class=info

    echo '<script>E("close").focus();</script>';
}//end Admin_Page() //**********************************************************

function Hash_Page() {//********************************************************
    global $_, $ONESCRIPT, $param1, $param3, $INPUT_NUONCE, $PWUN_RULES;

    if (!isset($_POST['whattohash'])) {
        $_POST['whattohash'] = '';
    }
?>
    <style>#message_box {font-family: courier; min-height: 3.1em;}</style>

    <h2><?= hsc($_['Generate_Hash']) ?></h2>

    <form id="hash" name="hash" method="post" action="<?= $ONESCRIPT.$param1.$param3; ?>">
        <?= $INPUT_NUONCE; ?>
        <?= hsc($_['pass_to_hash']) ?>
        <input type="text" name="whattohash" id="whattohash" value="<?= hsc($_POST['whattohash']) ?>">
        <p><?php Cancel_Submit_Buttons($_['Generate_Hash']) ?>
        <script>E('whattohash').focus()</script>
    </form>

    <div class="info">
        <p><?= hsc($_['hash_txt_01']) ?><br>
        <ol><li><?= hsc($_['hash_txt_06']) ?><br>
                <?= hsc($_['hash_txt_07']) ?>
            <li><?= hsc($_['hash_txt_08']) ?><br>
                <?= hsc($_['hash_txt_09']) ?><br>
                <?= hsc($_['hash_txt_10']) ?><br>
            <li><?= hsc($_['hash_txt_12']) ?>
        </ol>
        <?= $PWUN_RULES ?>
    </div>
<?php
}//end Hash_Page() //***********************************************************

function Hash_response() {//****************************************************
    global $_, $MESSAGE;
    $_POST['whattohash'] = trim($_POST['whattohash']); // trim whitespace.

    //Ignore/don't hash an empty string - passwords can't be blank.
    if ($_POST['whattohash'] == "") { return; }

    //The second parameter to hashit(), 1, tells hashit() to also do the "pre-hash", which is
    //normally done client-side during a login attempt, p/w change, or u/n change.
    $MESSAGE .= hsc($_['Password']).': '.hsc($_POST['whattohash']).'<br>';
    $MESSAGE .= hsc($_['Hash']).': '.hashit($_POST['whattohash'], 1).'<br>';
}//end Hash_response() //*******************************************************

//******************************************************************************
function Change_PWUN_Page($pwun, $type, $page_title, $label_new, $label_confirm) {
    //$pwun must = "pw" or "un"
    global $_, $EX, $ONESCRIPT, $param1, $param2, $param3, $INPUT_NUONCE, $PWUN_RULES;

    $params = $param1.$param2.'&amp;p='. $_SESSION['recent_pages'][1];
    //preserve space for message_box even when there's no message.
?>
    <style>#message_box {min-height: 2em;}</style>

    <h2><?= hsc($page_title) ?></h2>

    <form id="change" method="post" action="<?= $ONESCRIPT.$param1.$param3; ?>">
        <input type="hidden" name="<?= $pwun ?>" value="">

        <?= $INPUT_NUONCE; ?>

        <p><?= hsc($_['pw_current']) ?><br>
        <input type="password" name="password" id="password" value="">

        <p><?= hsc($label_new) ?><br>
        <input type="<?= $type ?>" name="new1" id="new1" value="">

        <p><?= hsc($label_confirm) ?><br>
        <input type="<?= $type ?>" name="new2" id="new2" value="">

        <p><input type="button" class="button" id="cancel" value="<?= hsc($_['Cancel']) ?>"
            onclick="parent.location = '<?= $ONESCRIPT.$params ?>'">
        <input type="button" class="button"    id="submitty" value="<?= hsc($_['Submit']) ?>">

        <script>E('password').focus()</script>
    </form>

    <div class="info">
    <?= $PWUN_RULES ?>
    <p><?= hsc($_['pw_txt_12']) ?>
    <p><?= hsc($_['pw_txt_14']) ?>
    </div>
<?php
    //Note: The button with id="submitty" above must NOT be of type="submit",
    //NOR have an id="submit", or the event_scripts won't work.
    pwun_event_scripts('change', 'submitty', $pwun); //Doesn't work if an id="submit"
    js_hash_scripts();
}//end Change_PWUN_Page() //****************************************************

//******************************************************************************
function Update_config($search_for, $replace_with, $search_file, $backup_file) {
    global  $_, $EX, $MESSAGE;

    $search_file_OS = Convert_encoding($search_file);
    $backup_file_OS = Convert_encoding($backup_file);

    if ( !is_file($search_file_OS) ) {
        $MESSAGE .= $EX.' <b>'.hsc($_['Not_found']).': </b>'.hsc($search_file).'<br>';
        return false;
    }

    //Read file into an array for searching.
    $search_lines = file($search_file_OS, FILE_IGNORE_NEW_LINES);

    //Search start of each $line in (array)$search_lines for (string)$search_for.
    //If match found, replace $line with $replace_with, end search.
    $search_len = mb_strlen($search_for);
    $found = false;
    foreach ($search_lines as $key => $line) {
        if ( mb_substr($line,0,$search_len) == $search_for ) {
            $found = true;
            $search_lines[$key] = $replace_with;
            break 1; //only replace first occurrance of $search_for
        }
    }

    //This should not happen, but just in case...
    if (!$found) {
        $MESSAGE .= $EX.' <b>'.hsc($_['Not_found']).': </b>'.hsc($search_for).'<br>';
        return false;
    }

    copy($search_file_OS, $backup_file_OS); // Just in case...

    $updated_contents = implode("\n", $search_lines);

    if (file_put_contents($search_file_OS, $updated_contents, LOCK_EX) === false) {
        $MESSAGE .= $EX.'<b>'.hsc($_['Update_failed']).'</b><br>';
        return false;
    } else {
        return true;
    }
}//end Update_config() //*******************************************************

function Change_PWUN_response($PWUN, $msg) {//**********************************
    //Update $USERNAME or $HASHWORD. Default $page = changepw or changeun
    global $_, $ONESCRIPT, $USERNAME, $HASHWORD, $EX, $MESSAGE, $page,
        $ONESCRIPT_file, $ONESCRIPT_file_backup, $CONFIG_FILE, $CONFIG_FILE_backup, $VALID_CONFIG_FILE;

    // trim white-space from input values
    $current_pass = trim($_POST['password']);
    $new_pwun     = trim($_POST['new1']);
    $confirm_pwun = trim($_POST['new2']);

    $error_msg   = $EX.'<b>'.hsc($msg).'</b> ';

    //If all fields are blank, do nothing.
    if ( ($current_pass == "") && ($new_pwun == "") && ($confirm_pwun == "") ) {
        return;
    } elseif ( ($current_pass == "") || ($new_pwun == "") || ($confirm_pwun == "") ) {
        //If any field is blank...
        $MESSAGE .= $error_msg.hsc($_['change_pw_07']).'<br>';
    } elseif ($new_pwun != $confirm_pwun) {
        //If new & Confirm values don't match...
        $MESSAGE .= $error_msg.hsc($_['change_pw_04']).'<br>';
    } elseif (hashit($current_pass) != $HASHWORD) {
        //If incorrect current p/w, logout.  (new == confirm at this point)
        $MESSAGE .= $error_msg.'<br>'.hsc($_['change_pw_03']).'<br>';
        Logout();
    } else {
        //Else change username or password
        if ($PWUN == "pw") {
            $search_for   = '$HASHWORD '; //include space after $HASHWORD
            $replace_with = '$HASHWORD = "'.hashit($new_pwun).'";';
            $success_msg  = '<b>'.hsc($_['change_pw_01']).'</b>';
        } else { //$PWUN = "un"
            $search_for   = '$USERNAME '; //include space after $USERNAME
            $replace_with = '$USERNAME = "'.$new_pwun.'";';
            $success_msg  = '<b>'.hsc($_['change_un_01']).'</b>';
        }

        //If specified & it exists, update external config file.
        if ( $VALID_CONFIG_FILE ) {
            $MESSAGE .= hsc($_['change_pw_05']).' '.hsc($_['change_pw_06']).'. . . ';
            $updated = Update_config($search_for, $replace_with, $CONFIG_FILE, $CONFIG_FILE_backup);
        } else{ //Update OneFileCMS
            $MESSAGE .= hsc($_['change_pw_05']).' OneFileCMS . . . ';
            $updated = Update_config($search_for, $replace_with, $ONESCRIPT_file, $ONESCRIPT_file_backup);
        }

        if ($updated === false) {
            $MESSAGE .= $error_msg.'<br>';
        } else {
            $MESSAGE .= $success_msg.'<br>';
        }

        $page = "admin"; //Return to Admin page.
    }
}//end Change_PWUN_response() //************************************************

function Logout() {//***********************************************************
    global $page;
    session_regenerate_id(true);
    session_unset();
    session_destroy();
    session_write_close();
    unset($_GET);
    unset($_POST);
    $_SESSION = array();
    $_SESSION['valid'] = 0;
    $page = 'login';
}//end Logout() //**************************************************************

function Login_Page() {//*******************************************************
    global $_, $ONESCRIPT;
?>
    <?php //preserve space for message_box even when there's no message. ?>
    <style>#message_box {height: 3.1em;}</style>

    <h2><?= hsc($_['Log_In']) ?></h2>
    <form method="post" id="login_form" name="login_form" action="<?= $ONESCRIPT; ?>">
        <label for ="username"><?= hsc($_['Username']) ?>:</label>
        <input name="username" type="text"     id="username">
        <label for ="password"><?= hsc($_['Password']) ?>:</label>
        <input name="password" type="password" id="password">
        <input type="button"  class="button"   id="login" value="<?= hsc($_['Enter']) ?>">
    </form>
    <script>E('username').focus();</script>
<?php
    //Note: The "login" button above must NOT be of type="submit", NOR have an id="submit", or the event_scripts won't work.
    pwun_event_scripts('login_form', 'login');
    js_hash_scripts();
}//end Login_Page() //**********************************************************

function Login_response() {//***************************************************
    global $_, $EX, $ONESCRIPT_file, $MESSAGE, $page, $USERNAME, $HASHWORD,
        $LOGIN_ATTEMPTS, $MAX_ATTEMPTS, $LOGIN_DELAY, $LOGIN_DELAYED, $LOG_LOGINS, $LOGIN_LOG_file;

    $_SESSION = array();    //make sure it's empty
    $_SESSION['valid'] = 0; //Default to failed login.
    $attempts = 0;
    $elapsed  = 0;
    $LOGIN_ATTEMPTS = Convert_encoding($LOGIN_ATTEMPTS); //$LOGIN_ATTEMPTS only used for filesystem access.

    $LOGIN_DELAYED = 0; //used to start Countdown at end of file

    //Check for prior login attempts (but don't increment count just yet)
    if (is_file($LOGIN_ATTEMPTS)) {
        $attempts = (int)file_get_contents($LOGIN_ATTEMPTS);
        $elapsed  = time() - filemtime($LOGIN_ATTEMPTS);
    }

    if ($attempts > 0) {
        $MESSAGE .= '<b>'.hsc($_['login_msg_01a']).' '.$attempts.' '.hsc($_['login_msg_01b']).'</b><br>';
    }

    if ( ($attempts >= $MAX_ATTEMPTS) && ($elapsed < $LOGIN_DELAY) ) {
        $LOGIN_DELAYED = ($LOGIN_DELAY - $elapsed);
        $MESSAGE .= hsc($_['login_msg_02a']).' <span id=timer0></span> '.hsc($_['login_msg_02b']);
        return;
    }

    //Trim any incidental whitespace before validating.
    $_POST['password'] = trim($_POST['password']);
    $_POST['username'] = trim($_POST['username']);

    //validate login.
    if ( ($_POST['password'] == "") || ($_POST['username'] == "") ) {
        return; //Ignore login attempt if either username or password is blank.
    } elseif ( (hashit($_POST['password']) == $HASHWORD) && ($_POST['username'] == $USERNAME) ) {
        session_regenerate_id(true);
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT']; //for user consistancy check.
        $_SESSION['valid'] = 1;
        $page = "index";
        if ( is_file($LOGIN_ATTEMPTS) ) {
            unlink($LOGIN_ATTEMPTS);
        } //delete count/file of $LOGIN_ATTEMPTS

    } else {
        file_put_contents($LOGIN_ATTEMPTS, ++$attempts); //increment attempts
        $MESSAGE  = $EX.'<b>'.hsc($_['login_msg_03']).$attempts.'</b><br>';
        if ($attempts >= $MAX_ATTEMPTS) {
            $LOGIN_DELAYED = $LOGIN_DELAY;
            $MESSAGE .= hsc($_['login_msg_02a']).' <span id=timer0></span> '.hsc($_['login_msg_02b']);
        }
    }

    //Log login attempts
    if ($LOG_LOGINS) {
        $log_file    = Convert_encoding($LOGIN_LOG_file);
        $pass_fail   = $_SESSION['valid'].' ';
        $timestamp   = date("Y-m-d H:i:s").' ';
        $client_IP   = $_SERVER['REMOTE_ADDR'].' ';
        $client_port = $_SERVER['REMOTE_PORT'].' ';
        $client      = '"'.$_SERVER['HTTP_USER_AGENT'].'"';

        file_put_contents($log_file, $pass_fail.$timestamp.$client_IP.$client_port.$client."\n",FILE_APPEND);
    }//
}//end Login_response() //******************************************************

function Create_Table_for_Listing() {//*****************************************
    global $_, $ONEFILECMS, $ipath, $ipath_OS, $ICONS, $TABINDEX, $ACCESS_ROOT, $DIRECTORY_COLUMNS;

    //Header row: | Select All|[ ]|[X](folders first)      Name      (ext) |   Size   |    Date    |

    $new_path = URLencode_path(dir_name($ipath)); //for "../" entry in dir list.

    $file_owner_header = $file_group_header = "";
    if (function_exists('posix_getpwuid')) {
        $file_owner_header = $_['Owner'];
        $file_group_header = $_['Group'];
    }

    $ti = $TABINDEX + 6;
    if ($ipath == $ACCESS_ROOT)
    { $file_0 = "<a id=f0c5 tabindex=$ti>&nbsp;</a>"; }
    else
    { $file_0 = "<a id=f0c5 tabindex=$ti href='$ONEFILECMS?i=$new_path'>{$ICONS['up_dir']} <b>..</b> /</a>"; }

    //<input hidden> is a dummy input to make sure files[] is always an array for Select_All() & Confirm_Ready().
?>
    <input type=hidden name="files[]" value="">

<?php //RE: $TABINDEX's below
// In order to have ['Name'] (it's background) expand to fill available space in header,
// (ext) is float'ed right, but has to be listed first, before ['Name'].
// However, tabindex's need to be in order as displayed, not in order as listed in source.
?>

    <table class="index_T">
    <tr>
    <th colspan=3><LABEL for=select_all_ckbox id=select_all_label><?= hsc($_['Select_All']) ?></LABEL></th>
    <th><div class=ckbox>
            <INPUT id=select_all_ckbox tabindex=<?= $TABINDEX + 0 ?> TYPE=checkbox NAME=select_all VALUE=select_all>
        </div>
    </th>

    <th class=mono>sogw</th>

    <th class=file_name>
        <div id=ff_ckbox_div class=ckbox>
            <INPUT tabindex=<?= $TABINDEX + 1?> TYPE=checkbox id=folders_first_ckbox NAME=folders_first VALUE=folders_first checked>
        </div>
        <label for=folders_first_ckbox id=folders_first_label title="<?php  echo hsc($_['folders_first_info']) ?>">
            (<?= hsc($_['folders_first']) ?>)
        </label>
        <a tabindex=<?= $TABINDEX + 3 ?> href="#" id=header_sorttype>(<?= hsc($_['ext']) ?>)</a>
        <a tabindex=<?= $TABINDEX + 2 ?>     href="#" id=header_filename><?= hsc($_['Name']) ?></a>
    </th>

    <th class=file_size><a tabindex=<?= $TABINDEX + 4 ?> href="#" id=header_filesize><?= hsc($_['Size']." (".$_['bytes'].")") ?></a></th>
    <th class=file_time><a tabindex=<?= $TABINDEX + 5 ?> href="#" id=header_filedate><?= hsc($_['Date']) ?></a></th>

    <th><?= $file_owner_header ?></th>
    <th><?= $file_group_header ?></th>
    </tr>

    <tr><?php // "../" directory entry ?>
        <td colspan=5 id=header_msg></td>
        <td><?= $file_0 ?></td>
        <td></td><?php //file size  ?>
        <td></td><?php //date time  ?>
        <td></td><?php //file owner  ?>
        <td></td><?php //file group  ?>
    <tr>

    <?php //Directory & footer content will be inserted later. ?>
    <tbody id=DIRECTORY_LISTING></tbody>
    <tr><td id=DIRECTORY_FOOTER colspan="<?= $DIRECTORY_COLUMNS ?>"></td></tr>
    </table>
<?php
    $TABINDEX += 7;
}//Create_Table_for_Listing() //************************************************

function Get_File_Stats($filename_OS) {//***************************************
    //Get file size, date, mode (permissions), etc.

    $file_stats = lstat($filename_OS); //returns [file size, mtime, uid, guid, etc...]

    $file_stats['is_writable'] = is_writable($filename_OS) * 1; //1 or 0  (true or false)

    if ($file_stats) {
        $file_stats['perms'] = decoct($file_stats['mode'] & 07777);
    } else {
        $file_stats['perms'] = "";
        $file_stats['size']  = "";
        $file_stats['mtime'] = "";
    }

    //Get file owner & group names. Some systems, like Windows, don't have posix_getpwuid().
    if ($file_stats && function_exists('posix_getpwuid')) {
        $fileowner_uid  		  = $file_stats['uid'];
        $fileowner_info 		  = posix_getpwuid($fileowner_uid);
        $file_stats['owner'] = $fileowner_info['name'];

        $filegroup_uid			  = $file_stats['gid'];
        $filegroup_info			  = posix_getgrgid($filegroup_uid);
        $file_stats['group'] = $filegroup_info['name'];
    } else {
        $file_stats['owner'] = "";
        $file_stats['group'] = "";
    }

    if (is_link($filename_OS)) {
        $file_stats['link_target'] = " -> ".readlink($filename_OS);
    } else {
        $file_stats['link_target'] = "";
    }

    return $file_stats;

}//end Get_File_Stats() {//*****************************************************

function Get_DIRECTORY_DATA($raw_list) {//**************************************
    global $_, $ONESCRIPT, $ipath, $ipath_OS, $param1, $ICONS, $MESSAGE,
        $FTYPES, $FCLASSES, $EXCLUDED_LIST, $STYPES, $SHOWALLFILES,
        $DIRECTORY_DATA, $ENC_OS;

    //Doesn't use global $filename or $filename_OS in this function (because they shouldn't exist on the Index page)
    //$filename below is JUST the file's name.  In other functions, it's the full/path/filename

    clearstatcache();

    $file_count = 0; //final count to exclude . & .., and any $excluded file names
    foreach ($raw_list as $raw_filename) { //$raw_list is in server's File System encoding

        if ( ($raw_filename == '.') || ($raw_filename == '..') ) {
            continue;
        }

        $filename_OS = $ipath_OS.$raw_filename;

        $file_stats = Get_File_Stats($filename_OS);

        //Normalize filename encoding for general use & display. (UTF-8, which may not be same as the server's File System)
        if ($ENC_OS == 'UTF-8') {
            $filename = $raw_filename;
        } else {
            $filename = Convert_encoding($raw_filename,'UTF-8');
        }

        //Get file .ext & check against $STYPES (files types to show)
        $filename_parts = explode(".", mb_strtolower($filename));

        //Check for no $ext:  "filename"  or ".filename"
        $segments = count($filename_parts);
        if( $segments === 1 || (($segments === 2) && ($filename_parts[0] === "")) ) {
            $ext =  '';
        } else {
            $ext = end($filename_parts);
        }

        //Check $filename & $ext against white & black lists. If not to be shown, get next $filename...
        if (!is_dir($filename_OS)) {
            if ($SHOWALLFILES || in_array($ext, $STYPES)) {
                $SHOWTYPE = true;
            } else {
                $SHOWTYPE = false;
            }

            if (in_array($filename, $EXCLUDED_LIST)) {
                $excluded = true;
            } else {
                $excluded = false;
            }

            if ( !$SHOWTYPE || in_array($filename, $EXCLUDED_LIST) ) {
                continue;
            }
        }

        //Set icon type based on if dir, or file type ($ext).
        if (is_dir($filename_OS)) {
            $type = 'dir';
        } else {
            $type = $FCLASSES[array_search($ext, $FTYPES)];
        }

        //Determine icon to show
        if (in_array($type,$FCLASSES)) {
            $icon = $ICONS[$type];
        } elseif ($type == 'dir') {
            $icon = $ICONS['folder'];
        } else {
            $icon = $ICONS['bin'];
        } //default

        //Store data
        $DIRECTORY_DATA[$file_count] = array('', '', 0, 0, 0, '', '', '', '', '');
        $DIRECTORY_DATA[$file_count][ 0] = $type;  //used to determine icon & f_or_f
        $DIRECTORY_DATA[$file_count][ 1] = $filename;
        $DIRECTORY_DATA[$file_count][ 2] = $file_stats['size'];
        $DIRECTORY_DATA[$file_count][ 3] = $file_stats['mtime'];
        $DIRECTORY_DATA[$file_count][ 4] = Set_IS_OFCMS($ipath.$filename); //If = 1, Don't show ren, del, ckbox.
        $DIRECTORY_DATA[$file_count][ 5] = $ext; //##### Is this used?
        $DIRECTORY_DATA[$file_count][ 6] = $file_stats['perms'];
        $DIRECTORY_DATA[$file_count][ 7] = $file_stats['owner'];
        $DIRECTORY_DATA[$file_count][ 8] = $file_stats['group'];
        $DIRECTORY_DATA[$file_count][ 9] = $file_stats['link_target'];
        $DIRECTORY_DATA[$file_count][10] = $file_stats['is_writable'];  //1 or 0 (true or false)

        $file_count++;
    }//end foreach file

    return $file_count;
}//end Get_DIRECTORY_DATA() //**************************************************

function Index_Page_buttons_top($file_count) {//********************************
    global $_, $ONESCRIPT, $param1, $ICONS, $TABINDEX;

    echo '<div id=index_page_buttons>'."\n";

    echo '<div id=mcd_submit>'."\n";
    if ($file_count > 0) {
        echo '<button id=b1 tabindex='.$TABINDEX++.' type=button>'.$ICONS['move'  ].hsc($_['Move']  )."</button\n>";
        echo '<button id=b2 tabindex='.$TABINDEX++.' type=button>'.$ICONS['copy'  ].hsc($_['Copy']  )."</button\n>";
        echo '<button id=b3 tabindex='.$TABINDEX++.' type=button>'.$ICONS['delete'].hsc($_['Delete'])."</button\n>";
    }
    echo '</div>'."\n"; //end mcd_submit

    echo '<div class="front_links">'."\n";
    echo '<a id=b4 tabindex='.$TABINDEX++.' href="'.$ONESCRIPT.$param1.'&amp;p=newfolder">'.$ICONS['folder_new'].hsc($_['New_Folder']) .'</a>';
    echo '<a id=b5 tabindex='.$TABINDEX++.' href="'.$ONESCRIPT.$param1.'&amp;p=newfile">'  .$ICONS['file_new']  .hsc($_['New_File'])   .'</a>';
    echo '<a id=b6 tabindex='.$TABINDEX++.' href="'.$ONESCRIPT.$param1.'&amp;p=upload">'   .$ICONS['upload']    .hsc($_['Upload_File']).'</a>';
    echo '</div>'; //end front_links

    echo '</div>'."\n"; //end index_page_buttons

} //end Index_Page_buttons_top() //*********************************************

function Index_Page() {//*******************************************************
    global  $ONESCRIPT, $ipath_OS, $param1, $INPUT_NUONCE, $DIRECTORY_DATA, $DIRECTORY_COUNT;

    //Get current directory list  (unsorted)
    $raw_list = scandir('./'.$ipath_OS);
    $DIRECTORY_COUNT = Get_DIRECTORY_DATA($raw_list);

    if ($DIRECTORY_COUNT < 1) {
        $json_encoded_DIRECTORY_DATA = "[]";
    } else {
        $json_encoded_DIRECTORY_DATA = json_encode($DIRECTORY_DATA);
    }

    //<form> to contain directory, including buttons at top.
    echo "<form method=post id=mcdselect action='{$ONESCRIPT}{$param1}&amp;p=mcdaction'>\n";
    echo "<input type=hidden name=mcdaction value=''>\n"; //along with $page, affects response
    echo $INPUT_NUONCE; //Needed for file permission updates.

    Index_Page_buttons_top($DIRECTORY_COUNT);

    Create_Table_for_Listing(); //sets up table with empty <tbody></tbody>

    echo "</form>\n\n\n";
    echo "<script>\n";
    echo "var DIRECTORY_DATA = $json_encoded_DIRECTORY_DATA;\n";
    echo "var DIRECTORY_ITEMS = DIRECTORY_DATA.length;\n";
    echo "</script>\n";

    init_ICONS_js();
    Index_Page_scripts();
    Index_Page_events();
}//end Index_Page() //**********************************************************

function Edit_Page_buttons_top($text_editable,$file_ENC, $file_stats) {//*******
    global $_, $ONESCRIPT, $param1, $param2, $filename, $filename_OS, $IS_OFCMS,
        $WYSIWYG_VALID, $EDIT_WYSIWYG, $WYSIWYG_label, $MESSAGE;

    clearstatcache();

    //[View Raw] button.
    if ($text_editable) {
        $view_raw_button = '<button type=button id=view_raw class=button>'.hsc('View Raw')."</button>\n";
    } else {
        $view_raw_button = '';
    }

    //[Wide View] / [Normal View] button.  Label is what button will do, not an indicator the current state.
    $wide_view_button = "";
    if ($text_editable && !$EDIT_WYSIWYG) {
        if ($_COOKIE['wide_view'] === "on") {
            $wv_label = hsc($_['Normal_View']);
        } else {
            $wv_label = hsc($_['Wide_View']);
        }
        $wide_view_button = "<button type=button id=wide_view class=button value={$_COOKIE['wide_view']}>$wv_label</button>\n";
    }

    //[Edit WYSIWYG] / [Edit Source] button.
    $WYSIWYG_button  = '';
    if ($text_editable && $WYSIWYG_VALID && !$IS_OFCMS) { //Only show when needed/applicable
        //Set current mode for Edit page, and label for [Edit WYSIWIG/Source] button
        if ( isset($_COOKIE['edit_wysiwyg']) && ($_COOKIE['edit_wysiwyg'] == '1')) {
            //wysiwyg mode
            $EDIT_WYSIWYG = '1';
            $WYSIWYG_label = $_['Source'];
        } else {
            //plain text mode
            $EDIT_WYSIWYG = '0';
            $WYSIWYG_label = $_['WYSIWYG'];
        }

        $WYSIWYG_button  = '<button type=button id=edit_WYSIWYG class=button>';
        $WYSIWYG_button .= hsc($_['Edit']).' '.hsc($WYSIWYG_label).'</button>';
    }

    //[Close] button
    $close_button = '<button type=button id=close1 class=button>'.hsc($_['Close']).'</button>';

?>
    <div class="edit_btns_top">
        <div class="file_meta">
            <span class="file_size">
                <?= hsc($_['meta_txt_01']).' '.number_format($file_stats['size']).' '.hsc($_['bytes']); ?>
            </span>	&nbsp;
            <span class="file_time">
                <?= hsc($_['meta_txt_03']).' <script>FileTimeStamp('.$file_stats['mtime'].', 1, 1, 1);</script>'; ?>
                <?= '&nbsp; '.$file_ENC; ?>
            </span><br>
        </div>

        <div class="buttons_right">
            <?= $view_raw_button  ?>
            <?= $wide_view_button ?>
            <?= $WYSIWYG_button   ?>
            <?= $close_button     ?>
        </div>
        <div class=clear></div>
    </div>
<?php
}//end Edit_Page_buttons_top() //***********************************************

function Edit_Page_buttons($text_editable, $too_large_to_edit, $writable) {//***
    global $_, $MESSAGE, $ICONS, $MAX_IDLE_TIME, $IS_OFCMS, $WYSIWYG_VALID, $EDIT_WYSIWYG, $filename_OS;

    //Using ckeditor WYSIWYG editor, <input type=reset> button doesn't work. (I don't know why.)
    $reset_button = '<input type=reset  id="reset" class=button value="'.hsc($_['reset']).'" onclick="return Reset_File();">';
    if ($WYSIWYG_VALID && $EDIT_WYSIWYG) {
        $reset_button = '';
    }

    echo '<div class="edit_btns_bottom">';

    if ($text_editable && !$too_large_to_edit && !$IS_OFCMS && $writable) { //Show save & reset only if editable file
        echo '<span id=timer1  class="timer"></span>';
        echo '<button type="submit" class="button" id="save_file">'.hsc($_['save_1']).'</button>'; //Submit Button
        echo $reset_button;
    }//end if editable

    function RCD_button($action, $icon, $label) {//***************
        global $ICONS;
        echo '<button type=button id="'.$action.'_btn" class="button RCD">'.$ICONS[$icon].'&nbsp;'.hsc($label).'</button>';
    }//end RCD_button() //****************************************

    //Don't show [Rename] or [Delete] if viewing OneFileCMS itself.
    if (!$IS_OFCMS && $writable) {
        RCD_button('renamefile', 'ren_mov', $_['Ren_Move']);
    }
    /***  Always show Copy  ***/ { RCD_button('copyfile'  , 'copy'   , $_['Copy']); }
    if (!$IS_OFCMS && $writable) {
        RCD_button('deletefile', 'delete' , $_['Delete']);
    }

    echo '</div>';

}//end Edit_Page_buttons() //***************************************************

//******************************************************************************
function Edit_Page_form($ext, $text_editable, $too_large_to_edit, $too_large_to_view, $file_ENC){
    global $_, $ONESCRIPT, $param1, $param2, $param3, $filename, $filename_OS, $ITYPES, $INPUT_NUONCE, $EX, $MESSAGE,
        $FILECONTENTS, $WYSIWYG_VALID, $EDIT_WYSIWYG, $IS_OFCMS, $MAX_EDIT_SIZE, $MAX_VIEW_SIZE, $LINE_WRAP;

    //Line-wrap on or off?  $LINE_WRAP default value set in configuration section.
    //Used to set initial value of on/off button below textarea. A default value is in config section.
    if (isset($_COOKIE['line_wrap'])) {
        if (($_COOKIE['line_wrap'] === "on") || ($_COOKIE['line_wrap'] === "off")) {
            $LINE_WRAP = $_COOKIE['line_wrap'];
        }
    }

    $too_large_to_edit_message =
        '<b>'.hsc($_['too_large_to_edit_01']).' '.number_format($MAX_EDIT_SIZE).' '.hsc($_['bytes']).'</b><br>'.
        hsc($_['too_large_to_edit_02']).'<br>'.hsc($_['too_large_to_edit_03']).'<br>'.hsc($_['too_large_to_edit_04']);

    $too_large_to_view_message =
        '<b>'.hsc($_['too_large_to_view_01']).' '.number_format($MAX_VIEW_SIZE).' '.hsc($_['bytes']).'</b><br>'.
        hsc($_['too_large_to_view_02']).'<br>'.hsc($_['too_large_to_view_03']).'<br>';

    clearstatcache();

    $file_stats = Get_File_Stats($filename_OS);
    $file_perms = Format_Perms($file_stats['perms']);
    $writable   = $file_stats['is_writable'];

    if (!$writable) {
        $MESSAGE .= "<span class=mono>";
        $MESSAGE .= $file_perms." ".$file_stats['owner']." ".$file_stats['group']." :".get_current_user().":</span> ";
        $MESSAGE .= $_['edit_txt_05']." ".$_['edit_txt_00']."<br>";
    }

    echo "\n".'<form id=edit_form name=edit_form method=post action="'.$ONESCRIPT.$param1.$param2.$param3.'">'."\n";

    echo $INPUT_NUONCE;

    Edit_Page_buttons_top($text_editable, $file_ENC, $file_stats);

    if ( !in_array( mb_strtolower($ext), $ITYPES) ) { //If non-image...

        //Did htmlspecialchars return an empty string from a non-empty file?
        $bad_chars = ( ($FILECONTENTS == "") && ($file_stats['size'] > 0) );

        if (!$text_editable) {
            $MESSAGE .= hsc($_['edit_txt_01']).'<br>';
        } elseif ( $text_editable && $too_large_to_view ) {
            echo '<p class="message_box_contents">'.$too_large_to_view_message.'</p>';
        } else {

            if ($IS_OFCMS || $too_large_to_edit || !$writable) {
                $readonly = "readonly";
            } else {
                $readonly = "";
            }

            if ( $too_large_to_edit ) {
                $MESSAGE .= $too_large_to_edit_message;
            }

            if ($bad_chars){ //Show message: possible bad character in file
                echo '<pre class="edit_disabled">'.$EX.hsc($_['edit_txt_02']).'<br>';
                echo hsc($_['edit_txt_03']).'<br>';
                echo hsc($_['edit_txt_04']).'<br></pre>'."\n";
            } else {          //show editable <textarea>

                //<input name=filename> is used only to signal an Edit_response().
                echo '<input type=hidden name=filename value="'.rawurlencode($filename).'">';

                echo "<div id=wrapper_linenums_editor>\n";
                echo 	"<div id=line_numbers tabindex='-1'><div id=line_1>1</div><div id=line_0></div></div>\n";
                echo 	"<textarea $readonly id=file_editor name=contents cols=70 rows=25>$FILECONTENTS</textarea>\n";
                echo "</div>\n";

                $wrap_on_off  = hsc($_['Line_Wrap'])." ";
                $wrap_on_off .= "<span id=w_on>" .hsc($_['on']) ."</span>/";
                $wrap_on_off .= "<span id=w_off>".hsc($_['off'])."</span>";

                echo "<button type=button class=button id=toggle_wrap name=toggle_wrap value=$LINE_WRAP>$wrap_on_off</button>";
            }
        }//end if/elseif...

    }//end if non-image

    Edit_Page_buttons($text_editable, $too_large_to_edit, $writable);
    echo "\n</form>\n";

    Edit_Page_scripts();

    if ( !$IS_OFCMS && $text_editable && !$too_large_to_edit && !$bad_chars && $writable) {
        Edit_Page_Notes();
    }
}//end Edit_Page_form() //******************************************************

function Edit_Page_Notes() {//**************************************************
    global $_, $MAX_IDLE_TIME;
    $SEC = $MAX_IDLE_TIME;
    $HRS = floor($SEC/3600);
    $SEC = fmod($SEC,3600);
    $MIN = floor($SEC/60);   if ($MIN < 10) { $MIN = "0".$MIN; };
    $SEC = fmod($SEC,60);    if ($SEC < 10) { $SEC = "0".$SEC; };
    $HRS_MIN_SEC = $HRS.':'.$MIN.':'.$SEC;
?>
            <div id="edit_notes">
                <div class="notes"><?= hsc($_['edit_note_00']) ?></div>
                <div class="notes"><b>1)
                    <?= hsc($_['edit_note_01a']).' $MAX_IDLE_TIME '.hsc($_['edit_note_01b']) ?>
                    <?= ' '.$HRS_MIN_SEC.'. '.hsc($_['edit_note_02']) ?></b>
                </div>
                <div class="notes"><b>2) </b> <?= hsc($_['edit_note_03']) ?></div>
            </div>
<?php
}//end Edit_Page_Notes() //*****************************************************

function Edit_Page() {//********************************************************
    global $_, $filename, $filename_OS, $FILECONTENTS, $ETYPES, $ITYPES, $EX, $MESSAGE, $page,
        $MAX_EDIT_SIZE, $MAX_VIEW_SIZE, $WYSIWYG_VALID, $IS_OFCMS, $DOC_ROOT;

    //Get "path/filename" relative to root of website (instead of filesystem).
    $len_doc_root = strlen(trim($DOC_ROOT,'/'));
    $filename1 = substr($filename,$len_doc_root);

    //Determine if a text editable file type
    $filename_parts = explode(".", mb_strtolower($filename));
    $ext = end($filename_parts);
    if ( in_array($ext, $ETYPES) ) { $text_editable = TRUE;  }
    else                           { $text_editable = FALSE; }

    $too_large_to_edit = (filesize($filename_OS) > $MAX_EDIT_SIZE);
    $too_large_to_view = (filesize($filename_OS) > $MAX_VIEW_SIZE);

    //Don't load $WYSIWYG_PLUGIN if not needed
    if (!$text_editable || $too_large_to_edit) {$WYSIWYG_VALID = 0;}

    //Get file contents
    if (($text_editable && !$too_large_to_view) || $IS_OFCMS) {
        $raw_contents = file_get_contents($filename_OS);
        $file_ENC = mb_detect_encoding($raw_contents); //ASCII, UTF-8, ISO-8859-1, etc...
        if ($file_ENC != 'UTF-8') { $raw_contents = mb_convert_encoding($raw_contents, 'UTF-8', $file_ENC); }
    }else{
        $file_ENC     = "";
        $raw_contents = "";
    }


    if (PHP_VERSION_ID < 50400) { $FILECONTENTS = hsc($raw_contents); }
    else						{ $FILECONTENTS = htmlspecialchars($raw_contents,ENT_SUBSTITUTE | ENT_QUOTES, 'UTF-8');	}

    if     ($too_large_to_view || !$text_editable)				 { $header2 = "";}
    elseif ($text_editable && !$too_large_to_edit && !$IS_OFCMS) { $header2 = hsc($_['edit_h2_2']); }
    else									  					 { $header2 = hsc($_['edit_h2_1']); }

    echo '<h2 id="edit_header">'.$header2.' ';
    echo '<a class="h2_filename" href="'.URLencode_path($filename1).'" target="_blank" title="'.hsc($_['Open_View']).'">';
    echo hsc(basename($filename)).'</a>';
    echo '</h2>'."\n";

    Edit_Page_form($ext, $text_editable, $too_large_to_edit, $too_large_to_view, $file_ENC);

    if ( in_array( $ext, $ITYPES) ) { Show_Image($filename1); } //If image, show below the [Rename/Move] [Copy] [Delete] buttons

    echo '<div class=clear></div>';

    //If viewing OneFileCMS itself, show Edit Disabled message.
    if ($IS_OFCMS && $page == "edit") {
        $MESSAGE .= '<style>.message_box_contents {background: red;}</style>';
        $MESSAGE .= '<style>#message_box          {color: white;}   </style>';
        $MESSAGE .= '<b>'.$EX.hsc($_['edit_caution_02']).' &nbsp; '.$_['edit_txt_00'].'</b><br>';
    }
}//end Edit_Page() //***********************************************************

function Edit_response() {//***If on Edit page, and [Save] clicked *************
    global $_, $EX, $MESSAGE, $filename, $filename_OS;

    $contents    = $_POST['contents'];

    $contents = str_replace("\r\n", "\n", $contents); //Normalize EOL
    $contents = str_replace("\r"  , "\n", $contents); //Normalize EOL

    $bytes = file_put_contents($filename_OS, $contents);

    if ($bytes !== false) {
        $MESSAGE .= '<b>'.hsc($_['edit_msg_01']).' '.number_format($bytes).' '.hsc($_['edit_msg_02']).'</b><br>';
    }else{
        $MESSAGE .= $EX.'<b>'.hsc($_['edit_msg_03']).'</b><br>';
    }
}//end Edit_response() //*******************************************************

function Upload_Page() {//******************************************************
    global $_, $ONESCRIPT, $ipath, $param1, $INPUT_NUONCE, $UPLOAD_FIELDS, $MAIN_WIDTH;

    $max_file_uploads = ini_get('max_file_uploads');
    if ($max_file_uploads < 1) {
        $max_file_uploads = $UPLOAD_FIELDS;
    }

    if ($max_file_uploads < $UPLOAD_FIELDS) {
        $UPLOAD_FIELDS = $max_file_uploads;
    }

    //$main_width is used below to determine size (width) of <input type=file> in FF.
    $main_width = $MAIN_WIDTH * 1;   //set in config section. Default is 810px.
    $main_units = mb_substr($MAIN_WIDTH, -2); //should be px, pt, or em.
    //convert to px.  16px = 12pt = 1em
    if     ( $main_units == "em") {
        $main_width = $main_width * 16;
    } elseif ( $main_units == "pt") {
        $main_width = $main_width * (16 / 12);
    }

    echo '<h2>'.hsc($_['Upload_File']).'</h2>';
    echo '<p>';
    echo hsc($_['upload_txt_03']).' '.ini_get('upload_max_filesize').' '.hsc($_['upload_txt_01']).'<br>';
    echo hsc($_['upload_txt_04']).' '.ini_get('post_max_size')      .' '.hsc($_['upload_txt_02']).'<br>';

    echo '<form enctype="multipart/form-data" action="'.$ONESCRIPT.$param1.'&amp;p=uploaded" method="post">';
    echo $INPUT_NUONCE;

    echo '<div class="action"><LABEL>'.hsc($_['upload_txt_05']).'</LABEL></div>';
    echo '<div class="ren_over">'; //So <LABEL>'s wrap w/o word breaks if $MAIN_WIDTH is narrow.
    echo '<label><INPUT TYPE=radio NAME=ifexists VALUE=rename checked> '.hsc($_['upload_txt_06']).'</label>';
    echo '<label><INPUT TYPE=radio NAME=ifexists VALUE=overwrite     > '.hsc($_['upload_txt_07']).'</label>';
    echo '</div>';

    for ($x = 0; $x < $UPLOAD_FIELDS; $x++) {
        //size attibute is for FF (and is not em, px, pt, or %).
        //width attribute is for IE & Chrome, and can be set via css (in style_sheet()).
        //In FF, width of <input type="file" size=1> is 121px. If size=2, width = 128, etc. The base value is 114px.
        echo '<input type="file" name="upload_file[]" size="'.floor(($main_width - 114) / 7).'"><br>'."\n";
    }
    echo '<p>';
    Cancel_Submit_Buttons($_['Upload']);
    echo "\n</form>\n";
}//end Upload_Page() //*********************************************************

function Upload_response() {//**************************************************
    global $_, $ipath, $ipath_OS, $page, $EX, $MESSAGE, $UPLOAD_FIELDS;

    $page  = "index"; //return to index.

    $filecount = 0;
    foreach ($_FILES['upload_file']['name'] as $N => $name) {
        if ($name == "") { continue; } //ignore empty upload fields

        $filecount++;
        $filename_up = $ipath.$_FILES['upload_file']['name'][$N]; //just filename, no path.
        $filename_OS = Convert_encoding($filename_up);

        $savefile_msg = '';

        $MAXUP1 = ini_get('upload_max_filesize');
        //$MAXUP2 = ''; //number_format($_POST['MAX_FILE_SIZE']).' '.hsc($_['bytes']);
        $ERROR = $_FILES['upload_file']['error'][$N];

        if     ( $ERROR == 1 ) {
            $ERRMSG = hsc($_['upload_err_01']).' upload_max_filesize = '.$MAXUP1;
        } elseif (($ERROR > 1) && ($ERROR < 9)) {
            $ERRMSG = hsc($_['upload_err_0'.$ERROR]);
        } else {
            $ERRMSG = '';
        }

        if ( ($ipath === false) || (($ipath != "") && !is_dir($ipath_OS))) {
            $MESSAGE .= $EX.'<b>'.hsc($_['upload_msg_02']).'</b><br>';
            $MESSAGE .= '<span class="filename">'.hsc($ipath).'</span></b><br>';
            $MESSAGE .= hsc($_['upload_msg_03']).'</b><br>';
        }else{
            $MESSAGE .= '<b>'.hsc($_['upload_msg_04']).'</b> <span class="filename">'.hsc(basename($filename_up)).'</span><br>';

            if ( isset($_POST['ifexists']) && ($_POST['ifexists'] == 'overwrite') ) {
                if (is_file($filename_OS)) {
                    $savefile_msg .= hsc($_['upload_msg_07']);
                }
            }else{ //rename to "file.etc.001"  etc...
                $filename_up = add_serial_num($filename_up, $savefile_msg);
            }

            $filename_OS = Convert_encoding($filename_up);
            if(move_uploaded_file($_FILES['upload_file']['tmp_name'][$N], $filename_OS)) {
                $MESSAGE .= '<b>'.hsc($_['upload_msg_05']).'</b> '.$savefile_msg.'<br>';
            } else {
                $MESSAGE .= '<b>'.$EX.hsc($_['upload_msg_06']).'</b> '.$ERRMSG.'</b><br>';
            }
        }
    }//end foreach $_FILES

    if ($filecount == 0) { $page = "upload"; } //If nothing selected, just reload Upload page.
}//end Upload_response() //*****************************************************

function New_Page($title, $new_f_or_f) {//**********************************************
    global $_, $FORM_COMMON, $INVALID_CHARS;

    echo '<h2>'.hsc($title).'</h2>';
    echo $FORM_COMMON;
    echo '<p>'.hsc($_['new_file_txt_01'].' '.$_['new_file_txt_02']);
    echo '<span class="mono"> '.hsc($INVALID_CHARS).'</span></p>';
    echo '<input type="text" name="'.$new_f_or_f.'" id="'.$new_f_or_f.'" value=""><p>';
    Cancel_Submit_Buttons($_['Create']);
    echo "\n</form>\n";
}//end New_Page() //************************************************************

function New_response($post, $isfile) {//***************************************
    global $_, $ipath, $ipath_OS, $filename, $filename_OS, $page, $param1, $param2, $param3, $MESSAGE, $EX, $INVALID_CHARS, $WHSPC_SLASH;

    $page      = "index"; //Return to index if folder, or on error.

    $new_name     = trim($_POST[$post], $WHSPC_SLASH); //Trim whitespace & slashes.

    $filename     = $ipath.$new_name;
    $filename_OS  = Convert_encoding($filename);

    if ($isfile) { $f_or_f = "file";   }
    else         { $f_or_f = "folder"; }

    $msg_new  = '<span class="filename">'.hsc($new_name).'</span><br>';

    if (has_invalid_char($new_name)){
        $MESSAGE .= $EX.'<b>'.hsc($_['new_file_msg_01']).'</b> '.$msg_new;
        $MESSAGE .= '<b>'.hsc($_['new_file_msg_02']).'<span class="mono"> '.hsc($INVALID_CHARS).'</span></b>';

    } elseif ($new_name == "") { //No new name given.
        $page   = "new".$f_or_f;
        $param3 = '&amp;p=index'; //For [Cancel] button

    } elseif (file_exists($filename_OS)) { //Does file or folder already exist ?
        $MESSAGE .= $EX.'<b>'.hsc($_['new_file_msg_04']).' '.$msg_new;

    } elseif ($isfile && touch($filename_OS) ) { //Create File
        $MESSAGE .= '<b>'.hsc($_['new_file_msg_05']).'</b> '.$msg_new; //New File success.
        $page     = "edit";                                      //Return to edit page.
        $param2   = '&amp;f='.rawurlencode(basename($filename)); //for Edit_Page() buttons
        $param3   = '&amp;p=edit';                               //for Edit_Page() buttons

    } elseif (!$isfile && mkdir($filename_OS,0755)) { //Create Folder
        $MESSAGE .= '<b>'.hsc($_['new_file_msg_07']).'</b> '.$msg_new; //New folder success
        $ipath    = $filename;  //return to new folder
        $ipath_OS = Convert_encoding($filename);
        $param1   = '?i='.URLencode_path($ipath);

    } else {
        $MESSAGE .= $EX.'<b>'.hsc($_['new_file_msg_01']).':</b><br>'.$msg_new; //'Error - new file not created:'
    }
}//end New_response() //********************************************************

function Set_Input_width() {//**************************************************
    global $_, $MAIN_WIDTH, $ACCESS_ROOT;

    // Adjust (shorten) <input> width based on width of $ACCESS_ROOT
    // (width of <input type=text>) = $MAIN_WIDTH - (Width of <label>) - (width of <span> / </span>)
    // $MAIN_WIDTH: Set in config section, may be in em, px, pt, or %. Ignoring % for now.
    // Width of 1 character = .625em = 10px = 7.5pt  (1em = 16px = 12pt)

    $main_units  = mb_substr($MAIN_WIDTH, -2);
    $main_width  = $MAIN_WIDTH * 1;

    $root_width  = mb_strlen('/'.$ACCESS_ROOT);
    $label_width = mb_strlen($_['New_Location']);

    //convert to em
    $root_width  *= .625;
    $label_width *= .625;
    if ($main_units == "px") {
        $main_width = $main_width / 16;
    } elseif ($main_units == "pt") {
        $main_width = $main_width / 12;
    }

    //The .4 at the end is needed for some rounding erros above. Or something... I don't know.
    $input_type_text_width = ($main_width - $label_width - $root_width - .4).'em';

    echo '<style>input[type="text"] {width: '.$input_type_text_width.';}';
    echo 'label {display: inline-block; width: '.$label_width.'em; }</style>';

}//end Set_Input_width() //*****************************************************

function CRM_Page($action, $title, $action_id, $old_full_name) {//*******************
    //$action    = 'Copy' or 'Rename'.
    //$action_id = 'copy_file' or 'rename_file'
    global $_, $ipath, $param1, $filename, $FORM_COMMON, $ACCESS_ROOT, $ACCESS_PATH, $MESSAGE;

    $new_full_name = $old_full_name; //default

    if (is_dir(Convert_encoding($old_full_name))) {
        $param1 = '?i='.dir_name($ipath); //If dir, return to parent on [Cancel]
        $ACCESS_PATH = dir_name($ACCESS_PATH);
    }

    Set_Input_width();

    echo '<h2>'.hsc($action.' '.$title).'</h2>';

    echo $FORM_COMMON;
    echo '<input type="hidden" name="'.hsc($action_id).'"  value="'.hsc($action_id).'">';
    echo '<input type="hidden" name=old_full_name     value="'.hsc($old_full_name).'">';

    echo '<label>'.hsc($_['CRM_txt_04']).':</label>';
    echo '<input type=text name=new_name id=new_name value="'.hsc(basename($new_full_name)).'"><br>';

    echo '<label>'.hsc($_['New_Location']).':</label>';
    echo '<span class="web_root">'.hsc('/'.$ACCESS_ROOT).'</span>';
    echo '<input type=text name=new_location id=new_location value="'.hsc($ACCESS_PATH).'"><br>';

    echo '('.hsc($_['CRM_txt_02']).')<p>';
    Cancel_Submit_Buttons($action);
    echo "\n</form>\n";
}//end CRM_Page() //************************************************************

function CRM_response($action, $msg1, $show_message = 3) {//********************
    //$action = 'rCopy' or 'rename'.  Returns 0 if successful, 1 on error.
    //$show_message: 0 = none; 1 = errors only; 2 = successes only; 3 = all messages (default).
    global $_, $ONESCRIPT, $ipath, $ipath_OS, $filename, $page, $param1, $param2, $param3,
        $MESSAGE, $EX, $INVALID_CHARS, $WHSPC_SLASH;

    $old_full_name = trim($_POST['old_full_name'], $WHSPC_SLASH);     //Trim whitespace & slashes.
    $new_name_only = trim($_POST['new_name'], $WHSPC_SLASH);
    $new_location  = trim($_POST['new_location'], $WHSPC_SLASH);
    if ($new_location != "") {
        $new_location .= '/';
    }
    $new_full_name = $new_location.$new_name_only;
    $filename      = $old_full_name; //default if error.

    //for function calls that access the server file system, such as rCopy, rename, file_exists, etc...
    $old_full_name_OS = Convert_encoding($old_full_name);
    $new_full_name_OS = Convert_encoding($new_full_name);
    $new_location_OS  = Convert_encoding($new_location);

    $isfile = 0; if (is_file($old_full_name_OS)) { $isfile = 1;} //File or folder?

    //Common message lines
    $com_msg  = '<div id="message_left">'.hsc($_['From']).'<br>'.hsc($_['To']).'</div>';
    $com_msg .= '<b>: </b><span class="filename">'.hsc($old_full_name).'</span><br>';
    $com_msg .= '<b>: </b><span class="filename">'.hsc($new_full_name).'</span><br>';

    $bad_name = ""; //bad file or folder name (can be either old_ or new_)

    $err_msg = ''; //Error message.
    $scs_msg = ''; //Success message.

    $error_code = 0; //1 = success (no error), 0 = an error. Used for return value.

    //Check old name for invalid chars (like .. ) (Unlikely to be false outside a malicious attempt)
    if ( Check_path($old_full_name,$show_message) === false ) {
        $bad_name = $old_full_name;
    }elseif ( !file_exists($old_full_name_OS) ) {
        $err_msg .= $EX.'<b>'.hsc($msg1.' '.hsc($_['CRM_msg_02'])).'</b><br>';
        $bad_name = $old_full_name;
        //Ignore if new name is blank.
    }elseif ( mb_strlen($new_name_only) == 0 ) {
        $page = 'copyfile';
        $param3 = '&amp;p=copyfile';
        return 0;
        //Check new name for invalid chars, including slashes.
    }elseif ( has_invalid_char($new_name_only) ) {
        $err_msg .= $EX.'<b>'.hsc($_['new_file_msg_02']).'<span class="filename"> '.hsc($INVALID_CHARS).'</span></b><br>';
        $bad_name = $new_name_only;
        //Check new location for invalid chars etc.
    }elseif ( Check_path($new_location,$show_message) === false ) {
        $bad_name = $new_location;
        //$new_location must already exist as a directory
    }elseif ( ($new_location != "") && !is_dir($new_location_OS) ) {
        $err_msg .= $EX.'<b>'.hsc($msg1.' '.hsc($_['CRM_msg_01'])).'</b><br>';
        $bad_name = $new_location;
        //Don't overwrite existing files.
    }elseif ( file_exists($new_full_name_OS) ) {
        $bad_name = $new_full_name;
        $err_msg .= $EX.'<b>'.hsc($msg1.' '.hsc($_['CRM_msg_03'])).'</b><br>';
    }else{ //attempt $action
        $error_code = $action($old_full_name_OS, $new_full_name_OS);
        if ( $error_code > 0 ) {
            $scs_msg .= '<b>'.hsc($msg1.' '.hsc($_['successful'])).'</b><br>'.$com_msg;
            if ($isfile) {
                $filename = $new_full_name;
            }
            $ipath    = $new_location;
            $ipath_OS = $new_location_OS;
        }else{
            $err_msg .= $EX.'<b>'.hsc($_['CRM_msg_05'].' '.$msg1).'</b><br>'.$com_msg;
        }
    }//

    if (($bad_name !='' ) && ($error_code == 0)) {
        $err_msg .= '<span class="filename">'.hsc($bad_name).'</span><br>';
    }

    if (($show_message & 1) && ($error_code == 0)) {
        $MESSAGE .= $err_msg;
    } //Show error message.

    if ( $show_message & 2) {
        $MESSAGE .= $scs_msg;
    } //Show success message.

    //Prior page should be either index or edit
    $page = $_SESSION['recent_pages'][1];
    $param1 = '?i='.URLencode_path($ipath);
    if ($isfile & $page == "edit") {
        $param2 = '&amp;f='.rawurlencode(basename($filename));
    }

    return $error_code; //
}//end CRM_response() //********************************************************

function Delete_response($target, $show_message=3) {//**************************
    global $_, $ipath, $ipath_OS, $param1, $filename, $param2, $page, $MESSAGE, $EX;

    if ($target == "") { return 0; } //Prevent accidental delete of entire website.

    $target = Check_path($target,$show_message);
    $target = trim($target,'/');
    $page = "index"; //Return to index

    //If came from admin page, return there.
    if ( $_SESSION['admin_page'] ) { $page = 'admin'; }

    $err_msg = ''; //On error, set this message.
    $scs_msg = ''; //On success, set this message.

    $error_code = rDel($target);
    if ($error_code > 0) { // 0 = error, > 0 is number of successes
        $scs_msg .= '<b>'.hsc($_['Deleted']).':</b> ';
        $scs_msg .= '<span class="filename">'.hsc(basename($target)).'</span></br>';
        $ipath  = dir_name($target); //Return to parent dir.
        $ipath_OS = Convert_encoding($ipath);
        $param1 = '?i='.URLencode_path($ipath);
        $filename = "";
        $param2   = "";
    }else { //Error
        $err_msg .= $EX.'<b>'.hsc($_['delete_msg_03']).'</b> <span class="filename">'.hsc($target).'</span><br>';
        $page = $_SESSION['recent_pages'][1];
        if ($page == "edit") {
            $filename = $target;
            $param2 = '&amp;f='.basename($filename);
        }
    }

    if ($show_message & 1) { $MESSAGE .= $err_msg; } //Show error message.

    if ($show_message & 2) { $MESSAGE .= $scs_msg; } //Show success message.

    return $error_code;
}//end Delete_response() //*****************************************************

function MCD_Page($action, $page_title, $classes = '') {//**********************
    //$action = mcd_mov or mcd_cpy or mcd_del
    global $_, $ONESCRIPT, $ipath, $ipath_OS, $param1, $filename, $page,
        $ICONS, $ACCESS_ROOT, $ACCESS_PATH, $INPUT_NUONCE, $MESSAGE;

    //Prep for a single file or folder
    if( $page == "deletefile" || $page == "deletefolder" ){
        $_POST['mcdaction'] = 'delete'; //set mcdaction != copy or move (see below).

        if ($page == "deletefile") { $_POST['files'][1]  = basename($filename); }
        //If  $page == deletefolder,   $_POST['files'][1] is set in Verify_Page_Conditions()
    }

    Set_Input_width();

    echo '<h2>'.hsc($page_title).'</h2>';

    echo '<form method="post" action="'.$ONESCRIPT.$param1.'">'.$INPUT_NUONCE;
    echo '<input type="hidden" name="'.hsc($action).'" value="'.hsc($action).'">'."\n";

    if ( ($_POST['mcdaction'] == 'copy') || ($_POST['mcdaction'] == 'move') ) {
        echo '<label>'.hsc($_['New_Location']).':</label>';
        echo '<span class="web_root">'.hsc('/'.$ACCESS_ROOT).'</span>';
        echo '<input type=text   name=new_location  id=new_location value="'.hsc($ACCESS_PATH).'">';
        echo '<p>('.hsc($_['CRM_txt_02']).')</p>';
    }

    echo '<p><b>'.hsc($_['Are_you_sure']).'</b></p>';
    Cancel_Submit_Buttons($page_title);

    //List selected folders & files
    $full_list = Sort_Seperate($ipath, $_POST['files']);

    echo '<table class="verify '.$classes.'">';
    echo '<tr><th>'.hsc($_['Selected_Files']).':</th></tr>'."\n";
    foreach ($full_list as $file) {
        $file_OS = Convert_encoding($file);
        if (is_dir($ipath_OS.$file_OS)) { echo '<tr><td>'.$ICONS['folder'].'&nbsp;'.hsc($file).' /</td></tr>'; }
        else                            { echo '<tr><td>'                          .hsc($file).'</td></tr>'; }
        echo '<input type=hidden  name="files[]" value="'.hsc($file).'">'."\n";
    }

    echo '</table>';
    echo "\n</form>\n";
}//end MCD_Page() //************************************************************

function MCD_response($action, $msg1, $success_msg = '') {//********************
    global $_, $ipath, $ipath_OS, $EX, $MESSAGE, $WHSPC_SLASH;

    $files      = $_POST['files']; //List of files to delete (path not included)
    $errors     = 0; //number of failed moves, copies, or deletes
    $successful = 0;

    $new_location = "";
    if (isset($_POST['new_location'])) {
        $new_location    =                  $_POST['new_location'];
        $new_location_OS = Convert_encoding($_POST['new_location']);
    }

    $show_message = 1; //1= show error msg only.

    if ( ($new_location != "") && !is_dir($new_location_OS)) {
        $MESSAGE .= $EX.'<b>'.hsc($msg1.' '.$_['CRM_msg_01']).'</b><br>';
        $MESSAGE .= '<span class="filename">'.hsc($_POST['new_location']).'</span><br>';
        return;
    }elseif ($action == 'rDel') {
        foreach ($files as $file){
            if ($file == "") {continue;} //a blank file name would cause $ipath to be deleted.
            $error_code = Delete_response($ipath.$file, $show_message);
            $successful += $error_code;
            if ($error_code == 0) {
                $errors++;
            }
        }
    } else { //move or rCopy
        $mcd_ipath = $ipath; //CRM_response() changes $ipath to $new_location

        foreach ($files as $file){
            $_POST['old_full_name'] = $mcd_ipath.$file;
            $_POST['new_name'] = $file;
            //$_POST['new_location'] should already be set by the client ( via MCD_Page() ).
            $error_code = CRM_response($action, $msg1, $show_message);
            $successful += $error_code;
            if ($error_code == 0) {$errors++;}
        }
    }

    if ($errors) {
        $MESSAGE .= $EX.' <b>'.$errors.' '.hsc($_['errors']).'.</b><br>';
    }

    $MESSAGE .= '<b>'.$successful.' '.hsc($success_msg).'</b><br>';

    if ($action != 'rDel') {
        if ($successful > 0) { //"From:" & "To:" lines if any successes.
            $MESSAGE .= '<div id="message_left"><b>'.hsc($_['From']).'<br>'.hsc($_['To']).'</b></div>';
            $MESSAGE .= '<b>:</b><span class="filename"> '.hsc($mcd_ipath).'</span><br>';
            $MESSAGE .= '<b>:</b><span class="filename"> '.hsc($ipath).'</span><br>';
        }
    }
}//end MCD_response() //********************************************************

function Format_Perms($perms_oct) {//*******************************************
    //$perms_oct is a 3 or 4 digit octal string (7777).

    //file           file  |s s s|owner|group|world
    //permissions   t y p e|u g t|r w x|r w x|r w x
    //
    //bits          1|4 2 1|4 2 1|4 2 1|4 2 1|4 2 1
    //octal         1   7     7     7     7     7
    //
    //bits          8 4 2 1|8 4 2 1|8 4 2 1|8 4 2 1
    //hex              F       F       F       F

    $ugt = ['...', '..t', '.g.', '.gt', 'u..', 'u.t', 'ug.', 'ugt']; //SetUid SetGid sTicky
    $rwx = ['---', '--x', '-w-', '-wx', 'r--', 'r-x', 'rw-', 'rwx'];

    if (strlen($perms_oct) > 3) {
        $ugidsticky = substr($perms_oct, -4, 1);
    } else {
        $ugidsticky = 0;
    }
    $owner = substr($perms_oct, -3, 1);
    $group = substr($perms_oct, -2, 1);
    $world = substr($perms_oct, -1, 1);

    return "[$perms_oct][".$ugt[$ugidsticky]." ".$rwx[$owner]." ".$rwx[$group]." ".$rwx[$world]."]";

}//end Format_Perms() {//*******************************************************

function Update_File_Permissions() {//******************************************
    //Validate new_perms & update.
    //$_POST['new_perms'] must be an octal value with 3 or 4 digits (0-7) only.
    global $_, $MESSAGE;

    $new_perms   = trim($_POST['new_perms']);
    $len         = strlen($new_perms);
    $errors      = 0; //No errrors
    $ipath		 = $_POST['ipath'];
    $ipath_OS	 = Convert_encoding($ipath);
    $filename	 = $_POST['perms_filename'];
    $filename_OS = Convert_encoding($ipath.$filename); //Full path/filename

    //Verify that each digit is octal (0-7), and that $new_perms is only 3 or 4 digits in length.
    if (preg_match("/^[0-7]{3,4}$/", $new_perms) != 1) {
        $errors++;
    }

    if ($errors > 0) {
        $MESSAGE .= "<b>".$_['Invalid'].": [$new_perms]</b> ".$_['Permissions_msg_1'].".";
    }

    //Validate path & filename. Valid_Path() required to prevent access outside $ACCESS_ROOT.
    if (!Valid_Path($ipath_OS, false)) {
        $errors++; $MESSAGE .= $_['Invalid_path'].". \n";
    }

    if (!file_exists($filename_OS)) {
        $errors++; $MESSAGE .= $_['get_get_msg_01']."\n ";
    }

    if ($errors == 0) {
        //Update the file permissions...
        if (!chmod($filename_OS, octdec($new_perms))) {
            $errors++;
            $MESSAGE .= "<b>".$_['Update_failed'].":</b> <span ";
            $MESSAGE .= "class=mono>chmod(\"$filename_OS\", octdec($new_perms))</span>";
        }
    }
    clearstatcache();
    $new_perms = decoct((fileperms($filename_OS) & 07777)); //May not actually be new, if chmod() failed.
    $new_perms = str_pad($new_perms, 3, "0", STR_PAD_LEFT); //Always at least three digits:  000

    if ($errors == 0) {
        $MESSAGE .= "<b>".hsc($_['meta_txt_03'])."</b> ";
        $MESSAGE .= "<span class=mono> <b>".Format_Perms($new_perms)."</b> ".hsc($filename)."</span><br>";
    }

    $new_perms_response = "";
    $new_perms_response['new_perms'] 	  = $new_perms;
    $new_perms_response['perms_filename'] = $ipath.$filename;
    $new_perms_response['nuonce']		  = $_SESSION['nuonce'];
    $new_perms_response['early_output']   = ob_get_clean(); //Should always be empty unless error or trouble-shooting.
    $new_perms_response['errors']		  = $errors."";
    $new_perms_response['MESSAGE']		  = $MESSAGE;
    $new_perms_response['writable']		  = is_writable($filename_OS) * 1; //1 or 0 (true or false)
    echo json_encode($new_perms_response);
}//end Update_File_Permissions() //*********************************************

function Page_Title() {//***<title>Page_Title()</title>*************************
    global $_, $page;

    if     (!$_SESSION['valid'])     { return $_['Log_In'];        }
    elseif ($page == "admin")        { return $_['Admin_Options']; }
    elseif ($page == "hash")         { return $_['Generate_Hash']; }
    elseif ($page == "changepw")     { return $_['pw_change'];     }
    elseif ($page == "changeun")     { return $_['un_change'];     }
    elseif ($page == "edit")         { return $_['Edit_View'];     }
    elseif ($page == "upload")       { return $_['Upload_File'];   }
    elseif ($page == "newfile")      { return $_['New_File'];      }
    elseif ($page == "copyfile" )    { return $_['Copy_Files'];    }
    elseif ($page == "copyfolder" )  { return $_['Copy_Files'];    }
    elseif ($page == "renamefile")   { return $_['Ren_Move'].' '.$_['File'];}
    elseif ($page == "deletefile")   { return $_['Del_Files'];     }
    elseif ($page == "deletefolder") { return $_['Del_Files'];     }
    elseif ($page == "newfolder")    { return $_['New_Folder'];    }
    elseif ($page == "renamefolder") { return $_['Ren_Folder'];    }
    elseif ($page == "mcdaction" && ($_POST['mcdaction'] == "copy") )   { return $_['Copy_Files'];}
    elseif ($page == "mcdaction" && ($_POST['mcdaction'] == "move") )   { return $_['Move_Files'];}
    elseif ($page == "mcdaction" && ($_POST['mcdaction'] == "delete") ) { return $_['Del_Files']; }
    else                             { return $_SERVER['SERVER_NAME']; }
}//end Page_Title() //**********************************************************

function Load_Selected_Page() {//***********************************************
    global $_, $ONESCRIPT, $ipath, $filename, $page;

    if     (!$_SESSION['valid'])     { Login_Page(); }
    elseif ($page == "admin")        { Admin_Page(); }
    elseif ($page == "hash")         { Hash_Page();  }
    elseif ($page == "changepw")     { Change_PWUN_Page('pw', 'password', $_['pw_change'], $_['pw_new'], $_['pw_confirm']);}
    elseif ($page == "changeun")     { Change_PWUN_Page('un', 'text',     $_['un_change'], $_['un_new'], $_['un_confirm']);}
    elseif ($page == "edit")         { Edit_Page();  }
    elseif ($page == "upload")       { Upload_Page();}
    elseif ($page == "newfile")      { New_Page($_['New_File']  , "new_file");  }
    elseif ($page == "newfolder")    { New_Page($_['New_Folder'], "new_folder");}
    elseif ($page == "copyfile")     { CRM_Page($_['Copy'],     $_['File']  , 'copy_file'  , $filename);}
    elseif ($page == "copyfolder")   { CRM_Page($_['Copy'],     $_['Folder'], 'copy_file'  , $ipath);}
    elseif ($page == "renamefile")   { CRM_Page($_['Ren_Move'], $_['File']  , 'rename_file', $filename);}
    elseif ($page == "renamefolder") { CRM_Page($_['Ren_Move'], $_['Folder'], 'rename_file', $ipath);}
    elseif ($page == "deletefile")   { MCD_Page('mcd_del', $_['Del_Files'],'verify_del'); }
    elseif ($page == "deletefolder") { MCD_Page('mcd_del', $_['Del_Files'],'verify_del'); }
    elseif ($page == "mcdaction")    {
        if ($_POST['mcdaction'] == 'move')  { MCD_Page('mcd_mov', $_['Move_Files']); }
        if ($_POST['mcdaction'] == 'copy')  { MCD_Page('mcd_cpy', $_['Copy_Files']); }
        if ($_POST['mcdaction'] == 'delete'){ MCD_Page('mcd_del', $_['Del_Files'], 'verify_del'); }
    }
    else /* default if session valid */ { Index_Page(); }
}//end Load_Selected_Page() //**************************************************

function Respond_to_POST() {//**************************************************
    global $_, $VALID_POST, $ipath, $page, $EX, $ACCESS_ROOT, $MESSAGE;

    // $_POST['key']'s must all be unique, or order of if/elseif's below becomes significant.

    if (!$VALID_POST) { return; }

    //First, validate any $_POST'ed paths against $ACCESS_ROOT.
    if (isset($_POST["old_full_name"]) && !Valid_Path($_POST["old_full_name"], false)) {
        //unlikely, but just in case
        $MESSAGE .= $EX.'<b>'.hsc($_['Invalid_path']).': </b><span class=filename>'.hsc($_POST["old_full_name"]).'</span>';
        $VALID_POST = 0;
        return;
    }
    if (isset($_POST["new_location"])) {
        $_POST["new_location"] = $ACCESS_ROOT.$_POST["new_location"];
        if (!Valid_Path($_POST["new_location"], false)) {
            $MESSAGE .= $EX.'<b>'.hsc($_['Invalid_path']).': </b><span class=filename>'.hsc($_POST["new_location"]).'</span>';
            $VALID_POST = 0;
            return;
        }
    }

    if     (isset($_POST['mcd_mov']      )) { MCD_response('rename', $_['Ren_Move'], $_['mcd_msg_01']); } //move == rename
    elseif (isset($_POST['mcd_cpy']      )) { MCD_response('rCopy' , $_['Copy']    , $_['mcd_msg_02']); }
    elseif (isset($_POST['mcd_del']      )) { MCD_response('rDel'  , $_['Delete']  , $_['mcd_msg_03']); }
    elseif (isset($_POST['whattohash']   )) { Hash_response(); }
    elseif (isset($_POST['pw']           )) { Change_PWUN_response('pw', $_['change_pw_02']);}
    elseif (isset($_POST['un']           )) { Change_PWUN_response('un', $_['change_un_02']);}
    elseif (isset($_POST['filename']     )) { Edit_response(); }
    elseif (isset($_POST['new_file']     )) { New_response('new_file'  , 1);} //1=file
    elseif (isset($_POST['new_folder']   )) { New_response('new_folder', 0);} //0=folder
    elseif (isset($_POST['rename_file']  )) { CRM_response('rename', $_['Ren_Move']);}
    elseif (isset($_POST['copy_file']    )) { CRM_response('rCopy' , $_['Copy']   );}
    elseif (isset($_FILES['upload_file']['name']))  { Upload_response(); }
    elseif (isset($_POST['new_perms']    )) { Update_File_Permissions(); } //die()'s after return from Resopnd_to_POST().

    //If Changed p/w, u/n, or other Admin Page action, make sure to not return to a folder outside of $ACCESS_ROOT.
    Valid_Path($ipath, true);
}//end Respond_to_POST() //*****************************************************

?>
