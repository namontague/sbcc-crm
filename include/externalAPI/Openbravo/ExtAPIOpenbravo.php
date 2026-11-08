<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2012 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/

require_once('include/externalAPI/Base/ExternalAPIBase.php');
require_once('OpenbravoRequest.php');
require_once ('Zend/Http/Client.php');


class ExtAPIOpenbravo extends ExternalAPIBase { 
    public $authMethod = 'password'; 
    public $useAuth = true;
    public $requireAuth = true;
    protected $authData;
    public $needsUrl = false; 
    public $supportedModules = array(); 
    public $connector = 'ext_rest_openbravo';
    public $url = '';
    public $sales_order_link = '?tabId=186&recordId=';

    function __construct() {
        $this->url = $this->getConnectorParam('openbravo_base_url');
        $this->sales_order_link = 'http://'.$this->url.$this->sales_order_link;
    }

    public function loadEAPM($eapmBean,$sync_user = false) {
        if($sync_user){
           $this->account_name = $this->getConnectorParam('openbravo_sync_user');//'sugarcrm';
           $this->account_password = $this->getConnectorParam('openbravo_sync_user_password');//'password';
        }else{
            parent::loadEAPM($eapmBean);         
        }
        
        OpenbravoRequest::init($this->url, $this->account_name, $this->account_password); 
    }

    
    function getProductCategories(){
        $ob = new OpenbravoRequest('product_category');
        $ob->setReturnAsXml(true);
        return $ob->get();
    }
    
    //External Account setup for a user will call this to validate and display "Connected" for the EAPM Status
    public function checkLogin($eapmBean = null) {
        if($this->account_name == $this->getConnectorParam('openbravo_sync_user') || !$this->getConnectorParam('connection_session_enable')) {
            $status = $this->login();            
        } else {        
            if(!empty($_SESSION[OpenbravoRequest::OPENBRAVO_API_SESSION_ID])) {
                 return array('success' => true);             
            }else{
                $status = $this->login();                
            }
        }
        if( !$status['success'] ) {
            $GLOBALS['log']->error("Unable to logon to Openbravo at $this->url.");
            return false;
        }else{
            return $status;
        }
    }

    public function login(){
        $ob = new OpenbravoRequest('login');
        
        if($this->account_name == $this->getConnectorParam('openbravo_sync_user') || !$this->getConnectorParam('connection_session_enable')){
            if($ob->login_basic_auth()){
                return array('success' => true);
            }else{
                $GLOBALS['log']->error($ob->getError());
                return array('success' => false, 'error'=>$ob->getError());
            }
        }elseif($ob->login()){
            return array('success' => true);
        }else {
            $GLOBALS['log']->error($ob->getError());
            return array('success' => false, 'error'=>$ob->getError());
        }
        
//        if($ob->login()) {
//            return array('success' => true);
//        } else {
//            $GLOBALS['log']->error($ob->getError());
//            return array('success' => false, 'error'=>$ob->getError());
//        }
    }
    
 
    public function getSummary($record){
        $focus = $record;
        
        if($record->object_name == 'Call' && $record->parent_type == 'Accounts'){
            $parent_bean = BeanFactory::getBean($focus->parent_type, $focus->parent_id);
            if(empty($parent_bean->id)) {
                return array('success'=>FALSE,'errorMessage'=>'Unable to retrieve the requested record in Sugar.');
            }
            $focus = $parent_bean;
        }else if(is_a($record, 'Contact') && !empty($record->account_id)){
            $parent_bean = BeanFactory::getBean('Accounts', $record->account_id);
            if(empty($parent_bean->id)) {
                return array('success'=>FALSE,'errorMessage'=>'Unable to retrieve the requested record in Sugar.');
            }
            $focus = $parent_bean;
            
        }
        
        if(empty($focus->id)) {
            return array('success'=>FALSE,'errorMessage'=>'Unable to retrieve the requested record in Sugar.');
        }
        
        //if the id is the code return the busines partner object and get the id
        if(strlen($focus->erp_id) < 32){
            $business_partner = $this->retrieve_business_partner($record,true);
            if(isset($business_partner['data']['id'])){
                $focus->erp_id = $business_partner['data']['id'];
            }else{
                return array('success'=>FALSE,'errorMessage'=>$business_partner->errorMessage);
            }            
        }        

        $ob = new OpenbravoRequest('summary');
        $ob->erp_id = $focus->erp_id;

//        $this->show_balance = $this->getConnectorParam('account_balance');
//        $this->show_freight = $this->getConnectorParam('freight_information');
//        $this->show_purchase_history = $this->getConnectorParam('purchase_history');

        $parameters['balance'] = $this->getConnectorParam('account_balance');
        $parameters['freight'] = $this->getConnectorParam('freight_information');
        $parameters['purchasehistory'] = $this->getConnectorParam('purchase_history');

        return $ob->get($parameters);
//        if($ob->success()) {
//            if(isset($data['id'])) {
//                return array('success'=>TRUE,'summary'=> $data);
//            }
//        }
//        return array('success'=>FALSE,'errorMessage'=>$ob->getError());
       

    }


