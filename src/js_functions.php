<?php
function init_ICONS_js() {//****************************************************
    global $ICONS;

    //Currently, only icons for dir listing are needed in js
?>

<script>
    var ICONS = [];
    ICONS['bin']	 = '<?= $ICONS["bin"]	 ?>';
    ICONS['z']		 = '<?= $ICONS["z"]		 ?>';
    ICONS['img']	 = '<?= $ICONS["img"]	 ?>';
    ICONS['svg']	 = '<?= $ICONS["svg"]	 ?>';
    ICONS['txt']	 = '<?= $ICONS["txt"]	 ?>';
    ICONS['htm']	 = '<?= $ICONS["htm"]	 ?>';
    ICONS['php']	 = '<?= $ICONS["php"]	 ?>';
    ICONS['css']	 = '<?= $ICONS["css"]	 ?>';
    ICONS['cfg']	 = '<?= $ICONS["cfg"]	 ?>';
    ICONS['dir']     = '<?= $ICONS["dir"]     ?>';
    ICONS['folder']  = '<?= $ICONS["folder"]  ?>';
    ICONS['ren_mov'] = '<?= $ICONS["ren_mov"] ?>';
    ICONS['move']    = '<?= $ICONS["move"]    ?>';
    ICONS['copy']    = '<?= $ICONS["copy"]    ?>';
    ICONS['delete']  = '<?= $ICONS["delete"]  ?>';
</script>


<?php
}//end init_ICONS_js() //*******************************************************




