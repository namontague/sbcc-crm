<?PHP
/*********************************************************************************
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 ********************************************************************************/

class WorkflowEvent extends SugarBean
{
  // WorkflowEvent Bean properties
  var $new_schema = true;
  var $module_dir = 'WorkflowEvents';
  var $object_name = 'WorkflowEvent';
  var $table_name = 'workflowevents';
  var $importable = false;

  // WorkflowEvent Bean fields
  var $id;
  var $name;

  // WorkflowEvent Bean methods
  function WorkflowEvent() {
    parent::SugarBean();
  }

  public function save($check_notify = false) {
	  return parent::save($check_notify);
  }
}
?>