    public function getClient($record, $module = 'Account')   {
            //first load the parent bean
            global $beanList;
            //Generic method of loading a bean
            if(is_a($record,'Account')){
                $bean = $record;
            }else{
                $bean = new $beanList[$module];
                $bean->retrieve($record);
            }

            if(empty($bean->id)) {
                return array('success'=>FALSE,'errorMessage'=>'Unable to retrieve the requested record in Sugar.');
            }

            $ob = new OpenbravoRequest('client');
            $ob->erp_id = $bean->erp_id;

            return $ob->get('GET');
            
//            if($ob->success()) {
//                $result = $ob->getResponse();
//                $client = getJSONobj()->decode($result->getBody());
//
//                if(!isset($client['id'])) {
//                    return array('success'=>FALSE,'errorMessage'=>'No client in Openbravo found.');
//                }
//            } else {
//                return array('success'=>FALSE,'errorMessage'=>'No client in Openbravo found.');
//            }
//
//            return array('success'=>TRUE,'client'=> $client);
	}

	
    public function logout() {
        $result = 'Not logged in';
        if($this->sessionId) {
            unset($_SESSION[self::OPENBRAVO_API_SESSION_ID]);
          try {
            $result = $this->httpClient->delete($this->url.'ws/au.com.eintel.openbravo.service.web.core.session');
            unset($this->sessionId);
          } catch(Exception $e) {
            unset($this->sessionId);
            $result = 'Error logging out:'.$e->getMessage();
          }
        }
        return $result;
    }

    public function getSessionInfo() {
        $this->checkLogin();
        return $this->httpClient->get($this->url.'ws/au.com.eintel.openbravo.service.web.core.session');
    }

    function accountCategoryOptions($category) {
        $options = array(
            'Both' => 'B',
            'Customer' => 'D',
            'Employee' => 'E',
            'Supplier' => 'C',
            'Standard' => 'standard'
        );

         if(isset($options[$category])){
            return $options[$category];
         }else{
           return 'standard';
         }
    }
    
    function retrieve_webservice_count($entity = 'client') {
        $ob = new OpenbravoRequest($entity);
        $ob->url = $ob->getUrl().'count';
        $result = $ob->get();
        if($result['success']) {
           return $result['data'];      
        }
        return false;
    }
    
    //Create a Business Partner Contact
    //URI : <business partner url>/<business partner identifier>/contact
    //Operation: PUT
    function create_account($bean, $params = array()){
        return $this->create_business_partner($bean);
    }
    //Create a Business Partner
    //URI : <business partner url>
    //Operation: PUT
     //ERPSync Fucntions
    function create_business_partner($bean){
        $ob = new OpenbravoRequest('client');
        $account = array(
            'searchKey' => $bean->code,
            'name' => $bean->name,
            'description' => $bean->description,
            'taxID' => $bean->abn,
            'category' => $this->accountCategoryOptions($bean->account_type),
            'url' => $bean->website
        );
        //use the location logic hooks to create/save the business partner locations
        return $ob->put($account);
    }

    
    //Call update Business Partner
    function update_account($bean,$params = array()){
        return $this->update_business_partner($bean);
    }
    //Update an existing Business Partner
    //URI : <business partner url>/<business partner identifier>
    //Operation: POST
    function update_business_partner($bean){
      
        $ob = new OpenbravoRequest('client');
        $ob->erp_id = $bean->erp_id;

        $account = array(
            'searchKey' => $bean->code,
            'name' => $bean->name,
            'description' => $bean->description,
            'taxID' => $bean->abn,
            'category' => $this->accountCategoryOptions($bean->account_type),
            'url' => $bean->website,
        );
        //use the location logic hooks to create/save the business partner locations
        return $ob->post_json($account);

    }
    
