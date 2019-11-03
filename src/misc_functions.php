<?php
function System_Setup() {//*****************************************************

    global $_, $MAX_IDLE_TIME, $LOGIN_ATTEMPTS, $LOGIN_DELAYED,
        $MAIN_WIDTH, $WIDE_VIEW_WIDTH, $MAX_EDIT_SIZE, $MAX_VIEW_SIZE, $EXCLUDED_FILES, $TAB_SIZE,
        $EDIT_FILES, $SHOW_FILES, $SHOW_IMGS, $FILE_TYPES, $FILE_CLASSES,
        $SHOWALLFILES, $ETYPES, $STYPES, $ITYPES, $FTYPES, $FCLASSES, $EXCLUDED_LIST,
        $LANGUAGE_FILE, $ACCESS_ROOT, $ACCESS_ROOT_len, $WYSIWYG_PLUGIN, $WYSIWYG_VALID, $WYSIWYG_PLUGIN_OS,
        $INVALID_CHARS, $WHSPC_SLASH, $VALID_PAGES, $LOGIN_LOG_url, $LOGIN_LOG_file,
        $ONESCRIPT,  $ONESCRIPT_file, $ONESCRIPT_backup, $ONESCRIPT_file_backup,
        $CONFIG_backup, $CONFIG_FILE, $CONFIG_FILE_backup, $VALID_CONFIG_FILE,
        $DOC_ROOT, $WEBSITE, $PRE_ITERATIONS, $EX, $MESSAGE, $ENC_OS, $DEFAULT_PATH,
        $DELAY_Expired_Reload, $DELAY_Sort_and_Show_msgs, $DELAY_Start_Countdown, $DELAY_final_messages,
        $MIN_DIR_ITEMS, $DIRECTORY_COLUMNS;

    //Used to pass & display any setup $MESSAGES only if $_SESSION['valid'].
    //(So they don't display on the Login screen.)
    $setup_messages = "";

    //Requires PHP 5.1 or newer, due to changes in explode() (and maybe others).
    define('PHP_VERSION_ID_REQUIRED',50100);   //Ex: 5.1.23 is 50123
    define('PHP_VERSION_REQUIRED'  ,'5.1 + '); //Used in exit() message.
    //The predefined constant PHP_VERSION_ID has only been available since 5.2.7.
    //So, if needed, convert PHP_VERSION (a string) to PHP_VERSION_ID (an integer).
    //Ex: 5.1.23 converts to 50123.
    if (!defined('PHP_VERSION_ID')) {
        $phpversion = explode('.', PHP_VERSION);
        define('PHP_VERSION_ID', ($phpversion[0] * 10000 + $phpversion[1] * 100 + $phpversion[2]));
    }
    if( PHP_VERSION_ID < PHP_VERSION_ID_REQUIRED ) {
        exit( 'PHP '.PHP_VERSION.'<br>'.hsc($_['OFCMS_requires']).' '.PHP_VERSION_REQUIRED );
    }


    mb_detect_order("UTF-8, ASCII, Windows-1252, ISO-8859-1");


    //Get server's File System encoding.  Windows NTFS uses ISO-8859-1 / Windows-1252.
    //Needed when working with non-ascii filenames.
    if (php_uname("s") == 'Windows NT') {
        $ENC_OS = 'Windows-1252';
    } else {
        $ENC_OS = 'UTF-8';
    }

    //Allow OneFileCMS.php to be started from any dir on the site.
    //This also effects the path in an include("path/somefile.php")
    chdir('/');

    $INVALID_CHARS = '< > ? * : " | / \\'; //Illegal characters for file & folder names.  Space deliminated.
    $WHSPC_SLASH = "\x00..\x20/";  //Whitespace & forward slash. For trimming file & folder name inputs.

    //$DOC_ROOT is normalized to always be (for ex:) "server/doc/root/", instead of "C:/server/doc/root/" if on Windows.
    $ds_pos   = strpos($_SERVER['DOCUMENT_ROOT'], "/") * 1;
    $DOC_ROOT = trim(substr($_SERVER['DOCUMENT_ROOT'], $ds_pos), '/').'/';

    $WEBSITE   = $_SERVER['HTTP_HOST'].'/';

    $ONESCRIPT			   = URLencode_path($_SERVER['SCRIPT_NAME']);  //Used for URL's in HTML attributes
    $ONESCRIPT_file		   = $_SERVER['SCRIPT_FILENAME'];              //Non-url file system use.
    $ONESCRIPT_backup	   = $ONESCRIPT.'-BACKUP.txt';                 //used for p/w & u/n updates.
    $ONESCRIPT_file_backup = $ONESCRIPT_file.'-BACKUP.txt';            //used for p/w & u/n updates.
    $LOGIN_ATTEMPTS		   = $ONESCRIPT_file.'.invalid_login_attempts';//Non-url file system use.
    $LOGIN_LOG_url		   = $ONESCRIPT.'-LOGIN.log';
    $LOGIN_LOG_file		   = $ONESCRIPT_file.'-LOGIN.log';


    //If specified, check for & load $LANGUAGE_FILE
    if (isset($LANGUAGE_FILE)) {
        $LANGUAGE_FILE_OS = Convert_encoding($LANGUAGE_FILE);
        if (is_file($LANGUAGE_FILE_OS)) {
            include($LANGUAGE_FILE_OS);
        } else {
            $setup_messages .= '<b>$LANGUAGE_FILE '.hsc($_['Not_found']).":</b> ".hsc($LANGUAGE_FILE)."<br>";
        }
    }


    //If specified, validate $WYSIWYG_PLUGIN. Actual include() is at end of OneFileCMS.
    $WYSIWYG_VALID = 0; //Default to invalid.
    if (isset($WYSIWYG_PLUGIN)) {
        $WYSIWYG_PLUGIN_OS = Convert_encoding($WYSIWYG_PLUGIN); //Also used for include()
        if (is_file($WYSIWYG_PLUGIN_OS)) {
            $WYSIWYG_VALID = 1;
        } else {
            $setup_messages .= '<b>$WYSIWYG_PLUGIN '.hsc($_['Not_found']).':</b> '.hsc($WYSIWYG_PLUGIN)."<br>";
        }
    }


    //If specified & found, include $CONFIG_FILE.
    $VALID_CONFIG_FILE = 0;
    if (isset($CONFIG_FILE)) {
        $CONFIG_FILE_OS = Convert_encoding($CONFIG_FILE);
        if (is_file($CONFIG_FILE_OS)) {
            $VALID_CONFIG_FILE = 1;
            include($CONFIG_FILE_OS);
            $CONFIG_backup      = URLencode_path($CONFIG_FILE).'-BACKUP.txt'; //used for p/w & u/n updates.
            $CONFIG_FILE_backup = $CONFIG_FILE.'-BACKUP.txt';                 //used for p/w & u/n updates.
        } else {
            $setup_messages .= $EX.'<b>$CONFIG_FILE '.hsc($_['Not_found']).':</b> '.hsc($CONFIG_FILE).'<br>';
            $CONFIG_FILE = $CONFIG_FILE_OS = '';
        }
    }

    //Clean up & validate $ACCESS_ROOT
    if (!isset($ACCESS_ROOT) || $ACCESS_ROOT == '') {
        $ACCESS_ROOT = $DOC_ROOT;
    } //Make sure it's set.

    $ACCESS_ROOT = trim($ACCESS_ROOT, ' /'); //Trim to '' or 'some/path'
    if ($ACCESS_ROOT != '') {
        $ACCESS_ROOT = $ACCESS_ROOT.'/';
    }

    $ACCESS_ROOT_OS = Convert_encoding($ACCESS_ROOT);

    if (!is_dir('/'.$ACCESS_ROOT_OS)) {
        $setup_messages .= $EX.'<b>$ACCESS_ROOT '.hsc($_['Invalid']).":</b> $ACCESS_ROOT<br>";
        $ACCESS_ROOT	= $DOC_ROOT;
        $ACCESS_ROOT_OS = Convert_encoding($ACCESS_ROOT);
    }

    $ACCESS_ROOT_enc = mb_detect_encoding($ACCESS_ROOT);
    $ACCESS_ROOT_len = mb_strlen($ACCESS_ROOT, $ACCESS_ROOT_enc);


    //Clean up & validate $DEFAULT_PATH
    //It must either be = $ACCESS_ROOT, or $ACCESS_ROOT."some/valid/path/"
    if (!isset($DEFAULT_PATH) || $DEFAULT_PATH == '') {
        $DEFAULT_PATH = $ACCESS_ROOT;
    } //Make sure it's set.
    $DEFAULT_PATH = trim($DEFAULT_PATH, ' /');  //Trim to 'some/path'
    if ($DEFAULT_PATH != '') {
        $DEFAULT_PATH .= '/';
    }
    $DEFAULT_PATH_OS = Convert_encoding($DEFAULT_PATH);

    //Verify that $DEFAULT_PATH is equal to, or a decendant of, $ACCESS_ROOT.
    $needle        = realpath($ACCESS_ROOT);  //ex: /some/access/root
    $haystack      = realpath($DEFAULT_PATH); //ex: /some/access/root/some/default/path
    $needle_len    = strlen($needle);
    $valid_subpath = (substr($haystack, 0, $needle_len) === $needle);

    if (!is_dir('/'.$DEFAULT_PATH_OS)) {
        $setup_messages .= $EX.'<b>$DEFAULT_PATH '.$_['Invalid'].":</b> $DEFAULT_PATH<br>";
        $DEFAULT_PATH	 = $ACCESS_ROOT;
        $DEFAULT_PATH_OS = Convert_encoding($DEFAULT_PATH);
    } else if (!$valid_subpath) {
        $setup_messages .= $EX.'<b>'.$_['must_be_decendant'].'</b><br>';
        $setup_messages .= "\$ACCESS_ROOT = $ACCESS_ROOT<br>";
        $setup_messages .= "\$DEFAULT_PATH = $DEFAULT_PATH<br>";
        $DEFAULT_PATH	 = $ACCESS_ROOT;
        $DEFAULT_PATH_OS = Convert_encoding($DEFAULT_PATH);
    }

    $MAIN_WIDTH      = validate_units($MAIN_WIDTH);
    $WIDE_VIEW_WIDTH = validate_units($WIDE_VIEW_WIDTH);

    //Just some basic validation.  The 80 is just a round number that seems reasonable.
    $TAB_SIZE = intval($TAB_SIZE);
    if (($TAB_SIZE < 1) || ($TAB_SIZE > 80)) {
        $TAB_SIZE = 8;
    }

    ini_set('session.gc_maxlifetime', $MAX_IDLE_TIME + 100); //in case the default is less.

    $VALID_PAGES = [
        "login",
        "logout",
        "admin",
        "hash",
        "changepw",
        "changeun",
        "index",
        "edit",
        "upload",
        "uploaded",
        "newfile",
        "renamefile",
        "copyfile",
        "deletefile",
        "deletefolder",
        "newfolder",
        "renamefolder",
        "copyfolder",
        "mcdaction",
        "phpinfo",
        "raw_view"
    ];

    //Make arrays out of a few config variables for actual use later.
    //First, remove spaces and make lowercase (for *types).
    //shown file types
    $SHOWALLFILES = $STYPES = false;
    if ($SHOW_FILES == '*') {
        $SHOWALLFILES = true;
    } else {
        $STYPES   = explode(',', mb_strtolower(str_replace(' ', '', $SHOW_FILES)));
    }
    $ETYPES        = explode(',', mb_strtolower(str_replace(' ', '', $EDIT_FILES)));   //editable file types
    $ITYPES        = explode(',', mb_strtolower(str_replace(' ', '', $SHOW_IMGS)));    //images types to display
    $FTYPES        = explode(',', mb_strtolower(str_replace(' ', '', $FILE_TYPES)));   //file types with icons
    $FCLASSES      = explode(',', mb_strtolower(str_replace(' ', '', $FILE_CLASSES))); //for file types with icons
    $EXCLUDED_LIST = explode(',', str_replace(' ', '', $EXCLUDED_FILES));

    //A few variables for values that were otherwise hardcoded in js.
    //$DELAY_... values are in milliseconds.
    //The values were determined thru quick experimentation, and may be tweaked if desired, except as noted.
    $DELAY_Sort_and_Show_msgs = 20; //Needed so "Working..." message shows during directory sorts. Mostly for Firefox.
    $DELAY_Start_Countdown    = 25; //Needs to be > than $Sort_and_Show_msgs. Used in Timeout_Timer().
    $DELAY_final_messages     = 25; //Needs to be > than $Sort_and_Show_msgs. Delays final Display_Messages().
    $DELAY_Expired_Reload     = 10000; //Delay from Session Expired to page load of login screen. Ten seconds, but can be less/more.
    $MIN_DIR_ITEMS            = 25; //Minimum number of directory items before "Working..." message is needed/displayed.


    //Validate wide_view cookie...
    if ( !isset($_COOKIE['wide_view']) || ($_COOKIE['wide_view'] !== "on") ) {
        $_COOKIE['wide_view'] = "off";
    }

    //This will probably never change, again...
    //Value deterimined in Create_Table_for_Listing(), and used in Assemble_Insert_row() & Init_Dir_table_rows().
    $DIRECTORY_COLUMNS = 10;

    //Used in hashit() and js_hash_scripts().  IE<9 is WAY slow, so keep it low.
    //For 200 iterations: (time on IE8) > (37 x time on FF). And the difference grows with the iterations.
    //If you change this, or any other aspect of either hashit() or js_hash_scripts(), do so while logged in.
    //Then, manually update your password as instructed on the Admin/Generate Hash page.
    $PRE_ITERATIONS = 10000;

    return $setup_messages;
}//end  System_Setup() //*******************************************************




