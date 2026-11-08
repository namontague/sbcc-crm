<?php
class countTotalHook
{
  // before_save is used in Cases, Project and ProjectTask
  function before_save(&$bean, $event = null, $arguments = null)
  {
    global $current_user;

    // total actual and total billable hours should not be editable
    $bean->actual_effort = @$bean->fetched_row['actual_effort'];
    $bean->billable_effort = @$bean->fetched_row['billable_effort'];

    // budget field could be edited by Project leader only
    switch ($bean->object_name) {
      case 'Case':
        $q = "SELECT assigned_user_id FROM accounts WHERE id = '{$bean->account_id}'";
        $assigned_user_id = $bean->db->getOne($q);
        $condition = $current_user->id != $assigned_user_id;
      break;

      case 'Project':
        $condition = $current_user->id != $bean->assigned_user_id;
      break;

      case 'ProjectTask':
        if (!empty($bean->project_id)) {
          $q = "SELECT assigned_user_id FROM project WHERE id = '{$bean->project_id}'";
          $assigned_user_id = $bean->db->getOne($q);
          $condition = $current_user->id != $assigned_user_id;
        }
        else {
          $condition = $current_user->id != $bean->assigned_user_id;
        }
      break;
    }
    if ($condition && !is_admin($current_user)) {
      $bean->budget = @$bean->fetched_row['budget'];
    }
  }

}
?>