    //Delete an existing Business Partner
    //URI : <business partner url>/<business partner identifier>
    //Operation: DELETE
    function delete_business_partner($bean){
        $ob = new OpenbravoRequest('client');
        $ob->erp_id = $bean->erp_id;
        return $ob->delete();
    }
            
    //Retrieve a Business Partner
    //URI : <business partner url>/<business partner identifier>
    //Operation: GET
    function retrieve_business_partner($bean,$use_account_code = false){
        if(!is_a($bean, 'Account')){        
            $this->loadAccount($bean);
            $account_code = $bean->account_bp->code;
            $erp_id = $bean->account_bp->erp_id;
            if($use_account_code && !empty($erp_id)){
                $use_account_code = false;                
            }            
        }else{
            $erp_id = $bean->erp_id;
            $account_code = $bean->code;
        }
        
        if($use_account_code){
            $erp_id = $account_code;
        }
        if(empty($erp_id)){
            //if no id is passed every single business partner is returned.
            //we dont want this to happen
            return false;
        }

        $ob = new OpenbravoRequest('client');
        $ob->erp_id = $erp_id;
        $result = $ob->get();
        if($result['success']) {
            return $result['data'];
        }
        return false;

    }

    function retrieve_business_partners($page = 1,$max = 1000) {
        $ob = new OpenbravoRequest('client');
        $params = array(
            'locations' => 'true',
            'contacts' => 'true',
            'page' => $page,
            'max' => $max
            );
        $result = $ob->get($params);
        if($result['success']) {
//            $business_partner = array();
//            foreach($result['data'] as $bp) {
//                $business_partner[$bp['id']] = $bp;
//            }
//            return $business_partner;  
            return $result['data'];      
        }
    }

    function retrieve_all_contacts() {
        $ob = new OpenbravoRequest('contact');
        $result = $ob->get();
        if($result['success']) {
            $business_partner_contacts = array();
            foreach($result['data'] as $contact) {
                $business_partner_contacts[$contact['businessPartner']['id']][] = $contact;
            }
            return $business_partner_contacts;
        }
    }
    

   /*
    * CONTACT WEBSERVICES
    */
    
    //Create a Business Partner Contact
    //URI : <business partner url>/<business partner identifier>/contact
    //Operation: PUT
    function create_contact($bean, $params = array()){
        $account = null;
        $location_id = null;
        if(!empty($params)){
            if(isset($params['account'])){
                $account = $params['account'];
            }
            if(isset($params['location_id'])){
                $location_id = $params['location_id'];
            }
        } 
        return $this->create_business_partner_contact($bean,$account,$location_id);
    }
    
    function create_business_partner_contact($bean, $account = null,$location_id = null){
        if(!isset($account)){
            $this->loadAccount($bean);
        }else{
            $bean->account_bp = $account;
        }
        
        $ob = new OpenbravoRequest('contact');
        //override url
        $ob->url = "http://{$ob->_domain}{$ob->urls['client']}{$bean->account_bp->erp_id}/contact/";

        $contact = array(
                'isActive' => true,
                'firstName' => $bean->first_name,
                'lastName' => $bean->last_name,
                'phone' => $bean->phone_work,
                'alternativePhone' => $bean->phone_other,
                'email' => $bean->email1,
                'position' => strlen($bean->title) > 40 ? substr($bean->title,0,40) : $bean->title,
                'comments' => $bean->description,
                'fax' => $bean->phone_fax,
                'mobile' => $bean->phone_mobile,
                'location' => $location_id,
            );
        return $ob->put($contact);
    }
    
    function add_contact_to_business_partner($bean,$account = null){
        if(!empty($bean->erp_id)){
            return $this->update_contact($bean,$account);
        }else{
            return $this->create_contact($bean,$account);
        }
    }

    //Update an existing Business Partner Contact
    //URI : <business partner url>/<business partner identifier>/contact/<contact id>
    //Operation: POST
    function update_contact($bean,$params = array()){
        $account = null;
        $location_id = null;
        $active = true;
        if(!empty($params)){
            if(isset($params['account'])){
                $account = $params['account'];
            }
            if(isset($params['location_id']) && !empty($params['location_id'])){
                $location_id = $params['location_id'];
            }
            if(isset($params['active'])){
                $active = $params['active'];
            }
        } 
        return $this->update_business_partner_contact($bean,$account,$location_id,$active);
    }
    
