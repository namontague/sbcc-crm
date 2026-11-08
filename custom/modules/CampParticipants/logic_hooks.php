<?php
// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
$hook_version = 1; 
$hook_array = Array(); 
// position, file, function 

$hook_array['after_relationship_add'] = Array(); 
$hook_array['after_relationship_add'][] = Array(98, 'Add participant to list', 'custom/modules/CampParticipants/logic_hooks_class.php','logic_hooks_class', 'after_relationship_add_method'); 
$hook_array['after_relationship_delete'] = Array(); 
$hook_array['after_relationship_delete'][] = Array(98, 'Remove participant from list', 'custom/modules/CampParticipants/logic_hooks_class.php','logic_hooks_class', 'after_relationship_remove_method'); 

$hook_array['after_retrieve'] = Array();
$hook_array['after_retrieve'][] = Array(
        //Processing index. For sorting the array.
        1,       
        //Label. A string value to identify the hook.
        'after_retrieve example',       
        //The PHP file where your class is located.
        'custom/modules/CampParticipants/logic_hooks_class.php',       
        //The class the method is in.
        'logic_hooks_class',       
        //The method to call.
        'after_retrieve_method'
    );

$hook_array['process_record'] = Array();
$hook_array['process_record'][] = Array(
        //Processing index. For sorting the array.
        1,       
        //Label. A string value to identify the hook.
        'process_record example',       
        //The PHP file where your class is located.
        'custom/modules/CampParticipants/logic_hooks_class.php',       
        //The class the method is in.
        'logic_hooks_class',       
        //The method to call.
        'process_record_method'
    );


?>