function Default_Language() { // ***********************************************
    global $_;
    // OneFileCMS Language Settings v3.6.09  (Not always in sync with OFCMS version#, if no changes to displayed wording.)

    $_['LANGUAGE'] = 'English';
    $_['LANG'] = 'EN';

    // If no translation or value is desired for a particular setting, do not delete
    // the actual setting variable, just set it to an empty string.
    // For example:  $_['some_unused_setting'] = '';
    //
    // Remember to slash-escape any single quotes that may be within the text:  \'
    // The back-slash itself may or may not also need to be escaped:  \\
    //
    // If present as a trailing comment, "## NT ##" means 'Needs Translation'.
    //
    // These first few settings control a few font and layout settings.
    // In some instances, some langauges may use significantly longer words or phrases than others.
    // So, a smaller font or less spacing may be desirable in those places to preserve page layout.
    $_['front_links_font_size']  = '1.0em';   //Buttons on Index page.
    $_['front_links_margin_L']   = '1.0em';
    $_['MCD_margin_R']           = '1.0em';   //[Move] [Copy] [Delete] buttons
    $_['button_font_size']       = '0.9em';   //Buttons on Edit page.
    $_['button_margin_L']        = '0.7em';
    $_['button_padding']         = '4px 4px 4px 4px'; //T R B L  ,or,   V H   if only two values.
    $_['image_info_font_size']   = '1em';     //show_img_msg_01  &  _02
    $_['image_info_pos']         = '';        //If 1 or true, moves the info down a line for more space.
    $_['select_all_label_size']  = '.84em';   //Font size of $_['Select_All']
    $_['select_all_label_width'] = '72px';    //Width of space for $_['Select_All']

    $_['HTML']    = 'HTML';
    $_['WYSIWYG'] = 'WYSIWYG'; //...

    $_['Admin']      = 'Admin';
    $_['bytes']      = 'bytes';
    $_['Cancel']     = 'Cancel';
    $_['cancelled']  = 'cancelled';
    $_['Close']      = 'Close';
    $_['Copy']       = 'Copy';
    $_['Copied']     = 'Copied';
    $_['Create']     = 'Create';
    $_['Date']       = 'Date';
    $_['Delete']     = 'Delete';
    $_['DELETE']     = 'DELETE';
    $_['Deleted']    = 'Deleted';
    $_['Edit']       = 'Edit';
    $_['Enter']      = 'Enter';
    $_['Error']      = 'Error';
    $_['errors']     = 'errors';
    $_['ext']        = '.ext';    //## NT ## filename[.ext]ension
    $_['File']       = 'File';
    $_['files']      = 'files';
    $_['Folder']     = 'Folder';
    $_['folders']    = 'folders';
    $_['From']       = 'From';
    $_['Group']      = 'Group';  //## NT ## as of 3.6.09
    $_['Hash']       = 'Hash';
    $_['Invalid']    = 'Invalid'; //## NT ## as of 3.5.23
    $_['Move']       = 'Move';
    $_['Moved']      = 'Moved';
    $_['Name']       = 'Name';   //...
    $_['off']        = 'off';
    $_['on']         = 'on';
    $_['Owner']      = 'Owner';  //## NT ## as of 3.6.09
    $_['Password']   = 'Password';
    $_['Rename']     = 'Rename';
    $_['reset']      = 'Reset';
    $_['save_1']     = 'Save';
    $_['save_2']     = 'SAVE CHANGES';
    $_['Size']       = 'Size';
    $_['Source']     = 'Source'; //## NT ##
    $_['successful'] = 'successful';
    $_['To']         = 'To';
    $_['Upload']     = 'Upload';
    $_['Username']   = 'Username';
    $_['View']       = 'View';

    $_['Log_In']             = 'Log In';
    $_['Log_Out']            = 'Log Out';
    $_['Admin_Options']      = 'Administration Options';
    $_['Are_you_sure']       = 'Are you sure?';
    $_['View_Raw']           = 'View Raw'; //## NT ### as of 3.5.07
    $_['Open_View']          = 'Open/View in browser window';
    $_['Edit_View']          = 'Edit / View';
    $_['Wide_View']          = 'Wide View';
    $_['Normal_View']        = 'Normal View';
    $_['Word_Wrap']          = 'Word Wrap'; //## NT ## as of 3.5.19
    $_['Line_Wrap']          = 'Line Wrap'; //## NT ## as of 3.5.20
    $_['Upload_File']        = 'Upload File';
    $_['New_File']           = 'New File';
    $_['Ren_Move']           = 'Rename / Move';
    $_['Ren_Moved']          = 'Renamed / Moved';
    $_['folders_first']      = 'folders first'; //## NT ##
    $_['folders_first_info'] = 'Sort folders first, but don\'t change primary sort.'; //## NT ##
    $_['New_Folder']         = 'New Folder';
    $_['Ren_Folder']         = 'Rename / Move Folder';
    $_['Submit']             = 'Submit Request';
    $_['Move_Files']         = 'Move File(s)';
    $_['Copy_Files']         = 'Copy File(s)';
    $_['Del_Files']          = 'Delete File(s)';
    $_['Selected_Files']     = 'Selected Folders and Files';
    $_['Select_All']         = 'Select All';
    $_['Clear_All']          = 'Clear All';
    $_['New_Location']       = 'New Location';
    $_['No_files']           = 'No files selected.';
    $_['Not_found']          = 'Not found';
    $_['Invalid_path']       = 'Invalid path'; //## NT ##
    $_['must_be_decendant']  = '$DEFAULT_PATH must be a decendant of, or equal to, $ACCESS_ROOT'; //## NT ## as of 3.5.23
    $_['Update_failed']      = 'Update failed';
    $_['Working']            = 'Working - please wait...'; //## NT ##

    $_['Enter_to_edit']		= '[Enter] to edit'; //## NT ## as of 3.6.07
    $_['Press_Enter']       = 'Press [Enter] to save changes. Press [Esc] or [Tab] to cancel.'; //## NT ## as of 3.6.07
    $_['Permissions_msg_1'] = 'Permissions must be exactly 3 or 4 octal digits (0-7)';  //## NT ## as of 3.6.07
    $_['verify_msg_01']     = 'Session expired.';
    $_['verify_msg_02']     = 'INVALID POST';
    $_['get_get_msg_01']    = 'File does not exist:';
    $_['get_get_msg_02']    = 'Invalid page request:';
    $_['check_path_msg_02'] = '"dot" or "dot dot" path segments are not permitted.';
    $_['check_path_msg_03'] = 'Path or filename contains an invalid character:';
    $_['ord_msg_01']        = 'A file with that name already exists in the target directory.';
    $_['ord_msg_02']        = 'Saving as';
    $_['rCopy_msg_01']      = 'A folder can not be copied into one of its own sub-folders.';
    $_['show_img_msg_01']   = 'Image shown at ~';
    $_['show_img_msg_02']   = '% of full size (W x H =';

    $_['hash_txt_01']   = 'The hashes generated by this page may be used to manually update $HASHWORD in OneFileCMS, or in an external config file.  In either case, make sure you remember the password used to generate the hash!';
    $_['hash_txt_06']   = 'Type your desired password in the input field above and hit Enter.';
    $_['hash_txt_07']   = 'The hash will be displayed in a yellow message box above that.';
    $_['hash_txt_08']   = 'Copy and paste the new hash to the $HASHWORD variable in the config section.';
    $_['hash_txt_09']   = 'Make sure to copy ALL of, and ONLY, the hash (no leading or trailing spaces etc).';
    $_['hash_txt_10']   = 'A double-click should select it...';
    $_['hash_txt_12']   = 'When ready, logout and login.';
    $_['pass_to_hash']  = 'Password to hash:';
    $_['Generate_Hash'] = 'Generate Hash';

    $_['login_msg_01a'] = 'There have been';
    $_['login_msg_01b'] = 'invalid login attempts.';
    $_['login_msg_02a'] = 'Please wait';
    $_['login_msg_02b'] = 'seconds to try again.';
    $_['login_msg_03']  = 'INVALID LOGIN ATTEMPT #';

    $_['edit_note_00']  = 'NOTES:';
    $_['edit_note_01a'] = 'Remember- ';
    $_['edit_note_01b'] = 'is';
    $_['edit_note_02']  = 'So save changes before the clock runs out, or the changes will be lost!';
    $_['edit_note_03']  = 'With some browsers, such as Chrome, if you click the browser [Back] then browser [Forward], the file state may not be accurate. To correct, click the browser\'s [Reload].';

    $_['edit_h2_1']   = 'Viewing:';
    $_['edit_h2_2']   = 'Editing:';
    $_['edit_txt_00'] = 'Edit disabled.'; //## NT ## as of 3.5.07
    $_['edit_txt_01'] = 'Non-text or unkown file type. Edit disabled.';
    $_['edit_txt_02'] = 'File possibly contains an invalid character. Edit and view disabled.';
    $_['edit_txt_03'] = 'htmlspecialchars() returned an empty string from what may be an otherwise valid file.';
    $_['edit_txt_04'] = 'This behavior can be inconsistant from version to version of php.';
    $_['edit_txt_05'] = 'File is readonly.';

    $_['too_large_to_edit_01'] = 'Edit disabled. Filesize >';
    $_['too_large_to_edit_02'] = 'Some browsers (ie: IE) bog down or become unstable while editing a large file in an HTML <textarea>.';
    $_['too_large_to_edit_03'] = 'Adjust $MAX_EDIT_SIZE in the configuration section of OneFileCMS as needed.';
    $_['too_large_to_edit_04'] = 'A simple trial and error test can determine a practical limit for a given browser/computer.';
    $_['too_large_to_view_01'] = 'View disabled. Filesize >';
    $_['too_large_to_view_02'] = 'Click [View Raw] to view the raw/"plain text" file contents in a seperate browser window.'; //** NT ** changed wording as of 3.5.07
    $_['too_large_to_view_03'] = 'Adjust $MAX_VIEW_SIZE in the configuration section of OneFileCMS as needed.';
    $_['too_large_to_view_04'] = '(The default value for $MAX_VIEW_SIZE is completely arbitrary, and may be adjusted as desired.)';

    $_['meta_txt_01'] = 'Filesize:';
    $_['meta_txt_03'] = 'Updated:';

    $_['edit_msg_01'] = 'File saved:';
    $_['edit_msg_02'] = 'bytes written.';
    $_['edit_msg_03'] = 'There was an error saving file.';

    $_['upload_txt_03'] = 'Maximum size of each file:';
    $_['upload_txt_01'] = '(php.ini: upload_max_filesize)';
    $_['upload_txt_04'] = 'Maximum total upload size:';
    $_['upload_txt_02'] = '(php.ini: post_max_size)';
    $_['upload_txt_05'] = 'For uploaded files that already exist: ';
    $_['upload_txt_06'] = 'Rename (to filename.ext.001 etc...)';
    $_['upload_txt_07'] = 'Overwrite';

    $_['upload_err_01'] = 'Error 1: File too large. From php.ini:';
    $_['upload_err_02'] = 'Error 2: File too large. (Exceeds MAX_FILE_SIZE HTML form element)';
    $_['upload_err_03'] = 'Error 3: The uploaded file was only partially uploaded.';
    $_['upload_err_04'] = 'Error 4: No file was uploaded.';
    $_['upload_err_05'] = 'Error 5:';
    $_['upload_err_06'] = 'Error 6: Missing a temporary folder.';
    $_['upload_err_07'] = 'Error 7: Failed to write file to disk.';
    $_['upload_err_08'] = 'Error 8: A PHP extension stopped the file upload.';

    $_['upload_error_01a'] = 'Upload Error. Total POST data (mostly filesize) exceeded post_max_size =';
    $_['upload_error_01b'] = '(from php.ini)';

    $_['upload_msg_02'] = 'Destination folder invalid:';
    $_['upload_msg_03'] = 'Upload cancelled.';
    $_['upload_msg_04'] = 'Uploading:';
    $_['upload_msg_05'] = 'Upload successful!';
    $_['upload_msg_06'] = 'Upload failed:';
    $_['upload_msg_07'] = 'A pre-existing file was overwritten.';

    $_['new_file_txt_01'] = 'File or Folder will be created in the current folder.';
    $_['new_file_txt_02'] = 'Some invalid characters are:';
    $_['new_file_msg_01'] = 'File or folder not created:';
    $_['new_file_msg_02'] = 'Name contains an invalid character:';
    $_['new_file_msg_04'] = 'File or folder already exists:';
    $_['new_file_msg_05'] = 'Created file:';
    $_['new_file_msg_07'] = 'Created folder:';

    $_['CRM_txt_02'] = 'The new location must already exist.';
    $_['CRM_txt_04'] = 'New Name';
    $_['CRM_msg_01'] = 'Error - new parent location does not exist:';
    $_['CRM_msg_02'] = 'Error - source file does not exist:';
    $_['CRM_msg_03'] = 'Error - new file or folder already exists:';
    $_['CRM_msg_05'] = 'Error during';

    $_['delete_msg_03']   = 'Delete error:';
    $_['session_warning'] = 'Warning: Session timeout soon!';
    $_['session_expired'] = 'SESSION EXPIRED';
    $_['unload_unsaved']  = ' Unsaved changes will be lost!';
    $_['confirm_reset']   = 'Reset file and loose unsaved changes?';
    $_['OFCMS_requires']  = 'OneFileCMS requires PHP';
    $_['logout_msg']      = 'You have successfully logged out.';
    $_['edit_caution_01'] = 'CAUTION';
    $_['edit_caution_02'] = 'You are viewing the active copy of OneFileCMS.'; //## NT ## changed wording 3.5.07
    $_['time_out_txt']    = 'Session time out in:';

    $_['error_reporting_01'] = 'Display errors is';
    $_['error_reporting_02'] = 'Log errors is';
    $_['error_reporting_03'] = 'Error reporting is set to';
    $_['error_reporting_04'] = 'Showing error types';
    $_['error_reporting_05'] = 'Unexpected early output';
    $_['error_reporting_06'] = '(nothing, not even white-space, should have been output yet)';

    $_['admin_txt_00'] = 'Old Backup Found';
    $_['admin_txt_01'] = 'A backup file was created in case of an error during a username or password change. Therefore, it may contain old information and should be deleted if not needed. In any case, it will be automatically overwritten on the next password or username change.';
    $_['admin_txt_02'] = 'General Information';
    $_['admin_txt_03'] = 'Session Path'; //## NT ## as of 3.5.23
    $_['admin_txt_04'] = 'Connected to'; //## NT ## as of 3.5.23
    $_['admin_txt_14'] = 'For a small improvement to security, change the default salt and/or method used by OneFileCMS to hash the password (and keep them secret, of course). Every little bit helps...';
    $_['admin_txt_16'] = 'OneFileCMS can not be used to edit itself directly.  However, you can make a copy and edit it.'; //## NT ## Changed wording in 3.5.07

    $_['pw_current'] = 'Current Password';
    $_['pw_change']  = 'Change Password';
    $_['pw_new']     = 'New Password';
    $_['pw_confirm'] = 'Confirm New Password';

    $_['un_change']  = 'Change Username';
    $_['un_new']     = 'New Username';
    $_['un_confirm'] = 'Confirm New Username';

    $_['pw_txt_02'] = 'Password / Username rules:';
    $_['pw_txt_04'] = 'Case-sensitive: "A" =/= "a"';
    $_['pw_txt_06'] = 'Must contain at least one non-space character.';
    $_['pw_txt_08'] = 'May contain spaces in the middle. Ex: "This is a password or username!"';
    $_['pw_txt_10'] = 'Leading and trailing spaces are ignored.';
    $_['pw_txt_12'] = 'In recording the change, only one file is updated: either the active copy of OneFileCMS, or - if specified, an external configuration file.';
    $_['pw_txt_14'] = 'If an incorrect current password is entered, you will be logged out, but you may log back in.';

    $_['change_pw_01'] = 'Password changed!';
    $_['change_pw_02'] = 'Password NOT changed.';
    $_['change_pw_03'] = 'Incorrect current password. Login to try again.';
    $_['change_pw_04'] = '"New" and "Confirm New" values do not match.';
    $_['change_pw_05'] = 'Updating';
    $_['change_pw_06'] = 'external config file';
    $_['change_pw_07'] = 'All fields are required.';

    $_['change_un_01'] = 'Username changed!';
    $_['change_un_02'] = 'Username NOT changed.';

    $_['mcd_msg_01'] = 'file(s) and/or folder(s) moved.';
    $_['mcd_msg_02'] = 'file(s) and/or folder(s) copied.';
    $_['mcd_msg_03'] = 'file(s) and/or folder(s) deleted.';
}//end Default_Language() //****************************************************