    function update_business_partner_contact($bean,$account = null,$location_id = null,$active = true){
           if(!isset($account)){
            $this->loadAccount($bean);
        }else{
            if(is_array($account)){
                //used when we pass the account that has 
                //been retrieved from openbravo and not sugar.
                $bean->account_bp->erp_id = $account['businessPartner']['id'];
            }else {
                $bean->account_bp = $account;
            }
        }
        $ob = new OpenbravoRequest('contact');
        //override url
        $ob->url = "http://{$ob->_domain}{$ob->urls['client']}{$bean->account_bp->erp_id}/contact/{$bean->erp_id}";

        $contact = 
            array(
                'isActive' => $active,
                'firstName' => $bean->first_name,
                'lastName' => $bean->last_name,
                'phone' => $bean->phone_work,
                'alternativePhone' => $bean->phone_other,
                'email' => $bean->email1,
                'position' => strlen($bean->title) > 40 ? substr($bean->title,0,40) : $bean->title,
                'comments' => $bean->description,
                'fax' => $bean->phone_fax,
                'mobile' => $bean->phone_mobile,
                //'location' => array($location_id),
                    
            );
            if($active && !is_null($location_id)){
                $contact['location'] = array('id'=>$location_id);
            }
            //will move locations to use openbravo id as id
            
            //only update locations if the contact is active, 
            //other wise the error: 'Location 'XXXXXXXXXXXX' does not belong to the business partner of contact will occur 
            //as the bp context is not right for inactive contacts
            
        
            
       
        return $ob->post_json($contact);
    }
    
    
    
    
    //Remove an existing Business Partner Contact
    //URI : <business partner url>/<business partner identifier>/contact/<contact id>
    //Operation: DELETE
    function delete_contact($bean){
        $this->loadAccount($bean);
        
        $ob = new OpenbravoRequest('contact');
        $ob->url = "http://{$ob->_domain}{$ob->urls['client']}{$bean->account_bp->erp_id}/contact/{$bean->erp_id}";
        
        return $ob->delete();
    }

    function remove_contact_from_account($bean, $account){
        //if contact is just moving to a new account we need to update the contact.
        return $this->update_contact($bean,$account);
    }

    
    function loadAccount($bean){
        $bean->account_bp = BeanFactory::getBean('Accounts', $bean->account_id);
    }



    
    
    
    
    
    /*
     * LOCATION WEBSERVICES
     */
    //Retrieve  Business Partner Locations
    //API
    //URI : <business partner url>/<business partner identifier>/location
    //Operation: GET
    //Returns:
    function retrieve_business_partner_location($bean){
        if(!is_a($bean, 'Account')){        
            $this->loadAccount($bean);
        }
                
        $ob = new OpenbravoRequest('location');
        $ob->erp_id = $bean->account_bp->erp_id;
        $ob->url = "http://{$ob->_domain}{$ob->urls['client']}{$bean->account_bp->erp_id}/location";
     
        $result = $ob->get();
        
        if($result['success']){
            return $result['data'];
        }
        return false;
        
    }
 
    //Call create Business Partner Location
    function create_location($bean){
        return $this->create_business_partner_location($bean);
    }
    //Create a Business Partner Location
    //API
    //URI : <business partner url>/<business partner identifier>/location
    //Operation: PUT
    function create_business_partner_location($bean){
        if(!is_a($bean, 'Account')){        
            $this->loadAccount($bean);
        }
        $ob = new OpenbravoRequest('location');
        $ob->url = "http://{$ob->_domain}{$ob->urls['client']}{$bean->account_bp->erp_id}/location";

        
        
        $address1 = trim(trim($bean->building_type." ".$bean->number)." ".$bean->address1);
        
        $location = array(
            'isActive' => true,
            'name' => ".{$bean->city}, {$address1}",
            'line1' => $address1,
            'line2' => $bean->address2,//this is never used as address2 doesnt get used
            'city' => $bean->city,
            'region' => strtoupper($bean->state),
            'postCode' => $bean->postalcode,
            'country' => $bean->country,
            'phone' => $bean->phone,
            'alternativePhone' => $bean->alternate_phone,
            'fax' => $bean->fax,
            'shipTo' => ($bean->isshipping)? true:false,
            'invoiceTo' => ($bean->isbilling)?true:false,
            'defaultShipTo' => ($bean->defaultshipto)?true:false,
            'defaultInvoiceTo' => ($bean->defaultinvoiceto)?true:false,
            
        );
        
        return $ob->put($location);

    }

