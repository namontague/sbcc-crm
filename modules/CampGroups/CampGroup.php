<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/* * *******************************************************************************
 * (C) 2013 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */

class CampGroup extends Basic {

	var $new_schema = true;
    var $module = 'CampGroups';
	var $module_dir = 'CampGroups';
	var $object_name = 'CampGroup';
	var $table_name = 'campgroups';
	var $importable = true;
	var $id;
	var $name;
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	var $modified_by_name;
	var $created_by;
	var $created_by_name;
	var $description;
	var $deleted;
	var $created_by_link;
	var $modified_user_link;
	var $assigned_user_id;
	var $assigned_user_name;
	var $assigned_user_link;
	var $camp_id;
    var $camp_name;
    
	var $update_vcal = true;
	var $additional_column_fields = array();

	function CampGroup()	{
            parent::Basic();
	}


	function bean_implements($interface) {
            switch ($interface) {
                    case 'ACL': return true;
            }
            return false;
	}
    
        function get_summary_text() {
            return "$this->name";
        }

        function groupCreate($bean){
         
            $sort_by = get_select_options(array('participant_age'=>'Age','contact_name'=>'Name'),'');

            $cell_ext .= "<form action='index.php' method='POST' name='EditView' id='EditView'>";
            $cell_ext .= "<link rel='stylesheet' type='text/css' href='modules/CampGroups/lb/lb.css' media='screen' />";
            $cell_ext .= "<script type='text/javascript' src='modules/CampGroups/lb/lb.js'></script>";
            $cell_ext .= "<input type='hidden' name='camp_id' id='camp_id' size='30' value='{$bean->id}' title='' tabindex='104' />";
            $cell_ext .= "
                <table border='0' style='background-color:#ffffff;'>
                    <tr>
                        <td style='border:0px;'>
                            <input type='hidden' name='module' value='CampGroups'>
                            <input type='button' title='Create Groups and Assign Participants' value='Create Groups' onClick='openbox(true);' />
                            <div id='filter_create_groups' class='filter'></div>
                            <div id='box_create_groups' class='box' style='background-color:#ffffff;'>
                                <div id='boxcontent_create_groups'>
                                    <table cellpadding='0' cellspacing='0' width='100%' style='border:3px solid #4e8ccf; border-left:none; border-right:none; margin:0; background-color:#ffffff;'>
                                        <tr>
                                            <td><h3 style='padding-left:10px;'>Create Groups and Assign Participants</h3></td>
                                        </tr>
                                    </table>
                                    <div style='background-color:#f6f6f6; height:500px; overflow:auto;'>
                                        <div style='float:left; width:100%; align:left;'>
                                            <strong style='padding-left:10px;padding-top:20px;float:left;'> Manage Groups</strong>
                                        </div>
                                        <div style='padding-top:20px;padding-left:20px;float:left;'>
                                            <table border='0' cellspacing='0' cellpadding='0' class='tablebox'>
                                                <tbody>
                                                    <tr>
                                                        <td valign='top' id='type_label' width='12.5%' scope='row' style='text-align: left;border: none;'>Max Participants per group:</td>
                                                        <td valign='top' id='type_data' width='37.5%' colspan='4' style='text-align: left;border: none;'><input type='text' name='group_size' id='group_size' size='30' value='' title='' tabindex='104' /></td>
                                                    </tr>
                                                    <tr>
                                                        <td valign='top' id='source_label' width='12.5%' scope='row'  style='text-align: left;border: none;'>Group Participants by:</td>
                                                        <td valign='top' id='source_data' width='37.5%' colspan='4' style='text-align: left;border: none;'><select name='sort_by' id='sort_by' title='' tabindex='102'>{$sort_by}</select></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                              

                                           
                                        </div>

                                        <div style='clear:both;margin-bottom:15px;'></div>
                                        <div style='padding-left:10px;'>
                                            <input type='button' id='SaveReadingbtn' value='Create' onClick=\"if(check_form('EditView'))javascript:createGroups();\">&nbsp;
                                            <input type='button' id='SaveReadingbtn' value='Cancel' onClick=\"javascript:closebox();\">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>";

     $cell_ext .=  "

                        </div>
                    </div>
                    </tr>
                </table></form>";
   

     $javascript = "<script type='text/javascript'>
                        addForm('EditView');
                        addToValidate('EditView', 'group_size', 'int', true,'group_size' );          
                     </script>";

/*
      $javascript .= "  addForm('EditView');
                        addToValidate('EditView', 'name', 'name', true,'Id' );
                        addToValidate('EditView', 'date_entered_date', 'date', false,'Date Created' );
                        addToValidate('EditView', 'date_modified_date', 'date', false,'Date Modified' );
                        addToValidate('EditView', 'modified_user_id', 'assigned_user_name', false,'Modified By' );
                        addToValidate('EditView', 'modified_by_name', 'relate', false,'Modified By Name' );
                        addToValidate('EditView', 'created_by', 'assigned_user_name', false,'Created By' );
                        addToValidate('EditView', 'created_by_name', 'relate', false,'Created By' );
                        addToValidate('EditView', 'description', 'text', false,'Description' );
                        addToValidate('EditView', 'deleted', 'bool', false,'Deleted' );
                        addToValidate('EditView', 'type', 'enum', false,'Type' );
                        addToValidate('EditView', 'source', 'enum', false,'Source' );
                        addToValidate('EditView', 'doc_id', 'varchar', false,'Doc Id' );
                        addToValidate('EditView', 'transaction_description', 'varchar', false,'Transaction Description' );
                        addToValidate('EditView', 'reason', 'varchar', false,'Reason' );
                        
                        addToValidate('EditView', 'date_time_date', 'date', true,'Date/Time of Reading' );
                        addToValidate('EditView', 'meter_id', 'id', false,'Meter ID' );
                        addToValidate('EditView', 'meter_name', 'relate', true,'Meter' );
                        addToValidate('EditView', 'serviceinvoice_id', 'id', false,'LBL_SERVICE_INVOICE_ID' );
                        addToValidate('EditView', 'invoiced', 'bool', false,'Invoiced' );
                        addToValidateBinaryDependency('EditView', 'assigned_user_name', 'alpha', false,'No match for field: Assigned to', 'assigned_user_id' );
                        addToValidateBinaryDependency('EditView', 'meter_name', 'alpha', true,'No match for field: Meter', 'meter_id' );";
        if($javascript_vaildate){
            $javascript .= $javascript_vaildate;
        }
        $javascript .=  "</script>";
*/
        return $cell_ext.$javascript;

           

        }
}
?>