function validate_units($cssvalue) {//******************************************
    //Determine if valid units are set for $cssvalue:  px, pt, em, or %.
    $main_units   = mb_substr($cssvalue, -2);
    if ( ($main_units != "px") && ($main_units != "pt") && ($main_units != "em") && (mb_substr($cssvalue, -1) != '%')) {
        $cssvalue = ($cssvalue * 1).'px';   //If not, assume px.
    }
    return $cssvalue;
}//end validate_units() //******************************************************

function hsc($input) {//********************************************************
    $enc = mb_detect_encoding($input); //It should always be UTF-8 (or ASCII), but, just in case...
    if ($enc == 'ASCII') {$enc = 'UTF-8';} //htmlspecialchars() doesn't recognize "ASCII"
    return htmlspecialchars($input, ENT_QUOTES, $enc);
}//end hsc() //*****************************************************************

function Convert_encoding($string, $to_enc = "") {//****************************
    global $ENC_OS;
    //mb_convert_encoding($string, $to_enc, $from_enc)
    if ($to_enc == 'UTF-8') {
        // Convert to UTF-8
        return mb_convert_encoding($string, 'UTF-8', $ENC_OS);
    } else {
        // default
        return mb_convert_encoding($string, $ENC_OS, 'UTF-8');
    } // Convert to server's/OS's filesystem enc
}//end Convert_encoding() //****************************************************