    //Call update Business Partner Location
    function update_location($bean){
        return $this->update_business_partner_location($bean);
    }
    //Update an existing Business Partner Location
    //API
    //URI : <business partner url>/<business partner identifier>/location/<location id>
    //Operation: POST
    //Data:
    function update_business_partner_location($bean,$active = true){
        if(!is_a($bean, 'Account')){        
            $this->loadAccount($bean);
        }
         
        $ob = new OpenbravoRequest('location');
        $ob->url = "http://{$ob->_domain}{$ob->urls['client']}{$bean->account_bp->erp_id}/location/{$bean->erp_id}";
        
        $address1 = trim(trim($bean->building_type." ".$bean->number)." ".$bean->address1);
        
        $location = array(
            'isActive' => $active,
            'name' => ".{$bean->city}, {$address1}",
            'line1' => $address1,
            'line2' => $bean->address2,
            'city' => $bean->city,
            'region' => strtoupper($bean->state),
            'postCode' => $bean->postalcode,
            'country' => $bean->country,
            'phone' => $bean->phone,
            'alternativePhone' => $bean->alternate_phone,
            'fax' => $bean->fax,
            'shipTo' => ($bean->isshipping)? true:false,
            'invoiceTo' => ($bean->isbilling)? true:false,
            'defaultShipTo' => ($bean->defaultshipto)?true:false,
            'defaultInvoiceTo' => ($bean->defaultinvoiceto)?true:false,
        );
        return $ob->post_json($location);
    
    }

    //Remove an existing Business Partner Location
    //API
    //URI : <business partner url>/<business partner identifier>/location/<location id>
    //Operation: DELETE
    //Data:
    function delete_business_partner_location($bean){
        $this->loadAccount($bean);
        $ob = new OpenbravoRequest('location');
        $ob->url = "http://{$ob->_domain}{$ob->urls['client']}{$bean->account_bp->erp_id}/location/{$bean->erp_id}";
        
        return $ob->delete();
    }

    /*
     * END LOCATION WEBSERVICES
     */


    /*
      Business Partners Web Service
         * Retrieve a Business Partner
         * Create a Business Partner
         * Update an existing Business Partner
         * Retrieve a list of Business Partners
         * Retrieve  Business Partner Contacts
         * Create a Business Partner Contact
         * Update an existing Business Partner Contact
         * Remove an existing Business Partner Contact
     */






    //Retrieve a list of Business Partners
    //URI : <business partner url>
    //Operation: GET

    //Retrieve  Business Partner Contacts
    //URI : <business partner url>/<business partner identifier>/contact
    //Operation: GET

    function retrieve_business_partner_contacts($bean,$use_account_code = false){
        if(!is_a($bean, 'Account')){        
            $this->loadAccount($bean);
        }else{
            if(!empty($bean->erp_id)){
                $bean->account_bp = $bean;
            }
        }
        if(empty($bean->account_bp)){
            //we need the account bp to get contacts.
            //exit is we do not have it afer loading the account.
            return false;
        }

        if($use_account_code && (!isset($bean->account_bp->erp_id) ||$bean->account_bp->erp_id == '')){
            $bean->erp_id = $bean->account_bp->code;
        }
        
        
       
        $ob = new OpenbravoRequest('client');
        $ob->url = "http://{$ob->_domain}{$ob->urls['client']}{$bean->account_bp->erp_id}/contact/";
        
        $ob->erp_id = $bean->erp_id;
        $result = $ob->get();
        if($result['success']){
            return $result['data'];
        }
        return false;

    }


    function retrieve_contacts_business_partner($bean){
        $ob = new OpenbravoRequest('contact');
        $ob->erp_id = $bean->erp_id;
        $result = $ob->get();
        if($result['success']){
            return $result['data'];
        }
        return false;

    }

    

    



    /*
     * SALES ORDER WEBSERVICES
     */
    


