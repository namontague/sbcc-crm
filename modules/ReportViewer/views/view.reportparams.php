<?php
/*********************************************************************************
 *
 * (C) 2009 Enterprise Intelligence Pty Ltd
 * Not to be used or redistributed without permission.
 *
 ********************************************************************************/



    require_once('include/MVC/View/SugarView.php');
    require_once('modules/ReportServer/ReportRestClient.php');
   
    
    class ReportViewerViewReportParams extends SugarView {

        function ReportViewerViewReportParams(){
            parent::SugarView();
            
        }
        

        function process(){
            // This will bypass the sugar header and footer so only the required text is sent
            $this->collectParams();
        }

        function paramDefaultValue($paramName, $report, $bean) {
            $paramPath = explode('.',$paramName, 2);
            if( count($paramPath) > 1 ) {
                if( $paramPath[0] == 'bean') {
                    $bean = $bean;
                } else if( $paramPath[0] == 'user') {
                    global $current_user;
                    $bean = $current_user;
                }
                $paramName = $paramPath[1];
            }
            $value = null;
            if( isset($bean)){
                if(array_key_exists($paramName, $bean->field_defs)) {
                    $value = $bean->$paramName;
                }
            }
            return $value;
        }

        function collectParams() {
        //load report bean to get report details
        
            $report= new ReportViewer();
            $id = $_GET['id'];
            $report = $report->retrieve($id);



            //get report input controllers

            $rp = new ReportRestClient();
            $rp->reportUnit =$report->report_location;
            $rp->format =$report->format;
            $rp->report_name = $report->name;
            
            $paramsList = $rp->getRestInputsArray($rp->reportUnit);
                    
            $rp->stripParamsType($paramsList);
            
            $userInputCount = count($paramsList);
            if( $userInputCount > 0 ) {
                $record_id = $_GET['record_id'];
                $module = null;
                if(!empty($report->related_module)  && isset($record_id) ){
                    $moduleBean = $report->related_module;
                    $module = new $moduleBean();
                    $module->retrieve($record_id);
                }

                foreach($paramsList as $param) {
                    $value = $report->paramDefaultValue((string)$param->id, $module);
                    if( $value != null ) {
                        $param->hasdefault = true;
                        $param->value = $value;
                        $userInputCount--;
                    }
                }

            }


            
            $form = '<div id="image" style="display:none">
                        <center>
                            <img id="loading_image" src="modules/ReportViewer/loader.gif" />
                        </center>
                    </div>
                    <div id="param_form">
                    <form name="reportInputs" method="post" >
                        <script type="text/javascript" src="include/eintel/jquery/js/jquery-1.6.1.min.js"></script>
                        <script type="text/javascript" src="cache/include/javascript/sugar_grp1_yui.js"></script>
                        <script type="text/javascript" src="cache/include/javascript/sugar_grp1.js"></script>
                        <script type="text/javascript" src="cache/jsLanguage/en_us.js"></script>
                        <script type="text/javascript" src="include/javascript/calendar.js"></script>
                        <link href="cache/themes/Sugar5/css/yui.css" type="text/css" rel="stylesheet" />
                        <link href="cache/themes/Sugar5/css/deprecated.css" type="text/css" rel="stylesheet" />
                        <link href="cache/themes/Sugar5/css/style.css" type="text/css" rel="stylesheet"/>

                    <table>
                    <tr><td>
                    <input id="module" type="hidden" value="ReportViewer" name="module" />
                    <input id="action" type="hidden" value="ReportRun" name="action" />
                    <input id="format" type="hidden" value="'.$rp->format.'" name="format" />
                    <input id="report" type="hidden" value="'.$rp->reportUnit.'" name="report" />
                    <input id="report" type="hidden" value="'.$rp->report_name.'" name="report_name" />
                    </td></tr>';
              $html = '';
            if($paramsList){
                   $param_type =  array(
                        'singleValueDate' => 'P_D_','singleValueDatetime' => 'P_DT_',
                        'singleValueText' => 'P_T_','singleValueNumber'  => 'P_T_','multiSelectCheckbox'=> 'P_C_',
                        'multiSelect' => 'P_MS_',
                        'bool' => 'P_S_','singleSelectRadio' => 'P_S_','singleSelect'=> 'P_S_', 
                    );
                
                
                    foreach($paramsList as $input) {
                        $input->id = str_replace('.','_-_', $input->id);
                     
                        $html.="<tr><td>";
                      
                        $disabled = '';
                        // Value from module
                        if($input->hasdefault && $input->visible != 'false'){
                            $disabled = "disabled = disabled";
                            $html .= "<input type='hidden' name='{$input->id}' value ='{$input->value}'>";
                            
                        }                        
                        if( $input->visible == 'false' ) {
                            $input->id = $param_type[(string)$input->{type}].$input->id;
                            $html .= "<input type='hidden' id='{$input->id}' name='{$input->id}' value ='{$input->value}'>";
                            $param_array_list[] = $input->id;
                            continue;
                        }
                        
                        //Text Field
                        if(substr($input->type,0,11) == 'singleValue') { //'singleValue'{type}
                            if($input->type == 'singleValueDate'){
                                $input->id = "P_D_$input->id"; 
                                //if we already have the date value
                                global $timedate;
                                $date_format = $timedate->get_date_format();
                                
                                if(isset($input->value) && $input->value != null){
                               
                                    $html .= "<tr><td>{$input->label}</td><td><input id ='{$input->id}-inputField' type='text' {$disabled} name='{$input->id}' value ='{$input->value}'/></tr>";
                               
                                //Else we need the calendar button
                                }else{
                                    
                                    $defaultStateValue = (!empty($input->state->value))? str_replace ('%','',$timedate->to_display($input->state->value, 'Y-m-d', $timedate->get_cal_date_format())):null;
                                    
                                    $html .= "<tr><td>{$input->label}</td><td><input id ='{$input->id}-inputField' type='text' {$disabled} name='{$input->id}' value ='{$defaultStateValue}'/>";
                                    $html .= "<img src='themes/default/images/jscalendar.gif?s=210350f907e0ed7a2309dd40866aca55&c=1&developerMode=405972866' id='{$input->id}-trigger' title='Date selector' alt='Date selector'/>";
                                    $html .= "<script type='text/javascript'>
                                      Calendar.setup(
                                        {
                                          inputField  : '{$input->id}-inputField',          // ID of the input field
                                          ifFormat    : '{$timedate->get_cal_date_format()}', // the date format
                                          button      : '{$input->id}-trigger'              // ID of the button
                                        }
                                      );
                                    </script></td></tr>";
                                   
                                }
                                $param_array_list[] = $input->id;
                            }elseif($input->type == 'singleValueDatetime') {
                                $input->id = "P_DT_$input->id"; 
                                global $timedate;
                                $defaultStateValue = (!empty($input->state->value))? str_replace ('%','',$timedate->to_display($input->state->value, 'Y-m-d', $timedate->get_cal_date_time_format())):null;
                                $html .= "<tr><td>{$input->label}</td><td><input id ='{$input->id}-inputField' type='text' {$disabled} name='{$input->id}' value ='{$defaultStateValue}'/>";
                                $html .= "<img src='themes/default/images/jscalendar.gif?s=210350f907e0ed7a2309dd40866aca55&c=1&developerMode=405972866' id='{$input->id}-trigger' title='Date selector' alt='Date selector'/>";
                                $html .= "<script type='text/javascript'>
                                      Calendar.setup(
                                        {
                                          inputField  : '{$input->id}-inputField',          // ID of the input field
                                          ifFormat    : '{$timedate->get_cal_date_time_format()}', // the datetime format
                                          button      : '{$input->id}-trigger'              // ID of the button
                                        }
                                      );
                                    </script></td></tr>";
                                $param_array_list[] = $input->id;
                            //else not a date field create a normal text input field with no calendar button
                            }else {  
                                $input->id = "P_T_$input->id"; 
                                if(isset($input->state->value) && !empty($input->state->value)){
                                    //set the value to the current state of the input
                                    $input->value = $input->state->value;
                                }
                                $html .= "<tr><td>{$input->label}</td><td><input id ='{$input->id}' type='text' {$disabled} name='{$input->id}' value ='{$input->value}'/></td></tr>";
                                $param_array_list[] = $input->id;
                                
                            }

                        //Check Box(es)
                        } 
                        else if ($input->type == 'multiSelectCheckbox') {    
                            $input->id = "P_C_$input->id"; 
                            
                            $html .= "<tr><td style='vertical-align: top;'>{$input->label}</td>";
                            
                                
                                if(isset($input->state->options->option)){
                                    $options = $input->state->options->option;
                                    $html .= '<td><table>';
                                    foreach ($options AS $option) {
                                        $selected = '';
                                        if($option->selected == "true"){
                                            $selected = "checked";
                                        }
                                        $html .= "<tr><td>{$option->label}</td><td><input type='checkbox' name='P_C_{$option->label}' value ='{$option->value}' $selected readonly></td></tr>";

                                    }
                                    $html .= '</table></td></tr>';                                   
                                }else{
                                
                                $html .= "<td><input type='checkbox' name='{$input->id}' value ='{$input->value}'></td></tr>";
                            
                                }
                                $param_array_list[] = $input->id;
                        } else if (in_array($input->type, array('singleSelect','multiSelect'))){    
                            $html .= "<tr><td style='vertical-align: top;'>{$input->label}</td>";
                           
                            if($input->type == 'multiSelect'){
                                $input->id = "P_MS_$input->id"; 
                                $html .= "<td><select name='{$input->id}[]' multiple='multiple' size='10'>";
                            }else{
                                $input->id = "P_S_$input->id"; 
                                $html .= "<td><select name='{$input->id}'>";
                            }
                            if(!empty($input->state->options)) {
                                $input_options = $input->state->options;
                                foreach ($input_options->option AS $option) {
                                    $selected = '';
                                    if($option->selected == "true" && ($input->hasdefault !="1")){ 
                                        $selected= 'selected'; 
                                    }elseif($input->hasdefault=="1" && ((string)$input->value === (string)$option->value)){
                                        $selected= 'selected'; 
                                    }   
                                    $html .= "<option value='".htmlspecialchars($option->value)."' {$selected}>                                                                       
                                             ".htmlspecialchars($option->label)." 
                                             </option>";
                                }
                            }
                            $html .= "</select>";
                            $param_array_list[] = $input->id;
                        } else if (in_array($input->type, array('bool','singleSelectRadio'))){    
                            $input->id = "P_S_$input->id"; 
                            $html .= "<tr><td style='vertical-align: top;'>{$input->label}</td>";
                            if($input->type == 'bool'){
                                //1 check box
                                $html .= "<td><input type='checkbox' name='{$input->id}' value ='{$input->value}'></td></tr>";
                            }
                             //radio buttons
                            $param_array_list[] = $input->id; 
                        }
                       
                    }
                }
                $str_parameters = '';
                if(isset($param_array_list) && !empty($param_array_list)
                        && is_array($param_array_list)){
                  $str_parameters =  implode(",", $param_array_list);
                }
                $html .= ' <input type=hidden name="parameters_list" value="'.htmlspecialchars($str_parameters).'">';
                echo $form.$html;
                
                if($userInputCount > 0) {
                    ?>
                   <tr><td>
                    <input id='execute' type='submit' value='Execute Report' name='execute_button'/>
                       <script type="text/javascript" >
                           YAHOO.util.Event.addListener("execute","click",function(){
                               var dhxWins = parent.dhxWins;
                               var reportWindow = dhxWins.window("reportWindow");
                               reportWindow.setText("Requested Report");
                               reportWindow.setDimension(800, 600);
                               reportWindow.centerOnScreen();
                               var pos = reportWindow.getPosition();
                               var scroll = 0;
                               if(parent.document.body.scrollTop ) {
                                   scroll = parent.document.body.scrollTop;
                               } else if (parent.window.pageYOffset) {
                                   scroll = parent.window.pageYOffset;
                               }
                               reportWindow.setPosition(pos[0],pos[1]+scroll);
                               document.getElementById('param_form').style.display="none";
                               document.getElementById('image').style.display="block";
                           });
                       </script>
                    </td></tr>
                    <?php
                } else {
                    if($rp->format == 'RTF' || $rp->format == 'XLS'){
                        ?>
                        <script language="javascript" type="text/javascript">
                            document.reportInputs.submit();

                            var dhxWins = parent.dhxWins;
                            var reportWindow = dhxWins.window("reportWindow");
                            document.getElementById('param_form').style.display="none";
                            document.getElementById('image').style.display="block";


                        </script>
                        <?php
                    }else {
                        ?>
                        <script language="javascript" type="text/javascript">
                                document.reportInputs.submit();

                                var dhxWins = parent.dhxWins;
                                var reportWindow = dhxWins.window("reportWindow");
                                reportWindow.setText("Requested Report");
                                reportWindow.setDimension(800, 600);
                                reportWindow.centerOnScreen();
                                var pos = reportWindow.getPosition();
                                var scroll = 0;
                                if(parent.document.body.scrollTop ) {
                                    scroll = parent.document.body.scrollTop;
                                } else if (parent.window.pageYOffset) {
                                    scroll = parent.window.pageYOffset;
                                }
                                reportWindow.setPosition(pos[0],pos[1]+scroll);
                                document.getElementById('param_form').style.display="none";
                                document.getElementById('image').style.display="block";
                        </script>
                        <?php
                    }
                }
            ?>           
            </table>
            </form>
            </div>
            <?php
        }

    }

?>
