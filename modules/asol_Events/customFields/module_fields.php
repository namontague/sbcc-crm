<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');


global $db, $beanList, $beanFiles, $mod_strings, $timedate;

$focus = new asol_Events();
$focus->retrieve($_REQUEST['record']);

//echo "id=[".$focus->id."]<BR>";
//echo "condition_event=[".$focus->condition_event."]<BR>";
//echo "trigger_event=[".$focus->trigger_event."]<BR>";
/*$triggerModuleTmp = explode(" - ", $focus->trigger_event);
$triggerModule = (isset($_REQUEST['triggerModule'])) ? $_REQUEST['triggerModule'] : $triggerModuleTmp[0];
*/
$module_name = $focus->getContextModule();
$triggerModule = $module_name;

//echo "triggerModule=[".$triggerModule."]<BR>";
$condition_event = isset($_REQUEST['event_conditions']) ? htmlspecialchars_decode($_REQUEST['event_conditions']) : $focus->condition_event;
if( $condition_event === null || $condition_event === 'null') {
  $condition_event ='';
}

//echo "\$condition_event=[".$condition_event."]";

$rhs_key = $_REQUEST['rhs_key']; //recogemos la clave de relacion para los campos del LEFT JOIN
$relate_Field = $_REQUEST['rhs_key']; //devuelve el campo del modulo seleccionado para obtener sus related fields

