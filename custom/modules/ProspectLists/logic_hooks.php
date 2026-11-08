<?php
// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
 $hook_version = 1; 
$hook_array = Array(); 
// position, file, function 

$hook_array['after_relationship_add'] = Array(); 
$hook_array['after_relationship_add'][] = Array(99, 'SyncSMSListMember', 'modules/SMS_SMS/Hooks/logic_hooks.php','sms_hooks', 'ManageList'); 
$hook_array['after_relationship_add'][] = Array(99, 'Add contacts to list form selected camp', 'custom/modules/ProspectLists/sbcc_logic_hooks.php','SBCC_ProspectLists_hooks', 'AddtoList'); 
$hook_array['after_relationship_delete'] = Array(); 
$hook_array['after_relationship_delete'][] = Array(99, 'SyncSMSListMember', 'modules/SMS_SMS/Hooks/logic_hooks.php','sms_hooks', 'ManageList'); 



?>