function Common_Scripts() {//***************************************************
    global $_, $TO_WARNING, $MESSAGE, $page, $DELAY_Expired_Reload;
?>



<script>
function E(id) { return document.getElementById(id); }

var $MESSAGE = "";


function pad(num){ if ( num < 10 ){ num = "0" + num; }; return num; }



function hsc(text) {//************************************************
    //A basic htmlspecialchars()

    text = text.replace(/&/g, "&amp;");
    text = text.replace(/</g, "&lt;");
    text = text.replace(/>/g, "&gt;");
    text = text.replace(/"/g, "&quot;");
    text = text.replace(/'/g, "&#039;");

    return text
}//end hsc() //*******************************************************



function trim($string) {//********************************************

    //trim leading whitespace
    $len = $string.length;
    $trimmed = "";
    for (var $x=0; $x < $len; $x++) {
        $charcode = $string.charCodeAt($x);
        if ( $charcode > 32) { $trimmed += $string.substr($x); $x = $len; }
    }

    //trim trailing whitespace
    $string = $trimmed;
    $len = $string.length;
    $trimmed = "";
    for ($x=($len-1); $x >= 0; $x--) {
        $charcode = $string.charCodeAt($x);
        if ( $charcode > 32) { $trimmed += $string.substr(0,$x+1); $x = -1; }
    }

    return $trimmed;
}//end trim() //******************************************************



function FormatTime(Seconds) {//**************************************
    var Hours = Math.floor(Seconds / 3600); Seconds = Seconds % 3600;
    var Minutes = Math.floor(Seconds / 60); Seconds = Seconds % 60;
    if ((Hours == 0) && (Minutes == 0)) { Minutes = "" } else { Minutes = pad(Minutes) }
    if (Hours == 0) { Hours = ""} else { Hours = pad(Hours) + ":"}

    return (Hours + Minutes + ":" + pad(Seconds));
}//end FormatTime() //************************************************



function format_number(number, sep) {//*******************************
    sep = typeof sep !== 'undefined' ? sep : ','; //default to a comma
    var number	= number + "";   // 1234567890     convert number to a string
    var result  = "";            // 1,234,567,890  sample result

    for (var x = 0; x < number.length ; x += 3) {
        a = number.length - x - 3;
        b = number.length - x;
        result = number.substring(a,b) + result;
        if (a > 0) {result = sep + result} //add sep if still have more digits
    }
    return result;
}//end format_number() //*********************************************



//********************************************************************
function Countdown(count, End_Time, Timer_ID, Action){
    var Timer        = E(Timer_ID);
    var Current_Time = Math.round(new Date().getTime()/1000); //js uses milliseconds
        count        = End_Time - Current_Time;
    var params = count + ', "' + End_Time + '", "' + Timer_ID + '", "' + Action + '"';

    var $msg_box = E('message_box');

    Timer.innerHTML = FormatTime(count);

    if ((count == <?php echo $TO_WARNING; ?>) && (Action != "")) { //Two minute warning...

        var timeout_warning  = '<div class="message_box_contents"><b><?= hsc($_['session_warning']) ?></b> ';
            timeout_warning += '<b><span id=timer2>:--</span></b></div>';
        $msg_box.innerHTML  = timeout_warning;
        setTimeout('Start_Countdown(' + count + ',"timer2","")',25);

        var Timer2 = E('timer2');
        Timer.style.color           = Timer2.style.color           = "red";
        Timer.style.fontWeight      = Timer2.style.fontWeight      = "900";
        Timer.style.backgroundColor = Timer2.style.backgroundColor = "white";
    }

    if ( count < 1 ) {
        if ( Action == 'LOGOUT') {
            Timer.innerHTML        = '<?= hsc($_['session_expired']) ?>';
            $msg_box.innerHTML = '<div class=message_box_contents><b><?= hsc($_['session_expired']) ?></b></div>';
            //Load login screen, but delay first to make sure really expired:
            setTimeout('window.location = window.location.pathname', <?= $DELAY_Expired_Reload ?>);
        }
        return;
    }
    setTimeout('Countdown(' + params + ')',1000); //1000 = one second
}//end Countdown() //*************************************************



function Start_Countdown(count, ID, Action) {//***********************

    var Time_Start  = Math.round(new Date().getTime()/1000); //in seconds
    var Time_End    = Time_Start + count;

    Countdown(count, Time_End, ID, Action); //(seconds to count, id of element)
}//end Start_Countdown() //*******************************************



//********************************************************************
function FileTimeStamp(php_filemtime, show_date, show_offset, write_return){

    //php's filemtime returns seconds, javascript's date() uses milliseconds.
    var FileMTime = php_filemtime * 1000;

    var TIMESTAMP  = new Date(FileMTime);
    var YEAR  = TIMESTAMP.getFullYear();
    var	MONTH = pad(TIMESTAMP.getMonth() + 1);
    var DATE  = pad(TIMESTAMP.getDate());
    var HOURS = TIMESTAMP.getHours();
    var MINS  = pad(TIMESTAMP.getMinutes());
    var SECS  = pad(TIMESTAMP.getSeconds());

    if ( HOURS < 12) { AMPM = "am"; } else { AMPM = "pm"; }
    if ( HOURS > 12 ) {HOURS = HOURS - 12; }
    HOURS = pad(HOURS);

    var GMT_offset = -(TIMESTAMP.getTimezoneOffset()); //Yes, I know- seems wrong, but its works.

    if (GMT_offset < 0) { NEG = -1; SIGN = "-"; } else { NEG = 1; SIGN = "+"; }

    var offset_HOURS = Math.floor(NEG*GMT_offset/60);
    var offset_MINS  = pad( NEG * (GMT_offset % 60) );
    var offset_FULL  = "UTC " + SIGN + offset_HOURS + ":" + offset_MINS;

    var FULLDATE = YEAR + "-" + MONTH + "-" + DATE;
    var FULLTIME = HOURS + ":" + MINS + ":" + SECS + " " + AMPM;

    var               DATETIME = FULLTIME;
    if (show_date)  { DATETIME = FULLDATE + " &nbsp;" + FULLTIME;}
    if (show_offset){ DATETIME += " ("+offset_FULL+")"; }

    if (write_return) { document.write(DATETIME); }
    else 			  { return DATETIME; }
}//end FileTimeStamp() //*********************************************



function Display_Messages($msg, take_focus) {//***********************

    if (typeof take_focus === 'undefined') {take_focus = 0;}  //default is X_box doesn't take focus()

    if (typeof $tabindex_xbox === 'undefined') {$tabindex_xbox = 0;}

    var $page     = '<?= $page ?>';
    var new_focus = '';

    if      ($page == 'index') { new_focus = 'header_filename'; }
    else if ($page == 'edit')  { new_focus = 'close1'; }
    else if ($page == 'login') { new_focus = 'username'; }
    else if ($page == 'hash')  { new_focus = 'whattohash'; }
    else if ($page == 'admin') { new_focus = 'close'; }

    var $X_box		 = '<button tabindex=' + $tabindex_xbox + ' type=button id=X_box>&times;</button>';
    var $msg_div	 = '<div class=message_box_contents>' + $msg + '</div>';
    var $msg_box     = E("message_box");
    var $new_focus	 = E(new_focus)

    if ($msg == '') { $msg_box.innerHTML = ' '; } //innerHTML must be given a space or $msg_box won't clear.
    else {
        $msg_box.innerHTML = $X_box + $msg_div;
        var $X_box_btn	 = E('X_box');
        if (take_focus) {$X_box_btn.focus();}
        $X_box_btn.onclick = function () { $msg_box.innerHTML = " "; $new_focus.focus();}
    }

}//end Display_Messages() //******************************************

</script>


<?php
}//end Common_Scripts() //******************************************************




function Index_Page_events() {//************************************************
    global $_, $ONESCRIPT, $ipath, $PAGEUPDOWN, $EX;
?>

<script>
var Move_Button			= E('b1');
var Copy_Button			= E('b2');
var Delete_Button		= E('b3');


E('header_filename').focus();

//These buttons aren't present if folder is empty...
if (Move_Button)   { Move_Button.onclick   = function () {Confirm_Submit('move');}   }
if (Copy_Button)   { Copy_Button.onclick   = function () {Confirm_Submit('copy');}   }
if (Delete_Button) { Delete_Button.onclick = function () {Confirm_Submit('delete');} }

//Always present...
E('select_all_ckbox').onclick = function () {Select_All();}


E('select_all_ckbox').onfocus = function() {  //ckbox_label_focus
    this.parentNode.classList.add("ckbox_label_focus");
    E('select_all_label').classList.add("ckbox_label_focus");
}
E('select_all_ckbox').onblur = function() {
    this.parentNode.classList.remove("ckbox_label_focus");
    E('select_all_label').classList.remove("ckbox_label_focus");
}


E('folders_first_ckbox').onfocus = function() {
    this.parentNode.classList.add("ckbox_label_focus");
    E('folders_first_label').classList.add("ckbox_label_focus");
}

E('folders_first_ckbox').onblur = function() {
    this.parentNode.classList.remove("ckbox_label_focus");
    E('folders_first_label').classList.remove("ckbox_label_focus");
}

E('folders_first_ckbox').onclick = function () {Sort_and_Show(SORT_by, SORT_order); this.focus();}

E('header_filename').onclick = function () {Sort_and_Show(1, FLIP_IF); this.focus(); return false;}
E('header_filesize').onclick = function () {Sort_and_Show(2, FLIP_IF); this.focus(); return false;}
E('header_filedate').onclick = function () {Sort_and_Show(3, FLIP_IF); this.focus(); return false;}
E('header_sorttype').onclick = function () {Sort_and_Show(5, FLIP_IF); this.focus(); return false;}



E("main").onkeydown = function(event) { //*****************************
    //Halt and be warned! For this be no lore, that here truly be, the dragons of yore!!!
    //It won't look back, if you enter this Abyss, it'll only swallow you hole, then [redacted]!
    //Control cursor keys to navigate index page. (Arrows, Page, Home, End)

    var jump = <?= $PAGEUPDOWN ?>;//# of rows to jump with Page Up/Page Down.

    //Get key pressed...
    if (!event) {var event = window.event;} //for IE
    var key = event.keyCode;

    //Assign a few handy "constants": Arrow U/D/L/R, Page Up/Down, etc...
    var AU = 38, AD = 40, AL = 37, AR = 39, PU = 33, PD = 34, END = 35, HOME = 36, ESC = 27, ENTER = 13;

    //Ignore any other key presses...
    if ((key != AU) && (key != AD) && (key != AL) && (key != AR) && (key != PU) && (key != PD) &&
        (key != HOME) && (key != END) && (key != ESC) && (key != ENTER)
    ) { return }

    //File Rows. "../" is 0, and files are indexed from 1 to DIRECTORY_ITEMS.
    var FROWS        = DIRECTORY_ITEMS;
    var FILENAME_COL = 5;
    var LAST_FILE    = "f" + FROWS + "c" + FILENAME_COL;
    var FIRST_FILE   = "f1c" + FILENAME_COL;
    var FILE_ZERO    = "f0c" + FILENAME_COL;

    //Get id of current focus (before this event). If focus is in file list, ID = 'fn', or 'fnn', etc.
    var ID      = document.activeElement.id;
    var x_focus = ID.substr(0,1);

    //When ID=f00c0 (zeros mean any digit)
    //FR: Digits after "f" are the (F)ile (R)ow    (0,1, ... FROWS)
    //FC: Digit  after 'c' is  the (F)ile (C)olumn (0=move, 1=copy, 2=del, 3=ckbox)
    var FR = parseInt(ID.substr(1));      if (isNaN(FR) || (x_focus != "f")) {FR = -1;} //If not in file list...
    var FC = parseInt(ID.split('c')[1]);  if (isNaN(FC)) {FC = -1;}


    //If no files in current folder, [Move][Copy][Delete] won't exist (id's b1 b2 b3). Use [New Folder] (id="b4").
    if (E("b2")) {var button_row = "b2"} else {var button_row = "b4"}

    //Indicate if current focus is on one of the elements of the table header row. (true / false)
    //Select All[ ] | [x](folders first) Name  (.ext) | Size |  Date  | Owner | Group
    var focus_header = ((ID == "select_all_ckbox") || (ID == "folders_first_ckbox") || (ID == "header_filename") ||
                        (ID == "header_sorttype")  || (ID == "header_filesize")     || (ID == "header_filedate"));

    //Prep for Arrow Left/Right keys ...
    //To simulate Tab/Shift-tab, get list of all tab-able tags.
    //Below, will compare each tabindex to current tabindex, and allow for skips in tabindex.
    //All tab-able tags should have a tabindex = 1, 2, 3... etc, with no duplicates.
    if ((key == AL) || (key == AR)){
        var focus_tabindex = document.activeElement.tabIndex;

        //Need to check all elements on each onkeydown(), in case things change (like closing of message box).
        var all_tags     = document.getElementsByTagName('*');
        var tag_count    = all_tags.length;
        var tabindex_IDs = []; //Array of ID's of all tags with a tabindex, indexed by tabindex.

        //Create array of the ID's of all tags with a tabindex. (All tab-able elements should have a tabindex set.)
        for (var x = 0; x < tag_count; x++) {
            var ti = all_tags[x].tabIndex;
            if (ti > 0) { tabindex_IDs[ti] = all_tags[x].id; }
        }
    }


    //Get ID of current directory (path_END).
    //For example: <h2 id=path_header>/  home / [user] / [www1] / [some] / [path] / </h2>
    //path_items[x]:         [0]      /(no id)/  [1]   /  [2]   /  [3]   /   [4]
    //path_items.length = 5
    //path_items[x] id's: path_header /(no id)/ path_0 / path_1 / path_2 / path_3
    //   So, path_END id = path_(5 - 2) = path_3.
    if ( (key == PU) || (key == PD) || (key == AU) || (key == AD) || ((x_focus == "p") && (key == HOME || key == END)) ) {
        var path_items = document.querySelectorAll('[id^="path_"]');
        var path_END   = "path_" + (path_items.length - 2);
    }


    //PROCESS THE KEYDOWN EVENT... /////////////////////////////////////////////
    //In general:
    //  ENTER - enabled to check/unckeck checkboxes, and respond as needed.
    //  Tab- handle checkbox's (parent <div>'s & <label>'s), otherwise allow default action.
    //  Esc simply removes focus from active element.
    //  If focus in path_header, Home & End stay in path_header. Otherwise,
    //	Home goes to the first row in list (first acutal file, not the .../), and
    //	End goes to last file in list.
    //	Arrow Up/Down will loop from (top to bottom)/(bottom to top) of page (no hard stops).
    //  Page Up/Down will likewise loop thru page, with soft-stops at first/last filenames.
    //  Arrow Left/Right will function similarly to Tab/Shift-Tab, but hard stop at first/last link on page.

    if 		((key == HOME) && (x_focus == "p")) { ID = "path_0"; }
    else if ((key ==  END) && (x_focus == "p")) { ID = path_END; }
    else if (key == HOME) {	ID = FIRST_FILE; }
    else if (key == END)  { ID = LAST_FILE;  }
    else if (key == ESC)  { document.activeElement.blur(); return; }

    else if (key == ENTER) {
        if (ID == "select_all_ckbox") {
            E('mcdselect').select_all.checked = !E('mcdselect').select_all.checked
            Select_All();
        }
        else if (ID == "folders_first_ckbox") {
            E('folders_first_ckbox').checked = !E('folders_first_ckbox').checked;
            Sort_and_Show(SORT_by, SORT_order);
        }
        else if (FC == 3) {  //Is focus on a checkbox next to a file name?
            E(ID).checked = !E(ID).checked;
        }

        // Prevent the hair-pulling "implicit submit on enter" that only occurs sometimes.
        // Specifically, at least here, if there's only one item in the current directory,
        // and if focus is on an <input>, and enter is pressed, then the form would also submit.
        // Not any more, as that behaviour is NOT wanted, expected, or intuitive.
        // But, allow "enter" on <button>'s & <a>'s ([Move], [Copy], [Delete], and  [M][C][D] & [file names])
        //
        var has_focus = document.activeElement;
        if (has_focus.tagName == 'INPUT') { event.preventDefault(); }
        return;
    }
    else if (key == AL) {
        //Find first tab-able element to the Left (usually just (focus_tabindex - 1))
        for (var new_index = (focus_tabindex - 1); new_index > 0; new_index--) {
            if (tabindex_IDs[new_index]) { ID = tabindex_IDs[new_index]; break; }
        }
    }
    else if (key == AR) {
        //Find first tab-able element to the Right (usually just (focus_tabindex + 1))
        for (var new_index = (focus_tabindex + 1); new_index < tabindex_IDs.length; new_index++) {
            if (tabindex_IDs[new_index]) { ID = tabindex_IDs[new_index]; break; }
        }
    }
    else if (ID == "admin") {
        if      (key == AU) {ID = LAST_FILE}
        else if (key == PU) {ID = LAST_FILE}
        else if (key == AD) {ID = "logo"}
        else if (key == PD) {ID = "logo"}
    }
    else if (ID == "logo") {
        if      (key == AU) {ID = "admin"}
        else if (key == PU) {ID = "admin"}
        else if (key == AD) {ID = "path_0"}
        else if (key == PD) {ID = path_END}
    }
    else if ((ID == "website") || (ID == "logout")) {
        if      (key == AU) {ID = "admin"}
        else if (key == PU) {ID = "admin"}
        else if (key == AD) {ID = path_END}
        else if (key == PD) {ID = path_END}
    }
    else if (ID == "X_box") {
        if      (key == AU)   {ID = "path_0"}
        else if (key == PU)   {ID = "logo"}
        else if (key == AD)   {ID = button_row}
        else if (key == PD)   {ID = FILE_ZERO}
    }
    else if (x_focus == 'p') { //In path_header: webroot/current/path/
        if      (key == AU)   {ID = "logo"}
        else if (key == PU)   {ID = "logo"}
        else if (key == AD)   {ID = button_row}
        else if (key == PD && FROWS  > 0)   {ID = FIRST_FILE}
        else if (key == PD && FROWS == 0)   {ID = FILE_ZERO}
    }
    else if (x_focus == "b") { //[Move][Copy][Delete]  [New Folder][New File][Upload File]
        if      (ID == "b1" && key == AD) {ID = "select_all_ckbox"} //[Move]
        else if (ID == "b4" && key == AU) {ID = path_END;}          //[New Folder]
        else if (FROWS == 0 && key == PD) {ID = FILE_ZERO}
        else if (key == AU) {ID = "path_0"		   }
        else if (key == PU) {ID = path_END		   }
        else if (key == AD) {ID = "header_filename"}
        else if (key == PD) {ID = FIRST_FILE	   }
    }
    else if (ID == 'select_all_ckbox') {
        if		(E('b1')    && key == AU) {ID = 'b1'}
        else if (FROWS == 0 && key == AD) {ID = FILE_ZERO}
        else if (FROWS == 0 && key == PD) {ID = FILE_ZERO}
        else if (key == AU) {ID = button_row}
        else if	(key == PU) {ID = path_END}
        else if (key == AD) {ID = "f1c3"}
        else if (key == PD) { FR = jump; if (FR < FROWS) {ID = "f" + FR + "c3"} else {ID = "f" + FROWS + "c3";}}
    }
    else if (focus_header) { //Table header row
        if      (key == AU) {ID = button_row}
        else if (key == PU) {ID = path_END}
        else if	(key == AD) {ID = FILE_ZERO}
        else if	(key == PD) {FR += jump; if (FR < FROWS) {ID = "f" + FR + "c" + FILENAME_COL} else {ID = LAST_FILE}}
    }
    else if ((FROWS == 0) && (FR == 0)) { //empty folder
        if		(key == AU) {ID = "header_filename"}
        else if	(key == PU) {ID = path_END}
        else if (key == AD) {ID = "admin";}
        else if (key == PD) {ID = "admin";}
    }
    else if ((FROWS == 1) && (FR == 1)) {
        if		(ID == FIRST_FILE && key == AU) { ID = FILE_ZERO }
        else if	(key == AU) { ID = "select_all_ckbox" }
        else if	(key == PU) { ID = path_END  }
        else if (key == AD) { ID = "admin"   }
        else if (key == PD) { ID = "admin"   }
    }
    else if (FR == FROWS) { //Last row (FROWS is the number of files listed)
        if		(key == AU) { FR--      ; if (FR >= 1) {ID = "f" + FR + "c" + FC} else {ID = "header_filename" } }
        else if	(key == PU) { FR -= jump; if (FR >= 1) {ID = "f" + FR + "c" + FC} else {ID = "f1c" + FC} }
        else if (key == AD) { ID = "admin" }
        else if (key == PD) { ID = "admin" }
    }
    else if (FR == 0) { // [ ../ ]
        if		(key == AU) { ID = "header_filename" }
        else if	(key == PU) { ID = path_END}
        else if (key == AD) { FR++      ; if (FR <= FROWS) {ID = "f" + FR + "c" + FC} else {ID = "path_0";}  }
        else if (key == PD) { FR += jump; if (FR <= FROWS) {ID = "f" + FR + "c" + FC} else {ID = LAST_FILE;} }
    }
    else if (FR == 1) { // The first actual file(or folder) in the dir list.
        if      (ID == FIRST_FILE && key == AU) { ID = FILE_ZERO }
        else if (FROWS == 1 && key == AD) { ID = "admin" }
        else if (FROWS == 1 && key == PD) { ID = "admin" }
        else if (key == AU) { ID = "select_all_ckbox" }
        else if	(key == PU) { ID = path_END }
        else if	(key == AD) { ID = "f2c" + FC }
        else if (key == PD) { FR += jump; if (FR <= FROWS)  {ID = "f" + FR + "c" + FC} else {ID = "f" + FROWS + "c" + FC;} }
    }
    else if (FR > 0){ //Middle rows...
        if		(key == AU) { FR--      ; ID = "f" + FR + "c" + FC;	}
        else if	(key == PU) { FR -= jump; if (FR > 1)       { ID = "f" + FR + "c" + FC} else {ID = "f1c" + FC} }
        else if (key == AD) { FR++; 	  if (FR <= FROWS)  { ID = "f" + FR + "c" + FC} else {ID = "path_0"; } }
        else if (key == PD) { FR += jump; if (FR <= FROWS)  { ID = "f" + FR + "c" + FC} else {ID = "f" + FROWS + "c" + FC;} }
    }
    else if (FR == -1) {ID = "path_0"}     //Anyplace other than path_header, buttons, table
    else {
        //just in case I missed something...
        var error_message  = '<?= __LINE__.$EX.'<b>'.hsc($_['Error']).'</b> onkeydown(): ' ?>';
            error_message += "key = " + key + ", FR = " + FR + ", ID = " + ID + ",x_focus = " + x_focus
        Display_Messages(error_message);
        return;
    }

    E(ID).focus();

    //Prevent default browser scrolling via arrow & Page keys, so focus()'d element stays visible/in view port.
    //(A few exceptions skip this via a return in the above  if/else's.)
    if ( (ID != 'path_0') || ((ID == 'path_0') && (key == AD)) || ((ID == 'path_0') && (key == PD))) {
        if (event.preventDefault) { event.preventDefault() } else { event.returnValue = false }
    }
}//end E("main").onkeydown() //***************************************




function Perms_onkeydown(event, $perms, filename) {//*****************

    //Get key pressed...
    if (!event) {var event = window.event;} //for IE
    var key = event.keyCode;

    var TAB = 9, ENTER = 13, ESC = 27;

    if (key == ENTER)  { //Toggle readonly state of permissions.
        $perms.value = $perms.value.trim();
        $perms.readOnly = !$perms.readOnly;
        if ($perms.readOnly) {
            Display_Messages("");
            $perms.classList.remove("edit_perms");

            if ($perms.value != $perms.prior_value) { Validate_and_Post($perms, filename); }
        }
        else {
            Enable_Edit_Perms($perms);
        }

        event.preventDefault();
        return false;
    }//end ENTER

    if ($perms.readOnly) { return; }

    event.stopPropagation(); //Should precede if(ESC or TAB)

    if ((key == ESC) || (key == TAB)) { Cancel_Perm_Changes($perms) }

    Octal_Input_Only(event);

    return true; //false would .preventDefault(), which is not wanted here (TAB's).

}//end Perms_onkeydown() {//******************************************




function Cancel_Perm_Changes($perms) {//******************************
        $perms.value = $perms.prior_value;
        $perms.readOnly = true;
        $perms.classList.remove("edit_perms");
        Display_Messages("");
}//end Cancel_Perm_Changes() {//**************************************




function Directory_Events($ckbox, $perms, $file, filename) {//********

    //$ckbox events are assigned in Insert_mdx()

    $perms.onblur	 = function(event) { Cancel_Perm_Changes($perms) }

    $perms.onfocus   = function(event) {
        var deselect = function() { $perms.setSelectionRange(0, 0); }
        setTimeout(deselect, 1);
        $perms.prior_value = $perms.value;
    }

    $perms.onkeydown = function(event) { return Perms_onkeydown(event, $perms, filename); }

    $perms.onclick   = function(event) { Enable_Edit_Perms($perms); }

}//end Directory_Events() {//*****************************************




function Validate_and_Post($perms, filename) { //*********************

    $perms.value = $perms.value.trim();

    if ($perms.value == $perms.prior_value) { return };

    //Verify that each digit is octal (0-7), and that $perms is only 3 or 4 digits in length.
    var octal = /^[0-7]{3,4}$/;
    var valid =  octal.test($perms.value);

    if (!valid) {
        var msg  = "<b>" + hsc("<?= $_['Invalid'] ?>: [" + $perms.value + "]") + "</b> ";
            msg += hsc("<?= $_['Permissions_msg_1'] ?>.");
        $perms.value = $perms.prior_value;
        Display_Messages(msg);
        return false;
    }

    Post_New_File_Perms($perms, filename);

    return true;
}//end Validate_and_Post() //*****************************************




function Enable_Edit_Perms($perms) {//********************************

    var msg = hsc(" <?= $_['Press_Enter'] ?>");
    msg += "<br><span class=mono>" + Format_Perms($perms.value) + "</span>";
    Display_Messages(msg);
    $perms.readOnly = false;
    $perms.setSelectionRange(0, 0); //Just for consistency.
    $perms.classList.add("edit_perms");

}//end Enable_Edit_Perms() {//****************************************




function Octal_Input_Only(event) { //*********************************
    //Restrict input to digits & a few special keys.

    //This function works with keyboards, but not touchscreens etc.
    //However, total input is validated on [Enter] anyway, regardless of device.

    function Stop_Prop(event) { event.stopImmediatePropagation() }

    //Normalize the event codes...
    if (!event) {var event = window.event;} //for IE
    var key = event.which || event.keyCode || event.charCode;

    //Allow:
    if ((key >=  96) && (key <= 103)) { Stop_Prop(event); return; } //keypad numbers 0-7
    if ((key >=  45) && (key <=  55)) { Stop_Prop(event); return; } //insert, delete, keyboard top row numbers 0-7
    if ((key ==  37) || (key ==  39)) { Stop_Prop(event); return; } //arrows  <- ->
    if ((key ==  35) || (key ==  36)) { Stop_Prop(event); return; } //end, home
    if ((key ==   8) || (key ==  13)) { Stop_Prop(event); return; } //backspace, enter
    if (event.ctrlKey     || (key ==   9)) { Stop_Prop(event); return; } //control & tab keys

    event.preventDefault();
    return false;

}//end Octal_Input_Only() //******************************************




function Perms_Update_Response(request, $perms) { //******************

    //##### Need to also handle non-200 response cases... ##### ###########

    if ((request.readyState != 4) || (request.status != 200)) { return; }

    var update_response = JSON.parse(request.responseText);

    $perms.value = update_response.new_perms.trim();

    $perms.prior_value = $perms.value;

    E('nuonce').value = update_response.nuonce; //For the next post...

    var frow = $perms.id.split('c')[0].substr(1); //id = "fNNcN", frow = the NN after the "f"
    var drow = frow - 1; //See Assemble_Insert_row() for description/explanation.

    DIRECTORY_DATA[drow][6]  = $perms.value;
    DIRECTORY_DATA[drow][10] = update_response.writable;

    var cells = E("DIRECTORY_LISTING").rows[drow].cells;
    Insert_mdx(drow, cells); //Show/Hide [M]    [D][X]   file options


    var msg = update_response.MESSAGE;

    //Should always be blank unless troubleshooting, or an error server side.
    if (update_response.early_output != "") { msg += "<hr>" + update_response.early_output; }

    //#####	msg += "<hr>" + hsc(request.responseText); //For trouble-shooting...

    Display_Messages(msg);
    window.scroll(0,0); //Leave focus on perms of file, but scroll message box into view if needed.

}//end Perms_Update_Response() //*************************************




function Post_New_File_Perms($perms, filename) { //*******************
    //key input restricted to 0-7 client-side, and validated both client & server-side.

    Display_Messages("<?= $_['Working'] ?>");

    var request_update = new XMLHttpRequest();

    request_update.onreadystatechange = function() { Perms_Update_Response(this, $perms); }

    request_update.open("POST", "<?= $ONESCRIPT ?>", true);

    request_update.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    var post_data  = "new_perms=" + $perms.value;
        post_data += "&ipath=<?= $ipath ?>";
        post_data += "&perms_filename=" + filename;
        post_data += "&nuonce=" + E('nuonce').value;  //Needed to confirm $VALID_POST

    request_update.send(post_data );

}//end Post_New_File_Perms() //***************************************

</script>

<?php
}//end Index_Page_events() //***************************************************




function Index_Page_scripts() {//***********************************************
    global $_, $ONESCRIPT, $param1, $ipath, $MESSAGE, $DELAY_Sort_and_Show_msgs, $MIN_DIR_ITEMS, $TABINDEX, $DIRECTORY_COLUMNS;
?>

<script>
var ONESCRIPT	= "<?= $ONESCRIPT ?>";
var PARAM1		= "<?= $param1 ?>";  //capitalized here as it is used as a constant.
var TABINDEX	= <?= $TABINDEX ?>;  //TABINDEX only used by js from this point on...
var DIRECTORY_COLUMNS = <?= $DIRECTORY_COLUMNS ?>;

//a few usefull constants for using sort_DIRECTORY()
var DESCENDING	= 0;
var ASCENDING	= 1;
var FLIP		= 2; //Reverse the current direction (ascending <-> descending)
var FLIP_IF		= 3; //Flip only if new column selected.

//A few flags for using sort_DIRECTORY(). These are not constants.
var SORT_by		     = '1';   // Sort key (column) from DIRECTORY_DATA[x][key].
var SORT_order       = true;  // Default to "normal" sort orders (ascending). Set to false for reverse (descending).
var SORT_folders_1st = true;  // Initially set to true. false = did not consider folders during prior sort.

//Used to either show or hide [Mov][Del]  [X] options depending on if file is readonly or not.
//Made 2D & assigned values in Assemble_mdx().
//These need to be global as they're used in Insert_mdx(), which is called from two different functions.
var MOV_rw = []; //Move/Rename
var DEL_rw = []; //Delete
var CBX_rw = []; //checkbox


function Sort_Folders_First() {//*************************************
    //Maintain existing sort order (by name, ext, date, etc.), but place all folders first.

    //DIRECTORY_DATA[x] = ("type", "file name", filesize, timestamp, is_ofcms)

    var type = ""; //= row_data[0] = DIRECTORY_DATA[x][0]
    var files    = [];
    var folders  = [];
    var row_data = [];
    var F = 0, D = 0, row = 0;  //indexes

    //Seperate folders & files into two seperate arrays...
    for (row = 0; row < DIRECTORY_DATA.length; row++) {;
        row_data = DIRECTORY_DATA[row];
        type     = row_data[0];
        if (type == "dir") { folders[D++] = row_data; }
        else 			   { files[F++]   = row_data; }
    }//end for

    //Merge folders[] & files[] back together.
    DIRECTORY_DATA = folders.concat(files);

    SORT_folders_1st = true;

}//end Sort_Folders_First() //****************************************




function sort_DIRECTORY(col, direction) {//***************************

    if (DIRECTORY_DATA.length < 2) {return} //can't sort 1 or zero items.

    //sort DIRECTORY_DATA[] by col and direction

    //col: 1 for "file name", 2 for filesize, 3 for timestamp, 5 for "ext"
    //direction: 0 = desending, 1 = ascending, 2 = flip, 3 = flip only if new col != SORT_by

    //SORT_by, SORT_order, and SORT_folders_1st: values set by prior (or initial) sort.

    //If needed, set default col and/or direction.
    if (typeof col       === 'undefined') { col = 1 }
    if (typeof direction === 'undefined') { direction = ASCENDING }

    //Filename ckboxes are cleared automatically on a resort, in Assemble_Insert_row(), so this needs cleared also.
    E('select_all_ckbox').checked = false;

    //If new sort column, sort ascending. (FLIP overides, but is not currently used.)
    if ((col != SORT_by) && (direction != FLIP)) { direction = ASCENDING; SORT_by = col; }

    //Get status of [x](folders first) checkbox
    var folders_first_checked = E('folders_first_ckbox').checked;

    //If '[x](folders first)' is now checked, but was previously unchecked,
    //no need to re-sort by col, just sort by folders. Otherwise, first resort by column.
    if ( !(folders_first_checked && !SORT_folders_1st) ) {

        if      ( direction == ASCENDING  ) { SORT_order = true;  }
        else if ( direction == DESCENDING ) { SORT_order = false; }
        else if ( direction == FLIP       ) { SORT_order = !SORT_order; }
        else if ( direction == FLIP_IF    ) { SORT_order = !SORT_order; }
        else                                { SORT_order = true; }

        if (col == 1 || col == 5) {  // If "file name" or "ext", sort alphabetically
            if (SORT_order) { DIRECTORY_DATA.sort( function (a, b) {return a[col].localeCompare(b[col]);} ); }
            else            { DIRECTORY_DATA.sort( function (b, a) {return a[col].localeCompare(b[col]);} ); }
        } else { //sort numerically
            if (SORT_order) { DIRECTORY_DATA.sort( function (a, b) {return a[col]       -       b[col] ;} ); }
            else            { DIRECTORY_DATA.sort( function (b, a) {return a[col]       -       b[col] ;} ); }
        }
    }//end if folders first only / full resort

    if (folders_first_checked == true) { Sort_Folders_First(); }

}//end sort_DIRECTORY() //********************************************




function Init_Dir_table_rows() {//************************************
    //initialize <tr>'s with empty <td>'s

    var drow, cell, cells, tr, td;

    //Number of columns in directory listing.
    var last_cell = DIRECTORY_COLUMNS;

    for (drow = 0; drow < DIRECTORY_ITEMS; drow++){
        tr = E("DIRECTORY_LISTING").insertRow(-1); //-1 adds row after last row.
        for (cell = 0; cell < last_cell; cell++) {
            td = tr.insertCell(-1);
        }
        cells = tr.cells;

        //assign classes
        var c = 4;
        cells[c++].className = 'meta_T perms';  //file permissions
        cells[c++].className = 'file_name';
        cells[c++].className = 'file_size meta_T';
        cells[c++].className = 'file_time meta_T';
        <?php if (function_exists('posix_getpwuid')) { ?>
            cells[c++].className = 'meta_T'; //file owner
            cells[c++].className = 'meta_T'; //file group
        <?php } ?>
    }
}//end Init_Dir_table_rows() {//**************************************




//********************************************************************
function Assemble_mdx(drow, href, f_or_f, filename, tabindex) {

    //Assemble [mov], [del], & [x](checkbox)
    //[mov], [del], and [x] are not available for OFCMS or readonly files.
    //([copy] & [perms] are always available)
    //The empty <a>'s are to accommodate keyboard nav via onkeydown() in Index_Page_events()...

    var frow = drow + 1; //See Assemble_Insert_row() for description/explanation.

    var ren_id   = 'f' + frow + 'c0';
    var del_id	 = 'f' + frow + 'c2';
    var ckbox_id = 'f' + frow + 'c3';

    var IS_OFCMS = DIRECTORY_DATA[drow][4];
    var sogw = parseInt(DIRECTORY_DATA[drow][6] + "",8); //File permissions (suid sgid sticky)(owner)(group)(world)
    var writable = (((sogw & 0o200)/0o200) && !IS_OFCMS) * 1 ;  //Check file owner write bit, or if IS_OFCMS.

    //Declared earlier in global scope (near top of Index_Page_scripts()).
    //For storing both sets of html below.  For a given file, only one set used at a time, depending on write bit.
    //[0] = file is not writable, contains empty placeholder <a> (needed for keyboard nav),
    //[1] = file is writable, (write bit is set),contains working <a> or <input>
    MOV_rw[frow] = [];
    DEL_rw[frow] = [];
    CBX_rw[frow] = [];

    //Used when file is not writable, or IS_OFCMS.  ([M], [D], & [X],  are unavailable.)
    MOV_rw[frow][0] = '<a id=' + ren_id   + ' tabindex='+ (tabindex + 0) +'>&nbsp;</a>';
    DEL_rw[frow][0] = '<a id=' + del_id   + ' tabindex='+ (tabindex + 2) +'>&nbsp;</a>';
    CBX_rw[frow][0] = '<a id=' + ckbox_id + ' tabindex='+ (tabindex + 3) +'>&nbsp;</a>';

    //Used when file is writable.
    MOV_rw[frow][1]  = '<a id=' + ren_id + ' tabindex='+ (tabindex + 0) +' class=MCD href="' + href + '&amp;p=rename' + f_or_f;
    MOV_rw[frow][1] += '" title="<?= hsc($_['Ren_Move']) ?>">' + ICONS['ren_mov'] + '</a>';
    DEL_rw[frow][1]  = '<a id=' + del_id + ' tabindex='+ (tabindex + 2) +' class=MCD href="' + href + '&amp;p=delete' + f_or_f;
    DEL_rw[frow][1] += '" title="<?= hsc($_['Delete'])   ?>">' + ICONS['delete']  + '</a>';
    CBX_rw[frow][1]  = '<div class=ckbox><INPUT id=' + ckbox_id + ' tabindex='+ (tabindex + 3);
    CBX_rw[frow][1] += ' TYPE=checkbox class=select_file NAME="files[]"  VALUE="'+ hsc(filename) +'"></div>';

}//end Assemble_mdx() {//*********************************************




function Insert_mdx(drow, cells) {//**********************************

    var IS_OFCMS = DIRECTORY_DATA[drow][4];
    var sogw = parseInt(DIRECTORY_DATA[drow][6] + "",8); //File permissions (suid sgid sticky)(owner)(group)(world)
    var writable = DIRECTORY_DATA[drow][10];     //1 or 0 (true or false)
        writable = (writable && !IS_OFCMS) * 1;  //1 or 0 (true or false)

    var frow = drow + 1; //See Assemble_Insert_row() for description/explanation.

    //MOV_rw, DEL_rw, & CBX_rw, are globals, with values set in Assemble_mdx()
    cells[0].innerHTML = MOV_rw[frow][writable];
    cells[2].innerHTML = DEL_rw[frow][writable];
    cells[3].innerHTML = CBX_rw[frow][writable];

    //Assign checkbox events. (See Directory_Events() for other directory events.)
    $ckbox = E('f' + frow + 'c' + 3);
    $ckbox.onfocus   = function() { this.parentNode.classList.add("ckbox_parent_focus");    }
    $ckbox.onblur    = function() { this.parentNode.classList.remove("ckbox_parent_focus"); }

}//end Insert_mdx() {//***********************************************




//********************************************************************
function Assemble_Insert_row(drow, href, filename, file_name, file_time){

    //The number of tab-able items per row affects the (TABINDEX + 5) offset near end of Build_Directory(),
    //and the $TABINDEX calculation for the [Admin] link in page footer.
    //There are currently 6 tab-able items per (file) row:  [m] [c] [d] [x] [sogw] [file name]
    //[m][c][d][x][sogw] tabindexes are set below.  [filename]'s tabinex is set in Build_Directory().

    var cells = E("DIRECTORY_LISTING").rows[drow].cells;

    var filetype = DIRECTORY_DATA[drow][0];
    var filesize = DIRECTORY_DATA[drow][2];

    if (filetype == "dir") {
        var f_or_f = 'folder';
        var file_size = '';
    }
    else {
        var f_or_f = 'file';
        var file_size = format_number(filesize);
    }

    //While DIRECTORY_DATA[], and the table <tbody> rows created to list the data, are indexed from 0 (zero),
    //the id's of files in the directory list are indexed from 1 (f1c5, f2c5...), as "../" is listed first with id=f0c5.
    //The id's are used in Index_Page_events() "cursor" control.
    var frow = drow + 1;

    var copy, perms;

    var copy_id  = 'f' + frow + 'c1';
    var ckbox_id = 'f' + frow + 'c3';
    var perms_id = 'f' + frow + 'c4';
    var file_id  = 'f' + frow + 'c5';

    //[copy] & [perms] are always available.
    copy  = '<a id=' + copy_id + ' tabindex='+ (TABINDEX + 1) +' class=MCD href="' + href + '&amp;p=copy'   + f_or_f;
    copy += '" title="<?= hsc($_['Copy']) ?>">' + ICONS['copy'] + '</a>';

    perms  = '<input id=' + perms_id + ' tabindex=' + (TABINDEX + 4) + ' class=perms';
    perms += ' value="' + DIRECTORY_DATA[drow][6]+ '" maxlength=4 readonly>';

    //Assemble & Insert contents for cells[0], [2], & [3]  ([Mov], [Del], [ckbox])
    Assemble_mdx(drow, href, f_or_f, filename, TABINDEX);
    Insert_mdx(drow, cells);//cells[0],[2],[3]
    TABINDEX = TABINDEX + 5;

    //Insert contents for the remaining cells...
    cells[1].innerHTML = copy;

    cells[4].innerHTML = perms;
    cells[5].innerHTML = file_name;
    cells[6].innerHTML = file_size;
    cells[7].innerHTML = file_time;
    cells[8].innerHTML = DIRECTORY_DATA[drow][7]; //File owner. Will be blank on Windows machines.
    cells[9].innerHTML = DIRECTORY_DATA[drow][8]; //File group. Will be blank on Windows machines.

    Directory_Events(E(ckbox_id), E(perms_id), E(file_id), filename);

}//end Assemble_Insert_row() //***************************************




function Build_Directory() {//****************************************

    TABINDEX = <?= $TABINDEX ?>;  //Reset global TABINDEX

    //Has the directory table been init'd yet?  (<tbody id=DIRECTORY_LISTING></tbody>)
    if (E("DIRECTORY_LISTING").rows.length < 1)	{ Init_Dir_table_rows(); }

    //If directory is empty (no files or folders, but still has header & footer rows),
    //then the bottm, L, & R, directory <table> borders do not show.  I don't know why.
    //Inserting a blank row solves this. (Even without inserting any cells.)
    if (DIRECTORY_ITEMS < 1) {
        var tr = E("DIRECTORY_LISTING").insertRow(-1);
        return;
    }


    //Fill 'er up!
    for (var drow = 0; drow < DIRECTORY_ITEMS; drow++) {

        var frow = drow + 1; //See Assemble_Insert_row() for description/explanation.

        var filetype = DIRECTORY_DATA[drow][0];
        var filename = DIRECTORY_DATA[drow][1];
        var filesize = DIRECTORY_DATA[drow][2];
        var filetime = DIRECTORY_DATA[drow][3];
        var link_target = DIRECTORY_DATA[drow][9]; //empty unless file is a symlink.

        //folder or file?
        if (filetype == "dir") {
            var DS        = ' /';
            var href      = ONESCRIPT + PARAM1 + encodeURIComponent(filename);
        } else {
            var DS        = '';
            var href      = ONESCRIPT + PARAM1 + '&amp;f=' + encodeURIComponent(filename) + '&amp;p=edit';
        }

        var file_col = 5; //column of file names

        //The (TABINDEX + 5) accounts for the [m][c][d][x][perms] links which are added in Assemble_Insert_Row().
        var file_name  = '<a id=f'+ frow +'c'+ file_col + ' tabindex='+ (TABINDEX + 5) +' href="' + href  + '"';
            file_name += ' title="<?= hsc($_['Edit_View']) ?>: ' + hsc(filename) + '" >';
            file_name += ICONS[filetype] + '&nbsp;' + hsc(filename + DS + link_target) + '</a>';
        var file_time  = FileTimeStamp(filetime, 1, 0, 0);

        Assemble_Insert_row(drow, href, filename, file_name, file_time);

        TABINDEX++; //For the next item after file_name.
    }//end for (drow...
}//end Build_Directory() //*******************************************




function Directory_Summary() {//**************************************

    var total_items  = DIRECTORY_DATA.length;
    var folder_count = 0;
    var total_bytes  = 0;
    var SUMMARY      = "";

    //Add up file sizes...
    for (x=0; x < DIRECTORY_DATA.length; x++) {
        filetype = DIRECTORY_DATA[x][0];
        filename = DIRECTORY_DATA[x][1];
        if (filetype == "dir"){ folder_count++; }
        total_bytes += DIRECTORY_DATA[x][2];
    }

    //Directory Summary
    SUMMARY += folder_count + " <?= hsc($_['folders']) ?>, &nbsp; ";
    SUMMARY += total_items - folder_count + ' <?= hsc($_['files']) ?>, ';
    SUMMARY += '&nbsp; ' + format_number(total_bytes) + " <?= hsc($_['bytes']) ?>";

    return SUMMARY;

}//end Directory_Summary() //*****************************************




function Sort_and_Show(col, direction) {//****************************

    var DELAY = 0;

    if (DIRECTORY_ITEMS > <?= $MIN_DIR_ITEMS ?>) { //
        //(Any pre-existing $MESSAGE will be displayed after directory is displayed.)
        Display_Messages('<b><?= $_['Working'] ?></b>');

        DELAY = <?= $DELAY_Sort_and_Show_msgs ?>;
    }

    //setTimeout() needed so 'Working' message will actually get displayed *before* the sort.
    setTimeout( function () {
        sort_DIRECTORY(col, direction); //Sort DIRECTORY_DATA
        Build_Directory();
        E('DIRECTORY_FOOTER').innerHTML = Directory_Summary();
        Display_Messages('');
    }, DELAY);

}//end Sort_and_Show() //*********************************************




function Select_All() {//********************************************

    //Does not work in IE if the variable name is spelled the same as the Element Id
    //So, prefix with a dollar sign (a valid character in JS for variable names).
    $select_all_label = E('select_all_label');

    var files = E('mcdselect').elements['files[]'];
    var last  = files.length; //number of files
    var select_all = E('mcdselect').select_all;

    if (select_all.checked) {
        $select_all_label.innerHTML = '<?= hsc($_['Clear_All']) ?>';
    }else{
        $select_all_label.innerHTML = '<?= hsc($_['Select_All']) ?>';
    }

    //Start x at 1 as files[0] is a dummy <input> used to force an array even if only one file is in a folder.
    for (var x = 1; x < last ; x++) { files[x].checked = select_all.checked; }
}//end Select_All() //************************************************




function Confirm_Submit(action) {//***********************************

    var files = E('mcdselect').elements['files[]'];
    var last  = files.length;   //number of files
    var no_files = true;
    var f_msg    = "<?= hsc($_['No_files']) ?>";

    E('mcdselect').mcdaction.value = action;

    //Confirm at least one file is checked
    for (var x = 0; x < last ; x++) {
        if (files[x].checked) { no_files = false ; break; }
    }

    //Don't submit form if no files are checked.
    if ( no_files ) { Display_Messages(f_msg, 1); return false; }

    E('mcdselect').submit(); //submit form.
}//end Confirm_Submit() //********************************************




function Format_Perms(perms_oct) {//**********************************
    //returns them formatted as [7777][ugt rwx rwx rwx]

    //$perms_oct is a 3 or 4 digit octal string (7777).

    //file           file   s s s owner group world
    //permissions   t y p e u g t r w x r w x r w x
    //
    //bits          1 4 2 1 4 2 1 4 2 1 4 2 1 4 2 1
    //octal         1   7     7     7     7     7
    //
    //bits          8 4 2 1 8 4 2 1 8 4 2 1 8 4 2 1
    //hex              F       F       F       F

    var ugt = ['---', '--t', '-g-', '-gt', 'u--', 'u-t', 'ug-', 'ugt']; //setUid setGid sTicky
    var rwx = ['---', '--x', '-w-', '-wx', 'r--', 'r-x', 'rw-', 'rwx'];

    if ((perms_oct.length * 1) > 3) { var ugidsticky = perms_oct.substr(-4, 1); }
    else							{ var ugidsticky = 0; }
    var owner = perms_oct.substr(-3, 1);
    var group = perms_oct.substr(-2, 1);
    var world = perms_oct.substr(-1, 1);

    return "[" + perms_oct + "][" + ugt[ugidsticky] + " " + rwx[owner] + " " + rwx[group] + " " + rwx[world] + "]";

}//end Format_Perms() {//*********************************************

</script>


<?php
}//end Index_Page_scripts() //**************************************************




function Edit_Page_scripts() {//************************************************
    global $_, $ONESCRIPT, $ONESCRIPT_file, $ipath, $param1, $param2, $filename, $LINE_WRAP,
        $MAIN_WIDTH, $WIDE_VIEW_WIDTH, $current_view, $WYSIWYG_VALID, $EDIT_WYSIWYG, $TAB_SIZE;

    //Get current view width.
    $current_view = $MAIN_WIDTH; //default
    if ( $_COOKIE['wide_view'] === "on" ) { $current_view = $WIDE_VIEW_WIDTH; }

    //For [Edit WYSIWYG/Source] button
    $WYSIWYG_onclick  = "parent.location = onclick_params + 'edit'; ";
    $WYSIWYG_onclick .= "document.cookie='edit_wysiwyg=".(!$EDIT_WYSIWYG*1)."';";

    //For [Close] button
    $close_params = $ONESCRIPT.$param1;
    if ( $_SESSION['admin_page'] ) { $close_params .= '&p=admin'; } //If came from admin page, return there.
?>


<script>
function Set_File_Textarea_Width() {
    var Mw  =  parseInt(window.getComputedStyle(Main_div).getPropertyValue("width"));
    var Mbr =  parseInt(window.getComputedStyle(Main_div).getPropertyValue("border-right-width"));
    var Mbl =  parseInt(window.getComputedStyle(Main_div).getPropertyValue("border-left-width"));
    var Lw  =  parseInt(window.getComputedStyle(Line_Numbers_div).getPropertyValue("width"));
    var Lmr =  parseInt(window.getComputedStyle(Line_Numbers_div).getPropertyValue("margin-right"));
    var Fml =  parseInt(window.getComputedStyle(File_textarea).getPropertyValue("margin-left"));
    File_textarea.style.width = (Mw - Lw - Mbr - Mbl- Lmr - Fml) + "px";
}




function Correct_Word_Wrapping(text_area) {

    //Correct word-wrapping and save cursor postion/selection after toggling
    // between view modes (Wide/Normal or Wrap on/off).
    //When width or wrap changed dynamically, the browser doesn't always wrap
    // correctly, or seem to use break-all.
    //
    //This is so line-numbers line-up correctly.

    var tmp = text_area.value;
    var cursorX1 = text_area.selectionStart;
    var cursorx2 = text_area.selectionEnd;
    text_area.value = "";
    text_area.value = tmp;
    text_area.selectionStart = cursorX1;
    text_area.selectionEnd   = cursorx2;
}




function Wide_View() {

    var normal_view_width = '<?= $MAIN_WIDTH ?>';
    var wide_view_width	  = '<?= $WIDE_VIEW_WIDTH ?>';

    //Toggle view width
    if (Wide_View_button.value == "on") {
        Main_div.style.width       = normal_view_width;
        Set_File_Textarea_Width();
        Wide_View_button.innerHTML = "<?= hsc($_['Wide_View'])?>"; //Button label is what to do next click, not current state.
        document.cookie            = 'wide_view=off';
        Wide_View_button.value 	   = "off"
    }else{
        Main_div.style.width       = wide_view_width;
        Set_File_Textarea_Width();
        Wide_View_button.innerHTML = '<?= hsc($_['Normal_View']) ?>';
        document.cookie            = 'wide_view=on';
        Wide_View_button.value 	   = "on"
    }

    Correct_Word_Wrapping(File_textarea);

    Line_Numbers.Set_Line_Numbers();
}




function Toggle_Line_Wrap(on_off) {

    if (on_off.value == "on") {
        on_off.value  				 = "off"
        document.cookie 			 = 'line_wrap=off'
        File_textarea.style["white-space"] = "pre";
        E('w_on').style.textDecoration  = "none";
        E('w_off').style.textDecoration = "underline";
    }
    else {
        on_off.value  				 = "on"
        document.cookie				 = 'line_wrap=on'
        File_textarea.style["white-space"] = "pre-wrap";
        E('w_on').style.textDecoration  = "underline";
        E('w_off').style.textDecoration = "none";
    }

    Correct_Word_Wrapping(File_textarea);

    Line_Numbers.Set_Line_Numbers();

}//end Toggle_Line_Wrap()




function Reset_file_status_indicators() {
    changed = false;
    File_textarea.style.backgroundColor = "#F5FFF5";  //light green
    Save_File_button.style.borderColor  = "";
    Save_File_button.innerHTML          = "<?= hsc($_['save_1'])?>";
    Reset_button.disabled               = "disabled";
}




function Check_for_changes(event){
    if (!event) {var event = window.event;} //if IE
    var keycode = event.keyCode? event.keyCode : event.charCode;
    changed = (File_textarea.value != start_value);

    if (changed){
        E('message_box').innerHTML = " "; //Must have a space, or it won't clear the msg.
        File_textarea.style.backgroundColor    = "white";
        Save_File_button.style.borderColor	   = "#F33";
        Save_File_button.innerHTML			   = "<?= hsc($_['save_2'])?>";
        Reset_button.disabled				   = "";
    }else{
        Reset_file_status_indicators()
    }
}




//Reset textarea value to when page was loaded.
//Used by [Reset] button, and when page unloads (browser back, etc).
//Needed becuase if the page is reloaded (ctl-r, or browser back/forward, etc.),
//the text stays changed, but var "changed" gets set to false, which looses warning.
//
function Reset_File() {

    <?php //use addslashes() because this is for a js alert() or confirm(), not HTML ?>
    if (changed) { if ( !(confirm("<?= addslashes($_['confirm_reset']) ?>")) ) { return false; } }

    File_textarea.value = start_value;
    Reset_file_status_indicators();

    File_textarea.selectionStart = 0;
    File_textarea.selectionEnd = 0;

    Line_Numbers.Set_Line_Numbers();

    Close_button.focus();

    //needed so textarea cursor selectionStart/End stay set to 0.  I don't known why. I wish I did...
    return false;
}




function Line_Numbering(wrapper_id, line_numbers_id, listing_id, line0_id, line1_id) {


    //***** functions ************************************************

    function Display_Width_Chars() {
        return Math.floor((listing.clientWidth - padding_L - padding_R) / char_width)
    }



    function Line_Count(str) {
        var i = 0
        var line_count = 0;

        for (i = 0; i < str.length; ++i) {
            //Line-endings are normalized to \n elsewhere in OFCMS. (server-side)
            if(str[i] == '\n') { line_count++; }
        }

        line_count++;  //Last line doesn't have a new-line at the end.
        return line_count;

    }//end Line_Count()



    function Effective_Line_Length(line, tab_size){
        var TAB = "\t";
        var effective_length = 0;
        var next_tab_stop    = 0;

        for (x = 0; x < line.length; x++) {
            //At this point, this may include a tab char, but not any subsequent space(s) to the next tab-stop.
            effective_length++;
            if (effective_length > next_tab_stop) {next_tab_stop += tab_size;}
            if (line[x] == TAB) {effective_length = next_tab_stop;}  //adds the subsequent space to the next tab-stop.
        }

        return effective_length;

    }//end Effective_Line_Length()



    function Create_Line_Numbers(){

        var current_width = Display_Width_Chars();

        var lc = Line_Count(listing.value)
        var numbers  = '';	//String to contain the line numbers.
        var lines    = listing.value.split('\n');
        var cur_line = 0;	//listing[cur_line]
        var line_len = 0;	//line length adjusted for tabs, to get effective length, and effective word-wrap.
        var effective_line_length = 0; //line length after tabs are expanded...
        var line_count_wrapped = 0;   //number of visual lines, not neccessarily numbered lines, with line-wrap on.

        for(var num = 1; num <= lc; num++) {

            cur_line = num - 1;

            numbers += num + "\n";  // \n is MUCH faster than <br>

            effective_line_length = Effective_Line_Length(lines[cur_line], TAB_SIZE);

            if ((Toggle_Wrap.value == "on") && (effective_line_length > current_width)) {

                line_count_wrapped = Math.ceil(effective_line_length / current_width) - 1; //# of addtional lines after wrapped...

                //If desired, to mark a continued line, put a dash, plus, or whatever, just before the \n.
                for (var x = 0; x < (line_count_wrapped); x++) { numbers += "\n"; }
            }
        }

        line_one.innerHTML = numbers;

    }//end Create_Line_Numbers()



    function Set_Line_Numbers(event) {

        //Ignore a few keys that don't affect/not needed for line numbering.
        if (event) { //event
            if (window.event) { var event = window.event }

            //Page Up/Down, End, Home, Arrow L/U/R/D
            if ((event.keyCode > 32) && (event.keyCode < 41)) {return;}
        }

        Create_Line_Numbers();

        //Scroll/align the line numbers with the textarea.
        line_numbers.scrollTop = listing.scrollTop;

    }//end Set_Line_Numbers()

    //*** end functions **********************************************



    //*** common variables *******************************************

    var wrapper		 = E(wrapper_id);
    var line_numbers = E(line_numbers_id);
    var listing 	 = E(listing_id);
    var line_zero    = E(line0_id); //empty: used for char-width calc.
    var line_one     = E(line1_id); //<div>1</div> will contain the line numbers.

    line_zero.innerHTML = ""; //Just makin' sure. It's only used for the char_width calc.

    var char_width     = (line_zero.offsetLeft - line_one.offsetLeft);
    var padding_L 	   = parseInt(window.getComputedStyle(listing).getPropertyValue("padding-left"));
    var padding_R 	   = parseInt(window.getComputedStyle(listing).getPropertyValue("padding-right"));


    //Check for tabSize css support.  Default/standard size is 8.
    var TAB_SIZE = 8;

    if ("tabSize" in document.body.style){
        TAB_SIZE = <?= $TAB_SIZE ?>;
        listing.style.tabSize = TAB_SIZE;
    }
    else if ("MozTabSize" in document.body.style) {
        TAB_SIZE = <?= $TAB_SIZE ?>;
        listing.style.MozTabSize = TAB_SIZE;
    }
    else if ("OTabSize" in document.body.style) {
        TAB_SIZE = <?= $TAB_SIZE ?>;
        listing.style.OTabSize = TAB_SIZE;
    }

    //*** end common variables ***************************************



    //*** attach events **********************************************

    if (listing.getAttribute('readonly') === null) {
        //If not readonly (ie: editable)...
        listing.addEventListener("keyup",   function(event){Set_Line_Numbers(event)});
        listing.addEventListener("mouseup", function(event){Set_Line_Numbers(event)});
        listing.addEventListener("paste",   function(event){Set_Line_Numbers(event)});
    }

    listing.addEventListener("scroll",  function(event){Set_Line_Numbers(event)});

    //*** end attach events ******************************************


    //Set initial cursor location to start of file, instead of end (the default).
    listing.selectionStart = 0;
    listing.selectionEnd = 0;


    //Set_Line_Numbers() is also used by Wide_View() & Toggle_Line_Wrap().
    //Toggle_Line_Wrap() will make the initial call to Set_Line_Numbers().
    var LN = {};
    LN.Set_Line_Numbers = Set_Line_Numbers;

    return LN;

}//end Line_Numbering() //********************************************




//***** Global variables *********************************************
var Main_div		   = E('main');
var Ln_Editor_wrapper  = E('wrapper_linenums_editor');
var Line_Numbers_div   = E('line_numbers');
var File_textarea      = E('file_editor');
var View_Raw_button    = E('view_raw');
var Wide_View_button   = E('wide_view');
var WYSIWYG_button	   = E('edit_WYSIWYG');
var Close_button       = E('close1');
var Toggle_Wrap		   = E('toggle_wrap');
var Save_File_button   = E('save_file');
var Reset_button       = E('reset');
var Rename_File_button = E('renamefile_btn');
var Copy_File_button   = E('copyfile_btn');
var Delete_File_button = E('deletefile_btn');

var submitted  = false;
var changed    = false;

if (File_textarea) { var start_value = File_textarea.value; }

var onclick_params = '<?= $ONESCRIPT.$param1.'&f='.rawurlencode(basename($filename)).'&p=' ?>';

//Wide View / Normal View init...
Main_div.style.width = "<?= $current_view ?>"; //Set current width
//***** end Global variables *****************************************



//***** Events assignments *******************************************
//[Close], and [Copy], should always be present on Edit Page.
Close_button.onclick     = function () { parent.location = '<?= $close_params ?>'; }
Copy_File_button.onclick = function () { parent.location = onclick_params + 'copyfile';   }


//These elements do not exist if file is not editable, or maybe if in WYSIWYG mode.
if (View_Raw_button)    { View_Raw_button.onclick 	 = function () {window.open(onclick_params + 'raw_view'); } }
if (Wide_View_button)   { Wide_View_button.onclick 	 = function () {Wide_View();}    }
if (Save_File_button)   { Save_File_button.onclick 	 = function () {submitted=true;} }
if (WYSIWYG_button  )   { WYSIWYG_button.onclick 	 = function () {<?= $WYSIWYG_onclick ?>} }
if (Rename_File_button) { Rename_File_button.onclick = function () {parent.location = onclick_params + 'renamefile';} }
if (Delete_File_button) { Delete_File_button.onclick = function () {parent.location = onclick_params + 'deletefile';} }
if (File_textarea)      { File_textarea.addEventListener("keyup", function(event) {Check_for_changes(event);}) }
if (Toggle_Wrap)		{ Toggle_Wrap.onclick 		 = function () {Toggle_Line_Wrap(this);} }


window.onbeforeunload = function() {
    if ( changed && !submitted ) {
        //FF4+ Ingores the supplied msg below & only uses a system msg for the prompt.
        <?php //use addslashes(), not hsc(), because this is for a js alert() / confirm(), not HTML ?>
        return "<?= addslashes($_['unload_unsaved']) ?>";
    }
}


window.onunload = function() {
    //without this, a browser back then forward would reload file with local/
    // unsaved changes, but with a green b/g as tho that's the file's saved contents.
    if (!submitted) {
        File_textarea.value = start_value;
        Reset_file_status_indicators();
    }
}
//***** end Events assignments ***************************************



//***** A few function calls that only matter...
if (File_textarea) {

    Set_File_Textarea_Width();

    Line_Numbers = Line_Numbering("wrapper_linenums_editor", "line_numbers", "file_editor", "line_0", "line_1");

    //Set default/page load condition, and also init's the line numbers...
    //We're just setting the initial condition now, not actually toggling,
    //so "pre-toggle" the indicator here as Toggle_Line_Wrap() will toggle it back...
    //
    if (Toggle_Wrap.value === "off") {Toggle_Wrap.value = "on";} else {Toggle_Wrap.value = "off";}
    Toggle_Line_Wrap(Toggle_Wrap);

    Reset_file_status_indicators();
}


Close_button.focus();
</script>


<?php
}//end Edit_Page_scripts() //***************************************************




function pwun_event_scripts($form_id, $button_id, $pwun='') {//*****************
    global $_;

    //pre-hash "new1" & "new2" only if changing p/w (not if changing u/n).
    $hash_new_new = '';
    if ($pwun == 'pw') {
        $hash_new_new = " hash('new1'); hash('new2');";
    }//end if changing p/w --------------------------------------
?>

<script>
var $form          = E('<?= $form_id ?>');
var $submit_button = E('<?= $button_id ?>');
var $pwun_msg_box   = E('message_box');
var $thispage      = false; //Used to ignore keyup if keydown started on prior page.
var $submitdown    = false; //Used in document.mouseup event


//Key or mouse down events trigger "Working..." message.
$form.onkeydown            = function(event) {events_down(event, 13);} //Form captures Enter key (13)
$submit_button.onkeydown   = function(event) {events_down(event, 32);} //Submit button captures Space key (32)
$submit_button.onmousedown = function(event) {$submitdown = true; events_down(event,  0);}

//Key or mouse up events trigger hash and submit.
$form.onkeyup              = function(event) {events_up(event, 13);}
$submit_button.onkeyup     = function(event) {events_up(event, 32);}
$submit_button.onmouseup   = function(event) {events_up(event,  0);} //For mouse events, keyCode is 0 or undefined, and ignored.


function events_down(event, capture_key) {
    if (!event) {var event = window.event;} //if IE
    $thispage = true; //Make sure keydown was on this page.
    if ((event.type.substr(0,3) == 'key') && (event.keyCode != capture_key)) {return true;}
    $pwun_msg_box.innerHTML = '<div class="message_box_contents"><b><?= hsc($_['Working']) ?></b>';
}


function events_up(event, capture_key) {
    if (!event) {var event = window.event;} //if IE
    if (!$thispage) {return false;} //Ignore keyup if there was no keydown on this page.
    if ((event.type.substr(0,3) == 'key') && (event.keyCode != capture_key)) {return true;}
    if (!pre_validate_pwun()) {return false};
    $submit_button.disabled = "disabled";  //Prevent extra clicks
    hash('password');
    <?= $hash_new_new ?>
    $form.submit();
}


document.onmouseup = function(event) {
    if (!event) {var event = window.event;} //if IE

    //if mousedown was on submit button, but mouseup wasn't, clear message.
    var target = event.target || event.srcElement; //target = most brosers || IE
    if ($submitdown && ($submit_button.id != target.id) ) { $pwun_msg_box.innerHTML = ''; }
    $submitdown = false;
}


function pre_validate_pwun() {
    var $pw = E('password');

    var $username = $pw;
    var $new1 	  = $pw;
    var $new2 	  = $pw;

    if (E('username')){
        $username = E('username');
    }
    if (E('new1')){
        $new1 = E('new1');
        $new2 = E('new2');
    }


    //If any field is blank..
    if (($username.value == '') || ($pw.value == '') || ($new1.value == '') || ($new2.value == '')) {
        $pwun_msg_box.innerHTML = '<div class="message_box_contents"><b><?= hsc($_['change_pw_07']) ?></b>';
        return false;
    }
    //If new & confirm new values do not match...
    if (trim($new1.value) != trim($new2.value)) {
        $pwun_msg_box.innerHTML = '<div class="message_box_contents"><b><?= hsc($_['change_pw_04']) ?></b>';
        return false;
    }
    return true;
}//end pre_validate_pwun()
</script>


<?php
}//end pwun_event_scripts() //**************************************************




function js_hash_scripts() {//**************************************************
    global $SALT, $PRE_ITERATIONS;

//Used to hash p/w's client side.  This does not really add any security to the
//server side application that uses it, as the "pre-hash" becomes the actual p/w
//as far as the server is concerned, and is just as vulnerable to exposure while
//in transit. However, this does help to protect the user's plain-text p/w, which
//may be used elsewhere.
?>

<script>
/* hex_sha256() (and directly associated functions)
 *
 * A JavaScript implementation of SHA-256, as defined in FIPS 180-2
 * Version 2.2 Copyright Angel Marin, Paul Johnston 2000 - 2009.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for details.
 * Also http://anmar.eu.org/projects/jssha2/
 */
var hexcase=0;function hex_sha256(a){return rstr2hex(rstr_sha256(str2rstr_utf8(a)))}function sha256_vm_test(){return hex_sha256("abc").toLowerCase()=="ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad"}function rstr_sha256(a){return binb2rstr(binb_sha256(rstr2binb(a),a.length*8))}function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}function rstr2binb(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(24-c%32)}return a}function binb2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(24-c%32))&255)}return a}function sha256_S(b,a){return(b>>>a)|(b<<(32-a))}function sha256_R(b,a){return(b>>>a)}function sha256_Ch(a,c,b){return((a&c)^((~a)&b))}function sha256_Maj(a,c,b){return((a&c)^(a&b)^(c&b))}function sha256_Sigma0256(a){return(sha256_S(a,2)^sha256_S(a,13)^sha256_S(a,22))}function sha256_Sigma1256(a){return(sha256_S(a,6)^sha256_S(a,11)^sha256_S(a,25))}function sha256_Gamma0256(a){return(sha256_S(a,7)^sha256_S(a,18)^sha256_R(a,3))}function sha256_Gamma1256(a){return(sha256_S(a,17)^sha256_S(a,19)^sha256_R(a,10))}function sha256_Sigma1512(a){return(sha256_S(a,14)^sha256_S(a,18)^sha256_S(a,41))}function sha256_Gamma1512(a){return(sha256_S(a,19)^sha256_S(a,61)^sha256_R(a,6))}var sha256_K=new Array(1116352408,1899447441,-1245643825,-373957723,961987163,1508970993,-1841331548,-1424204075,-670586216,310598401,607225278,1426881987,1925078388,-2132889090,-1680079193,-1046744716,-459576895,-272742522,264347078,604807628,770255983,1249150122,1555081692,1996064986,-1740746414,-1473132947,-1341970488,-1084653625,-958395405,-710438585,113926993,338241895,666307205,773529912,1294757372,1396182291,1695183700,1986661051,-2117940946,-1838011259,-1564481375,-1474664885,-1035236496,-949202525,-778901479,-694614492,-200395387,275423344,430227734,506948616,659060556,883997877,958139571,1322822218,1537002063,1747873779,1955562222,2024104815,-2067236844,-1933114872,-1866530822,-1538233109,-1090935817,-965641998);function binb_sha256(n,o){var p=new Array(1779033703,-1150833019,1013904242,-1521486534,1359893119,-1694144372,528734635,1541459225);var k=new Array(64);var B,A,z,y,w,u,t,s;var r,q,x,v;n[o>>5]|=128<<(24-o%32);n[((o+64>>9)<<4)+15]=o;for(r=0;r<n.length;r+=16){B=p[0];A=p[1];z=p[2];y=p[3];w=p[4];u=p[5];t=p[6];s=p[7];for(q=0;q<64;q++){if(q<16){k[q]=n[q+r]}else{k[q]=safe_add(safe_add(safe_add(sha256_Gamma1256(k[q-2]),k[q-7]),sha256_Gamma0256(k[q-15])),k[q-16])}x=safe_add(safe_add(safe_add(safe_add(s,sha256_Sigma1256(w)),sha256_Ch(w,u,t)),sha256_K[q]),k[q]);v=safe_add(sha256_Sigma0256(B),sha256_Maj(B,A,z));s=t;t=u;u=w;w=safe_add(y,x);y=z;z=A;A=B;B=safe_add(x,v)}p[0]=safe_add(B,p[0]);p[1]=safe_add(A,p[1]);p[2]=safe_add(z,p[2]);p[3]=safe_add(y,p[3]);p[4]=safe_add(w,p[4]);p[5]=safe_add(u,p[5]);p[6]=safe_add(t,p[6]);p[7]=safe_add(s,p[7])}return p}function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)};
</script>




<script>
//OneFileCMS wrapper function for using the hex_sha256() functions
function hash($element_id) {
    var $input = E($element_id);
    var $hash = trim($input.value); //trim() defined in Common_Scripts()
    var $SALT = '<?= $SALT ?>';
    var $PRE_ITERATIONS = <?= $PRE_ITERATIONS ?>; //$PRE_ITERATIONS also used in hashit()
    if ($hash.length < 1) {$input.value = $hash; return;} //Don't hash nothing.
    for ( $x=0; $x < $PRE_ITERATIONS; $x++ ) { $hash = hex_sha256($hash + $SALT); }
    $input.value = $hash;
}//end hash()
</script>


<?php
}//end js_hash_scripts() //*****************************************************

?>
