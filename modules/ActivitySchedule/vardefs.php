<?php

$dictionary['ActivitySchedule'] = array(
    'table'=>'activity_schedules',
    'audited'=>false,
    'fields' => array(
        'type' => array(
           'name' => 'type',
           'type' =>'enum',
           'options' => 'activity_schedule_type_list',
           'comment' => 'The type of schedule',
           'vname' => 'LBL_ACTIVITY_SCHEDULE_TYPE',
           'len' => 4,
         ),
        'repeat_interval' => array(
           'name' => 'repeat_interval',
           'type' =>'int',
           'comment' => 'Interval of repetition',
           'len' => 4,
           'default' =>1
         ),
        'startdate' => array(
           'name' => 'startdate',
           'type' =>'datetime',
           'comment' => 'The date the schedule starts',
         ),
        'nextdate' => array(
           'name' => 'nextdate',
           'type' =>'datetime',
           'comment' => 'The next date the schedule is due to be active',
         ),
        'completion_offset_secs' => array(
           'name' => 'completion_offset_secs',
           'type' =>'int',
           'comment' => 'The offset of the due/completion date from the start date (in seconds)',
           'default' => 0,
         ),
        'enddate' => array(
           'name' => 'enddate',
           'type' =>'date',
           'comment' => 'The date the schedule ends',
         ),
        'numwithinmonth' => array(
           'name' => 'numwithinmonth',
           'type' =>'varchar',
           'len' => 30,
           'comment' => 'The day/week within month to repeat on',
         ),
        'daysofweek' => array(
           'name' => 'daysofweek',
           'type' =>'varchar',
           'len' => 20,
           'comment' => 'Days of week to repeat on',
           'isMultiSelect' => true
         ),
        'parent_type'=>  array(
            'name'=>'parent_type',
            'vname'=>'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType'=>'varchar',
            'group'=>'parent_name',
            'len'=>25,
            'comment' => 'The Sugar object to which the call is related'
        ),

        'parent_name'=> array(
            'name'=> 'parent_name',
            'parent_type'=>'record_type_display' ,
            'type_name'=>'parent_type',
            'id_name'=>'parent_id',
            'vname'=>'LBL_LIST_RELATED_TO',
            'type'=>'parent',
            'group'=>'parent_name',
            'source'=>'non-db',
            'options'=> 'parent_type_display',
        ),
        'parent_id'=> array(
            'name'=>'parent_id',
            'vname'=>'LBL_LIST_RELATED_TO_ID',
            'type'=>'id',
            'group'=>'parent_name',
            'reportable'=>false,
            'comment' => 'The ID of the parent Sugar object identified by parent_type'
        ),

     ),

    'relationships'=>array (
    ),
    'optimistic_lock'=>true,
);

VardefManager::createVardef('ActivitySchedule','ActivitySchedule');

?>