    public function createSalesOrderHeaderLink($account_id){
        if(!isset($account_id)){
            return  array('success'=>FALSE,'errorMessage'=>'Unable to retrieve, No linked account found.');
        }
        $account_bean = BeanFactory::getBean('Accounts', $account_id);
        if(empty($account_bean->id)) {
            return array('success'=>FALSE,'errorMessage'=>'Unable to retrieve the requested record in Sugar.');
        }
        if(empty($account_bean->erp_id)) {
            return array('success'=>FALSE,'errorMessage'=>"Unable to retrieve ERP ID for Account: {$account_bean->name}.");
        }

        
        $ob = new OpenbravoRequest('order');
        
        $timedate = TimeDate::getInstance();
        $account_bean->invoice_date = $timedate->asUserDate($timedate->getNow(true), true);
       
        
        $result = $this->create_sales_order_master($account_bean, null,null, true,false,$this->getConnectorParam('organization'));

        //$sales_order_header = $this->create_sales_order_header($account_bean,null,null, false);

        if(isset($result['success'])){           
            if(isset($result['data']['id'])){
                $result['data']['url'] = 'http://'.$ob->_domain.$ob->urls['order_table'].$result['data']['id'];
            }
            return $result;
        }else{
            $error_str = '';
            foreach($this->errors as $error){
                if($error != 'No lines to invoice' ){
                    //skip lines error
                    $error_str .= "{$error}.\n";
                }
            }
            if($result['errorMessage']){
                 $error_str .= "{$result['errorMessage']}";
            }
            
            return array('success'=>FALSE,'errorMessage'=> $error_str);
        }


    }


    //Create a Sales Order Header
    //URI : <order url>/sales
    //Operation: PUT
    function create_sales_order_header($bean, $price_list, $org = null, $txnDoc='Standard Order'){
        $bean->business_partner = $this->retrieve_business_partner($bean);
        
        $this->url = "{$this->service_url}sales";

        if(!isset($bean->business_partner['id'])){
            //no bp cannot create sales order
            $this->errors[] = 'Business partner ID missing';           
        }
        if(!isset($bean->business_partner['customer']['priceList']['id'])){
            //no pricelist cannot create sales order
            $this->errors[] = 'No price list found to be associated with business partner';           
        }
        if(!isset( $bean->business_partner['customer']['paymentTerms']['id'])){
            //no bp cannot create sales order
            $this->errors[] = 'Payment Terms not defined';           
        }
        
        $hasAddress = false;
        foreach($bean->business_partner['locations'] as $location){
            if($location['invoiceTo']){
                $invoice_id = $location['id'];
                $hasAddress = true;
            }
            if($location['shipTo']){
                $deliver_id = $location['id'];
                $hasAddress = true;
            }
        }
        
        if(!$hasAddress){
            //no bp location cannot create sales order
            $this->errors[] = 'There is no Address defined for Business Partner';           
        }
        
        
        //was there any missing required information?
        if(isset($this->errors) && !empty($this->errors)){
            return false;
        }

        $timeDate = TimeDate::getInstance();
        $orderDate= $timeDate->fromUserDate($bean->invoice_date)->asDbDate();

        


        $sales_order = array(
          'action' => 'complete',
          //'project' => $bean->project->erp_id,
          'transactionDocument' => $txnDoc,
          'orderReference' => $bean->reference,
          'description' => $bean->sales_order_notes,
          'deliveryNotes' => $bean->delivery_notes,
          'orderDate' => $orderDate,
          'scheduledDeliveryDate' => $orderDate,
          'businessPartner' => $bean->business_partner['id'],
          'currency' => 'AUD',
          'invoiceAddress' => array(
              'id' => $invoice_id
          ),
          'deliveryAddress' => array(
              'id' => $deliver_id
          ),
          'priceList' => (isset($price_list)?$price_list:$bean->business_partner['customer']['priceList']['id']),
          'paymentMethod'=> $bean->business_partner['customer']['paymentMethod']['id'],
          'paymentTerms' => $bean->business_partner['customer']['paymentTerms']['id'],

        );
        if(!empty($org)) {
            $sales_order['organization']  = $org;
        }
        return $sales_order;
    }
    
  
    