if (($triggerModule != '')){

	$beanName = $beanList[$triggerModule];
	require_once($beanFiles[$beanName]);
	$bean = new $beanName();
	
	$table = $bean->table_name;

	
	$rs = $focus->getSelectionResults("SHOW COLUMNS FROM ".$table);

	//Eliminamos campo deleted del array rs
	for ($i=0; $i<count($rs); $i++){

		if (($rs[$i]['Field'] == "deleted") || ($rs[$i]['Field'] == "id")) {
			array_splice($rs, $i, 1);
			break;
		}

	}


	$rs_c = $focus->getSelectionResults("SELECT name, type FROM fields_meta_data WHERE custom_module='".$triggerModule."' AND type NOT IN ('id', 'relate', 'html', 'iframe', 'encrypt')");
	$rs_cr = $focus->getSelectionResults("SELECT name, type, ext2, ext3 FROM fields_meta_data WHERE custom_module='".$triggerModule."' AND type IN ('relate')");

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

	$i=0;

	$fields; //Array con los campos de la tabla seleccionada
	$fields_labels; //Array con los labels de los campos de la tabla seleccionada
	$fields_type; // Array con los tipos de campos de cada elemento del array $fields
	$fields_options; // Array con las opciones (campos tipo "enum") de cada elemento del array $fields
	$has_related; //Array que indica si los elementos de la tabla fields tienen relacion con alguna otra tabla
	$related_fields; //Array con los campos related del campo pasado como parametro en el form
	$related_fields_labels; //Array con los labels de los campos de la tabla seleccionada
	$related_fields_type; // Array con los tipos de campos de cada elemento del array $related_fields
	$related_fields_options; // Array con las opciones (campos tipo "enum") de cada elemento del array $related_fields


	foreach($rs as $value){

		if ($value['Type'] != "non-db"){

			$fields[$i] = $value['Field'];
			$auxField = explode(".", $fields[$i]);
			$auxField = (count($auxField) == 2) ? $auxField[1 ]: $fields[$i];
			$fields_options[$i] = $focus->getFieldInfoFromVardefs($triggerModule, $auxField);


			if ($fields_options[$i] == 'currency') {
				$fields_type[$i] = 'currency';
			} else if ($fields_options[$i] != '') {
				$fields_type[$i] = 'enum';
			} else {
				$fields_type[$i] = $value['Type'];
			}


			if ($value['Field'] == "id"){

				$fields_labels[$i] = "id";
				//Si el campo a relacionar es el ID, buscar en la tabla relationships
				$tmpField = explode(".", $value['Field']);
				$tmpField = (count($tmpField) == 2) ? $tmpField[1] : $value['Field'];

				$rshr = $focus->getSelectionResults("SELECT DISTINCT lhs_module, lhs_table FROM relationships WHERE rhs_module LIKE '".$triggerModule."' AND rhs_key LIKE '".$tmpField."'");

				//Buscamos tb en la otra direccion
				$rshl = $focus->getSelectionResults("SELECT DISTINCT rhs_module, rhs_table FROM relationships WHERE lhs_module LIKE '".$triggerModule."' AND lhs_key LIKE '".$tmpField."'");

				if (count($rshr) == 0)
				$rshr = Array();

				if (count($rshl) == 0)
				$rshl = Array();

				foreach($rshl as $v){ //Evitar tablas repetidas

					$duplicatedTable = false;

					foreach($rshr as $v2){

						if ($v['rhs_table'] == $v2['lhs_table']){
							$duplicatedTable = true;
							break;
						}

					}

					if ($duplicatedTable == false) {
						$rshr[count($rshr)]['lhs_table'] = $v['rhs_table'];
						$rshr[count($rshr)-1]['lhs_module'] = $v['rhs_module'];
					}

				}

				if ((count($rshr) > 0) || ($value['Type'] == "relate"))
				$has_related[$i] = "true";
				else
				$has_related[$i] = "false";


				if($relate_Field == $value['Field']){

					$j=0;
					$k=0;

					if(count($rshr) > 0) {

						while ($j < count($rshr)){

							$rsrf = $focus->getSelectionResults("SHOW COLUMNS FROM ".$rshr[$j]['lhs_table']);

							foreach($rsrf as $val){
								$related_fields[$k] = $rshr[$j]['lhs_table'].".".$val['Field'];

								//$related_fields_labels[$k] = "";

								$related_fields_options[$k] = $focus->getFieldInfoFromVardefs($rshr[$j]['lhs_module'], $val['Field']);

								if ($related_fields_options[$k] == 'currency') {
									$related_fields_type[$k] = 'currency';
								} else if ($related_fields_options[$k] != '') {
									$related_fields_type[$k] = 'enum';
								} else {
									$related_fields_type[$k] = $val['Type'];
								}
								$k++;
							}

							$j++;

						}

					} else {

						$rsrf = $focus->getSelectionResults("SHOW COLUMNS FROM ".strtolower($value['RelateModule']));
						$rsrfc =  $focus->getSelectionResults("SHOW COLUMNS FROM ".strtolower($value['RelateModule'])."_cstm");

						foreach ($rsrfc as $customField){
							$rsrf[count($rsrf)] = $customField;
						}

						foreach($rsrf as $val){
							$related_fields[$k] = strtolower($value['RelateModule']).".".$val['Field'];
							$related_fields_options[$k] = $focus->getFieldInfoFromVardefs($value['RelateModule'], $val['Field']);

							if ($related_fields_options[$k] == 'currency') {
								$related_fields_type[$k] = 'currency';
							} else if ($related_fields_options[$k] != '') {
								$related_fields_type[$k] = 'enum';
							} else {
								$related_fields_type[$k] = $val['Type'];
							}
							$k++;
						}

						$j++;

					}

				}

			} else { //Buscar a traves del metodo get_related_fields() del bean

				global $beanList, $beanFiles, $app_list_strings;
				$class_name = $beanList[$triggerModule];
				require_once($beanFiles[$class_name]);
				$bean = new $class_name();

				$relatedInfo = $bean->get_related_fields();

				$tmpField = explode(".", $value['Field']);
				$tmpField = (count($tmpField) == 2) ? $tmpField[1] : $value['Field'];

				foreach ($relatedInfo as $info){

					if ($info['id_name'] == $tmpField){
						$fields_labels[$i] = $info['name'];
						$has_related[$i] = "true";
						break;

					} else{
						$fields_labels[$i] = $tmpField;
						$has_related[$i] = "false";

					}

				}

				$relatedTable = "";
				$relatedModule = "";

				if($relate_Field == $value['Field']){

					foreach ($relatedInfo as $info){

						if ($info['id_name'] == $tmpField){

							$relatedModule = $info['module'];
							$relatedTable = strtolower($relatedModule);
							break;

						}

					}

					$k = 0;


					$rsrf = $focus->getSelectionResults("SHOW COLUMNS FROM ".$relatedTable);

					//Eliminamos campo deleted del array rsrf
					for ($j=0; $j<count($rsrf); $j++){

						if ($rsrf[$j]['Field'] == "deleted") {
							array_splice($rsrf, $j, 1);
							break;
						}

					}

					$rsrfc =  $focus->getSelectionResults("SHOW COLUMNS FROM ".$relatedTable."_cstm");

					if(count($rsrfc) == 0)
					$rsrfc = Array();

					foreach ($rsrfc as $customField){
						if ($customField['Field'] != "id_c"){
							$customField['Field'] = $relatedModule.".".$customField['Field'];
							$rsrf[count($rsrf)] = $customField;
						}
					}

					foreach($rsrf as $val){

						$related_fields[$k] = (count(explode(".", $val['Field'])) == 1) ? $relatedModule.".".$val['Field'] : $val['Field'];

						$valExp = explode(".", $val['Field']);
						$auxVal = (count(explode(".", $val['Field'])) == 1) ? $val['Field'] : $valExp[1];

						$related_fields_options[$k] = $focus->getFieldInfoFromVardefs($relatedModule, $auxVal);

						if ($related_fields_options[$k] == 'currency') {
							$related_fields_type[$k] = 'currency';
						} else if ($related_fields_options[$k] != '') {
							$related_fields_type[$k] = 'enum';
						} else {
							$related_fields_type[$k] = $val['Type'];
						}
						$k++;
					}

				}

			}

		}

		$i++;
	}

}