function Session_Startup() {//**************************************************
    global $SESSION_NAME, $page, $VALID_POST;

    $limit    =  0; //0 = session.
    $path     = '';
    $domain   = ''; // '' = hostname
    $https    = false;
    $httponly = true; //true = unaccessable via javascript. Some XSS protection.
    session_set_cookie_params($limit, $path, $domain, $https, $httponly);

    session_name($SESSION_NAME);
    session_start();

    //Logging in?
    $page = 'login'; //Changed later in Login_response() or Get_GET() as appropriate.
    if ( isset($_POST['username']) && isset($_POST['password']) ) {
        Login_response();
    }

    if ( !isset($_SESSION['valid']) ) {
        $_SESSION['valid'] = 0;
    }

    session_regenerate_id(true); //Helps prevent session fixation & hijacking.

    $VALID_POST = 0;
    if ( $_SESSION['valid'] ) {
        Verify_IDLE_POST_etc();
    }

    $_SESSION['nuonce'] = sha1(mt_rand().microtime()); //provided in <forms> to verify POST
}//end Session_Startup() //*****************************************************

function Verify_IDLE_POST_etc() {//*********************************************
    global $_, $page, $EX, $MESSAGE, $VALID_POST, $MAX_IDLE_TIME;

    //Verify consistant user agent. This is set during login. (every little bit helps every little bit)
    if ( !isset($_SESSION['user_agent']) || ($_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']) ) {
        Logout();
    }

    //Check idle time
    if ( isset($_SESSION['last_active_time']) ) {
        $idle_time = ( time() - $_SESSION['last_active_time'] );
        if ( $idle_time > $MAX_IDLE_TIME ) {
            Logout();
            $MESSAGE .= hsc($_['verify_msg_01']).'<br>';
            return;
        }
    }

    $_SESSION['last_active_time'] = time();

    //If POSTing, verify...
    //##### NEED TO ACTUALLY CHECK IF HTTP POST (VS GET), THEN ALWAYS CHECK FOR NUONCE. #####
    //##### I think nuonce is now used on every page, so one should ALWAYS be sent with EVERY request.
    //##### So, if a nuonce is not present on a post - ignore the request & set $MESSAGE..
    if ( isset($_POST['nuonce']) ) {
        if ( $_POST['nuonce'] == $_SESSION['nuonce'] ) {
            $VALID_POST = 1;
        } else { //If it exists but doesn't match - something's wrong. Probably a page reload.
            $page  = "index";
            $_POST = "";
            $MESSAGE .= $EX.'<b>'.hsc($_['verify_msg_02']).'</b><br>';
        }
    }
}//end Verify_IDLE_POST_etc() //************************************************

function hashit($key,$pre = false) {//******************************************
    //This is the super-secret stuff - Keep it secret, keep it safe!
    //If you change anything here, or the $SALT, manually update the hash for your password from the Generate Hash page.
    global $SALT, $PRE_ITERATIONS;
    $hash = trim($key); // trim off leading & trailing whitespace.

    //Generally, the "pre-hash" is done client-side during a login attempt, or when changing p/w or u/n.
    //However, if generating a hash from the Hash_Page(), do the "pre-hash" now.
    if ($pre) {
        for ( $x=0; $x < $PRE_ITERATIONS; $x++ ) {
            $hash = hash('sha256', $hash.$SALT);
        }
    }

    for ( $x=0; $x < 10001; $x++ ) {
        $hash = hash('sha256', $hash.$SALT);
    }
    return $hash;
}//end hashit() //**************************************************************

function Error_reporting_status_and_early_output($show_status = 0, $show_types = 0) {//
    //Display the status of error_reporting(), and ini_get() of display_errors & log_errors.
    //Also displays any early output caught by ob_start().
    global $_, $early_output;

    $E_level = error_reporting();
    $E_types = '';
    $spc = ' &nbsp; '; // or '<br>' or PHP_EOL or whatever...
    if ( $E_level &     1 ) { $E_types .= 'E_ERROR'            .$spc; }
    if ( $E_level &     2 ) { $E_types .= 'E_WARNING'          .$spc; }
    if ( $E_level &     4 ) { $E_types .= 'E_PARSE'            .$spc; }
    if ( $E_level &     8 ) { $E_types .= 'E_NOTICE'           .$spc; }
    if ( $E_level &    16 ) { $E_types .= 'E_CORE_ERROR'       .$spc; }
    if ( $E_level &    32 ) { $E_types .= 'E_CORE_WARNING'     .$spc; }
    if ( $E_level &    64 ) { $E_types .= 'E_COMPILE_ERROR'    .$spc; }
    if ( $E_level &   128 ) { $E_types .= 'E_COMPILE_WARNING'  .$spc; }
    if ( $E_level &   256 ) { $E_types .= 'E_USER_ERROR'       .$spc; }
    if ( $E_level &   512 ) { $E_types .= 'E_USER_WARNING'     .$spc; }
    if ( $E_level &  1024 ) { $E_types .= 'E_USER_NOTICE'      .$spc; }
    if ( $E_level &  2048 ) { $E_types .= 'E_STRICT'           .$spc; }
    if ( $E_level &  4096 ) { $E_types .= 'E_RECOVERABLE_ERROR'.$spc; }
    if ( $E_level &  8192 ) { $E_types .= 'E_DEPRECATED'       .$spc; }
    if ( $E_level & 16384 ) { $E_types .= 'E_USER_DEPRECATED'  .$spc; }

    if ( $show_status && ( (error_reporting() !=  0) ||
        (ini_get('display_errors') == 'on') ||
        (ini_get('log_errors') == 'on') ) )
    {
?>

<style>
    .E_box {
        margin: 0;
        background-color: #F00;
        font-size: 1em;
        color: white;
        padding: 2px 5px 2px 5px;
        border: 1px solid white;
    }
</style>

<?php
        echo '<p class="E_box"><b>PHP '.PHP_VERSION.$spc;
        echo hsc($_['error_reporting_01']).': '.ini_get('display_errors').'.'.$spc;
        echo hsc($_['error_reporting_02']).': '.ini_get('log_errors')    .'.'.$spc;
        echo hsc($_['error_reporting_03']).': '.error_reporting()        .'.'.$spc;
        echo 'E_ALL = '.E_ALL.$spc.'</b>';

        if ($show_types) {
            echo '<br><b>'.hsc($_['error_reporting_04']).': </b>';
            echo '<span style="font: 400 .8em arial">'.$E_types.'</span>';
        }
        echo '</p>';
    }//end if (error reporting on)

    //$early_output is contents of ob_get_clean(), just before page output.
    if (mb_strlen($early_output) > 0 ) {
        echo '<pre style="background-color: #F00; border: 0px solid #F00;"><b>';
        echo hsc($_['error_reporting_05']).'</b> ';
        echo hsc($_['error_reporting_06']).'<b>:</b> ';
        echo '<span style="background-color: white; border: 1px solid white">';
        echo hsc($early_output).'</span></pre>';
    }
}//end Error_reporting_status_and_early_output() //*****************************

function Update_Recent_Pages() {//**********************************************
    global $page;

    if (!isset($_SESSION['recent_pages'])) {
        $_SESSION['recent_pages'] = array($page);
    }

    $pages = count($_SESSION['recent_pages']);

    //Only update if actually a new page
    if ( $page != $_SESSION['recent_pages'][0] ) {
        array_unshift($_SESSION['recent_pages'], $page);
        $pages = count($_SESSION['recent_pages']);
    }

    //Only need 3 most recent pages (increase if needed)
    if ($pages > 3) {
        array_pop($_SESSION['recent_pages']);
    }

}//end Update_Recent_Pages() //*************************************************

function undo_magic_quotes() {//************************************************

    function strip_array($var) {
        //stripslashes() also handles cases when magic_quotes_sybase is on.
        if (is_array($var)) {return array_map("strip_array", $var); }
        else                {return stripslashes($var); }
    }//end strip_array()

    if (get_magic_quotes_gpc()) {
        if (isset($_GET))    { $_GET     = strip_array($_GET);    }
        if (isset($_POST))   { $_POST    = strip_array($_POST);   }
        if (isset($_COOKIE)) { $_COOKIE  = strip_array($_COOKIE); }
    }
}//end undo_magic_quotes() //***************************************************

function Set_IS_OFCMS($fullpath_filename) {//***********************************
    //Used to restrict edit/del/etc. on active copy of OneFileCMS.
    global $MESSAGE, $DOC_ROOT;

    $is_ofcms = 0;
    $ofcms = trim($DOC_ROOT, '/').'/'.trim($_SERVER['SCRIPT_NAME'], '/');
    if ($fullpath_filename == $ofcms) {
        $is_ofcms = true;
    }

    return $is_ofcms;
}//end Set_IS_OFCMS() //********************************************************

function Validate_params() {//**************************************************
    global $_, $ipath, $filename, $page, $param1, $param2, $param3, $IS_OFCMS, $EX, $MESSAGE;

    //Pages that require a valid $filename
    $file_pages = array("edit", "renamefile", "copyfile", "deletefile");

    //Make sure $filename & $page go together
    if ( ($filename != "") && !in_array($page, $file_pages) ) {
        $filename = "";
    }
    if ( ($filename == "") &&  in_array($page, $file_pages) ) {
        $page = "index";
    }

    //Init $param's used in <a> href's & <form> actions
    $param1 = '?i='.URLencode_path($ipath); //$param1 must not be blank.
    if ($filename == "") {
        $param2 = "";
    } else {
        $param2 = '&amp;f='.rawurlencode(basename($filename));
    }
    if ($page == "") {
        $param3 = "";
    } else {
        $param3 = '&amp;p='.$page;
    }

    //Used to restrict edit/del/etc. on active copy of OneFileCMS.
    $IS_OFCMS = Set_IS_OFCMS($filename);

}//end Validate_params() //*****************************************************

function Valid_Path($path, $gotoroot=true) {//**********************************
    //$gotoroot: if true, return to index page of $ACCESS_ROOT.
    global  $ipath, $ipath_OS, $filename, $param1, $param2, $param3, $ACCESS_ROOT, $ACCESS_ROOT_len, $MESSAGE;

    //Limit access to the folder $ACCESS_ROOT:
    //$ACCESS_ROOT = some/root/path/
    //$path        = some/root/path/...(or deeper)   : good
    //$path        = some/root/                      : bad
    //$path        = some/other/path/                : bad

    $path_len  = mb_strlen($path);
    $path_root = mb_substr($path, 0, $ACCESS_ROOT_len);
    $good_path = false;


    if (isset($_SESSION['admin_page']) && $_SESSION['admin_page']) {
        //Permit Admin actions: changing p/w, u/n, viewing OneFile...
        $ACCESS_ROOT == '';
        return true;
    } elseif ( $path_len <  $ACCESS_ROOT_len ) {
        $good_path = false;
    } else {
        $good_path = ($path_root == $ACCESS_ROOT);
    }

    if (!$good_path && $gotoroot) {
        $ipath    = $ACCESS_ROOT;
        $ipath_OS = Convert_encoding($ipath);
        $filename = '';
        //$page     = 'index';  //#### If set to index here, can't logout.
        $param1   = '?i='.$ipath;
        $param2   = '';
        $param3   = '';
        $_GET     = '';
        $_POST    = '';
    }

    return $good_path;
}//end Valid_Path() //**********************************************************

function Get_GET() {//**** Get URL passed parameters ***************************
    global $_, $ipath, $ipath_OS, $filename, $filename_OS, $page, $VALID_PAGES, $EX, $MESSAGE, $DEFAULT_PATH;
    // i=some/path/,  f=somefile.xyz,          p=somepage,  m=somemessage
    // $ipath = i  ,  $filename = $ipath.f  ,  $page = p ,  $MESSAGE
    //   (NOTE: in some functions $filename = just the file's name, ie: $_GET['f'], with no path/)
    //#####  (Normalize $filename usage program-wide??)
    // Perform initial, basic, validation.
    // Get_GET() should not be called unless $_SESSION['valid'] == 1 (or true)


    //Initialize & validate $ipath
    if (isset($_GET["i"])) {
        $ipath = Check_path($_GET["i"],1);
        $ipath_OS = Convert_encoding($ipath);

        // if 		( $ipath === "") 						  {;} //root, aka '/', is valid.
        if ($ipath === false || !is_dir($ipath_OS)) {
            $ipath = $DEFAULT_PATH;
        } //not root & not valid
    } else {
        $ipath = $DEFAULT_PATH;
    }
    $ipath_OS = Convert_encoding($ipath);


    //Initialize & validate $filename
    if (isset($_GET["f"])) {
        $filename = $ipath.$_GET["f"];
    } else {
        $filename = "";
    }

    $filename_OS = Convert_encoding($filename);
    if ( ($filename != "") && !is_file($filename_OS)  ) {
        $MESSAGE .= $EX.'<b>'.hsc($_['get_get_msg_01']).'</b> ';
        $MESSAGE .= hsc(dir_name($filename)).'<b>'.hsc(basename($filename)).'</b><br>';
        $filename = $filename_OS = "";
    }

    //Initialize & validate $page
    if (isset($_GET["p"])) {
        $page = $_GET["p"];
    } else {
        $page = "index";
    }

    if (!in_array($page, $VALID_PAGES)) {
        $MESSAGE .= $EX.hsc($_['get_get_msg_02']).' <b>'.hsc($page).'</b><br>';
        $page = "index";  //If invalid $_GET["p"]
    }


    //Sanitize any message. Initialized on line 1 / top of this file.
    if (isset($_GET["m"])) {
        $MESSAGE .= hsc($_GET["m"]);
    }
}//end Get_GET() //*************************************************************

function Verify_Page_Conditions() {//*******************************************
    global $_, $ONESCRIPT_file, $ipath, $ipath_OS, $param1, $filename, $filename_OS, $page, $EX, $MESSAGE,
        $VALID_POST, $IS_OFCMS;

    //If exited admin pages, restore $ipath
    if ( ($page == "index") && $_SESSION['admin_page'] ) {
        //...unless clicked www/some/path/ from edit or copy page while in admin pages.
        if ( ($_SESSION['recent_pages'][0] != 'edit') && ($_SESSION['recent_pages'][0] != 'copyfile') ) {
            $ipath = $_SESSION['admin_ipath'];
            $param1 = '?i='.URLencode_path($ipath);
        }
        $_SESSION['admin_page']  = false;
        $_SESSION['admin_ipath'] = '';
    } elseif ( ($page == "login") && $_SESSION['valid'] ) {
        //Don't load login screen when already in a valid session.
        //$_SESSION['valid'] may be false after Respond_to_POST()
        $page = "index";
    } elseif ( $page == "logout" ) {
        Logout();
        $MESSAGE .= hsc($_['logout_msg']);
    } elseif ( ($page == "deletefolder" || $page == "renamefolder") && ($ipath == "") ) {
        //Don't load rename or delete folder pages at webroot. //##### is this still needed?
        $page = "index";
    } elseif ($page == "deletefolder") {
        //Prep MCD_Page() to delete a single folder selected via (x) icon on index page.
        $_POST['files'][1]  = basename($ipath); //Must precede next line (change of $ipath).
        $ipath    = dir_name($ipath);
        $ipath_OS = Convert_encoding($ipath);
        $param1 = '?i='.$ipath;
    }
    //There must be at least one 'file', and 'mcdaction' must = "move", "copy", or "delete"
    elseif ($page == "mcdaction") {
        if     (!isset($_POST['mcdaction'] )) {
            $page = "index";
        } elseif (!isset($_POST['files']) ) {
            $page = "index";
        } elseif ( ($_POST['mcdaction'] != "move") && ($_POST['mcdaction'] != "copy") && ($_POST['mcdaction'] != "delete") ) {
            $page = "index";
        }
    } elseif ( ($page == "uploaded") && !$VALID_POST ) {
        //if size of $_POST > post_max_size, PHP only returns empty $_POST & $_FILE arrays.
        $MESSAGE .= $EX.'<b> '.hsc($_['upload_error_01a']).' '.ini_get('post_max_size').'</b> '.hsc($_['upload_error_01b']).'<br>';
        $page = "index";
    }

    //[View Raw] file contents in a browser window (in plain text, NOT HTML).
    if ($page == "raw_view"){
        ob_start();
        $raw_contents = file_get_contents($filename_OS);
        $file_ENC = mb_detect_encoding($raw_contents); //ASCII, UTF-8, etc...
        header('Content-type: text/plain; charset=utf-8');
        echo mb_convert_encoding($raw_contents, 'UTF-8', $file_ENC);
        die;
    }
}//end Verify_Page_Conditions() //**********************************************

function has_invalid_char($string) {//******************************************
    global $INVALID_CHARS;
    $INVALID_CHARS_array = explode(' ', $INVALID_CHARS);
    foreach ($INVALID_CHARS_array as $bad_char) {
        if (mb_strpos($string, $bad_char) !== false) {
            return true;
        }
    }
    return false;
}//end has_invalid_char() //****************************************************

function URLencode_path($path){ // don't encode the forward slashes ************
    $path = str_replace('\\','/',$path);   //Make sure all forward slashes.
    $TS = '';  // Trailing Slash/
    if (mb_substr($path, -1) == '/' ) {
        $TS = '/';
    } //start with a $TS?

    $path_array = explode('/',$path);
    $path = "";
    foreach ($path_array as $level) {
        $path .= rawurlencode($level).'/';
    }

    $path = rtrim($path,'/').$TS;  //end with $TS only if started with one
    return $path;
}//end URLencode_path() //******************************************************

function dir_name($path) {//****************************************************
    //Modified dirname().
    $parent = dirname($path);
    if ($parent == "." || $parent == "/" || $parent == '\\' || $parent == "") {
        return "";
    } else {
        return $parent.'/';
    }
}//end dir_name() //************************************************************

function Check_path($path, $show_msg = false) {//*******************************
    // check for invalid characters & "dot" or "dot dot" path segments.
    // Does NOT check if exists - only if of valid construction.
    global  $_, $MESSAGE, $EX, $INVALID_CHARS, $WHSPC_SLASH;

    $path = str_replace('\\','/',$path);   //Make sure all forward slashes.
    $path = trim($path, $WHSPC_SLASH);     // trim whitespace & slashes

    if ( ($path == "") || ($path == ".") ) {
        return "";
    } // At root.

    $err_msg = "";
    $errors  = 0;

    $pathparts = explode( '/', $path);

    foreach ($pathparts as $part) {
        //Check for any '.' and '..' parts of the path to protect directories outside webroot.
        //They also cause issues in <h2>www / current / path /</h2>
        if ( ($part == '.') || ($part == '..') ) {
            $err_msg .= $EX.' <b>'.hsc($_['check_path_msg_02']).'</b><br>';
            $errors++;
            break;
        }

        //Check for invalid characters
        $invalid_chars = str_replace(' /','',$INVALID_CHARS); //The forward slash is not present, or invalid, at this point.
        if ( has_invalid_char($part) ) {
            $err_msg .= $EX.' <b>'.hsc($_['check_path_msg_03']).' &nbsp; <span class="mono"> '.$invalid_chars.'</span></b><br>';
            $errors++;
            break;
        }
    }

    if ($errors > 0) {
        if ($show_msg) { $MESSAGE .= $err_msg; }
        return false;
    }

    return $path.'/';
}//end Check_path() //**********************************************************

function Sort_Seperate($path, $full_list) {//***********************************
    //Sort list, then seperate folders & files

    natcasesort($full_list);
    $files= array();
    $folders= array();
    $F=1; $D=1;  //indexes
    foreach ( $full_list as $item ) {
        if ( ($item == '.') || ($item == '..') || ($item == "")) {
            continue;
        }
        $fullpath_OS = Convert_encoding($path.$item);
        if (is_dir($fullpath_OS)) {
            $folders[$D++] = $item;
        } else {
            $files[$F++] = $item;
        }
    }

    return array_merge($folders, $files);
}//end Sort_Seperate() //*******************************************************

function add_serial_num($filename, &$msg) {//***********************************
    //if file_exists(file.txt), add serial# to filename until it doesn't
    //ie: file.txt.001,  file.txt.002, file.txt.003  etc...
    global $_, $EX;

    $ordinal   = 0;

    //Convert $filename to server's File Syetem encoding
    $savefile    = $filename;
    $savefile_OS = Convert_encoding($savefile);

    if (file_exists($savefile_OS)) {

        $msg .= $EX.hsc($_['ord_msg_01']).'<br>';

        while (file_exists($savefile_OS)) {
            $ordinal = sprintf("%03d", ++$ordinal); //  001, 002, 003, etc...
            $savefile = $filename.'.'.$ordinal;
            $savefile_OS = Convert_encoding($savefile);
        }
        $msg .= '<b>'.hsc($_['ord_msg_02']).':</b> <span class="filename">'.hsc(basename($savefile)).'</span>';
    }
    return $savefile;
}//end add_serial_num() //******************************************************

function supports_svg() {//*****************************************************
    //IE < 9 is the only browser checked for currently.
    //EX: Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)
    $USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    $pos_MSIE   = mb_strpos($USER_AGENT, 'MSIE ');
    $old_ie     = false;
    if ($pos_MSIE !== false) {
        $ie_ver = mb_substr($USER_AGENT, ($pos_MSIE+5), 1);
        $old_ie = ( $ie_ver < 9 );
    }
    return !$old_ie;
}//end supports_svg() //********************************************************

function rCopy( $old_path, $new_path ) {//**************************************
    global $_, $WHSPC_SLASH, $EX, $MESSAGE;
    //Recursively copy $old_path to $new_path
    //Both $old_ & $new_path must ALREADY be in OS/file system's encoding.
    //(ie: usually UTF-8, but often ISO-8859-1 for Windows.)
    //Return number of successful copy's + mkdir's, or 0 on error.

    //$old_path & $new_path must already be in OS/filesystem's file name encoding

    //Avoid a bottomless pit of sub-directories:
    //    ok: copy root/1/ to root/1/Copy_of_1/
    //NOT OK: copy root/1/ to root/1/2/Copy_of_1/
    //

    $error_code = 0;

    //First, trim / and white-space that will mess up strlen() check.
    $old_path = trim($old_path,$WHSPC_SLASH);
    $new_path = trim($new_path,$WHSPC_SLASH);
    //
    $test_path = dirname($new_path);
    while (mb_strlen($test_path) >= mb_strlen($old_path)) {
        $test_path = dirname($test_path);
        if ( $test_path == $old_path ) {
            $MESSAGE .= $EX.' <b>'.hsc($_['rCopy_msg_01']).'</b><br>';
            return 0;
        }
    }

    if ( is_file($old_path) ) {
        return (copy($old_path, $new_path)*1);
    }

    if ( is_dir($old_path) )  {

        $dir_list = scandir($old_path); //MUST come before mkdir().
        $error_code = (mkdir($new_path, 0755)*1);

        if ( sizeof($dir_list) > 0 ) {
            foreach ( $dir_list as $file ) {
                if ( $file == "." || $file == ".." ) {
                    continue;
                }
                $error_code += rCopy( $old_path.'/'.$file, $new_path.'/'.$file);
            }
        }
        return $error_code;
    }

    return 0; //$old_path doesn't exist, or, I don't know what it is.
}//end rCopy() //***************************************************************

function rDel($path) {//********************************************************
    //Recursively delete $path & all sub-folders & files.
    //Returns number of successful unlinks & rmdirs.

    $path = trim($path, '/'); //Protect against deleting files outside of webroot.
    if ($path == "") {
        $path = '.';
    }

    $path_OS = Convert_encoding($path);

    $count = 0;

    if ( is_file($path_OS) ) {
        return (unlink($path_OS)*1);
    }
    if ( is_dir($path_OS) ) {

        $dir_list = scandir($path_OS);
        foreach ( $dir_list as $dir_item ) {
            $dir_item_OS = Convert_encoding($dir_item);
            if ( ($dir_item == '.') || ($dir_item =='..') ) {
                continue;
            }
            $count += rDel($path.'/'.$dir_item);
        }

        $count += rmdir($path_OS);
        return $count;
    }
    return false; //$path doesn't exists, or, I don't know what it is...
}//end rDel() //****************************************************************

function Current_Path_Header() {//**********************************************
    // Current path. ie: webroot/current/path/
    // Each level is a link to that level.
    global $ONESCRIPT, $ipath, $ACCESS_ROOT, $ACCESS_ROOT_len, $TABINDEX, $MESSAGE;

    $unaccessable    = '';
    $_1st_accessable = '';
    $remaining_path  = trim(mb_substr($ipath, $ACCESS_ROOT_len), ' /');

    if ($ACCESS_ROOT != '') {
        $unaccessable    = dirname($ACCESS_ROOT);
        $_1st_accessable = basename($ACCESS_ROOT);

        if ($unaccessable == '.') {
            $unaccessable = '/';
        } else {
            $unaccessable = '/'.dirname($ACCESS_ROOT).'/';
        }
        $unaccessable = '&nbsp;'.hsc(trim(str_replace('/', ' / ',$unaccessable)));
    }

    echo '<h2 id="path_header">';
    //Root (or $ACCESS_ROOT) folder of web site.
    $p1 = '?i='.URLencode_path($ACCESS_ROOT);

    if ($_1st_accessable == "") {
        echo '<a id=path_0 tabindex='.$TABINDEX++.' href="'.$ONESCRIPT.$p1.'" class="path"> /</a>';
    } else {
        echo $unaccessable.'<a id=path_0 tabindex='.$TABINDEX++.' href="'.$ONESCRIPT.$p1.'" class="path">'.hsc($_1st_accessable).'</a>/';
    }

    if ($remaining_path != "" ) { //if not at root, show the rest
        $path_levels  = explode("/",trim($remaining_path,'/') );

        $levels = count($path_levels); //If levels=3, indexes = 0, 1, 2  etc...
        $current_path = "";

        for ($x=0; $x < $levels; $x++) {
            $current_path .= $path_levels[$x].'/';
            $p1 = '?i='.URLencode_path($ACCESS_ROOT.$current_path);
            echo '<a id="path_'.($x+1).'" tabindex='.$TABINDEX++.' href="'.$ONESCRIPT.$p1.'" class="path">';
            echo hsc($path_levels[$x]).'</a>/';
        }
    }//end if(not at root)
    echo '</h2>';
}//end Current_Path_Header() //*************************************************

function Page_Header() {//******************************************************
    global  $_, $ONESCRIPT, $page, $WEBSITE, $MAIN_TITLE, $OFCMS_version, $FAVICON, $TABINDEX, $MESSAGE;

    $TABINDEX = 1; //Initial tabindex

    $FAVICON = trim($FAVICON,' /');

    $favicon_img = '';
    if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$FAVICON)) {
        $favicon_img =  '<img src="/'.URLencode_path($FAVICON).'" alt="[favicon]">';
    }

    echo '<div id="header">';
    echo '<a href="'.$ONESCRIPT.'" id="logo" tabindex='.$TABINDEX++.'>'.hsc($MAIN_TITLE).'</a> '.$OFCMS_version.' ';

    $on_php = '';
    if ($page == "admin") {
        $on_php = '('.hsc($_['on']).'&nbsp;php&nbsp;'.phpversion().')';
        $on_php = '<a id=on_php tabindex='.($TABINDEX++).' href="'.$ONESCRIPT.'?p=phpinfo'.'" target=_blank>'.$on_php.'</a>';
    }
    echo $on_php;

    echo '<div class="nav">';
    echo '<b><a id=website href="/" tabindex='.$TABINDEX++.' target="_blank">';
    echo $favicon_img.' '.hsc($WEBSITE).'</a></b>';
    if ($page != "login") {
        echo ' | <a id=logout tabindex='.$TABINDEX++.' href="'.$ONESCRIPT.'?p=logout">'.hsc($_['Log_Out']).'</a>';
    }
    echo '</div><div class=clear></div>';

    echo '</div>';//<!-- end header -->
}//end Page_Header() //*********************************************************

