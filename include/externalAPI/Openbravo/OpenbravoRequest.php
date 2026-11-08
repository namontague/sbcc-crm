<?php

require_once('Zend/Http/Client.php');
class OpenbravoRequest {

    const OPENBRAVO_API_SESSION_ID = 'OPENBRAVO_API_SESSION_ID';

    public static $_domain = '';

    public static $_username = '';

    public static $_password = '';

    public static $_method = '';

    public static $erp_id = '';

    public static $_error = '';
    
    public static $return_as_xml = '';

    public static $urls = array(
      'login' => 'secureApp/LoginHandler.html',
      'summary' => 'ws/au.com.eintel.openbravo.service.web.summary.businesspartner/',
      'client' => 'ws/au.com.eintel.openbravo.service.web.core.businesspartner/',
      'contact' => 'ws/au.com.eintel.openbravo.service.web.core.businesspartner/contact/',  
      'order' => 'ws/au.com.eintel.openbravo.service.web.core.order/sales',
      'product' => 'ws/au.com.eintel.openbravo.service.web.core.product/',
      'price_list' => '/ws/au.com.eintel.openbravo.service.web.core.pricelists',  
      'order_table' => '?tabId=186&recordId=',
      'accounts_table' => '?tabId=220&recordId=',
      'contacts_table' => '?tabId=496&recordId=',
      'sequence' => 'ws/au.com.eintel.openbravo.service.web.core.product/serialnumber/',  
      'requisition' => 'ws/au.com.eintel.openbravo.service.web.core.requisition/',
      'product_category' => '/ws/dal/ProductCategory?includeChildren=false',  
    );
    

    public function __construct($method) {
        $this->_method = $method;
        $this->_username = self::$_username;
        $this->_password = self::$_password;
        $this->_domain = self::$_domain;
        $this->urls = self::$urls;
        $this->_url = self::$urls[$this->_method];
        $this->setReturnAsXml();

    }
       
    public static function init($domain, $username, $password) {
        self::$_domain = $domain;
        self::$_username = $username;
        self::$_password = $password;
    }

    public function success()   {
        return $this->_success;
    }

    public function setReturnAsXml($return_as_xml = false)   {
        $this->return_as_xml = $return_as_xml;
    }
    public function getReturnAsXml()   {
        return $this->return_as_xml;
    }
    
    public function getError()  {
        return $this->_error;
    }

    public function getResponse(){
        return $this->_response;
    }

    public function getUrl(){        
        if(!empty($this->erp_id)){
            //add filter to add parameters
//            if($this->_method == 'summary'){
//                return 'http://'.$this->_domain.$this->_url.'.businesspartner/'.urlencode($this->erp_id);
//            }
            return 'http://'.$this->_domain.$this->_url.rawurlencode($this->erp_id);
        }
        return 'http://'.$this->_domain.$this->_url;
    }

    
    public function login_basic_auth() {        
        $zend_client = new Zend_Http_Client('http://'.$this->_domain.$this->urls['client']);
        $zend_client->setAuth(self::$_username,self::$_password);
        $zend_client->setParameterGet(array(
            'page' => 1,
            'max' => 1,
        ));        
        $data = $this->request($zend_client,'GET');
        if($data !== false){
            if(isset($data['success']) ){
                return $data['success'];
            }
        }
        return false;
    }
    
    public function login(){           
        $zend_client = new Zend_Http_Client($this->getUrl());
        $zend_client->setParameterPost(array(
            'Command'=>'Default',
            'IsAjaxCall'=> 1,
            'user'=> $this->_username,
            'password' => $this->_password,
        ));
        $data = $this->request($zend_client,'POST');
        if($data === false){
            return false;
        }

        if( isset($data['data'])) {
           $body = $data['data'];
        } else {
           $body = array('messageTitle' => 'No data returned');
        }
        if($this->_success && isset($body['target'])) {
          $headers = $this->_response->getHeaders();
          if( isset($headers['Set-cookie'])) {
            $setCookies = $headers['Set-cookie'];
            if(!is_array($setCookies)) {
              $setCookies = array($setCookies);
            }
            foreach($setCookies as $cookie) {
              $sessionId = $this->getSessionID($cookie);
              if( $sessionId) {
                $this->sessionId = $sessionId;
                $_SESSION[self::OPENBRAVO_API_SESSION_ID] = $sessionId;
                return true;
              }
            }
          }

        }else{
            $this->_error = $body['messageTitle'];
        }
        return false;
    }