$fieldsSelect = '<select name="fields" id="fields" style="width:178px" multiple size=10 onclick="if (this.options[this.selectedIndex].style.color == \'blue\') document.getElementById(\'show_related_button\').style.display = \'inline\'; else document.getElementById(\'show_related_button\').style.display = \'none\';" onDblClick="if (this.options[this.selectedIndex].style.color == \'blue\') { this.form.action.value=\''.$_REQUEST['action'].'\'; this.form.rhs_key.value=this.value; this.form.event_conditions.value=format_conditions(\'conditions_Table\'); this.form.submit()}">';

if (empty($fields))
$fields = Array();

foreach ($fields as $fieldK=>$field) {

	if ($has_related[$fieldK] == "true")
	$fieldsSelect .= ($rhs_key == $field) ? '<option style="color:blue;" title="'.$field.'" value="'.$field.'" selected>'.$fields_labels[$fieldK].' +</option>' : '<option style="color:blue;" title="'.$field.'" value="'.$field.'">'.$fields_labels[$fieldK].' +</option>';
	else
	$fieldsSelect .= '<option title="'.$field.'" value="'.$field.'">'.$fields_labels[$fieldK].'</option>';

}

$fieldsSelect .= '</select>';


if (empty($related_fields))
$related_fields = Array();

$relatedFieldsSelect = '<select id="related_fields" name="related_fields" size=10 style="width:178px" multiple>';


foreach ($related_fields as $rFieldK=>$relatedField)
	$relatedFieldsSelect .= '<option title="'.$relatedField.'" value="'.$relatedField.'">'.$relatedField.'</option>';

$relatedFieldsSelect .= '</select>';



