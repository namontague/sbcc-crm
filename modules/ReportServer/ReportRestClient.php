<?php
    require_once('Zend/Http/Client.php');

    class ReportRestClient {
 
        const webservices_path = "rest/";
        const webservices_path_v2 = "rest_v2/";
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
        private $webservices_uri_v2;
        private $username;
        private $password;
        private $basepath;
        public $reportUnit ='';
        public $report_name;
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
            
            $this->zend_client = new Zend_Http_Client();
            $this->zend_client->setStripArrayKeys(true);
            $this->username= !empty($sugar_config['reportserver_organization'])? $sugar_config['reportserver_username']."|".$sugar_config['reportserver_organization']:$sugar_config['reportserver_username'];
            $this->password= $sugar_config['reportserver_password'];
            $this->organization = $sugar_config['reportserver_organization']; 
            $this->webservices_uri=$sugar_config['reportserver_url'].self::webservices_path;
            $this->webservices_uri_v2=$sugar_config['reportserver_url'].self::webservices_path_v2;
            if( array_key_exists('reportserver_basefolder', $sugar_config) ) {
                $this->basepath='/'.$sugar_config['reportserver_basefolder'];
            } else {
                $this->basepath = '/';
            }
        }

        public function getCookieString(array $cookies){
            $cookieString = null;  
            foreach($cookies as $cookie){
                $part = explode(';',$cookie);
                $cookieString = ($cookieString == null)? $part[0] : $cookieString . '; ' . $part[0];
              }
            return $cookieString;
        }

        function connection($max_execution_time = self::MAX_EXECUTION_TIME) {
            $connection_params = array("user" => $this->username, "pass" => $this->password);

            if($max_execution_time) {
                $connection_params["timeout"] = $max_execution_time;
            }
            
            
            $this->zend_client->setUri($this->webservices_uri."login");
            $this->zend_client->setConfig(array('timeout'=>$max_execution_time));
            $this->zend_client->setParameterPost(array(
                'Command'=>'Default',
                'IsAjaxCall'=> 1,
                'j_username'=> $this->username,
                'j_password' => $this->password,
            ));
            $response = $this->zend_client->request('POST');  
            
            
            //Get Header from response
            $headers = $response->getHeaders();
            
            $this->zend_client->resetParameters();
            
           
            $cookie = explode('=', $this->getCookieString(array($headers["Set-cookie"])));
            
            $this->zend_client->setCookie($cookie[0],$cookie[1]);

            return $response;

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

        function runRestReport() {
            $info = $this->connection();

            //$url = "http://reports.eintelerp.com.au/jasperserver-pro/rest_v2/reports/Reports/{$this->report_location}.pdf";
            
            $url = $this->webservices_uri_v2."reports".$this->basepath.$this->reportUnit.".".strtolower($this->format);
            
            $this->zend_client->setUri($url);
           // $this->zend_client->setConfig(array('timeout'=>60));
            if($this->format == self::OUTPUT_FORMAT_HTML) {
                //$this->report_params['IMAGES_URI'] = 'reports.eintelerp.com.au';
                if(!empty($this->report_params)) {
                    $this->zend_client->setParameterGet($this->report_params);
                }
                $response = $this->zend_client->request('GET');
                header ( "Content-type: text/html");
                echo $response->getBody();

            }else{
                $file = fopen('php://temp', 'w');

                if(!empty($this->report_params)){
                    $this->zend_client->setParameterGet($this->report_params);
                }
                $response = $this->zend_client->setStream($file)->request('GET'); 

                //$response = $this->zend_client->setStream("/tmp/report_test.pdf")->request('GET'); 
                
                $file_name = $this->report_name.".".strtolower($this->format);  
                if ($file) {
                    @ob_end_clean();
                    ob_start();

                    header('Content-Description: File Transfer');
                    header("Content-Type: application/octet-stream"); 
                    //header("Content-Type: application/pdf");
                    //header("Content-Length: " . filesize($file)); 
                    header("Content-Disposition: attachment; filename={$file_name}"); 
                    echo stream_get_contents($response->getStream());
                    @ob_flush();
                }   
            }
        }
        
        //construct list request
        function listResources($path) {
            $info = $this->connection();
            $this->zend_client->setUri($this->webservices_uri."resources".$path);
            $request = $this->zend_client->request('GET')->getBody();  
            return $request;
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
        
        
      
        //used from emailing reports and achieving
        function displayBinaryResult() {
            //Using REST V2
            $url = $this->webservices_uri_v2."reports".$this->basepath.$this->reportUnit.".".strtolower($this->format);
            $this->zend_client->setUri($url);
            if(!empty($this->report_params)){
                $this->zend_client->setParameterGet($this->report_params);
            }
            $response = $this->zend_client->request('GET')->getBody(); 
            if(isset($response)){
                return $response;
            }
            return null;
            
            /* Using REST V1
            $this->zend_client->setUri($this->webservices_uri."report".$this->basepath.$this->reportUnit);
            $this->zend_client->setAuth($this->username, $this->password);
            if(!empty($this->report_params)) {
                $this->zend_client->setParameterPost($this->report_params);
            }
            $this->zend_client->setRawData("<resourceDescriptor wsType='reportUnit' uriString='{$this->basepath}{$this->reportUnit}'>{$this->report_params_xml}</resourceDescriptor>",'text/xml');
            $response = $this->zend_client->request('PUT');
            $body = $response->getBody();
            $xml = null;
            if(!empty($body)){
                $xml = new SimpleXMLElement($body);
                $this->zend_client->setParameterPost(array("RUN_OUTPUT_FORMAT" => $this->format));
                if(!empty($this->report_params)) {
                    $this->zend_client->setParameterGet($this->report_params);
                    $this->zend_client->setParameterGet(array('file'=>'report'));
                }
                $this->zend_client->setUri($this->webservices_uri."report/".(string)$xml->uuid);
                $response = $this->zend_client->request('GET');
                return $response->getBody();
            }   
            */      
        }
        
        //strip the word param_xxxxx_ off the param name
        function stripParamsType($paramsList){
            foreach ($paramsList AS $param_name) {
                $param = $param_name['name'];
                if(substr($param, 0, strlen("P_T_")) == "P_T_") {
                    $report_param = substr($param, strlen("P_T_"));
                    $this->createParamArray($report_param, $param);
                } else if(substr($param, 0, strlen("P_D_")) == "P_D_") {
                    $report_param = substr($param, strlen("P_D_"));
                    $this->createParamArray($report_param, $param);
                } else if(substr($param, 0, strlen("P_SL_")) == "P_SL_") {
                    $report_param = substr($param, strlen("P_SL_"));
                    $this->createParamArray($report_param, $param);
                } else if(substr($param, 0, strlen("P_S_")) == "P_S_") {
                    $report_param = substr($param, strlen("P_S_"));
                    $this->createParamArray($report_param, $param);
                } else if(substr($param, 0, strlen("P_MS_")) == "P_MS_") {
                    $report_param = substr($param, strlen("P_MS_"));
                    $this->createParamArray($report_param, $param);
                } else if(substr($param, 0, strlen("P_C_")) == "P_C_") {
                    $report_param = substr($param, strlen("P_C_"));
                    $this->createParamArray($report_param, $param);
                } 
            }
        }

        function getParams() {
            //create empty param list
            foreach (explode(",",$_POST['parameters_list']) AS $name) {
                $param_list[$name] = $name;
            }
            foreach ($param_list AS $param_name) {
               // if(substr($param_name, 0, 6) == "PARAM_") {
                    $params = true;

                    //$parameter = $parameters_list[$param_name];
                    $parameter = null;
                    if(substr($param_name, 0, strlen("P_T_")) == "P_T_") {
                        $report_param = substr($param_name, strlen("P_T_"));
                        //$parameter = $param_list[$param_name]; 
                        
                        if(isset($_POST[$param_name])){
                            $parameter = $_POST[$param_name];
                        }elseif (isset($_POST[$report_param])) {
                            //default hidden value on form
                            $parameter = $_POST[$report_param];                    
                        }
                        
                        $this->addParam($report_param, htmlspecialchars_decode($parameter, ENT_QUOTES));
                    }else if(substr($param_name, 0, strlen("P_B_")) == "P_B_") {
                        $report_param = substr($param_name, strlen("P_B_"));
                        //$parameter = $param_list[$param_name]; 
                        
                        if(isset($_POST[$param_name])){
                            $parameter = $_POST[$param_name];
                        }elseif (isset($_POST[$report_param])) {
                            //default hidden value on form
                            $parameter = $_POST[$report_param];                    
                        }
                        $this->addParam($report_param, htmlspecialchars_decode($parameter, ENT_QUOTES));
                    } else if(substr($param_name, 0, strlen("P_D_")) == "P_D_") {
                        $report_param = (substr($param_name, 0, strlen("P_DT_")) == "P_DT_")? substr($param_name, strlen("P_DT_")):substr($param_name, strlen("P_D_"));
                        
                        //$parameter = $param_list[$param_name]; 
                        
                        if(isset($_POST[$param_name])){
                            $parameter = $_POST[$param_name];
                        }elseif (isset($_POST[$report_param])) {
                            //default hidden value on form
                            $parameter = $_POST[$report_param];                    
                        }
                        
                        //$parameter = $parameters_list[$report_param];
                        global $timedate;
                        $toformat = 'Y-m-d';
                        if(!empty($parameter)){
                            $date_format = $timedate->get_date_format();
                            
                            $date = $parameter;
                            
                            if($date_format != $toformat){
                                $date = $timedate->to_display($date, $date_format, $toformat);
                                $date_array = date_parse($date);
                            }else{
                                $date_array = date_parse($date);
                            }                            
                        }else{
                            //stop date being sent as 1/1/00 when its not set by the user
                            $date = '';
                        }
                        
                        
                        
                        if(substr($param_name, 0, strlen("P_DT_")) == "P_DT_") {
                            //$report_param = substr($param_name, strlen("PARAM_DATE_TIME_"));
                            if(isset($date_array)){
                                $timestamp = gmmktime(0,0,0, $date_array['month'], $date_array['day'], $date_array['year']);
                                $date = sprintf("%d000", $timestamp);
                            }
                        }                        
                        $this->addParam($report_param, $date);
                    } else if(substr($param_name, 0, strlen("P_SL_")) == "P_SL_") {
                        $report_param = substr($param_name, strlen("P_SL_"));
                        //$parameter = $param_list[$param_name]; 
                        
                        if(isset($_POST[$param_name])){
                            $parameter = $_POST[$param_name];
                        }elseif (isset($_POST[$report_param])) {
                            //default hidden value on form
                            $parameter = $_POST[$report_param];                    
                        }
                        $this->addParam($report_param, htmlspecialchars_decode($parameter, ENT_QUOTES));
                    } else if(substr($param_name, 0, strlen("P_S_")) == "P_S_") {
                        $report_param = substr($param_name, strlen("P_S_"));
                        //$parameter = $param_list[$param_name]; 
                        
                        if(isset($_POST[$param_name])){
                            $parameter = $_POST[$param_name];
                        }elseif (isset($_POST[$report_param])) {
                            //default hidden value on form
                            $parameter = $_POST[$report_param];                    
                        }
                        $this->addParam($report_param, htmlspecialchars_decode($parameter, ENT_QUOTES));
                    } else if(substr($param_name, 0, strlen("P_MS_")) == "P_MS_") {
                        $report_param = substr($param_name, strlen("P_MS_"));
                        //$parameter = $param_list[$param_name]; 
                        $val = '';
                        if(isset($_POST[$param_name])){
                            $parameter = $_POST[$param_name];
                            $val = array();
                            foreach($parameter as $k => $v ) {
                                $val[$k] = htmlspecialchars_decode($v,ENT_QUOTES);
                            }
                        }
                        
                        $this->addParam($report_param, $val);
                    } else if(substr($param_name, 0, strlen("P_C_")) == "P_C_") {
                        $report_param = substr($param_name, strlen("P_C_"));
                        //$parameter = $param_list[$param_name]; 
                        
                        if(isset($_POST[$param_name])){
                            $parameter = $_POST[$param_name];
                        }elseif (isset($_POST[$report_param])) {
                            //default hidden value on form
                            $parameter = $_POST[$report_param];                    
                        }
                        $this->addParam($report_param, $parameter);
                    }
                //}
            }
        }
        
        function getRestInputsArray($path) {
            $info = $this->connection();
            $url = $this->webservices_uri_v2."reports".$this->basepath.$path."/inputControls";

            $this->zend_client->setUri($url);
            $request = $this->zend_client->request('GET')->getBody();  
                      
            $xml = null;
            if(!empty($request)){
                $xml = new SimpleXMLElement($request);
            }
            return $xml;
        }
        
       

    }
?>