    //to replace both create sales order and create sales order header calls
    //
    function create_sales_order_master($bean, $line_items,$price_list, $send,$retireve = false, $org = null,$txnDoc='Standard Order'){
        $sales_order = $this->create_sales_order_header($bean,$price_list,$org,$txnDoc);

        if(!empty($line_items)){
        //add lines if any
            $sales_order['lines'] = $this->add_invoice_lines($line_items);
        }else{
            //no lines, no sales order created
            unset($sales_order['action']);
            $this->errors[] = 'No lines to invoice';
            //return false;
        }
        if($send){
            //return $this->sendRequest($bean, $sales_order, HTTP_METH_PUT,$retireve,false);
            $ob = new OpenbravoRequest('order');
            return $ob->put($sales_order);
        }else{
            return $sales_order;
        }
    }

   
    
    //Retrieve a Sales Order
    //URI : <order url>/sales/<order identifier>
    //Operation: GET
    function retrieve_sales_order($bean){
        $this->url = "{$this->service_url}sales/$bean->erp_id";

        $sales_order = array();

        return $this->sendRequest($bean, $sales_order, HTTP_METH_GET, true);
    }


    //Create a Sales Order
    //URI : <order url>/sales
    //Operation: PUT
    function create_sales_order($bean, $line_items,$price_list, $send, $org = null, $txnDoc='Standard Order'){
        $bean->bp = $this->retrieve_business_partner($bean);
        $this->url = "{$this->service_url}sales";

         if(!isset($bean->bp->id)){
            //no bp cannot create sales order
            $this->errors[] = 'Business partner ID missing';
            return false;
        }
        if(empty($price_list)){
            //no pricelist cannot create sales order
            $this->errors[] = 'No price list found to be associated with business partner';
            return false;
        }


        foreach($bean->bp->locations as $location){
            if($location->invoiceTo){
                $invoice_id = $location->id;
            }
            if($location->shipTo){
                $deliver_id = $location->id;
            }
        }

        $lines = $this->add_invoice_lines($line_items);
        
        $timeDate = TimeDate::getInstance();
        $orderDate= $timeDate->fromUserDate($bean->invoice_date)->asDbDate();
        if(!empty($lines)){
            $sales_order = array(
              'action' => 'complete',
              //'project' => $bean->project->erp_id,
              'transactionDocument' => $txnDoc,
              'orderReference' => $bean->reference,
              'description' => $bean->sales_order_notes,
              'orderDate' => $orderDate,
              'deliveryNotes' => $bean->delivery_notes,
              'scheduledDeliveryDate' => $orderDate,
              'businessPartner' => $bean->bp->id,
              'currency' => 'AUD',
              'invoiceAddress' => array(
                  'id' => $invoice_id
              ),
              'deliveryAddress' => array(
                  'id' => $deliver_id
              ),
              'priceList' => $price_list,
              'paymentMethod'=> $bean->bp->customer->paymentMethod->id,
              'paymentTerms' => $bean->bp->customer->paymentTerms->id,
              'lines' =>  $lines,
            );
            if(!empty($org)) {
                $sales_order['organization']  = $org;
            }
        }else{
            //no lines, no sales order created
            $this->errors[] = 'No lines to invoice';
            return false;
        }
        
        if($send){
            //return $this->sendRequest($bean, $sales_order, HTTP_METH_PUT,false,false);
            $ob = new OpenbravoRequest('order');
            return $ob->put($sales_order);
        }else{
            return $sales_order;
        }

       
    }

    function add_invoice_lines($line_items){
        global $app_list_strings;
        $lines = array();

        foreach($line_items as $item){
            $lines[$item->line_number] =
                    array(
                       'product' => $item->erp_product_id,
                       //'product' => $item->product_code, //COPYCOST//LABOUR //PART
                       'description' => $item->description,
                       //'UOM' => '101',
                       'unitPrice' => (isset($item->unit_price))?$item->unit_price:'',
                       'quantity' => (!empty($item->quantity))?$item->quantity:0,
                       'lineNetAmount' => (!empty($item->quantity) && !empty($item->unit_price))? $item->quantity * $item->unit_price:'',
                       'tax' => 'GST' //GST
                    );
            if(empty($lines[$item->line_number]['product'])){
                unset($lines[$item->line_number]['product']);
            }
        }
        return $lines;
    }
    