//////////////////
// This code should not be here... but because of it can't be two onLoad in the same webpage(the webpage only is aware of the first onLoad, though, both are printed as html code)... I have to do this treak.
if ($trigger_type == "logic_hook") 
		echo '<body onload="document.getElementById(\'LBL_ASOL_TRIGGERING_PANEL\').style.display = \'block\'; document.getElementById(\'LBL_ASOL_EVENT_CONDITION_PANEL\').style.display = \'block\'; " >';
	else
		echo "<body onload='document.getElementById(\"type\").style.display = \"none\"; document.getElementById(\"type_label\").style.display = \"none\"; document.getElementById(\"LBL_ASOL_TRIGGERING_PANEL\").style.display = \"none\"; document.getElementById(\"LBL_ASOL_EVENT_CONDITION_PANEL\").style.display = \"none\";'  >";
/////////////////	

echo '
	<script src="modules/asol_Events/js/asol_events.js" type="text/javascript"></script>


	<input type="hidden" id="rhs_key" name="rhs_key" value="'.$rhs_key.'">
	<input type="hidden" id="event_conditions" name="event_conditions" value=\''.$condition_event.'\'>
	
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
							<input type="button" title="'.$mod_strings['LBL_ASOL_ADD_FIELDS'].'" class="button" name="fields_button" value="'.$mod_strings['LBL_ASOL_ADD_FIELDS'].'" onClick="ASOLEvents.insertConditions(\'conditions_Table\', \'fields\', null, \''.$rhs_key.'\' );">
							<input type="button" title="'.$mod_strings['LBL_ASOL_SHOW_RELATED'].'" class="button" style="display:none" id="show_related_button" name="show_related_button" value="'.$mod_strings['LBL_ASOL_SHOW_RELATED'].'" onClick="if ((document.getElementById(\'fields\').options[document.getElementById(\'fields\').selectedIndex].style.color == \'blue\') && (true)) {this.form.action.value=\''.$_REQUEST['action'].'\'; this.form.rhs_key.value=document.getElementById(\'fields\').value; this.form.event_conditions.value=ASOLEvents.saveConditions(\'conditions_Table\'); this.form.submit();} ">
						</td></tr>

						<tr><td>
							<h4>'.$mod_strings['LBL_ASOL_RELATED_FIELDS'].'</h4>
						</td></tr>

						<tr><td>
							'.$relatedFieldsSelect.'
						</td></tr>

						<tr><td>
							<input type="button" title="'.$mod_strings['LBL_ASOL_ADD_RELATED_FIELDS'].'" class="button" name="related_fields_button" value="'.$mod_strings['LBL_ASOL_ADD_RELATED_FIELDS'].'" onClick="ASOLEvents.insertConditions(\'conditions_Table\', null, \'related_fields\',\''.$rhs_key.'\' );">
						</td></tr>

					</table>
				</td>
					
			</tr>
		</tbody>
	</table>

	<script>
    

        var form_ = document.getElementById("EditView");
        var oldSubmit = form_.submit;
        form_.submit = function() {
            $("form#EditView").trigger("submit");
            oldSubmit.call(form_, arguments);
        }


        var init;
        init = function ()
        {
            

			$("#EditView").bind("submit", function () {
			    $("#event_conditions").val(ASOLEvents.saveConditions(\'conditions_Table\'));
			});

			ASOLEvents.fieldLabels='.json_encode($fields_labels).';
  			ASOLEvents.typeFields='.json_encode($fields_type).';
  			ASOLEvents.optionFields='.json_encode($fields_options).';
  			ASOLEvents.typeRelatedFields='.json_encode($related_fields_type).';
  			ASOLEvents.optionRelatedFields='.json_encode($related_fields_options).';
  			ASOLEvents.calendar_dateformat=\''.$timedate->get_cal_date_format().'\';

            ASOLEvents.loadConditions(\'conditions_Table\', \'event_conditions\');;
        };
        $(document).ready(init);
	</script>
	
	</body>
	';
//$(document).ready(function() {
        
           
		
//     	});
?>
