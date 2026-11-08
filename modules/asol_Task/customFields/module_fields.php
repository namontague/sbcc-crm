<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');


global $db, $beanList, $beanFiles, $mod_strings, $timedate;

$focus = new asol_task();
$focus->retrieve($_REQUEST['record']);

$task_type = (isset($_REQUEST['task_type'])) ? $_REQUEST['task_type'] : $focus->task_type;

$triggerModule = $focus->getContextModule();
$tmpMod = json_decode(htmlspecialchars_decode($focus->task_implementation));

$mod = (!empty($tmpMod->module)) ? $tmpMod->module: "";
$objModule = (isset($_REQUEST['objectModule'])) ? $_REQUEST['objectModule'] : $mod;

$objectModule = ($task_type == "modify_object") ? $triggerModule : $objModule;


//$rhs_key = $_REQUEST['rhs_key']; //recogemos la clave de relacion para los campos del LEFT JOIN
//$relate_Field = $_REQUEST['rhs_key']; //devuelve el campo del modulo seleccionado para obtener sus related fields

$fieldsSelect = '<select name="fields" id="fields" style="width:178px" multiple size=10>';

if (($objectModule != '')){

	$beanName = $beanList[$objectModule];
	require_once($beanFiles[$beanName]);
	$bean = new $beanName();
	$table = $bean->table_name;
	//$relatedInfo = $bean->get_related_fields();

	$has_related = array();
	$is_required = array();
	$relate_modules = array();
	$fields = array();
	$fields_labels = array();
	$fields_options = array();
	$fields_types = array();


	$rs = $focus->getSelectionResults("SHOW COLUMNS FROM ".$table);

	$rs_c = $focus->getSelectionResults("SELECT name, type FROM fields_meta_data WHERE custom_module='".$objectModule."' AND type NOT IN ('id', 'relate', 'html', 'iframe', 'encrypt')");
	$rs_cr = $focus->getSelectionResults("SELECT name, type, ext2, ext3 FROM fields_meta_data WHERE custom_module='".$objectModule."' AND type IN ('relate')");

	if (count($rs_c) == 0)
	$rs_c = Array();

	if (count($rs_cr) == 0)
	$rs_cr = Array();

	foreach ($rs_c as $custom_field){

		$rs[count($rs)]['Field'] = $table."_cstm.".$custom_field["name"];
		$rs[count($rs)-1]['Type'] = $custom_field["type"];

	}

	foreach ($rs_cr as $custom_field){

		$rs[count($rs)]['Field'] = $table."_cstm.".$custom_field["ext3"];
		$rs[count($rs)-1]['Type'] = $custom_field["type"];
		$rs[count($rs)-1]['RelateModule'] = $custom_field["ext2"];

	}

  $i = 0;
	foreach ($rs as $value){
		if ($value['Type'] != "non-db"){
			//id & deleted fields are supressed from select input
			if (($value['Field'] != "deleted") && ($value['Field'] != "id")) {

				$fields[$i] = $value['Field'];
				$auxField = explode(".", $fields[$i]);
				$auxField = (count($auxField) == 2) ? $auxField[1 ]: $fields[$i];
				$fields_options[$i] = $focus->getFieldInfoFromVardefs($objectModule, $auxField);


				if ($fields_options[$i] == 'currency') {
					$fields_type[$i] = 'currency';
				} else if ($fields_options[$i] != '') {
					$fields_type[$i] = 'enum';
				} else {
					$fields_type[$i] = $value['Type'];
				}


				global $beanList, $beanFiles, $app_list_strings;
				$class_name = $beanList[$objectModule];
				require_once($beanFiles[$class_name]);
				$bean = new $class_name();

				$relatedInfo = $bean->get_related_fields();

				$tmpField = explode(".", $value['Field']);
				$tmpField = (count($tmpField) == 2) ? $tmpField[1] : $value['Field'];

				foreach ($relatedInfo as $info){

					if ($info['id_name'] == $tmpField){
						$fields_labels[$i] = $info['name'];
						$relate_modules[$i] = $info['module'];
						$fields_type[$i] = "relate";
						$has_related[$i] = "true";
						break;

					} else{
						$fields_labels[$i] = $tmpField;
						$relate_modules[$i] = "";
						$has_related[$i] = "false";

					}
					
				}

				$is_required[$i] = ($focus->isRequiredField($objectModule, $value['Field'])) ? true : false;
				
				if ($is_required[$i] == "true")
					$fieldsSelect .= '<option title="'.$fields_labels[$i].'" value="'.$value['Field'].'">'.$fields_labels[$i].' *</option>';
				else 
					$fieldsSelect .= '<option title="'.$fields_labels[$i].'" value="'.$value['Field'].'">'.$fields_labels[$i].'</option>';

        $i++;
			}

		}

	}

}

$fieldsSelect .= '</select>';



echo '

	<input type="hidden" id="task_implementation" name="task_implementation" value="'.$task_implementation.'">
	
	<table border=0 width="100%">
		<tbody>
			<tr>
				<td>
					<table>
						<tr><td>
							<h4>'.$mod_strings['LBL_ASOL_FIELDS'].'</h4>
						</td></tr>

						<tr><td>
							'.$fieldsSelect.'
						</td></tr>

						<tr><td>
							<input type="button" title="'.$mod_strings['LBL_ASOL_ADD_FIELDS'].'" class="button" name="fields_button" value="'.$mod_strings['LBL_ASOL_ADD_FIELDS'].'" onClick="ASOLTask.insertObjectField(\'module_values_Table\', \'fields\')">
						</td></tr>
					</table>
				</td>
					
			</tr>
		</tbody>
	</table>
	
	<script>
		$(document).ready(function() {
            var form_ = document.getElementById("EditView");
            var oldSubmit = form_.submit;
            form_.submit = function() {
                $("form#EditView").trigger("submit");
                oldSubmit.call(form_, arguments);
            }


			ASOLTask.fieldLabels='.json_encode($fields_labels).';
  		ASOLTask.typeFields='.json_encode($fields_type).';
  		ASOLTask.optionFields='.json_encode($fields_options).';
  		ASOLTask.fieldRequired='.json_encode($is_required).';
  		ASOLTask.relatedModules='.json_encode($relate_modules).';
  		ASOLTask.calendar_dateformat=\''.$timedate->get_cal_date_format().'\';

     ';



if ((empty($focus->id)) || (($mod != $_REQUEST['objectModule']) && (!empty($_REQUEST['objectModule'])))) 
	echo   'ASOLTask.fillRequiredFields(\'module_values_Table\', \'fields\');
	';
	
echo '		
        


        $("#EditView").bind("submit", function () { document.getElementById("task_implementation").value=ASOLTask.saveImplementationField(\'task_type\'); } );
		
     	});
	</script>

	';


?>