function Cancel_Submit_Buttons($submit_label) {//*******************************
    //$submit_label = Rename, Copy, Delete, etc...
    global $_, $ONESCRIPT, $ipath, $param1, $param2, $page;

    $params = $param1.$param2.'&amp;p='. $_SESSION['recent_pages'][1]; //.'&amp;m='.hsc($_['cancelled']) not sure I like this.
?>
    <p>
    <button type="button" class="button" id="cancel" onclick="parent.location = '<?= $ONESCRIPT.$params ?>'">
        <?= hsc($_['Cancel']) ?></button>
    <button type="submit" class="button" id="submitty"><?= hsc($submit_label);?></button>

<script>E("cancel").focus();</script>

<?php
}//end Cancel_Submit_Buttons() //***********************************************

function Show_Image($url) {//***************************************************
    global $_, $filename, $MAX_IMG_W, $MAX_IMG_H;

    $IMG = $filename;
    $img_info = getimagesize($IMG);

    $W=0; $H=1; //indexes for $img_info[]
    $SCALE = 1; $SCALE_W = 1; $SCALE_H = 1;
    if ($img_info[$W] > $MAX_IMG_W) {
        $SCALE_W = ( $MAX_IMG_W/$img_info[$W] );
    }
    if ($img_info[$H] > $MAX_IMG_H) {
        $SCALE_H = ( $MAX_IMG_H/$img_info[$H] );
    }

    //Set $SCALE to the more restrictive scale.
    if ( $SCALE_W > $SCALE_H ) {
        //ex: if (.90 > .50)
        $SCALE = $SCALE_H;
    } else {
        $SCALE = $SCALE_W;
    } //If _H >= _W, or both are 1

    //For languages with longer words that don't fit next to [Wide] & [Close] buttons.
    if ($_['image_info_pos']){ echo '<div class=clear></div>'."\n"; }

    echo '<p class="image_info">';
    echo hsc($_['show_img_msg_01']).round($SCALE*100).
        hsc($_['show_img_msg_02']).' '.$img_info[0].' x '.$img_info[1].').</p>';
    echo '<div class=clear></div>'."\n";
    echo '<a  href="'.URLencode_path($url).'" target="_blank">'."\n";
    echo '<img src="'.URLencode_path($url).'" width="'.($img_info[$W] * $SCALE).'"></a>'."\n";
}//end Show_Image() //**********************************************************

