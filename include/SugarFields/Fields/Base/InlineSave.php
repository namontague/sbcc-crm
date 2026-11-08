<?php
    $module_bean = $_REQUEST['module_bean'];
    $field = $_REQUEST['field'];
    $bean_id = $_REQUEST['list_item_id'];
    $value = $_REQUEST['value'];
    
    if(isset($module_bean) && isset($field) && isset($bean_id) && isset($value))
    {
        $bean =  new $module_bean();
        $bean->retrieve($bean_id);
        if($value != $bean->$field)
        {
            $bean->$field = $value;
            $bean->save();
        }
    }
?>