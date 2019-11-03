<?php
//******************************************************************************
//Main logic to determine page action
//******************************************************************************

Default_Language();

$setup_messages = System_Setup();

Session_Startup();

if (!isset($_SESSION['admin_page'])) {
    $_SESSION['admin_page']  = false;
    $_SESSION['admin_ipath'] = '';
}

if ($_SESSION['valid']) {

    undo_magic_quotes();

    Init_ICONS();

    Get_GET();

    if ($page == "phpinfo") { phpinfo(); die; }

    Valid_Path($ipath, true);

    Validate_params();

    Init_Macros(); //Needs to be after Get_Get()/Validate_params()/Valid_Path()

    //$ACCESS_ROOT.$ACCESS_PATH == $ipath
    $ipath_len = mb_strlen($ipath);
    $ACCESS_PATH = '';
    if (($ACCESS_ROOT_len < $ipath_len)) {
        $ACCESS_PATH = trim(mb_substr($ipath, $ACCESS_ROOT_len), ' /').'/';
    }

    Respond_to_POST();

    Verify_Page_Conditions(); //Must come after Respond_to_POST()

    if ($page != "login") { $MESSAGE .= $setup_messages; } //Must come after Verify_Page_Conditions()

    if (isset($_POST['new_perms'])) { die(); } //die() here just for clarity.

    Update_Recent_Pages();

    //Don't show current/path/ header on some pages.
    $Show_Path = true;
    $pages_dont_show_path = array("login","admin","hash","changepw","changeun");
    if ( in_array($page, $pages_dont_show_path) ){ $Show_Path = false; } //

}//end if $_SESSION[valid]

//end logic to determine page action *******************************************

?>
