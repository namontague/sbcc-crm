<?php

    require_once('SOAP/Client.php');

    class ReportClient {
    //const webservices_uri = self::reportserver_uri."services/repository";
        const webservices_path = "services/repository";
        const jasper_namespace = "http://www.jaspersoft.com/namespaces/php";
        const OUTPUT_FORMAT_PDF ="PDF";
        const OUTPUT_FORMAT_JRPRINT="JRPRINT";
        const OUTPUT_FORMAT_HTML="HTML";
        const OUTPUT_FORMAT_XLS="XLS";
        const OUTPUT_FORMAT_XML="XML";
        const OUTPUT_FORMAT_CSV="CSV";
        const OUTPUT_FORMAT_RTF="RTF";
        const OUTPUT_IMAGES_URI="IMAGES_URI";
        const OUTPUT_PAGE="PAGE";

        private $webservices_uri;
        private $username;
        private $password;
        private $basepath;
        public $reportUnit ='';
        //private $report_params = array();
        public $report_params = array();
        public $param_array = array();
        public $format = self::OUTPUT_FORMAT_PDF;
        private $attachments;
        private $result;


        const MAX_EXECUTION_TIME = 120;

        const FULL_STOP_REPLACEMENT = '_-_';

        function __construct() {
            global $sugar_config;
            $this->username= $sugar_config['reportserver_username'];
            $this->password= $sugar_config['reportserver_password'];
            $this->webservices_uri=$sugar_config['reportserver_url'].self::webservices_path;
            if( array_key_exists('reportserver_basefolder', $sugar_config) ) {
                $this->basepath='/'.$sugar_config['reportserver_basefolder'];
            } else {
                $this->basepath = '/';
            }
        }


        function connection($max_execution_time = self::MAX_EXECUTION_TIME) {
            $connection_params = array("user" => $this->username, "pass" => $this->password);

            if($max_execution_time) {
                $connection_params["timeout"] = $max_execution_time;
            }
            $info = new SOAP_client($this->webservices_uri, false, false, $connection_params);
            return $info;

        }

        //get the base path
        function getBasePath() {
            $basepath = $this->basepath;
            return $basepath;
        }

        //Add stripped param to array
        function createParamArray($param_name, $param_value) {

            $index = count($this->param_array);
            $param_array = $this->param_array[$index]['name'] = $param_name;
            $param_array = $this->param_array[$index]['paramname'] = $param_value;
            //return $paramarray;
        }


        function addParam($param, $value) {
            // Convert back . characters
            $param_name= str_replace(self::FULL_STOP_REPLACEMENT, '.', $param);
            $this->report_params[$param_name] = $value;

        }


        function format($newFormat) {
            if( isset($newFormat) && !is_null($newFormat) && $newFormat != '' ) {
                $this->format = strtoupper($newFormat);
            }
            return $this->format;
        }


        function runReport() {
            $info = $this->connection();

            $op_xml = "<request operationName=\"runReport\" locale=\"en_AU\">";
            $op_xml .="<argument name=\"RUN_OUTPUT_FORMAT\">".$this->format."</argument>\n";
            $op_xml .="<argument name=\"USE_DIME_ATTACHMENTS\"><![CDATA[1]]></argument>";

            $op_xml .="<resourceDescriptor name=\"\" wsType=\"reportUnit\" uriString=\"".$this->basepath.$this->reportUnit."\" isNew=\"false\">".
                "<label></label>";

            $keys = array_keys($this->report_params);
            foreach ($keys AS $key) {
                if( is_array($this->report_params[$key])) {
                    foreach($this->report_params[$key] as $multival) {
                        if($multival !== '') {
                            $op_xml .="<parameter name=\"".$key."\" isListItem='true'><![CDATA[".$multival."]]></parameter>";
                        }
                    }
                } else {
                    if( $this->report_params[$key] !== '') {
                        $op_xml .="<parameter name=\"".$key."\"><![CDATA[".$this->report_params[$key]."]]></parameter>";
                    }
                }
            }

            $op_xml .="</resourceDescriptor></request>";

            $params = array("request" => $op_xml );

            $response = $info->call("runReport",$params,array('namespace' => "http://www.jaspersoft.com/client"));

            $this->attachments = $info->_soap_transport->attachments;

            $this->result = $response;
            return $response;
        }


        function displayResult() {
            if (get_class($this->result) == 'SOAP_Fault') {
                $errorMessage = $this->result->getFault()->faultstring;

                echo $errorMessage;
                exit();
            }

            $operationResult = $this->getOperationResult($this->result);

            if ($operationResult['returnCode'] != '0') {
                echo "Error executing the report:<br><font color=\"red\">".$operationResult['returnMessage']."</font>";
                exit();
            }

            if (is_array($this->attachments)) {

                @ob_end_clean();
                ob_start();

                if ($this->format == self::OUTPUT_FORMAT_PDF) {
                    header ( "Content-Description: File Transfer" );
                    header ( "Content-type: application/pdf" );
                    header ( "Content-Transfer-Encoding: binary" );
                    header ( 'Content-Disposition: inline; filename="report.pdf"');
                    header ( "Cache-Control: public" );
                    echo( $this->attachments["cid:report"]);
                } else if ($this->format == self::OUTPUT_FORMAT_HTML) {
                    // 1. Save attachments....

                    // 2. Print the report....

                        header ( "Content-type: text/html");

                        foreach (array_keys($this->attachments) as $key) {
                            if ($key != "cid:report") {
                                $f = fopen("images/".substr($key,4),"w");
                                fwrite($f, $this->attachments[$key]);
                                fclose($f);
                            }
                        }

                        echo "<center>";
                        echo "</center><hr>";

                        echo $this->attachments["cid:report"];

                    //print_r(array_keys($attachments));

                } else if ($this->format== self::OUTPUT_FORMAT_XLS) {
                    header ( 'Content-type: application/xls' );
                    header ( 'Content-Disposition: attachment; filename="report.xls"');
                    echo( $this->attachments["cid:report"]);
                } else if ($this->format== self::OUTPUT_FORMAT_RTF) {
                    header ( 'Content-type: application/rtf' );
                    header ( 'Content-Disposition: attachment; filename="report.rtf"');
                    echo( $this->attachments["cid:report"]);
                }

                @ob_flush();

                exit();
            } else {
                echo "No attachment found!";
            }
        }

        function displayBinaryResult() {
            $str = '';
             if (get_class($this->result) == 'SOAP_Fault') {
                 return null;
            }

            $operationResult = $this->getOperationResult($this->result);

            if ($operationResult['returnCode'] != '0') {
                  return null;
            }

            if (is_array($this->attachments)) {
                if ($this->format == self::OUTPUT_FORMAT_PDF) {
                   $str =  $this->attachments["cid:report"];
                } else if ($this->format == self::OUTPUT_FORMAT_HTML) {
                        foreach (array_keys($this->attachments) as $key) {
                            if ($key != "cid:report") {
                                $f = fopen("images/".substr($key,4),"w");
                                fwrite($f, $this->attachments[$key]);
                                fclose($f);
                            }
                        }
                       $str .=  "<center>";
                       $str .=  "</center><hr>";
                       $str .=  $this->attachments["cid:report"];
                } else if ($this->format== self::OUTPUT_FORMAT_XLS) {
                    $str =  $this->attachments["cid:report"];
                } else if ($this->format== self::OUTPUT_FORMAT_RTF) {
                    $str =  $this->attachments["cid:report"];
                }
                return $str;
            } else {
                return null;
            }
        }


        // ********** XML related functions *******************
        function getOperationResult($operationResult) {
            $domDocument = new DOMDocument();
            $domDocument->loadXML($operationResult);

            $operationResultValues = array();

            foreach( $domDocument->childNodes AS $ChildNode ) {
                if ( $ChildNode->nodeName != '#text' ) {

                    if ($ChildNode->nodeName == "operationResult") {
                        foreach( $ChildNode->childNodes AS $ChildChildNode ) {

                            if ( $ChildChildNode->nodeName == 'returnCode' ) {
                                $operationResultValues['returnCode'] = $ChildChildNode->nodeValue;
                            } else if ( $ChildChildNode->nodeName == 'returnMessage' ) {
                                    $operationResultValues['returnMessage'] = $ChildChildNode->nodeValue;
                            }
                        }
                    }
                }
            }

            return $operationResultValues;
        }


        function getResourceDescriptors($operationResult) {
            $domDocument = new DOMDocument();
            $domDocument->loadXML($operationResult);

            $folders = array();
            $count = 0;

            foreach( $domDocument->childNodes AS $ChildNode ) {
                if ( $ChildNode->nodeName != '#text' ) {

                    if ($ChildNode->nodeName == "operationResult") {
                        foreach( $ChildNode->childNodes AS $ChildChildNode ) {

                            if ( $ChildChildNode->nodeName == 'resourceDescriptor' ) {
                                $resourceDescriptor = $this->readResourceDescriptor($ChildChildNode);
                                $folders[ $count ] = $resourceDescriptor;
                                $count++;
                            }
                        }
                    }
                }
            }

            return $folders;
        }


        function readResourceDescriptor($node) {
            $resourceDescriptor = array();

            $resourceDescriptor['name'] = $node->getAttributeNode("name")->value;
            $resourceDescriptor['uri'] =  $node->getAttributeNode("uriString")->value;
            $resourceDescriptor['type'] = $node->getAttributeNode("wsType")->value;

            $resourceProperties = array();
            $subResources = array();
            $parameters = array();

            // Read subelements...
            foreach( $node->childNodes AS $ChildNode ) {
                if ( $ChildNode->nodeName == 'label' ) {
                    $resourceDescriptor['label'] = 	$ChildNode->nodeValue;
                } else if ( $ChildNode->nodeName == 'description' ) {
                        $resourceDescriptor['description'] = 	$ChildNode->nodeValue;
                    } else if ( $ChildNode->nodeName == 'creationDate' ) {
                            $resourceDescriptor['creationDate'] = 	$ChildNode->nodeValue / 1000;
                        } else if ( $ChildNode->nodeName == 'resourceProperty' ) {
                            //$resourceDescriptor['resourceProperty'] = $ChildChildNode->nodeValue;
                            // read properties...
                                $resourceProperty =$this->addReadResourceProperty($ChildNode );
                                $resourceProperties[ $resourceProperty["name"] ] = $resourceProperty;
                            } else if ( $ChildNode->nodeName == 'resourceDescriptor' ) {
                                    array_push( $subResources, $this->readResourceDescriptor($ChildNode));
                                } else if ( $ChildNode->nodeName == 'parameter' ) {
                                        $parameters[ $ChildNode->getAttributeNode("name")->value ] =  $ChildNode->nodeValue;
                                    }
            }

            $resourceDescriptor['properties'] = $resourceProperties;
            $resourceDescriptor['resources'] = $subResources;
            $resourceDescriptor['parameters'] = $parameters;


            return $resourceDescriptor;
        }


        function addReadResourceProperty($node) {
            $resourceProperty = array();

            $resourceProperty['name'] = $node->getAttributeNode("name")->value;

            $resourceProperties = array();

            // Read subelements...
            foreach( $node->childNodes AS $ChildNode ) {
                if ( $ChildNode->nodeName == 'value' ) {
                    $resourceProperty['value'] = $ChildNode->nodeValue;
                } else if ( $ChildNode->nodeName == 'resourceProperty' ) {
                    //$resourceDescriptor['resourceProperty'] = $ChildChildNode->nodeValue;
                    // read properties...
                        array_push( $resourceProperties, $this->addReadResourceProperty($ChildNode ) );
                }
            }

            $resourceProperty['properties'] = $resourceProperties;

            return $resourceProperty;
        }
        // ********** End XML functions     ********************

        //construct list request
        function listResources($url_id) {
            // Create connection object and strings
            $info = $this->connection();


            // Construct request
            $request = '<request operationName="get" locale="en"><resourceDescriptor name="" wsType="folder" uriString="'.$url_id.'" isNew="false"><label>null</label></resourceDescriptor></request>';
            $response = $info->call("list",$request,array('namespace' => ReportClient::jasper_namespace));

            return $response;
        }

        //construct get request
        function getResources($uri, $args = array()) {
            $info = $this->connection();

            $op_xml = "<request operationName=\"get\">";

            if (is_array ($args)) {
                $keys = array_keys($args);
                foreach ($keys AS $key) {
                    $op_xml .="<argument name=\"$key\">".$args[$key]."</argument>";
                }
            }

            $op_xml .= "<resourceDescriptor name=\"get\" wsType=\"reportUnit\" uriString=\"$uri\" isNew=\"false\">".
                "<label></label></resourceDescriptor></request>";

            $params = array("request" => $op_xml );
            $response = $info->call("get",$params,array('namespace' => ReportClient::jasper_namespace));

            return $response;
        }


        function reportUnit($id) {
            $currentUri = "$this->basepath$id";
            $result = $this->getResources($currentUri);
            $folders = $this->getResourceDescriptors($result);
            $reportUnit = $folders[0];

            return $reportUnit;
        }


        //strip the word param_xxxxx_ off the param name
        function stripParamsType($paramsList){
            foreach ($paramsList AS $param_name) {
                $param = $param_name['name'];
                if(substr($param, 0, strlen("PARAM_TEXT_")) == "PARAM_TEXT_") {
                    $report_param = substr($param, strlen("PARAM_TEXT_"));
                    $this->createParamArray($report_param, $param);
                } else if(substr($param, 0, strlen("PARAM_DATE_")) == "PARAM_DATE_") {
                    $report_param = substr($param, strlen("PARAM_DATE_"));
                    $this->createParamArray($report_param, $param);
                } else if(substr($param, 0, strlen("PARAM_SELECT_LIST_")) == "PARAM_SELECT_LIST_") {
                    $report_param = substr($param, strlen("PARAM_SELECT_LIST_"));
                    $this->createParamArray($report_param, $param);
                } else if(substr($param, 0, strlen("PARAM_SELECT_")) == "PARAM_SELECT_") {
                    $report_param = substr($param, strlen("PARAM_SELECT_"));
                    $this->createParamArray($report_param, $param);
                } else if(substr($param, 0, strlen("PARAM_MULTI_SELECT_LIST_")) == "PARAM_MULTI_SELECT_LIST_") {
                    $report_param = substr($param, strlen("PARAM_MULTI_SELECT_LIST_"));
                    $this->createParamArray($report_param, $param);
                } else if(substr($param, 0, strlen("PARAM_MULTI_SELECT_")) == "PARAM_MULTI_SELECT_") {
                    $report_param = substr($param, strlen("PARAM_MULTI_SELECT_"));
                    $this->createParamArray($report_param, $param);
                } else if(substr($param, 0, strlen("PARAM_CHECK_")) == "PARAM_CHECK_") {
                    $report_param = substr($param, strlen("PARAM_CHECK_"));
                    $this->createParamArray($report_param, $param);
                }
            }
        }


        function getParams() {
            foreach (array_keys($_GET) AS $param_name) {
                if(substr($param_name, 0, 6) == "PARAM_") {
                    $params = true;

                    if(substr($param_name, 0, strlen("PARAM_TEXT_")) == "PARAM_TEXT_") {
                        $report_param = substr($param_name, strlen("PARAM_TEXT_"));
                        $this->addParam($report_param, htmlspecialchars_decode($_GET[$param_name], ENT_QUOTES));
                    } else if(substr($param_name, 0, strlen("PARAM_DATE_")) == "PARAM_DATE_") {
                        global $timedate;
                        $toformat = 'm/d/Y';
                        if(!empty($_GET[$param_name])){
                            $date_format = $timedate->get_date_format();
                            if($date_format != $toformat){
                                $formatted_date = $timedate->swap_formats($_GET[$param_name], $date_format, $toformat);
                                $date_array = date_parse($formatted_date);
                            }else{
                                $date_array = date_parse($_GET[$param_name]);
                            }


                            $timestamp = gmmktime(0,0,0, $date_array['month'], $date_array['day'], $date_array['year']);
                            $timestamp = sprintf("%d000", $timestamp);
                        }else{
                            //stop date being sent as 1/1/00 when its not set by the user
                            $timestamp = '';
                        }
                        $report_param = substr($param_name, strlen("PARAM_DATE_"));
                        $this->addParam($report_param, $timestamp);
                    } else if(substr($param_name, 0, strlen("PARAM_SELECT_LIST_")) == "PARAM_SELECT_LIST_") {
                        $report_param = substr($param_name, strlen("PARAM_SELECT_LIST_"));
                        $this->addParam($report_param, htmlspecialchars_decode($_GET[$param_name], ENT_QUOTES));
                    } else if(substr($param_name, 0, strlen("PARAM_SELECT_")) == "PARAM_SELECT_") {
                        $report_param = substr($param_name, strlen("PARAM_SELECT_"));
                        $this->addParam($report_param, htmlspecialchars_decode($_GET[$param_name], ENT_QUOTES));
                    } else if(substr($param_name, 0, strlen("PARAM_MULTI_SELECT_LIST_")) == "PARAM_MULTI_SELECT_LIST_") {
                        $report_param = substr($param_name, strlen("PARAM_MULTI_SELECT_LIST_"));
                        $val = array();
                        foreach($_GET[$param_name] as $k => $v ) {
                            $val[$k] = htmlspecialchars_decode($v,ENT_QUOTES);
                        }
                        $this->addParam($report_param, $val);
                    } else if(substr($param_name, 0, strlen("PARAM_MULTI_SELECT_")) == "PARAM_MULTI_SELECT_") {
                        $report_param = substr($param_name, strlen("PARAM_MULTI_SELECT_"));
                        $val = array();
                        foreach($_GET[$param_name] as $k => $v ) {
                            $val[$k] = htmlspecialchars_decode($v,ENT_QUOTES);
                        }
                        $this->addParam($report_param, $val);
                    } else if(substr($param_name, 0, strlen("PARAM_CHECK_")) == "PARAM_CHECK_") {
                        $report_param = substr($param_name, strlen("PARAM_CHECK_"));
                        $this->addParam($report_param, $_GET[$param_name]);
                    }
                }
            }
        }


        function getInputsArray($id) {
            define("TYPE_FOLDER","folder");
            define("TYPE_REPORTUNIT","reportUnit");
            define("TYPE_DATASOURCE","datasource");
            define("TYPE_DATASOURCE_JDBC","jdbc");
            define("TYPE_DATASOURCE_JNDI","jndi");
            define("TYPE_DATASOURCE_BEAN","bean");
            define("TYPE_IMAGE","img");
            define("TYPE_FONT","font");
            define("TYPE_JRXML","jrxml");
            define("TYPE_CLASS_JAR","jar");
            define("TYPE_RESOURCE_BUNDLE","prop");
            define("TYPE_REFERENCE","reference");
            define("TYPE_INPUT_CONTROL","inputControl");
            define("TYPE_DATA_TYPE","dataType");
            define("TYPE_OLAP_MONDRIAN_CONNECTION","olapMondrianCon");
            define("TYPE_OLAP_XMLA_CONNECTION","olapXmlaCon");
            define("TYPE_MONDRIAN_SCHEMA","olapMondrianSchema");
            define("TYPE_XMLA_CONNTCTION","xmlaConntction");
            define("TYPE_UNKNOW","unknow");
            define("TYPE_LOV","lov"); // List of values...
            define("TYPE_QUERY","query"); // List of values...

            //These constants are copied here from DataType for facility
            define("DT_TYPE_TEXT",1);
            define("DT_TYPE_NUMBER",2);
            define("DT_TYPE_DATE",3);
            define("DT_TYPE_DATE_TIME",4);


            //These constants are copied here from InputControl for facility
            define("IC_TYPE_BOOLEAN",1);
            define("IC_TYPE_SINGLE_VALUE",2);
            define("IC_TYPE_SINGLE_SELECT_LIST_OF_VALUES",3);
            define("IC_TYPE_SINGLE_SELECT_QUERY",4);
            define("IC_TYPE_MULTI_VALUE",5);
            define("IC_TYPE_MULTI_SELECT_LIST_OF_VALUES",6);
            define("IC_TYPE_MULTI_SELECT_QUERY",7);
            define("PROP_VERSION","PROP_VERSION");
            define("PROP_PARENT_FOLDER","PROP_PARENT_FOLDER");
            define("PROP_RESOURCE_TYPE","PROP_RESOURCE_TYPE");
            define("PROP_CREATION_DATE","PROP_CREATION_DATE");

            // File resource properties
            define("PROP_FILERESOURCE_HAS_DATA","PROP_HAS_DATA");
            define("PROP_FILERESOURCE_IS_REFERENCE","PROP_IS_REFERENCE");
            define("PROP_FILERESOURCE_REFERENCE_URI","PROP_REFERENCE_URI");
            define("PROP_FILERESOURCE_WSTYPE","PROP_WSTYPE");

            // Datasource properties
            define("PROP_DATASOURCE_DRIVER_CLASS","PROP_DATASOURCE_DRIVER_CLASS");
            define("PROP_DATASOURCE_CONNECTION_URL","PROP_DATASOURCE_CONNECTION_URL");
            define("PROP_DATASOURCE_USERNAME","PROP_DATASOURCE_USERNAME");
            define("PROP_DATASOURCE_PASSWORD","PROP_DATASOURCE_PASSWORD");
            define("PROP_DATASOURCE_JNDI_NAME","PROP_DATASOURCE_JNDI_NAME");
            define("PROP_DATASOURCE_BEAN_NAME","PROP_DATASOURCE_BEAN_NAME");
            define("PROP_DATASOURCE_BEAN_METHOD","PROP_DATASOURCE_BEAN_METHOD");

            // ReportUnit resource properties
            define("PROP_RU_DATASOURCE_TYPE","PROP_RU_DATASOURCE_TYPE");
            define("PROP_RU_IS_MAIN_REPORT","PROP_RU_IS_MAIN_REPORT");

            // DataType resource properties
            define("PROP_DATATYPE_STRICT_MAX","PROP_DATATYPE_STRICT_MAX");
            define("PROP_DATATYPE_STRICT_MIN","PROP_DATATYPE_STRICT_MIN");
            define("PROP_DATATYPE_MIN_VALUE","PROP_DATATYPE_MIN_VALUE");
            define("PROP_DATATYPE_MAX_VALUE","PROP_DATATYPE_MAX_VALUE");
            define("PROP_DATATYPE_PATTERN","PROP_DATATYPE_PATTERN");
            define("PROP_DATATYPE_TYPE","PROP_DATATYPE_TYPE");
            define("PROP_REFERENCE_URI","PROP_REFERENCE_URI");

            // ListOfValues resource properties
            define("PROP_LOV","PROP_LOV");
            define("PROP_LOV_LABEL","PROP_LOV_LABEL");
            define("PROP_LOV_VALUE","PROP_LOV_VALUE");

            // InputControl resource properties
            define("PROP_INPUTCONTROL_TYPE","PROP_INPUTCONTROL_TYPE");
            define("PROP_INPUTCONTROL_IS_MANDATORY","PROP_INPUTCONTROL_IS_MANDATORY");
            define("PROP_INPUTCONTROL_IS_VISIBLE","PROP_INPUTCONTROL_IS_VISIBLE");
            define("PROP_INPUTCONTROL_IS_READONLY","PROP_INPUTCONTROL_IS_READONLY");

            // SQL resource properties
            define("PROP_QUERY","PROP_QUERY");
            define("PROP_QUERY_VISIBLE_COLUMNS","PROP_QUERY_VISIBLE_COLUMNS");
            define("PROP_QUERY_VISIBLE_COLUMN_NAME","PROP_QUERY_VISIBLE_COLUMN_NAME");
            define("PROP_QUERY_VALUE_COLUMN","PROP_QUERY_VALUE_COLUMN");
            define("PROP_QUERY_LANGUAGE","PROP_QUERY_LANGUAGE");

            // SQL resource properties
            define("PROP_QUERY_DATA","PROP_QUERY_DATA");
            define("PROP_QUERY_DATA_ROW","PROP_QUERY_DATA_ROW");
            define("PROP_QUERY_DATA_ROW_COLUMN","PROP_QUERY_DATA_ROW_COLUMN");
            define("MODIFY_REPORTUNIT","MODIFY_REPORTUNIT_URI");
            define("CREATE_REPORTUNIT","CREATE_REPORTUNIT_BOOLEAN");
            define("LIST_DATASOURCES","LIST_DATASOURCES");
            define("IC_GET_QUERY_DATA","IC_GET_QUERY_DATA");
            define("VALUE_TRUE","true");
            define("VALUE_FALSE","false");
            define("RUN_OUTPUT_FORMAT","RUN_OUTPUT_FORMAT");
            define("RUN_OUTPUT_FORMAT_PDF","PDF");
            define("RUN_OUTPUT_FORMAT_JRPRINT","JRPRINT");
            define("RUN_OUTPUT_FORMAT_HTML","HTML");
            define("RUN_OUTPUT_FORMAT_XLS","XLS");
            define("RUN_OUTPUT_FORMAT_XML","XML");
            define("RUN_OUTPUT_FORMAT_CSV","CSV");
            define("RUN_OUTPUT_FORMAT_RTF","RTF");
            define("RUN_OUTPUT_IMAGES_URI","IMAGES_URI");
            define("RUN_OUTPUT_PAGE","PAGE");

            $parametersCount = 0;
            $resources = $this->reportUnit($id);
//print_r($resources);
            $resources = $resources['resources'];
            // Find the datasource uri...
            $dsUri = '';
            for ($i=0; $i< count($resources); ++$i) {
                $rd = $resources[$i];

                if ($rd['type'] == TYPE_DATASOURCE ) {
                    $dsUri = $rd['properties'][PROP_REFERENCE_URI]['value'];  //getReferenceUri();
                }
                else if ($rd['type'] == TYPE_DATASOURCE_JDBC ||
                    $rd['type'] == TYPE_DATASOURCE_JNDI ||
                    $rd['type'] == TYPE_DATASOURCE_BEAN) {
                    $dsUri = $rd['uri'];
                }
            }

            $paramsList = array();

            for ($i=0; $i< count($resources); ++$i) {
                $rd = $resources[$i];

                $k = $rd['name'];
                if ($rd['type'] == TYPE_INPUT_CONTROL ) {
                    $parametersCount++;
                    $paramsList[$k]['label'] = $rd['label'];
                    $paramsList[$k]['hasdefault'] = false;
                    $paramsList[$k]['value'] = null;

                    $paramsList[$k]['properties'] = $rd['properties'];
                    $controlType = $rd['properties'][PROP_INPUTCONTROL_TYPE]['value'];


                    $paramsList[$k]['visible'] = true;
                    if( $rd['properties'][PROP_INPUTCONTROL_IS_VISIBLE]['value'] == 'false') {
                        $paramsList[$k]['visible'] = false;
                    }

                    if ($controlType == IC_TYPE_BOOLEAN) {
                        $paramsList[$k]['name'] = "PARAM_CHECK_".$rd['name'];
                        $paramsList[$k]['type'] = "checkbox";
                    } else if ($controlType == IC_TYPE_SINGLE_VALUE) {

                        $controlData = $rd['resources'][0];

                        $controlDataType = '';
                        if(isset($controlData['properties'][PROP_DATATYPE_TYPE])){
                            $controlDataType = $controlData['properties'][PROP_DATATYPE_TYPE]['value'];
                        }
                        #if controldatetype not set we look in for to see if there are any resources
                        #for this control and set the control type to the value.
                        #ATM this only works if the resource type is a date
                        if(empty($controlDataType) ){
                              $url_formatted = str_replace(strrchr($controlData['properties']['PROP_REFERENCE_URI']['value'],'/'),'',$controlData['properties']['PROP_REFERENCE_URI']['value']);
                              $listResources = new SimpleXMLElement($this->listResources($url_formatted));
                              if(isset($listResources->resourceDescriptor)){
                                  foreach($listResources->resourceDescriptor as $resource){
                                      foreach($resource->resourceProperty as $property){
                                          if($property['name'] == 'PROP_DATATYPE_TYPE'){
                                              $controlDataType = $property->value;
                                          }
                                      }
                                  }
                              }
                        }
                        ##End


                        if($controlDataType == DT_TYPE_DATE || $controlDataType == DT_TYPE_DATE_TIME) {
                            $paramsList[$k]['name'] = "PARAM_DATE_".$rd['name'];
                            $paramsList[$k]['type'] = "text";
                        } else {
                            $paramsList[$k]['name'] = "PARAM_TEXT_".$rd['name'];
                            $paramsList[$k]['type'] = "text";
                        }

                    } else if ($controlType == IC_TYPE_SINGLE_SELECT_LIST_OF_VALUES) {
                        $paramsList[$k]['name'] = "PARAM_SELECT_LIST_".$rd['name'];
                        $paramsList[$k]['type'] = "select_list";
                        $paramsList[$k]['options'] = $this->getLOVList($rd['resources']);

                    } else if ($controlType == IC_TYPE_SINGLE_SELECT_QUERY) {
                        $paramsList[$k]['name'] = "PARAM_SELECT_".$rd['name'];
                        $paramsList[$k]['type'] = "select_query";
                        // Get the list of entries....

                        $result = $this->getResources($rd['uri'], array( IC_GET_QUERY_DATA => $dsUri ) );

                        $rds = $this->getResourceDescriptors($result);
                        $rd =$rds[0];
                        $paramsList[$k]['options'] = $rd['properties'][PROP_QUERY_DATA]['properties'];

                    } else if ($controlType == IC_TYPE_MULTI_SELECT_LIST_OF_VALUES) {
                        $paramsList[$k]['name'] = "PARAM_MULTI_SELECT_LIST_".$rd['name'];
                        $paramsList[$k]['type'] = "select_list";
                        $paramsList[$k]['multi'] = true;

                        $paramsList[$k]['options'] = $this->getLOVList($rd['resources']);

                    } else if ($controlType == IC_TYPE_MULTI_SELECT_QUERY) {
                        $paramsList[$k]['name'] = "PARAM_MULTI_SELECT_".$rd['name'];
                        $paramsList[$k]['type'] = "select_query";
                        $paramsList[$k]['multi'] = true;
                        // Get the list of entries....

                        $result = $this->getResources($rd['uri'], array( IC_GET_QUERY_DATA => $dsUri ) );

                        $rds = $this->getResourceDescriptors($result);
                        $rd =$rds[0];
                        $paramsList[$k]['options'] = $rd['properties'][PROP_QUERY_DATA]['properties'];

                    }
                    //if not mandatory add a empty options '' => ''
                    if( isset($paramsList[$k]['options'] ) && $paramsList[$k]['properties'][PROP_INPUTCONTROL_IS_MANDATORY]['value'] == 'false') {
                        $nullOption = array();
                        $nullOption['name'] = "";
                        $nullOption['value'] = "";
                        array_unshift($paramsList[$k]['options'],$nullOption);
                    }

                    // Need to replace . characters as PHP will convert them to _ when form submitted
                    $paramsList[$k]['controlName'] = str_replace('.', self::FULL_STOP_REPLACEMENT, $paramsList[$k]['name']);
                }
            }
            return $paramsList;
        }


        function getLOVList($lov_resources) {
            if( is_array($lov_resources) ) {
                foreach ($lov_resources AS $lov) {
                    if ($lov['type'] == TYPE_LOV) {
                        return  $lov['properties'][PROP_LOV]['properties'];
                    } else if($lov['type'] == TYPE_REFERENCE) {
                        $result = $this->getResources($lov_resources[0]['properties'][PROP_REFERENCE_URI]['value'] );
                        $lov_resources2 = $this->getResourceDescriptors($result);
                        return $this->getLOVList($lov_resources2);
                    }
                }
            }
            return null;
        }

    }
?>