<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * This class is an implemenatation class for all the web services
 */
require_once('service/core/SugarWebServiceImpl.php');
require_once('service/v2eintel/EintelHelperWebService.php');
SugarWebServiceImpl::$helperObject = new EintelHelperWebService();

class EintelWebServiceImpl extends SugarWebServiceImpl {
    /**
     * Log a portal user into the application
     *
     * @param UserAuth array $user_auth -- Set user_name and password (password needs to be
     *      in the right encoding for the type of authentication the user is setup for.  For Base
     *      sugar validation, password is the MD5 sum of the plain text password.
     * @param String $application -- The name of the application you are logging in from.  (Currently unused).
     * @param array $name_value_list -- Array of name value pair of extra parameters. As of today only 'language' and 'notifyonsave' is supported
     * @return Array - id - String id is the session_id of the session that was created.
     * 				 - module_name - String - module name of user
     * 				 - name_value_list - Array - The name value pair of user_id, user_name, user_language
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    public function portal_login($user_auth, $application, $auth_modules) {
        $GLOBALS['log']->info('Begin: EintelWebServiceImpl->portal_login');
        global $sugar_config, $system_config;
        $error = new SoapError();
        $success = false;
        if(!empty($user_auth['encryption']) && $user_auth['encryption'] === 'PLAIN') {
            $user_auth['password'] = md5($user_auth['password']);
        }
        $system_config = new Administration();
        $system_config->retrieveSettings('system');

        if(!isset($auth_modules) || $auth_modules== null) {
            $auth_modules = array('Contacts','Accounts','Users');
        }

        global $current_user;
        $extraNameValue = array();

        $module_name = null;
        $type = null;
        if( $system_config->settings['portal_contacts_access'] && in_array('Contacts', $auth_modules)) {
            $module_name = 'Contacts';
            $type = 'portal_contact';

            $contact = new Contact();
            $query = "SELECT c.id, c.first_name, c.last_name, a.id as account_id, a.name as account_name  FROM contacts c
                        LEFT JOIN email_addr_bean_rel eb ON eb.bean_id = c.id AND eb.bean_module='Contacts' AND eb.deleted=0
                        LEFT JOIN email_addresses ea ON ea.id = eb.email_address_id and ea.deleted = 0
                        LEFT JOIN accounts_contacts ac ON ac.contact_id = c.id and ac.deleted = 0
                        LEFT JOIN accounts a ON ac.account_id = a.id and a.deleted = 0
                        WHERE c.deleted = 0
                        AND c.portal_active = 1
                        AND c.portal_password='".$user_auth['password']."'
                        AND (ea.email_address='".$user_auth['user_name']."' OR c.portal_name='".$user_auth['user_name']."')";
            $result = $contact->dbManager->query($query);
            if($result ) {
                $row = $contact->dbManager->fetchByAssoc($result);
                $user = new User();
                $user = $user->retrieve_by_string_fields(array('user_name'=>'portal_contacts', 'deleted'=>0, 'status'=>'Active', 'portal_only'=>1) );
                if($row && !empty($user) && !empty($user->id) && !$user->is_group) {
                    $success = true;
                    $current_user = $user;
                    $contact_id = $row['id'];
                    $account_id= $row['account_id'];
                    $extraNameValue['contact_id'] =self::$helperObject->get_name_value('contact_id', $row['id']);
                    $extraNameValue['contact_name'] =self::$helperObject->get_name_value('contact_name', $row['first_name'].' '.$row['last_name']);
                    if( array_key_exists('account_id', $row)) {
                        $extraNameValue['account_id'] =self::$helperObject->get_name_value('account_id', $row['account_id']);
                        $extraNameValue['account_name'] =self::$helperObject->get_name_value('account_name', $row['account_name']);
                    }
                }
            }
        }
        if( !$success &&  $system_config->settings['portal_accounts_access'] && in_array('Accounts', $auth_modules)) {
            $module_name = 'Accounts';
            $type = 'portal_account';

            $account = new Account();
            $query = "SELECT a.id, a.name FROM accounts a
                        LEFT JOIN email_addr_bean_rel eb ON eb.bean_id = a.id AND eb.bean_module='Accounts' AND eb.deleted=0
                        LEFT JOIN email_addresses ea ON ea.id = eb.email_address_id and ea.deleted = 0
                        WHERE a.deleted = 0
                        AND a.portal_active = 1
                        AND a.portal_password='".$user_auth['password']."'
                        AND (ea.email_address='".$user_auth['user_name']."')";
            $result = $account->dbManager->query($query);
            if($result ) {
                $row = $account->dbManager->fetchByAssoc($result);
                $user = new User();
                $user = $user->retrieve_by_string_fields(array('user_name'=>'portal_accounts', 'deleted'=>0, 'status'=>'Active', 'portal_only'=>1) );
                if($row && !empty($user) && !empty($user->id) && !$user->is_group) {
                    $success = true;
                    $current_user = $user;
                    $account_id= $row['id'];
                    $extraNameValue['account_id'] =self::$helperObject->get_name_value('account_id', $row['id']);
                    $extraNameValue['account_name'] =self::$helperObject->get_name_value('account_name', $row['name']);
                }
            }

        }

        if( !$success && $system_config->settings['portal_user_access']&& in_array('Users', $auth_modules)) {
            $module_name = 'Users';
            $type = 'portal_user';
            $user = new User();
            $user = $user->retrieve_by_string_fields(array('user_name'=>$user_auth['user_name'],'user_hash'=>$user_auth['password'], 'deleted'=>0, 'status'=>'Active', 'portal_only'=>0) );
            if(!empty($user) && !empty($user->id) && !$user->is_group) {
                $success = true;
                $current_user = $user;
            } else if(function_exists('mcrypt_cbc')) {
                $authController = new AuthenticationController((!empty($sugar_config['authenticationClass'])? $sugar_config['authenticationClass'] : 'SugarAuthenticate'));
                $password = self::$helperObject->decrypt_string($user_auth['password']);
                if($authController->login($user_auth['user_name'], $password) && isset($_SESSION['authenticated_user_id'])) {

                    $success = true;
                } // if
            } // else if
        }
        if($success) {
            session_start();
            //$current_user = $user;
            self::$helperObject->login_success($name_value_list);
            $current_user->loadPreferences();
            $_SESSION['is_valid_session']= true;
            $_SESSION['ip_address'] = query_client_ip();
            $_SESSION['user_id'] = $current_user->id;
            $_SESSION['type'] = $type;
            if( isset($contact_id)) {
                $_SESSION['contact_id'] = $contact_id;
            }
            if( isset($account_id)) {
                $_SESSION['account_id'] = $account_id;
            }
            $_SESSION['account_id'] =
            $_SESSION['avail_modules']= self::$helperObject->get_user_module_list($current_user);
            $_SESSION['unique_key'] = $sugar_config['unique_key'];
            $current_user->call_custom_logic('after_login');
            $GLOBALS['log']->info('End: EintelWebServiceImpl->portal_login - succesful login');
            $nameValueArray = array();
            global $current_language;
            $nameValueArray['user_id'] = self::$helperObject->get_name_value('user_id', $current_user->id);
            $nameValueArray['user_name'] = self::$helperObject->get_name_value('user_name', $current_user->user_name);
            $nameValueArray['user_language'] = self::$helperObject->get_name_value('user_language', $current_language);
            $nameValueArray['portal_logon'] =self::$helperObject->get_name_value('portal_logon', $user_auth['user_name']);
            $nameValueArray = array_merge($nameValueArray, $extraNameValue);
            $_SESSION['user_language'] = $current_language;
            return array('id'=>session_id(), 'module_name'=>$module_name, 'name_value_list'=>$nameValueArray);
        } // if
        LogicHook::initialize();
        $GLOBALS['logic_hook']->call_custom_logic('Users', 'login_failed');
        $error->set_error('invalid_login');
        self::$helperObject->setFaultObject($error);
        $GLOBALS['log']->info('End: EintelWebServiceImpl->portal_login - failed login');
    }

    public function portal_change_password($session,$password) {
        $GLOBALS['log']->info('Begin: EintelWebServiceImpl->portal_change_password');
    	$error = new SoapError();

    	if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '','','', $error)) {
        	$GLOBALS['log']->info('End: SugarWebServiceImpl->portal_change_password');
    		return;
    	}

        if( $_SESSION['type'] == 'portal_contact') {
            $contact = new Contact();
            $contact->retrieve($_SESSION['contact_id']);
            $contact->portal_password = md5($password);
            $contact->save();
        } else if( $_SESSION['type'] == 'portal_account') {
            $account = new Account();
            $account->retrieve($_SESSION['account_id']);
            $account->portal_password = md5($password);
            $account->save();
        } else if( $_SESSION['type'] == 'portal_user') {
            // Copied from User->change_password
            global $current_user;
            $user_hash = strtolower(md5($password));
            $current_user->setPreference('loginexpiration','0');
            //set new password
            $now=date("Y-m-d H:i:s");
            $query = "UPDATE $current_user->table_name SET user_hash='$user_hash', system_generated_password='0', pwd_last_changed='$now' where id='$current_user->id'";
            $current_user->db->query($query, true, "Error setting new password for $current_user->user_name: ");
            $_SESSION['hasExpiredPassword'] = '0';
        }
    }

       
    
    //fields array('crm'=>'erp');
    //accounts
    var $sync_map_accounts = array(
        'erp_id'        => 'id',
        'name'          => 'name',
        'code'          => 'searchKey',
        'description'   => 'description',
        'abn'           => 'taxID',
        'account_type'  => 'category',//$this->accountCategoryOptions($bean->account_type),
        'website'       => 'url',
        'shipping_location_id' => '',
        'billing_locations_id' => '',
    );
    //contacts
     var $sync_map_contacts = array(
        'erp_id'        => 'id',
        'first_name'    => 'firstName',
        'last_name'     => 'lastName',
        'phone_work'    => 'phone',
        'phone_other'   => 'alternativePhone',
        'email1'        => 'email',
        'title'         => 'position',
        'description'   => 'comments',
        'phone_fax'     => 'fax',
        'phone_mobile'  => 'mobile',
        'account_id'    => 'businessPartner',
        'account_name'  => '',
        'primary_location_id'   => '',
    );

     //locations
    var $sync_map_locations = array(
        'erp_id'            => 'id',        
        'name'              => 'name', 
        'address1'          => 'line1',
        'address2'          => 'line2', 
        'city'              => 'city',
        'state'             => 'region',  
        'postalcode'        => 'postCode',
        'country'           => 'country', 
        'isbilling'         => 'invoiceTo',
        'isshipping'        => 'shipTo', 
        'defaultinvoiceto'  => 'defaultInvoiceTo', 
        'defaultshipto'     => 'defaultShipTo',
        'account_id'        => 'businessPartner',
        'account_name'       => '',
    );
    
    public function sync_ob_new_businessparnter($session, $data) {
        $error = new SoapError();
	if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
		$error->set_error('invalid_login');
		$GLOBALS['log']->info('End: SugarWebServiceImpl->sync_ob_new_businessparnter');
		return;
	} 
        
        if(!empty($data['id'])) {
            $entity = $this->getEntityBean($data,'Accounts');
            if(!isset($entity->id) && empty($entity->id)){
                return $this->set_entry($session, 'Accounts', $data);
            }else{
                $error->set_error('no_records');
                $error->description = "Existing ERP ID found in Account: {$entity->name}";
            }
        }else{
             $error->set_error('no_records');   
             $error->description = "Missing Business Partner ID";
        }
        
        self::$helperObject->setFaultObject($error);
        $GLOBALS['log']->info('End: SugarWebServiceImpl->sync_ob_new_businessparnter ERROR Found');		
        return;
        
    }
    
    public function sync_ob_businessparnter($session, $data) {
        $error = new SoapError();
	if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
		$GLOBALS['log']->info('End: SugarWebServiceImpl->sync_ob_businessparnter');
		return;
	} 
        
        if(!empty($data['id'])) {            
            $entity = $this->getEntityBean($data,'Accounts');
            if(isset($entity->id)) {
                return $this->set_entry($session, 'Accounts', $data,$entity->id);             
            }else {
                $bean = BeanFactory::getBean('Accounts');
                //look for matching searchKey when id was not found
                
                $query = "select * from accounts WHERE code='".$bean->db->quote($data['searchKey'])."'";
                $result = $bean->db->query($query, true);

                $row = $bean->db->fetchByAssoc($result, false);
                if(isset($row['id'])) {
                    //existing account found
                    if($row['erp_id'] == null) {
                        //matching account found is not linked to a business partner 
                        //presume they are matches and link
                        
                        return $this->set_entry($session, 'Accounts', $data, $row['id']);      
                        
                        //return "Update Existing Account, currently not linked to this BP";
                        
                    }else {
                        //sync error,           
                        $error->set_error('no_records');   
                        $error->description = "An existing account with this code ({$data['searchKey']}) has been found, However it is linked to a different business partner with the id: {$data['erp_id']}";
                        self::$helperObject->setFaultObject($error);
                        $GLOBALS['log']->info('End: SugarWebServiceImpl->sync_ob_businessparnter ERROR Found');		
                        return;
                    }
                }else{
                    $error->set_error('no_records');   
                    $error->description = "No existing Account with code {$data['searchKey']} or erp_id {$data['id']} found.";
                    self::$helperObject->setFaultObject($error);
                    $GLOBALS['log']->info('End: SugarWebServiceImpl->sync_ob_businessparnter ERROR Found');		
                    return;
                    
                    //return $this->sync_ob_new_businessparnter($session,$data);
                                   
                }
            }
            
        }
        
        return "Missing Business Partner ID";
        
        
    }
      
    public function sync_ob_businessparnter_contact($session, $data){
        $error = new SoapError();
	if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
		$error->set_error('invalid_login');
		$GLOBALS['log']->info('End: SugarWebServiceImpl->sync_ob_businessparnter_contact');
		return;
	} 
        if(!empty($data['id'])) {
            $entity_id = null;
            $entity = $this->getEntityBean($data, 'Contacts');            
            if(isset($entity->id)){
                //update
                $entity_id = $entity->id;
            }else{
                //leave id as null to focus a create
            }            
            return $this->set_entry($session,'Contacts',$data,$entity_id);
            
        }
        $error->set_error('no_records');   
        $error->description = "Missing Contacts ID";
        self::$helperObject->setFaultObject($error);
        $GLOBALS['log']->info('End: SugarWebServiceImpl->sync_ob_businessparnter_contact - Missing Contacts ID');		
        return;
          
        
    }
    
    public function sync_ob_businessparnter_location($session, $data){
        $error = new SoapError();
	if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
		$error->set_error('invalid_login');
		$GLOBALS['log']->info('End: SugarWebServiceImpl->sync_ob_businessparnter_location');
		return;
	} 
        
        if($data['id']){
            $entity = $this->getEntityBean($data,'Locations');
            //create
            $entity_id = null;
            if(isset($entity->id) && !empty($entity->id) ){
               //update 
               $entity_id = $entity->id; 
            }
            return $this->set_entry($session,'Locations',$data,$entity_id);
            
        }
                 
        //??delete
        //$entity->delete();


        
    }
  
    
    function get_entry($session,$module_name ,$data) {
        $error = new SoapError();
	if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
            $error->set_error('invalid_login');
            $GLOBALS['log']->info('End: SugarWebServiceImpl->sync_ob_businessparnter_location');
            return;
	}        
        return parent::get_entry($session, $module_name, $this->getEntityBeanID($data,$module_name));
    }
    
    function set_entry($session,$module_name,$data,$entry_id = null) {
        $error = new SoapError();
        
        if(!$name_value_list = $this->name_value_list($module_name,$data,$error)) {
            return;
        }
        if(isset($entry_id)) {
            $name_value_list['id'] = $entry_id;      
        }
        return parent::set_entry($session, $module_name, $name_value_list);
    }
    
    function name_value_list($entity,$data,$error) {
        $name_value_list = array();
        foreach($this->{sync_map_.strtolower($entity)} as $crm => $erp) {
            if(!in_array($crm, array('account_id','account_name','shipping_location_id','billing_locations_id','primary_location_id'))){
                $name_value_list[$crm] = $data[$erp];
            }
        }
        
        //if account id is in the mapping load the account
        if($this->{sync_map_.strtolower($entity)}['account_id']) {
            //load bp bean
            $bp = $this->getBusinessParnterBean($data);
            if(isset($bp->id)) {
                $name_value_list['account_id'] = $bp->id;
                $name_value_list['account_name'] = $bp->name;
            } else {
                $error->set_error('no_records');
                self::$helperObject->setFaultObject($error);
                $GLOBALS['log']->info('End: SugarWebServiceImpl->name_value_list - No Account Found');		
                return;
            }
        }
        if(isset($data['location'])) {             
            $name_value_list = array_merge($name_value_list, (array)$this->loadLocationsData($entity,$name_value_list,$data));
        }
  
        return $name_value_list;       
    }    
    
 
    function getEntityBeanID($data,$entity){
         if(!empty($data['id'])) {
            $bean = BeanFactory::getBean($entity);
            $query = "select * from {$bean->table_name} WHERE erp_id='".$bean->db->quote($data['id'])."'";
            $result = $bean->db->query($query, true);
            $row = $bean->db->fetchByAssoc($result, false);
            if(isset($row['id'])){
                return $row['id'];               
            }
        }
        return;
    }
    
    function getEntityBean($data,$entity) {
         $bean = BeanFactory::getBean($entity);
         if(!empty($data['id'])) {
            $query = "select * from {$bean->table_name} WHERE erp_id='".$bean->db->quote($data['id'])."'";
            $result = $bean->db->query($query, true);
            $row = $bean->db->fetchByAssoc($result, false);
            if(isset($row['id'])){
                return BeanFactory::getBean($entity,$row['id']);
            }
        }
        return $bean;
    }
    
    
    function getBusinessParnterBean($data) {
        if($data['businessPartner']){
            $focus = BeanFactory::getBean('Accounts');
            $query = "select id from {$focus->table_name} WHERE erp_id='".$focus->db->quote($data['businessPartner']['id'])."'";
            $result = $focus->db->query($query, true);
            $row = $focus->db->fetchByAssoc($result, false);
            if($row['id']) {
                return BeanFactory::getBean('Accounts',$row['id']);
            }
        }
        return;
    }
    
    function getLocationBean($id) {
        if($id) {
            $focus = BeanFactory::getBean('Locations');
            $query = "select id from {$focus->table_name} WHERE erp_id='".$focus->db->quote($id)."'";
            $result = $focus->db->query($query, true);
            $row = $focus->db->fetchByAssoc($result, false);
            if($row['id']) {
                return BeanFactory::getBean('Locations',$row['id']);
            }
        }
        return;
    }
        
    function loadLocationsData($entity,$name_value_list,$data) {
        switch ($entity) {
            case 'Accounts':
                //if we only have 1 locations
                //change the array layout to be treated as if there where multiple locations
                if(isset($data['location']['id'])) {
                    $data['location'][] = $data['location'];
                }
                //must be presumed that the location in the database has already been updated via the location webservice
                foreach($data['location'] as $location) {
                    if($location['defaultInvoiceTo'] == true) {
                         $billing = $this->getLocationBean($location['id']);
                         if(!empty($billing->id)) {
                            $name_value_list['billing_location_id'] = $billing->id;
                            $name_value_list['billing_address_street']  = $billing->address1;
                            $name_value_list['billing_address_city'] = $billing->city;
                            $name_value_list['billing_address_state'] = $billing->state;
                            $name_value_list['billing_address_postalcode'] = $billing->postalcode;
                            $name_value_list['billing_address_country'] = $billing->country;
                         }
                    }
                    if($location['defaultShipTo'] == true) {
                         $shipping = $this->getLocationBean($location['id']);
                         if(!empty($shipping->id)) {
                            $name_value_list['shipping_location_id'] = $shipping->id;
                            $name_value_list['shipping_address_street']  = $shipping->address1;
                            $name_value_list['shipping_address_city'] = $shipping->city;
                            $name_value_list['shipping_address_state'] = $shipping->state;
                            $name_value_list['shipping_address_postalcode'] = $shipping->postalcode;
                            $name_value_list['shipping_address_country'] = $shipping->country;
                         }                       
                    }
                }
                break;
            case 'Contacts':
                //dont create any locations
                //just add return id and values of existing location
                $primary_location = $this->getLocationBean($data['location']['id']);           
                if($primary_location->id) {                     
                    $name_value_list['primary_location_id'] = $primary_location->id;
                    $name_value_list['primary_address_street']  = $primary_location->address1;
                    $name_value_list['primary_address_city'] = $primary_location->city;
                    $name_value_list['primary_address_state'] = $primary_location->state;
                    $name_value_list['primary_address_postalcode'] = $primary_location->postalcode;
                    $name_value_list['primary_address_country'] = $primary_location->country;
                }
                break;
            case 'Locations':       
                break;
            default:
                break;
        }            
         return $name_value_list;           
        
    } 
   
}

