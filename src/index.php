<?php
//******************************************************************************
//Output page contents
//******************************************************************************
$early_output = ob_get_clean(); // Should be blank unless trouble-shooting.
ob_start();
header('Content-type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="robots" content="noindex">
<?php
echo '<title>'.hsc($MAIN_TITLE.' - '.Page_Title()).'</title>'."\n";

Load_style_sheet();

Common_Scripts();

Error_reporting_status_and_early_output(0,0); //0,0 will only show early output.

if ($_SESSION['valid']) { echo '<div id=main >'; }
else                    { echo '<div id=login_page>'; }

Page_Header();

if ($_SESSION['valid'] && $Show_Path) { Current_Path_Header(); }

$TABINDEX_XBOX = $TABINDEX++; //Messages, and the [X] box, not displayed until later.
echo '<div id="message_box"></div>';

Load_Selected_Page();

//footer...
if ($_SESSION['valid']) {
    //Countdown timer
    echo "<hr id=hr_bottom>\n";
    echo "<span id=timer0  class='timer timeout'></span>";
    echo "<span class=timeout>".hsc($_['time_out_txt'])."</span>";

    //Adjust $TABINDEX to account for contents of directory list (created by Assemble_Insert_row()).
    //Directory list is created client-side by js, so tabindex is incremented by the js at that point.
    //Each row in directory list (with a filename) has 6 tab-able/focusable items:
    //	[m] [c] [d] [x] [sogw]   [file name]
    if (isset($DIRECTORY_COUNT)) { $TAB_INDEX = "tabindex=".($TABINDEX + ($DIRECTORY_COUNT * 6)); }
    else 						 { $TAB_INDEX = ""; }

    //Admin link
    if ( ($_SESSION['admin_page'] === false) ) {
        echo '<a id="admin" '.$TAB_INDEX.' href="'.$ONESCRIPT.$param1.$param2.'&amp;p=admin">'.hsc($_['Admin']).'</a>';
    }
}//end footer

echo "\n</div>\n"; //end main/login_page


if ( ($page == "edit") && $WYSIWYG_VALID && $EDIT_WYSIWYG ) { include($WYSIWYG_PLUGIN_OS); }

//Display any $MESSAGE's
echo "\n\n<script>\n";
echo 'var $tabindex_xbox = '.$TABINDEX_XBOX.";\n"; //Used in Display_Messages()
echo 'var $page = "'.$page.'";'."\n";
echo '$MESSAGE += "'.addslashes($MESSAGE).'";'."\n"; //js version of $MESSAGE is declared at top of Common_Scripts().
//Cause $MESSAGE's $X_box to take focus on these pages only.
echo 'if (($page == "index") || ($page == "edit")) {take_focus = 1}'."\n";
echo 'else										   {take_focus = 0}'."\n\n";

//Initial sort & display of the directory, by (filename, ascending).
if ($page == "index") {	echo "Sort_and_Show();\n\n"; }

//The setTimeout() delay should be greater than what is set for the Sort_and_Show() "working..." message.
echo 'setTimeout("Display_Messages($MESSAGE, take_focus)", '.$DELAY_final_messages.');';
echo "\n</script>\n\n";

//##### ACTUAL COUNTDOWN STARTS ON THE SERVER.
//##### DO I NEED TO ACCOUNT FOR TIME RECEIVING & LOADING PAGE CLIENT SIDE?

//start any timers...
if ($_SESSION['valid']) { echo Timeout_Timer($MAX_IDLE_TIME, 'timer0', 'LOGOUT'); }
if ($page == 'edit')    { echo Timeout_Timer($MAX_IDLE_TIME, 'timer1', 'LOGOUT'); }
if ($LOGIN_DELAYED > 0) { echo Timeout_Timer($LOGIN_DELAYED, 'timer0', ''); }

echo "</html>\n";  //***********************************************************
//##### Header (UTF-8) for [View Raw] incorrect or not getting sent??
//##### If file has non-ascii characters, browers display in ISO-8859-1/Windows-1252,
//##### Except IE, which asks to download the file...
//##### When browsers manually set to UTF-8, files display fine.

//##### END OF FILE ############################################################

?>
