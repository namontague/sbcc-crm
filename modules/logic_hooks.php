<?php
$generic_hooks = array(
    'after_ui_footer' => array(
         array(10, 'popup_onload', 'modules/SecurityGroups/AssignGroups.php','AssignGroups', 'popup_onload'),
    ),
    'after_ui_frame' => array(
        array(0, 'timerReminder', 'modules/Timesheet/logicHook.php','TimesheetLogicHook', 'reminder'),
        array(1, "closeOpenTimers", "modules/Timesheet/logicHook.php", "TimesheetLogicHook", "closeOpenTimers"),
        array(20, 'mass_assign', 'modules/SecurityGroups/AssignGroups.php','AssignGroups', 'mass_assign'),
    ),
    'after_save' => array(
        array(30, 'popup_select', 'modules/SecurityGroups/AssignGroups.php','AssignGroups', 'popup_select'),
    ),
    'after_retrieve' => array(
        array(68, 'Lock Individual Homepages', 'modules/Users/homepage_manager.php','defaultHomepage', 'resetConfig'),
    ),
);

$module_hooks = array(
    "Cases" => array(
        "before_save" => array(
            array(0, "total_hours", "modules/Timesheet/countTotalHook.php", "countTotalHook", "before_save"),
        ),
    ),
    "Project" => array(
        "before_save" => array(
            array(0, "total_hours", "modules/Timesheet/countTotalHook.php", "countTotalHook", "before_save")
        ),
    ),
    "ProjectTask" => array(
        "before_save" =>array(
            array(0, "total_hours", "modules/Timesheet/countTotalHook.php", "countTotalHook", "before_save")
        ),
    ),
    "Users" => array(
        "after_login" =>array(
            array(0, "Check users Homepage", "modules/Users/homepage_manager.php", "defaultHomepage", "afterLogin")
        ),
    ),

    "Calls" => array(
        "after_save" => array(
          array(0, "WakeUp Holded Item",  "modules/asol_Process/wfm_on_hold.php", "wfm_hook_on_hold", "wakeup_on_hold"),
          array(1, "Update Last Contacted",  "include/eintel/utils/logic_hook_functions.php", "eintel_logic_hooks", "updateLastContacted")

        ),
        "after_delete"=> array(
          array(0, "WakeUp Holded Item",  "modules/asol_Process/wfm_on_hold.php", "wfm_hook_on_hold", "wakeup_on_hold")
        ),
    ),

    "Tasks" => array(
        "after_save"=> array(
          array(0, "WakeUp Holded Item",  "modules/asol_Process/wfm_on_hold.php", "wfm_hook_on_hold", "wakeup_on_hold")
        ),
        "after_delete"=> array(
          array(0, "WakeUp Holded Item",  "modules/asol_Process/wfm_on_hold.php", "wfm_hook_on_hold", "wakeup_on_hold")
        ),
    ),

    "Meetings"=> array(
        "after_save" => array(
          array(0,  "Update Last Contacted",  "include/eintel/utils/logic_hook_functions.php", "eintel_logic_hooks", "updateLastContacted")
        ),
    ),

// These hooks have been moved into the appropriate classes in Timesheet module
//    'Timesheet' => array(
//        'before_save' => array(
//            array(0, "total_hours", "modules/Timesheet/countTotalHook.php", "countTotalHook", "doCount"),
//            array(1, "misc", "modules/Timesheet/logicHook.php", "TimesheetLogicHook", "before_save"),
//        ),
//        "before_delete" => array(
//            array(0, "total_hours", "modules/Timesheet/countTotalHook.php", "countTotalHook", "doCountOnDelete"),
//        ),
//    ),
);



?>
