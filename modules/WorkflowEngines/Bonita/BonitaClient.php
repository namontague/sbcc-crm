<?php
/*********************************************************************************
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 ********************************************************************************/

/**
 * Description of BonitaEngine
 * get the HTTP request object to call the BONITA REST API
 * using the parameters as set in the config.php file
 *
 * @author eintel
 */

class BonitaClient {
    var $httpRequest;
    var $bonitaServerHost;


    function BonitaClient() {
        $this->getHttpRequestObject();
        $this->bonitaServerHost = rtrim($GLOBALS['sugar_config']['bonita_server_host'],"/");
    }

    function getHttpRequestObject() {

        $process_url = '';

        $bonitaRestUsername = $GLOBALS['sugar_config']['bonita_rest_credentials']['username'];
        $bonitaRestPassword = $GLOBALS['sugar_config']['bonita_rest_credentials']['password'];

        $credentials = $bonitaRestUsername . ':' . $bonitaRestPassword;
        $options = array(httpauth => $credentials,
                         httpauthtype => HTTP_AUTH_BASIC,
                         protocol => HTTP_VERSION_1_1);

        $this->httpRequest = new HttpRequest($process_url, HTTP_METH_POST, $options);
        $this->httpRequest->setContentType = 'application/x-www-form-urlencoded; charset=UTF-8';

    }

    /*
     * Function to start a process on Bonita and return back the process id
     * which is used to store as part of the WorkflowEvent record.
     * recordID is the id of the record from the particular moduleName
     * triggerType is the type field in workflowevents module. This provides reason for workflow being triggered.
     * processName is the name fo the process being triggered
     */
    function startBonitaProcess($moduleName, $recordID, $triggerType, $processName = null) {
        if( empty($processName)) {
            $processName = $GLOBALS['sugar_config']['workflows'][$moduleName];
        }

        $post = array('variables'=>"<map><entry><string>id</string><string>" . $recordID . "</string></entry><entry><string>module</string><string>" . $moduleName . "</string></entry><entry><string>type</string><string>" . $triggerType . "</string></entry></map>");
        $this->prepareRequest("runtime", "instantiateProcessWithVariables", $processName, $post);

        $xml = $this->sendRequest();
        if($xml) {
            $valueString = explode($processName . "--", (string)($xml->value));
            $process_id = $valueString [1];

            // pass back the generated process id on Bonita
            return $process_id;
        }
    }

    function getTaskIdsFor($processName, $instanceId, $taskState='READY') {

        $this->prepareRequest("queryRuntime", "getTaskList", "$processName--$instanceId/$taskState");

        $xml = $this->sendRequest();
        if($xml) {
            $taskIds = array();
            foreach($xml->ActivityInstance as $activity) {
                $taskIds[] = (string)$activity->uuid->value;
            }
            return $taskIds;
        }
    }


    function setVariable($taskId, $name, $value) {
        $this->prepareRequest("runtime", "setVariable", $taskId, array('variableId'=>$name, 'variableValue'=>$value));
        $this->sendRequest();
    }

    function restUrl($api, $method, $resources) {
        $url = "$this->bonitaServerHost/bonita-server-rest/API/{$api}API/$method";
        if( $resources) {
            $url .= '/'.$resources;
        }
        return $url;
    }

    function prepareRequest($api, $method, $resources, $postData = null) {
        $this->httpRequest->setUrl($this->restUrl($api, $method, $resources));

        //set any extra HTTP body contents here.
        $this->httpRequest->setPostFields(array('options'=>"<map><entry><string>user</string><string>admin</string></entry></map>"));
        if( $postData ) {
            $this->httpRequest->addPostFields($postData);
        }
    }

    function sendRequest() {
        try {
            $this->httpRequest->send();

            if ($this->httpRequest->getResponseCode() == 200) {
                $xml = $this->httpRequest->getResponseBody();

                return simplexml_load_string($xml);
            } else {
                $requestResult = 'Failed with Response Code: ' . $this->httpRequest->getResponseCode();
                error_log($requestResult);
            }
        }
        catch (HttpException $ex) {
            $requestResult = (string)$ex;
            error_log($requestResult);
        }

    }

    function formUrl($taskId) {
        $url = "$this->bonitaServerHost/bonita/console/admin.html?locale=default#task=$taskId&mode=app";
        return $url;
    }

}

?>
