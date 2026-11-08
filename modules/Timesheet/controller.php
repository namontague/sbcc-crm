<?php
require_once('include/MVC/Controller/SugarController.php');

class TimesheetController extends SugarController
{ 
  function action_save()
  {
    if (isset($this->bean->id) && 
        isset($this->bean->fetched_row['parent_id']) && 
        $this->bean->parent_id != $this->bean->fetched_row['parent_id']) {
      // if we have changed the parent id, make correct calculations for 
      // total efforts
      // before delete hook will be fired
      // and calculate total for previous parent object
      $new_parent_id = $this->bean->parent_id;
      // set old parent id for correct calculation
      $this->bean->parent_id = $this->bean->fetched_row['parent_id'];
      $this->bean->mark_deleted($this->bean->id);
      // restore new parent id
      $this->bean->parent_id = $new_parent_id;
      // clear fetched_row array for calculation purposes
      $this->bean->fetched_row = array();

      // now unset bean id to initiate creating new record
      unset($this->bean->id);
      unset($this->bean->date_entered);
    }
    parent::action_save();
  }

   function action_List()
    {
		$this->view = 'list';
    }
}