function Timeout_Timer($COUNT, $ID, $ACTION="") {//*****************************
    global $DELAY_Start_Countdown;

    //These represent strings that need to be "quoted".
    $ID     = '"'.$ID.'"';
    $ACTION = '"'.$ACTION.'"';

    return "<script>setTimeout('Start_Countdown($COUNT, $ID, $ACTION)', $DELAY_Start_Countdown);</script>\n";

}//end Timeout_Timer() //*******************************************************

function Init_Macros() {//**** ($varibale="some reusable chunk of code")********
    global 	$_, $ONESCRIPT, $param1, $param2, $INPUT_NUONCE, $FORM_COMMON, $PWUN_RULES;

    $INPUT_NUONCE = '<input type="hidden" id="nuonce" name="nuonce" value="'.$_SESSION['nuonce'].'">'."\n";
    $FORM_COMMON = '<form method="post" action="'.$ONESCRIPT.$param1.$param2.'">'."\n".$INPUT_NUONCE."\n";

    $PWUN_RULES  = '<p>'.hsc($_['pw_txt_02']);
    $PWUN_RULES	.= '<ol><li>'.hsc($_['pw_txt_04']).'<li>'.hsc($_['pw_txt_06']);
    $PWUN_RULES .=     '<li>'.hsc($_['pw_txt_10']).'<li>'.hsc($_['pw_txt_08']).'</ol>';
}//end Init_Macros() //*********************************************************

?>
