<?php
/*********************************************************************************
 * (C) 2010 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 ********************************************************************************/
if (!defined('sugarEntry') || !sugarEntry) {
  die('Not A Valid Entry Point');
}

require_once('modules/vCals/vCal.php');

if (is_admin($current_user)) {

  global $mod_strings;
  echo $mod_strings['LBL_REBUILD_FREEBUSY_CACHE_DESC'];

  $user = new User();
  $users = $user->get_full_list();

  foreach ($users as $user) {
    vCal::cache_sugar_vcal($user);
  }
}
else {
  die('Admin Only Section');
}
?>