<?php
// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
 $hook_version = 1; 
$hook_array = Array(); 
// position, file, function 

$hook_array['after_save'] = Array(); 

$hook_array['after_save'][] = Array(90, 'Update Participants', 'custom/modules/Contacts/logic_hooks_class.php','contact_logic_hooks', 'after_save_method'); 
$hook_array['after_save'][] = Array(99, 'Update SMS list member', 'modules/SMS_SMS/Hooks/logic_hooks.php','sms_hooks', 'UpdateListMember'); 

$hook_array['before_save'] = Array(); 
$hook_array['before_save'][] = Array(99, 'Store unaltered bean', 'modules/SMS_SMS/Hooks/logic_hooks.php','sms_hooks', 'before_save_method'); 
?>