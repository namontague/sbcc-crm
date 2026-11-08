<?php

global $mod_strings;

echo "<form id='import_form' name='import_form' action='index.php?module=asol_Process&action=import' method='post' enctype='multipart/form-data' >";
echo "<input name='imported_process' type='file' />";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<input type='submit' value='".$mod_strings['LBL_ASOL_IMPORT']."' />";
echo "</form>";
?>
