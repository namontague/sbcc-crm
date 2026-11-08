<?php

    $record_id = $_REQUEST['record_id'];
    $module_bean = $_REQUEST['bean'];
    $field = $_REQUEST['field'];    
    $value = $_REQUEST['value'];    
    
    if(isset($record_id) && isset($value) && isset($module_bean) &&isset($field)){
        $bean =  new $module_bean();
        $bean->retrieve($record_id);
        if($value !=  $bean->$field){
            $bean->$field = ($value == 'true' ? (bool) 1 : (bool) 0);
            $bean->save();
        }
    }
?>