    //Update an existing Sales Order
    //URI : <order url>/sales/<order identifier>
    //Operation: POST
    //NOT USED
    function update_sales_order($bean){
        $bean->bp = $this->retrieve_business_partner($bean);

        $this->url = "{$this->service_url}sales/$bean->erp_id";

        $sales_order = $this->retrieve_sales_order($bean);

        foreach($bean->bp->locations as $location){
            if($location->invoiceTo){
                $invoice_id = $location->id;
            }
            if($location->shipTo){
                $deliver_id = $location->id;
            }
        }

        $sales_order['transactionDocument'] = 'Standard Order';
        $sales_order['orderDate'] = date('Y-m-d',time());
        $sales_order['scheduledDeliveryDate'] = date('Y-m-d',time());
        $sales_order['businessPartner'] = $bean->bp->id;
          //'orderReference' => ' <order reference>  (optional)',
        $sales_order['currency'] = 'AUD';
        $sales_order['invoiceAddress']['id'] = $invoice_id;
        $sales_order['deliveryAddress']['id'] = $deliver_id;
        $sales_order['priceList'] = $bean->bp->customer->priceList->id;
        $sales_order['paymentMethod']= $bean->bp->customer->paymentMethod->id;
        $sales_order['paymentTerms'] = $bean->bp->customer->paymentTerms->id;
        $sales_order['lines'] = $this->add_invoice_lines($bean);

        //$this->sendRequest($bean, $sales_order, HTTP_METH_POST);
    }



    //Retrieve a list of  Sales Order
    //URI : <order url>/sales
    //Operation: GET
    //NOT USED
    function retrieve_list_sale_order($bean){
        $this->url = "{$this->service_url}sales";

        $sales_order = array();

        //$this->sendRequest($bean, $sales_order, HTTP_METH_GET, true);
    }


    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*
      Product Web Service
         * Retrieve a Product
         * Retrieve a List of Products
         * Retrieve  Product Attachment
     */


    //Retrieve a Product
    //URI : <product url>/<product identifier>
    //Operation: GET
    function retrieve_product($bean){
        $ob = new OpenbravoRequest('product');
        $this->url = "{$this->product_url}{$bean->erp_id}";

        $product_list = array(
            // 'searchKey' => $bean->code,
            //'name' => $bean->name,
        );
        
        return $ob->get($product_list);
               
        
       // return $this->sendRequest($bean, $product_list, HTTP_METH_GET, true);
    }



    //Retrieve a list of Products
    //URI : <product url>
    //Operation: GET
    function retrieve_list_of_product($bean = null, $parameters = array()){
        $ob = new OpenbravoRequest('product');      
        return $ob->get($parameters);
    }


    //Retrieve  Price Lists
    //URI : <openbravo base url>/ws/au.com.eintel.openbravo.service.web.core.pricelists/
    //Operation: GET

    function retrieve_price_lists($bean){     
        $ob = new OpenbravoRequest('price_list');
        return $ob->get($price_lists);
    }

    
    function send_bulk_list($sales_order_list){
        $ob = new OpenbravoRequest('order');
        return $ob->put($sales_order_list);  
    }
    
    //Create Product Requisition
    //URI : <openbravo base url>/ws/au.com.eintel.openbravo.service.web.core.????/
    //Operation: POST
    function create_product_requisition($bean){
        return true;
        //return $this->sendRequest($bean, array(), HTTP_METH_PUT, true);
    }
    
    
    //Create a Sales requisition
    //API
    //URI : <requisition url>/
    //Operation: PUT
    //Data:
    function create_requisition($bean){
        global $timedate;
        $sales_requisition = array(
            //'project' => $bean->project->erp_id,
            'action' => 'complete',
            'requester' => $bean->requester,
            'description' => $bean->description,
        );
        $sales_requisition['lines'][10] = array(
            'needBy' => $timedate->to_display($bean->requested_date, $timedate->get_date_format(), 'Y-m-d'),
            'product' => $bean->erp_product_id,
            'quantity' =>  $bean->quantity,
        );
        $ob = new OpenbravoRequest('requisition');
        return $ob->put($sales_requisition);
    }



    function getNextSequence($id){
        $ob = new OpenbravoRequest('sequence');
        $ob->erp_id = $id;
        $result = $ob->post();    
        
        if($result['success']){
            if(isset($result['data'])){
                return $result['data'];
            }
        }
        return false;
    }


    
    
    
}