    public function post_json($parameters = null){
        $zend_client = new Zend_Http_Client(isset($this->url)?$this->url:$this->getUrl());
        $zend_client->setAuth(self::$_username,self::$_password);
        $zend_client->setParameterPost(array(
            'Command'=>'Default',
            'IsAjaxCall'=> 1,
            'source'=>'sugarcrm',
        ));
      
        if($parameters){
            $zend_client->setRawData(json_encode($parameters),'application/json');
        }
        return $this->request($zend_client,'POST');
    }

    public function post($parameters = null){
        $zend_client = new Zend_Http_Client(isset($this->url)?$this->url:$this->getUrl());
        $zend_client->setAuth(self::$_username,self::$_password);
        $zend_client->setParameterPost(array(
            'Command'=>'Default',
            'IsAjaxCall'=> 1,
            'source'=>'sugarcrm',
        ));
        if($parameters){
             $zend_client->setParameterPost($parameters);
        }
        return $this->request($zend_client,'POST');
    }

    public function get($parameters = null){
        $zend_client = new Zend_Http_Client(isset($this->url)?$this->url:$this->getUrl());
        $zend_client->setConfig(array('timeout'=>60));
        $zend_client->setAuth(self::$_username,self::$_password);
        $zend_client->setParameterGet(array(
            'Command'=>'Default',
            'IsAjaxCall'=> 1,
            'source'=>'sugarcrm',
        ));
        if($parameters){
            $zend_client->setParameterGet($parameters);
        }
        return $this->request($zend_client,'GET');
    }

    public function setPutParams(&$zend_client,$data){
        $zend_client->setParameterPost('source','sugarcrm');
        foreach($data as $key=>$value){            
            $zend_client->setParameterPost($key,$value);
        }
    }
    
    public function put($data){
        $zend_client = new Zend_Http_Client(isset($this->url)?$this->url:$this->getUrl());
        $zend_client->setConfig(array('timeout'=>60));
        $zend_client->setAuth(self::$_username,self::$_password);

        $zend_client->setParameterPost('source','sugarcrm');
       
        $zend_client->setRawData(json_encode($data));

        return $this->request($zend_client,'PUT');
    }

    public function delete($data = null){
        $zend_client = new Zend_Http_Client(isset($this->url)?$this->url:$this->getUrl());
        $zend_client->setAuth(self::$_username,self::$_password);
//        $zend_client->setParameterPost(array(
//            'Command'=>'Default',
//            'IsAjaxCall'=> 1,
//        ));
     
        if($data){
            $zend_client->setRawData(json_encode($data),'application/json');
        }
        return $this->request($zend_client,'DELETE');
    }
    
    
    public function request($zend_client,$method)   {
        try {
            ini_set("memory_limit","500M");
            
            $this->_response = $zend_client->request($method);           
        } catch( Zend_Http_Client_Exception $e) {
            $this->_error = $e->getMessage();
            //$this->_success = $this->_response->isSuccessful();
            return array('success'=>FALSE, 'errorMessage'=> $this->_error);            
        }

        $this->_success = $this->_response->isSuccessful();
        
        if($this->getReturnAsXml()){
            $data = new SimpleXMLElement($this->_response->getBody());
        }elseif($method == 'PUT') {
            $data = json_decode($this->_response->getRawBody(), true);
        }else {
            $data = json_decode($this->_response->getBody(), true);
        }
        
        if($this->success()) {
            if(isset($data)) {
                return array('success'=>TRUE, 'data'=> $data);
            }
            if($this->_method == 'sequence'){
                return array('success'=>TRUE, 'data'=> $this->_response->getRawBody());
            }
            //data is empty
            return array('success'=>TRUE);
        }else{            
            return array(
                'success' =>FALSE, 
                'errorMessage' => $this->_response->getMessage(),
                'bodyMessage' => $this->getMessage()   
            );
         
        }
        return array('success'=>FALSE, 'errorMessage'=> $this->_response->getMessage());

    }
    
    
    function getMessage(){
        if(isset($this->_response)){
            $xml = simplexml_load_string($this->_response->getRawBody());
            if(isset($xml->message)){
                return (string)$xml->message;
            }
        }
    }
    
   
    
    public function getSessionID($cookies) {
        $matches = array();
        if( preg_match('/JSESSIONID\s*=\s*([^ ;]+)\s*;/',$cookies,$matches)) {
          return $matches[1];
        }
        return NULL;
    }

}