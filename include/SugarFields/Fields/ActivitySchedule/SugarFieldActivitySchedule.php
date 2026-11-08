<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * SugarFieldActivitySchedule.php
 * SugarFieldActivitySchedule translates and displays fields from a vardef definition into different formats
 * for EditViews and DetailViews.  A sample invocation from a Meta-Data file is as follows:
 * 
 *  array (
 * 	   'name' => 'repetition_type',
 *	   'type' => 'activityschedule',
 *	   'displayParams'=>array('key'=>'repetition'),
 *  ),
 * 
 * Where name is set to the field for ACL verification, type is set to 'repetition'
 * to override the default field type and the displayParams array includes the key
 * for the repetition field.  Assumptions are made that the vardefs.php contains repetition
 * elements with the corresponding names. There is the optional displayParam index
 * 'copy' that accepts as value the key of the other repetition fields.  In our
 * example we may enable copying from the primary repetition fields with:
 * 
 *  array (
 * 	   'name' => 'altrepetition_street',
 *	   'type' => 'repetition',
 *	   'displayParams'=>array('key'=>'alt', 'copy'=>'primary'),
 *  ),
 * 
 */
require_once('include/SugarFields/Fields/Base/SugarFieldBase.php');
class SugarFieldActivitySchedule extends SugarFieldBase {
    var $firstCall = true;

    function getDetailViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
        $this->firstCall = false;
        if(!isset($displayParams['key'])) {
            $displayParams['key'] = 'activity_schedule';
        }
        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
        global $app_strings;
        if(!isset($displayParams['key'])) {
           $GLOBALS['log']->debug($app_strings['ERR_ACTIVITY_SCHEDULE_KEY_NOT_SPECIFIED']);
           $this->ss->trigger_error($app_strings['ERR_ACTIVITY_SCHEDULE_KEY_NOT_SPECIFIED']);
           return;
        }
        
        //Allow for overrides.  You can specify a Smarty template file location in the language file.
        if(isset($app_strings['SMARTY_ACTIVITY_SCHEDULE_DETAILVIEW'])) {
           $tplCode = $app_strings['SMARTY_ACTIVITY_SCHEDULE_DETAILVIEW'];
           return $this->fetch($tplCode);	
        }
        
        global $current_language;
        if(isset($current_language) && file_exists('include/SugarFields/Fields/ActivitySchedule/' . $current_language . '.DetailView.tpl')) {
          return $this->fetch('include/SugarFields/Fields/ActivitySchedule/' . $current_language . '.DetailView.tpl'); 	
        } else {
          return $this->fetch('include/SugarFields/Fields/ActivitySchedule/DetailView.tpl');
        } //if-else
    }
    
    function getEditViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
        $displayParams['firstCall'] = $this->firstCall;
        $this->firstCall= false;
        if(!isset($displayParams['key'])) {
            $displayParams['key'] = 'activity_schedule';
        }


        // Adjust column widths to cater for fact that repetition is displayed inside its own table
        if(isset($displayParams['colWidths']) && is_array($displayParams['colWidths'])) {
            $totalWidth = 0;
            foreach ($displayParams['colWidths'] as $k => $value) {
                $totalWidth += $value;
            }
            // calculate widths
            foreach ($displayParams['colWidths'] as $k => $value) {
              $displayParams['colWidths'][$k] = round($value / ($totalWidth / 100), 2);
            }
        }


        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);

        $schedule = new ActivitySchedule();
        $this->ss->assign('schedule_defs', $schedule->field_defs );
        global $app_strings;
        if(!isset($displayParams['key'])) {
           $GLOBALS['log']->debug($app_strings['ERR_ACTIVITY_SCHEDULE_KEY_NOT_SPECIFIED']);
           $this->ss->trigger_error($app_strings['ERR_ACTIVITY_SCHEDULE_KEY_NOT_SPECIFIED']);
           return;
        }

        //Allow for overrides.  You can specify a Smarty template file location in the language file.
        if(isset($app_strings['SMARTY_ACTIVITY_SCHEDULE_EDITVIEW'])) {
           $tplCode = $app_strings['SMARTY_ACTIVITY_SCHEDULE_EDITVIEW'];
           return $this->fetch($tplCode);	
        }       

        global $current_language;
        if(isset($current_language) && file_exists('include/SugarFields/Fields/ActivitySchedule/' . $current_language . '.EditView.tpl')) {
          return $this->fetch('include/SugarFields/Fields/ActivitySchedule/' . $current_language . '.EditView.tpl'); 	
        } else {
          return $this->fetch('include/SugarFields/Fields/ActivitySchedule/EditView.tpl');
        } //if-else

    }

    function getListViewSmarty($parentFieldArray, $vardef, $displayParams, $col) {
        $tabindex = 1;
        $this->firstCall = false;
        if(!isset($displayParams['key'])) {
            $displayParams['key'] = 'activity_schedule';
        }
        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex, false);
        global $app_strings;
        if(!isset($displayParams['key'])) {
           $GLOBALS['log']->debug($app_strings['ERR_ACTIVITY_SCHEDULE_KEY_NOT_SPECIFIED']);
           $this->ss->trigger_error($app_strings['ERR_ACTIVITY_SCHEDULE_KEY_NOT_SPECIFIED']);
           return;
        }
       
		return $this->fetch('include/SugarFields/Fields/ActivitySchedule/ListView.tpl');
        
    }
    
    
    public function save(&$bean, $params, $field, $properties, $prefix = '') {

    }


}
?>
