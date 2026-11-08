<?php
// created: 2011-06-21 13:32:53
$dictionary["asol_events_asol_activity"] = array(
  'true_relationship_type' => 'many-to-many',
  'relationships' => array(
    'asol_events_asol_activity' => array(
      'lhs_module' => 'asol_Events',
      'lhs_table' => 'asol_events',
      'lhs_key' => 'id',
      'rhs_module' => 'asol_Activity',
      'rhs_table' => 'asol_activity',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'asol_events_asol_activity',
      'join_key_lhs' => 'asol_events_id',
      'join_key_rhs' => 'asol_activity_id',
    ),
  ),
  'table' => 'asol_events_asol_activity',
  'fields' => array(
    array(
      'name' => 'id',
      'type' => 'varchar',
      'len' => 36,
    ),
    array(
      'name' => 'date_modified',
      'type' => 'datetime',
    ),
    array(
      'name' => 'deleted',
      'type' => 'bool',
      'len' => '1',
      'default' => '0',
      'required' => true,
    ),
    array(
      'name' => 'asol_events_id',
      'type' => 'varchar',
      'len' => 36,
    ),
    array(
      'name' => 'asol_activity_id',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' =>
  array(
    array(
      'name' => 'asol_events_asol_activity_pk',
      'type' => 'primary',
      'fields' =>
      array(
        'id',
      ),
    ),
    array(
      'name' => 'asol_events_asol_activity_alt',
      'type' => 'alternate_key',
      'fields' =>
      array(
        'asol_events_id',
        'asol_activity_id',
      ),
    ),
  ),
);
?>
