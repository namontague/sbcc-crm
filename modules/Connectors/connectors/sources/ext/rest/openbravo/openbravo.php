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

 
require_once('include/connectors/sources/ext/rest/rest.php');
require_once('include/externalAPI/ExternalAPIFactory.php');

class ext_rest_openbravo extends ext_rest {
        protected $_enable_in_wizard = false;
        protected $_enable_in_hover = true;
        protected $_has_testing_enabled = false;
        var $_use_custom_source_property_tpl = true;
        var $_path_custom_source_property_tpl = 'modules/Connectors/connectors/sources/ext/rest/openbravo/tpls/source_properties.tpl';

        var $db = null; //database instance
        var $api = null;
        var $urls = array(
          'login' => 'secureApp/LoginHandler.html',
          'summary' => 'ws/au.com.eintel.openbravo.service.web.summary',
          'client' => 'ws/au.com.eintel.openbravo.service.web.core.businesspartner/',
          'contact' =>  'ws/au.com.eintel.openbravo.service.web.core.businesspartner/contact/', 
           'order' => 'ws/au.com.eintel.openbravo.service.web.core.order/sales',
          'product' => 'ws/au.com.eintel.openbravo.service.web.core.product/',
          'order_table' => '?tabId=186&recordId=',
          'accounts_table' => '?tabId=220&recordId=',
          'contacts_table' => '?tabId=496&recordId=',
        );
        var $sync_map_account = array(
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
         var $sync_map_contact = array(
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
            'account_id'    => true,
            'account_name'  => '',
            'primary_location_id'   => '',
        );

        //locations
       var $sync_map_location = array(
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
           'account_id'        => true,
           'account_name'       => '',
       );

        //sugarcrm and openbravo address field mapping 
        //crm:openbravo
       public $address_array_map = array(
            //'name' => 'name', 
            'address1' => 'line1',
            'address2' => 'line2', 
            'city' => 'city',
            'state' => 'region',  
            'postalcode' => 'postCode',
            'country' => 'country', 
            'isbilling' => 'invoiceTo',
            'isshipping' => 'shipTo', 
           // 'erp_id' => 'id',
            //'defaultinvoiceto' => 'defaultInvoiceTo', 
            //'defaultshipto' => 'defaultShipTo'
        );
       public $address_equality_map = array(
            //'name' => 'name',
            'address1' => 'line1',
            'address2' => 'line2',
            'city' => 'city',
            'state' => 'region',
            'postalcode' => 'postCode',
            'country' => 'country',
        );
       public $address_flags_map= array(
            'isbilling' => 'invoiceTo',
            'isshipping' => 'shipTo',
            'defaultinvoiceto' => 'defaultInvoiceTo',
            'defaultshipto' => 'defaultShipTo'
        );


        public function __construct(){
            global $app_list_strings;
            //complete list of allowed modules
            $this->allowedModuleList = array(
                'Accounts' => $app_list_strings['moduleList']['Accounts'],
                'Contacts' => $app_list_strings['moduleList']['Contacts'],
                'Calls' => $app_list_strings['moduleList']['Calls']
            );

            //modules that can be synced
            $this->allowedSyncModules = array(
                'Accounts' => $app_list_strings['moduleList']['Accounts'],
                'Contacts' => $app_list_strings['moduleList']['Contacts'],
            );
            //modules to display the panel
            $this->allowedPanelModules = array(
                'Accounts' => $app_list_strings['moduleList']['Accounts'],
                'Contacts' => $app_list_strings['moduleList']['Contacts'],
                'Calls' => $app_list_strings['moduleList']['Calls'],
            );
            parent::__construct();
            
            $this->db = DBManagerFactory::getInstance();
        }
        
        public function getAPI(){
            $this->api = ExternalAPIFactory::loadAPI('Openbravo');
        }
                
        public function setProperties($properties=array())  {
            if(!empty($this->_config) && isset($this->_config['properties'])) {

                        foreach($properties as $property => &$value){
                            if($value === 'true'){
                                $properties[$property] = true;
                            }elseif($value === 'false'){
                                $properties[$property] = false;
                            }
                        }
                       $this->_config['properties'] = $properties;
               $this->config_decrypted = true; 
            }
        }

        public function saveConfig() {
           $config_str = "<?php\n/***CONNECTOR SOURCE***/\n";
            // Handle encryption
            if(!empty($this->_config['encrypt_properties']) && is_array($this->_config['encrypt_properties']) && !empty($this->_config['properties'])){
                require_once('include/utils/encryption_utils.php');
                foreach($this->_config['encrypt_properties'] as $name) {
                    if(!empty($this->_config['properties'][$name])) {
                        $this->_config['properties'][$name] = blowfishEncode(blowfishGetKey('encrypt_field'),$this->_config['properties'][$name]);
                    }
                }
            }
//            //should always be false and used as a flag for when to sync.
//            $mass_create_setting = $this->_config['properties']['sync_create_records'];
//            if($this->_config['properties']['sync_create_records'] == true){
//                $this->_config['properties']['sync_create_records'] = false;
//            }
//            
//            //should always be false and used as a flag for when to sync.
//            $mass_sync_setting = $this->_config['properties']['sync_setup_records'];
//            if($this->_config['properties']['sync_setup_records'] == true){
//                $this->_config['properties']['sync_setup_records'] = false;
//            }
                
            foreach($this->_config as $key => $val) {
                if(!empty($val)) {
                    $config_str .= override_value_to_string_recursive2('config', $key, $val, true);
                }                
            }
            
            //add mass sync setting back in to be check if true in the save stage
            $this->_config['properties']['sync_create_records'] = $mass_create_setting;
            
            //add mass sync setting back in to be check if true in the save stage
            $this->_config['properties']['sync_setup_records'] = $mass_sync_setting;
            
            
            $dir = str_replace('_', '/', get_class($this));

            if(!file_exists("custom/modules/Connectors/connectors/sources/{$dir}")) {
               mkdir_recursive("custom/modules/Connectors/connectors/sources/{$dir}");
            }
            file_put_contents("custom/modules/Connectors/connectors/sources/{$dir}/config.php", $config_str);



            //call mapping save to add/remove hooks
            //needed so config changes are picked up in reference to sycning
            $this->saveMappingHook($this->getMapping());
           
 	}

        public function getPropertiesFieldTypes() {
            if(!empty($this->_config) && isset($this->_config['properties_field_defs'])) {
               return $this->_config['properties_field_defs'];
            }
            return array();
        }

        public function _process_custom_template($smarty){
            $smarty->assign('properties_field_defs', $this->getPropertiesFieldTypes());
        }

        public function get_connector_urls(){
            return $this->urls;
        }

        public function filterAllowedModules( $moduleList ) {
            // Openbravo currently has no ability to talk to modules other than these 2
            $outModuleList = array();
            foreach ( $moduleList as $module ) {
                if ( !in_array($module,$this->allowedModuleList) ) {
                    continue;
                } else {
                    $outModuleList[$module] = $module;
                }
            }
            return $outModuleList;
        }

        public function saveMappingHook($mapping) {
            $removeList = array();
                        
            $disabled_module_list = array_diff_key($this->allowedModuleList, $mapping['beans']);

            if(!$this->getProperty('sync_records')){
                $removeList = $this->allowedSyncModules;
            }
            
            $removeList = array_merge($removeList, $disabled_module_list);

            if ( is_array($mapping['beans']) ) {

                foreach($mapping['beans'] as $module => $ignore) {
                    
                    if($this->allowedPanelModules[$module]){
                        check_logic_hook_file($module, 'after_ui_frame', array(1, strtolower($module). ' Openbravo Summary frame', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', 'showSummaryFrame') );
                    }

                    
                    if($this->allowedSyncModules[$module] && $this->getProperty('sync_records')){
                        check_logic_hook_file($module, 'before_save', array(1, strtolower($module). '_before_save', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', strtolower($module).'_before_save') );
                        check_logic_hook_file($module, 'before_delete', array(1, strtolower($module). '_before_delete', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', strtolower($module).'_before_delete') );
                    }

                    //account module only
                    if($module == 'Accounts' && $this->getProperty('sync_records')){
                        check_logic_hook_file($module, 'after_relationship_add', array(1, strtolower($module). '_after_relationship_add', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', strtolower($module).'_on_relationship_add') );
                        check_logic_hook_file($module, 'after_relationship_delete', array(1, strtolower($module). '_after_relationship_delete', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', strtolower($module).'_on_relationship_delete') );
                    }
                }
                /*
                 * If the locations module is enabled after the sync is turned on
                 * then the connector settings need to be resaved inorder
                 * for the location logic hooks to be created. 
                 */
                if(module_enabled('Locations')){
                    if($this->getProperty('sync_records')){
                        check_logic_hook_file('Locations', 'before_save', array(1, 'locations_before_save', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', 'locations_before_save') );
                        check_logic_hook_file('Locations', 'after_relationship_add', array(1, 'locations_after_relationship_add', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', 'locations_on_relationship_add') );
                        check_logic_hook_file('Locations', 'after_relationship_delete', array(1, 'locations_after_relationship_delete', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', 'locations_on_relationship_delete') );
                        check_logic_hook_file('Locations', 'before_delete', array(1, 'locations_before_delete', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', 'locations_before_delete') );
                    }
                }
            }




            foreach ( $removeList as $module ) {
                if($this->allowedPanelModules[$module] && $disabled_module_list[$module] ) {
                    remove_logic_hook($module, 'after_ui_frame', array(1, strtolower($module). ' Openbravo Summary frame', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', 'showSummaryFrame') );
                }

                if($this->allowedSyncModules[$module]) {
                    remove_logic_hook($module, 'before_save', array(1, strtolower($module). '_before_save', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', strtolower($module).'_before_save') );
                    remove_logic_hook($module, 'before_delete', array(1, strtolower($module). '_before_delete', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', strtolower($module).'_before_delete') );
                }

                if($module == 'Accounts' ) {
                    remove_logic_hook($module, 'after_relationship_add', array(1, strtolower($module). '_after_relationship_add', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', strtolower($module).'_on_relationship_add') );
                    remove_logic_hook($module, 'after_relationship_delete', array(1, strtolower($module). '_after_relationship_delete', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', strtolower($module).'_on_relationship_delete') );
                }

            }

            if(!$this->getProperty('sync_records')) {
                remove_logic_hook('Locations', 'before_save', array(1, 'locations_before_save', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', 'locations_before_save') );
                remove_logic_hook('Locations', 'after_relationship_add', array(1, 'locations_after_relationship_add', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', 'locations_on_relationship_add') );
                remove_logic_hook('Locations', 'after_relationship_delete', array(1, 'locations_after_relationship_delete', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', 'locations_on_relationship_delete') );
                remove_logic_hook('Locations', 'before_delete', array(1, 'locations_before_delete', 'modules/Connectors/connectors/sources/ext/rest/openbravo/OpenbravoLogicHook.php', 'OpenbravoLogicHook', 'locations_before_delete') );
            }
            
            return parent::saveMappingHook($mapping);
        }

        //implement for Import Wizard support
        public function getItem($args=array(), $module=null){}

        public function getList($args=array(), $module=null){}

        public function ext_user_auth($data) {
            //treat current user as sync user, 
            //pass true to ignore extrenal user login information
            $api = ExternalAPIFactory::loadAPI('Openbravo',true);
            $api->loadEAPM('',true);
            if($api != null) {
                return $api->checkLogin();
            }
            return array('success' => false);
            
            
        }
              
        public function ext_process_sales_order($data) {
            //need to create account when the account is not synced
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            if($api != null && $api->checkLogin()){
                //check for vaild buisness partner
                $business_partner = $api->retrieve_business_partner(BeanFactory::getBean('Accounts', $data['record']));
                if(isset($business_partner['id'])){
                    return $api->createSalesOrderHeaderLink($data['record'],'Accounts');
                }else{
                    //try to retrieve a matching business parter by matching account code
                    $business_partner = $api->retrieve_business_partner(BeanFactory::getBean('Accounts', $data['record']),true);
                    if(isset($business_partner['id'])){
                        return array('data' => $business_partner, 'errorMessage' => 'Business Parnter Not Synced', 'link' => true, 'record_id' => $data['record']);
                    }
                    return array('errorMessage' => 'Business Parnter Not Found','create'=>true,'record_id' => $data['record']);
                }
            }
            return array('errorMessage' => 'No Connection to Openbravo, Check users external account login details.');

        }

        public function ext_retrieve_business_partner($data){
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            if($api != null && $api->checkLogin()){
                return $api->retrieve_business_partner(BeanFactory::getBean($data['record_module'], $data['record']),true);
            }
            return false;

        }

        public function ext_retrieve_business_partner_contact($data){
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            if($api != null && $api->checkLogin()){
                $contact = $api->retrieve_contacts_business_partner(BeanFactory::getBean($data['record_module'], $data['record']));
                if($contact !== false){
                    //change location array name
                    $contact['locations'][0] = $contact['location'];
                    return $contact;
                }
            }
        }
        
        public function ext_retrieve_business_partner_contacts($data){
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            if($api != null && $api->checkLogin()){
                $contacts = $api->retrieve_business_partner_contacts(BeanFactory::getBean($data['record_module'], $data['record']),true);
                if($contacts === false){
                    return false;
                }
                if(!empty($contacts)){
                    $return_formatted_data = array();
                    foreach($contacts as $contact){
                        //make sure there is not a current contact linked to the contact being returned.
                        //can not have multiple contacts linking to the one contact in openbravo
                        if(!$this->hasExistingSync($contact,'contacts')){
                            $return_formatted_data[] = 
                                array(
                                   'id' => $contact['id'],
                                   'data' => $contact['firstName'].' '.$contact['lastName'].' '.$contact['phone'].' '.$contact['email']
                                );
                        }
                        
                    }
                    return $return_formatted_data;
                }
                //return true to allow for a success call for when 0 contacts are returned
                return true;
            }
            return false;
        }
                            
        public function hasExistingSync($record,$table){
            $db = DBManagerFactory::getInstance();
            $query = "SELECT {$table}.*
                      FROM {$table}
                      WHERE {$table}.erp_id = '{$record['id']}'                      
                      AND {$table}.deleted is false
                      LIMIT 1";
            $result = $db->query($query,true," Error finding existing sync record in {$table}");
            //find if there was a record
            $row = $db->fetchByAssoc($result);
            if($row['id'] != null) {
                return true;
            }
            return false;
        }
       
        public function ext_link_record_to_record($data){
            $record = BeanFactory::getBean($data['record_module'], $data['record']);
            if(isset($data['related_record_id'])){
                $record->erp_id = $data['related_record_id'];
                $record->in_sync=true;
                if(!empty($data['additional_fields'])) {
                    foreach($data['additional_fields'] as $f => $v) {
                        $record->$f = $v;
                    }
                }
                $record->save();
                return $record;
            }
            return false;
        }

        public function ext_create_record($data){
            global $beanList; 
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            if($api != null && $api->checkLogin()){
                $record = BeanFactory::getBean($data['record_module'], $data['record']);
                $method = 'create_'.strtolower($beanList[$data['record_module']]);
                $related_record = $api->$method($record);

                if($related_record['success']){
                    if(isset($related_record['data']['id'])){
                         $record->erp_id = $related_record['data']['id'];
                         $record->in_sync=true;
                         $record->save();
                         return $related_record;
                    }
                }else{
                    if(isset($related_record['errorMessage'])){
                        return $related_record;
                    }
                }
            }
                            

            return array('errorMessage' => 'Can not log in');

        }
        
        public function ext_update_record($data){
            global $beanList; 
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            if($api != null && $api->checkLogin()){
                $record = BeanFactory::getBean($data['record_module'], $data['record']);
                if(empty($record->id)){
                    return array('errorMessage' => 'Error retrieving current record');
                }
                if(empty($record->erp_id)){
                    //can not update as no external id found,
                    //attempt to create?
                    $method = 'create_'.strtolower($beanList[$data['record_module']]);
                }else{
                    $method = 'update_'.strtolower($beanList[$data['record_module']]);
                }
                $related_record = $api->$method($record);
                
                if($related_record['success']){
                    if(isset($related_record['data']['id'])){
                        if(empty($record->erp_id)){
                            //we have created a record
                            $record->erp_id = $related_record['data']['id'];
                        }
                        $record->in_sync=true;
                        $record->save();
                        return $related_record;
                    }
                }else{
                    if(isset($related_record['errorMessage'])){
                        return $related_record;
                    }
                }
            }
                            

            return array('errorMessage' => 'Can not log in');
        }
        
        
        
        /*
         * This function is to control the sync from the accounts module
         * ext_sync will be called when the link button is clicked from the accounts screen
         * 
         * Result:
         * -Will link the account
         * -Will look for making contacts
         * --Matched on Email address or First & Last names
         * -Will look for making locations 
         * --All fields need to make
         *  
         */
        
        
        public function ext_retrieve_business_partner_location($data){           
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            if($api != null && $api->checkLogin()){
                $locations = $api->retrieve_business_partner_location(BeanFactory::getBean($data['record_module'], $data['record']));
                if($locations === false){
                    return false;
                }
                 if(!empty($locations)){
                    $return_locations = array();
                    foreach($locations as $location){
                        //make sure there is not a current contact linked to the contact being returned.
                        //can not have multiple contacts linking to the one contact in openbravo
                        if(!$this->hasExistingSync($location,'locations')){
                            $return_locations[$location['id']] = $location;

                        }
                    }
                    return $return_locations;
                }
                //return true to allow for a success call for when 0 contacts are returned
                return true;
            }
            return false;
         }
       
        public function ext_sync($data){
            //if we are syncing a contact check the returned object type 
            //ifs its not account return the object
            //else continue
            $object = $this->ext_link_record_to_record($data);
            if(!is_a($object, 'Account')){
                return $object;
            }else{
                $account = $object;
            }
            
            //Link account to Business partner
            if($account !== false){
                
                //now attempt to match all the busniess partner locations
//                $erp_locations = $this->ext_retrieve_business_partner_location($data);
                               
//                $crm_locations->get_full_list("name",  "account_id='{$account->id}'",true);
                
                //link existing ones and create new ones in crm, dont create in openbravo
//                foreach($crm_locations as $crm_location) {
                        $matched = false;
                        //look to match the person
                        //match on emailaddress
//                        foreach($erp_locations as &$erp_location) {
                            //if 
                            //address 1 = address 1
                            //city = city
                            //state = state
                            //postalcode = postalcode
                            //country = country
                            //billing = billing
                            //shiiping = shipping
                            
                            
                            
                            
//                        }
//                            if((!empty($crm_contact->email1) && !empty($erp_contact['email'])) && $crm_contact->email1 == $erp_contact['email']) {
//                                //match found
//                                $sugar_contacts_used[$crm_contact->id] = $crm_contact->id;
//                                if(isset($erp_contact['match'])){
//                                    //if match is already set, then we have already found 1 match
//                                    //add the duplicate flag to be dealt with later
//                                    $erp_contact['duplicate_found'] = true;
//                                    continue;
//                                }   
//                                                                
//                                $erp_contact['match'] = $crm_contact->id;
//                                
//                                $erp_contact['matched'] = true;
//                                $matched = true;
//                            
//                            }else{
//                              //nothing, contact does not match current
//                            }
//                        }
                
                
                
                //now attempt to like contacts
                //only thoses contacts who have this account as their primary account
                //and contacts that dont have an existing link
                
                $crm_contacts = $this->getContactsFor($account->id);
                $erp_contacts = $this->retrieve_contacts_from_openbravo($data);
                $this->ext_sync_contacts($crm_contacts, $erp_contacts,false, $account);
           
                
                $sugar_contacts_used = array();
                
                if(is_array($crm_contacts)) {
                    //we have contacts
                    foreach($crm_contacts as $crm_contact) {
                        $matched = false;
                        //look to match the person
                        //match on emailaddress
                        foreach($erp_contacts as &$erp_contact) {
                            if((!empty($crm_contact->email1) && !empty($erp_contact['email'])) && $crm_contact->email1 == $erp_contact['email']) {
                                //match found
                                $sugar_contacts_used[$crm_contact->id] = $crm_contact->id;
                                if(isset($erp_contact['match'])){
                                    //if match is already set, then we have already found 1 match
                                    //add the duplicate flag to be dealt with later
                                    $erp_contact['duplicate_found'] = true;
                                    continue;
                                }   
                                                                
                                $erp_contact['match'] = $crm_contact->id;
                                
                                $erp_contact['matched'] = true;
                                $matched = true;
                            }elseif(
                                    (!empty($crm_contact->first_name) && !empty($erp_contact['firstName']) 
                                        && !empty($crm_contact->last_name) && !empty($erp_contact['lastName'])
                                    ) &&
                                    ($crm_contact->first_name == $erp_contact['firstName'] 
                                        && $crm_contact->last_name == $erp_contact['lastName'])
                                    ) {
                                
                                $sugar_contacts_used[$crm_contact->id] = $crm_contact->id;
                                if(isset($erp_contact['match'])){
                                     //if match is already set, then we have already found 1 match
                                    //add the duplicate flag to be dealt with later
                                    $erp_contact['duplicate_found'] = true;
                                    continue;
                                }   
                                
                                $erp_contact['match'] = $crm_contact->id;
                                $erp_contact['matched'] = true;
                                $matched = true;
                            }else{
                              //nothing, contact does not match current
                            }
                        }
                    }

                    foreach($crm_contacts as $crm_con) {
                        if(!in_array($crm_con->id, $sugar_contacts_used)){
                            //this contact was not found to match
                            //create in openbravo
                            $this->ext_create_record(array(
                                        'record_module' => 'Contacts', 
                                        'record' => $crm_con->id
                                    ));
                                                        
                        }                       
                    }
                    
                    
                    foreach($erp_contacts as $erp_con) {
                        if($erp_con['duplicate_found']){
                            //do nothing, 
                            //this contact was found to have more than one potental match, let user work it out.
                            continue;
                        }elseif(!empty($erp_con['match'])){
                            $this->ext_link_record_to_record(
                                    array(
                                        'record_module' => 'Contacts', 
                                        'record' => $erp_con['match'], 
                                        'related_record_id' => $erp_con['id']
                                    ));
                        }else{
                            //create
                            $new_contact = new Contact();
                            $new_contact->erp_id = $erp_con['id'];
                            $new_contact->first_name = $erp_con['firstName'];
                            $new_contact->last_name = $erp_con['lastName'];
                            $new_contact->email1 = $erp_con['email'];
                            $new_contact->title = $erp_con['position'];  
                            $new_contact->phone_work = $erp_con['phone'];
                            $new_contact->phone_other =  $erp_con['alternativePhone'];
                            $new_contact->phone_fax = $erp_con['fax'];
                            $new_contact->phone_mobile = $erp_con['mobile'];
                            $new_contact->description = $erp_con['comments'];
                            $new_contact->account_id = $account->id;
                            $new_contact->account_name = $account->name;
                            //$contact = array(
                            //    'location' => $location_id,
                            //);
                            $new_contact->in_sync = true;
                            $new_contact->save();
                                   
                        }
                        
                    }                                                                        

                        
                    }
                    
                    
                }             
            
            return $account;
            
        }
        
        public function ext_sync_contacts($crm_contacts, $erp_contacts,$create_new_contacts = false){
            $sugar_contacts_used = array();
                
            if(!is_null($crm_contacts) || is_array($crm_contacts)) {
                //we have contacts
                foreach($crm_contacts as $crm_contact) {
                    $matched = false;
                    //look to match the person
                    //match on emailaddress
                    foreach($erp_contacts as &$erp_contact) {
                        $erp_contact['duplicate_found'] = false;
                        
                        if(isset($crm_contact->erp_id) && $crm_contact->erp_id == $erp_contact['id']){
                            $sugar_contacts_used[$crm_contact->id] = $crm_contact->id;
                            //existing match, i.e skip
                            //add to existing list
                            if(isset($erp_contact['match'])){
                                //if match is already set, then we have already found 1 match
                                //add the duplicate flag to be dealt with later
                                $erp_contact['duplicate_found'] = true;
                                continue;
                            }   
                            $erp_contact['existing_sync'] = true;
                            $erp_contact['match'] = $crm_contact->id;
                            $erp_contact['matched'] = true;
                            $matched = true;                      
                            continue;
                            //exact match
                        }elseif((!empty($crm_contact->email1) && !empty($erp_contact['email'])) && strcasecmp ($crm_contact->email1,$erp_contact['email'])==0) {
                            //match found
                            $sugar_contacts_used[$crm_contact->id] = $crm_contact->id;
                            if(isset($erp_contact['match'])){
                                //if match is already set, then we have already found 1 match
                                //add the duplicate flag to be dealt with later
                                $erp_contact['duplicate_found'] = true;
                                continue;
                            }   

                            $erp_contact['match'] = $crm_contact->id;

                            $erp_contact['matched'] = true;
                            $matched = true;
                        }elseif(
                                (!empty($crm_contact->first_name) && !empty($erp_contact['firstName']) 
                                    && !empty($crm_contact->last_name) && !empty($erp_contact['lastName'])
                                ) &&
                                (strcasecmp ($crm_contact->first_name,$erp_contact['firstName']) == 0
                                    && strcasecmp($crm_contact->last_name, $erp_contact['lastName'])==0)
                                ) {

                            $sugar_contacts_used[$crm_contact->id] = $crm_contact->id;
                            if(isset($erp_contact['match'])){
                                 //if match is already set, then we have already found 1 match
                                //add the duplicate flag to be dealt with later
                                $erp_contact['duplicate_found'] = true;
                                continue;
                            }   

                            $erp_contact['match'] = $crm_contact->id;
                            $erp_contact['matched'] = true;
                            $matched = true;
                        }else{
                          //nothing, contact does not match current
                        }
                    }
                }
                
                //if($this->create_missing_openbravo) {
                if($create_new_contacts){
                    foreach($crm_contacts as $crm_con) {
                        if(!in_array($crm_con->id, $sugar_contacts_used)){
                            //this contact was not found to match
                            //create in openbravo
                            $this->ext_create_record(array(
                                        'record_module' => 'Contacts', 
                                        'record' => $crm_con->id
                                    ));

                        }                       
                    }
                }
            }

            foreach($erp_contacts as $erp_con) {
                $erp_con['locations'][0] = isset($erp_con['location'])? $erp_con['location']: null;

                if((isset($erp_con['duplicate_found']) && $erp_con['duplicate_found'])) {
                    //do nothing, 
                    //this contact was found to have more than one potental match, let user work it out.
                    //or this contact has already been synced
                    continue;
                }elseif(!empty($erp_con['match'])){
                    $this->ext_link_record_to_record(
                            array(
                                'record_module' => 'Contacts', 
                                'record' => $erp_con['match'], 
                                'related_record_id' => $erp_con['id']
                            ));
                }else{
                    if (isset($erp_con['existing_sync']) && $erp_con['existing_sync']){
                        //update
                        $this->set_entry_sugarcrm(BeanFactory::getBean('Contacts',$erp_con['match']),$erp_con);
                    }else{
                        //create
                        if($this->sync_missing_sugarcrm){
                            $this->set_entry_sugarcrm(BeanFactory::getBean('Contacts'),$erp_con);
                        }
                    }
                }

            }   
            
                
        }
        
        /*
         * Return array of contacts as objects
         */
        public function getContactsFor($primary_account_id){
            $db = DBManagerFactory::getInstance();
            $query = "select ac.contact_id as id
                from accounts_contacts ac
                inner join contacts c on (c.id = ac.contact_id and c.deleted = 0 )
                where ac.deleted=0 
                and ac.account_id='{$primary_account_id}'
                and ac.primary_account = 1";
            $result = $db->query($query);
            
            $contacts = array();
            while(($row = $db->fetchByAssoc($result)) != null) {
              $contact = new Contact();
              if($contact->retrieve($row['id'])) {
                $contacts[$row['id']] = $contact;
              }
            }
            return $contacts;                     
           
        }
        
        public function retrieve_contacts_from_openbravo($data) {
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            
            if($api != null && $api->checkLogin()){
                $contacts = $api->retrieve_business_partner_contacts(BeanFactory::getBean($data['record_module'], $data['record']),true);
                if($contacts === false){
                    return false;
                }
                if(!empty($contacts)) {
                    $return_contacts = array();
                    foreach($contacts as $contact) {
                        //make sure there is not a current contact linked to the contact being returned.
                        //can not have multiple contacts linking to the one contact in openbravo
                        if(!$this->hasExistingSync($contact,'contacts')) {
                            $return_contacts[$contact['id']] = $contact;

                        }
                    }
                    return $return_contacts;
                }
            }
            return false;
        }
        
        function sync_create_accounts($data){            
            
            $existing_synced_records = "SELECT erp_id FROM accounts WHERE (erp_id is not null OR erp_id <> '') and deleted is false";
            $records = $this->db->query($existing_synced_records);
            $recordsAsArray = array();
            while($record = $this->db->fetchByAssoc($records) ){
                $recordsAsArray[$record['erp_id']] = $record['erp_id'];
            }
            //loop until no records are returned
            $created = 0;
            $page = 1;
            while(($business_partners = $this->api->retrieve_business_partners($page++,$data['batch_size'],false,false)) != null ) {
                $insert = "INSERT INTO accounts(id,name,code,erp_id,description,abn,account_type,website,date_entered,date_modified,modified_user_id,created_by) VALUES ";    
                if(!empty($business_partners)) {                                                         
                    foreach($business_partners as $account) {
                        if(!in_array($account['id'],$recordsAsArray)){
                            $rows = "(uuid(),'".addslashes($account['name'])."','".addslashes($account['searchKey'])."','".addslashes($account['id'])."','".addslashes($account['description'])."','".addslashes($account['taxID'])."','".addslashes($account['category'])."','".addslashes($account['url'])."', now(),now(),'1','1')";
                            $this->db->query($insert . $rows,false);
                            $created++;
                        }
                    }
                }
                if($page > 1){
                    //used for testing
                    break;
                }
            }
            $result = array('success'=>true);
            if($created > 0) {
                $result['data']['message'] = "Inserted {$created} Accounts";
                //SugarApplication::appendErrorMessage("Inserted {$created} Accounts");
            }else{
                $result['data']['message'] = "No New Accounts";
            }
            return $result;
        }
        
        //Mass update sugarcrm with sync ids
        
        function perpare_sync_process(){
            //check if sync table exists
            if($this->db->getAffectedRowCount($this->db->query("SHOW TABLES LIKE 'sync_erp_values'")) != 1) {
                $this->db->query(
                    "CREATE TABLE `sync_erp_values` (
                        `id` varchar(32) NOT NULL,
                        `code` varchar(32) DEFAULT NULL,
                        `entity` varchar(255) DEFAULT NULL,
                        CONSTRAINT unique_record PRIMARY KEY (id,code)
                      )");
                if($this->db->getAffectedRowCount($this->db->query("SHOW TABLES LIKE 'sync_erp_values'")) != 1) {
                    return false;
                }
            }
            return true;
        }
        
        
        function execute_sync($data){
            if(($business_partners = $this->api->retrieve_business_partners($data['page'],$data['batch_size'])) != null) {
                //this function will return an array of erp_ids and also alter the $business partner array to be indexed by id and not 0-1000
                $bp_ids = $this->populate_sync_table($data,false,$business_partners);
                //check if we are syncing children as well
                if($this->sync_children) {
                    //get linked up accounts for the current 1000 bps
                    if(!empty($bp_ids)) {
                        $query = "SELECT erp_id, id, name
                                 FROM accounts 
                                 WHERE deleted is false
                                 AND erp_id is not null
                                 AND erp_id in (".implode(',', $bp_ids).")";
                        $result = $this->db->query($query);

                        if($this->db->getRowCount($result) > 0) {
                            if(!empty($business_partners)) {
                                while(($row = $this->db->fetchByAssoc($result)) != null) {
                                    unset($this->parent_bean);
                                    $this->parent_bean = BeanFactory::getBean('Accounts',$row['id']);

                                    if(!empty($business_partners[$row['erp_id']]['locations'])) {
                                        //create/link all business locations
                                        $this->ext_sync_locations($business_partners[$row['erp_id']]);
                                    }

                                    $this->set_entry_sugarcrm($this->parent_bean,$business_partners[$row['erp_id']]); 

                                    if(!empty($business_partners[$row['erp_id']]['contacts'])) {
                                        $crm_contacts = $this->getContactsFor($row['id']);
                                        if(!empty($crm_contacts)) {
                                            $this->ext_sync_contacts($crm_contacts,$business_partners[$row['erp_id']]['contacts'],false);
                                        }else if($this->sync_missing_sugarcrm){
                                            $this->ext_sync_contacts(null,$business_partners[$row['erp_id']]['contacts'],false);
                                        }                           
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        
        //this is called from the config options
        function ext_start_mass_sync_process($data){
            if(is_null($this->api)){
                $this->getAPI();
                if($this->api === false){
                    return array('success'=>false,'errorMessage'=>'Problem with API, check External Account settings');
                }
            }
            
            $this->master = $data['master'];
            $this->sync_children = ($data['sync_children']=='undefined')?false:$data['sync_children'];
            $this->sync_missing_sugarcrm = ($data['sync_missing_sugarcrm']=='undefined')?false:$data['sync_missing_sugarcrm'];
            $this->sync_missing_openbravo =  ($data['sync_missing_openbravo']=='undefined')?false:$data['sync_missing_openbravo'];
            if($this->sync_accounts()){
                if($this->sync_missing_sugarcrm){
                    //look for missing accouts
                    $this->sync_create_accounts($data);
                }
                
                
                return array('success'=>true);
            }
            return array('errorMessage'=>'Error');                

           
        }
        
        
        function ext_perpare_sync_process_v2($data){
            if(is_null($this->api)){
                $this->getAPI();
                if($this->api === false){
                    return array('success'=>false,'errorMessage'=>'Problem with API, check External Account settings');
                }
            }
            
            $this->master = $data['master'];
            $this->sync_children = ($data['sync_children']=='undefined')?false:$data['sync_children'];
            $this->sync_missing_sugarcrm = ($data['sync_missing_sugarcrm']=='undefined')?false:$data['sync_missing_sugarcrm'];
            $this->sync_missing_openbravo =  ($data['sync_missing_openbravo']=='undefined')?false:$data['sync_missing_openbravo'];
            
            //create sync table
            if(!$this->perpare_sync_process()){
                //problem with creating sync table 
                //end process
                return array('success'=>false,'errorMessage'=>'Problem with Sync table');
            }
            
            if($this->master == 'openbravo') {
                $message = array();
                if($this->sync_missing_sugarcrm){ 
                    //look for missing accouts
                   $result = $this->sync_create_accounts($data);
                   if($result['success'] == true){
                       if(isset($result['data']['message'])){
                           $message['notify'][] = $result['data']['message'];
                       }
                   }
                }
                $result = $this->ext_business_partner_count($data);
            }elseif($this->master == 'sugarcrm'){
                //update sync table 
                //then match up accounts that do not have an erp id 
                //and has a matching code in the sync table 
                if(is_null($this->populate_sync_table($data,true))){
                    return array('success' => false,'errorMessage'=>'Problem with Sync table');
                }                
                $result = $this->ext_account_count($data);
            }
  
            if($result['success'] == true){
                if(isset($result['data']['message'])){
                       $message['notify'][] = $result['data']['message'];
                }
                if(isset($message['notify'])){
                    $result['notify'] = $message['notify'];
                }
            }
            
            return $result;
            
        }          
        
        function ext_start_mass_sync_process_v2($data){
            if(is_null($this->api)){
                $this->getAPI();
                if($this->api === false){
                    return array('success'=>false,'errorMessage'=>'Problem with API, check External Account settings');
                }
            }
            $this->master = $data['master'];
            $this->sync_children = ($data['sync_children']=='undefined')?false:$data['sync_children'];
            $this->sync_missing_sugarcrm = ($data['sync_missing_sugarcrm']=='undefined')?false:$data['sync_missing_sugarcrm'];
            $this->sync_missing_openbravo =  ($data['sync_missing_openbravo']=='undefined')?false:$data['sync_missing_openbravo'];
            ini_set('max_execution_time', 300);
            if($this->master == 'openbravo'){
                $this->execute_sync($data);
            }elseif($this->master == 'sugarcrm'){
                return $this->execute_sync_ob($data);
            }
            return array('success'=>true);
        }
            
        function populate_sync_table($data,$all_business_partners = false,&$business_partners = null){
            if(is_null($business_partners)){
                if($all_business_partners) {
                    $business_partners = $this->api->retrieve_all_business_partners();
                }else{
                    $business_partners = $this->api->retrieve_business_partners($data['page'],$data['batch_size'],false,false);
                }
            }
            if(!is_null($business_partners)) {
                $bp_ids = array();
                $insert = "INSERT INTO sync_erp_values (id,code,entity) VALUES";    
                if(!empty($business_partners)) {
                    $bps =array();
                    foreach($business_partners as $account) {
                        $bps[$account['id']] = $account;
                        $rows = "('".addslashes($account['id'])."', '".addslashes($account['searchKey'])."', 'business_partner') ON DUPLICATE KEY UPDATE id='".addslashes($account['id'])."', code='".addslashes($account['searchKey'])."', entity='business_partner'";
                        $this->db->query($insert . $rows,false);
                        $bp_ids[] = "'{$account['id']}'";
                    }
                    if(!empty($bp_ids)) {
                        //link up accounts as rows were inserted
                        $update ="UPDATE accounts a
                                    LEFT JOIN sync_erp_values sync ON (BINARY a.code = BINARY sync.code AND sync.entity = 'business_partner')
                                    SET 
                                    a.erp_id = sync.id 
                                    WHERE a.deleted IS FALSE
                                    AND a.erp_id IS NULL;";
                        $update_result = $this->db->query($update);
                        SugarApplication::appendErrorMessage("Updated ".$this->db->getAffectedRowCount($update_result)." rows");
                    }
                    if(!empty($bps)){
                        $business_partners = $bps;
                    }
                }
                return $bp_ids;
            }
            return null;
            
        }
        
        function execute_sync_ob($data){
            $start_location = ($data['page'] - 1) * $data['batch_size'];
            $query = "SELECT id FROM accounts where deleted is false ORDER by date_entered LIMIT {$start_location},{$data['batch_size']}";
            $res = $this->db->query($query);
            if($this->db->getRowCount($res) > 0) {            
                while(($row = $this->db->fetchByAssoc($res)) != null){    
                    //sync each account
                    $bean = BeanFactory::getBean("Accounts",$row['id']);
                    $result = $this->set_entry_openbravo($bean);
                    if($result['success']) {
                        //set children data if required
                        if($this->sync_children){
                            $result = $this->handle_children_data($bean);
                        }
                    }
                    if(!$result['success']){
                        return $result;
                    }
                }
                return $result;
            }         
        }
            
        
        
        function ext_business_partner_count($data){
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            $count = $api->retrieve_webservice_count('client');
            if($count!=false){
                $div = 1000;
                if(isset($data['batch_size']) && $data['batch_size'] > 0){
                    $div = $data['batch_size'];
                }
                $loops = ceil($count / $div);
                return array('success'=>true,'data'=>array('count'=>$loops,'message'=>$count));
            }
            return $count;
        }
        
        function ext_account_count($data){
            $db = DBManagerFactory::getInstance();
            $count = $db->getOne('select count(id) from accounts where deleted is false');
            if($count !=false){
                $div = 1000;
                if(isset($data['batch_size']) && $data['batch_size'] > 0){
                    $div = $data['batch_size'];
                }
                $loops = ceil($count / $div);
                return array('success'=>true,'data'=>array('count'=>$loops,'message'=>$count));
            }
            return $count;
        }
        
        //do all the accounts
        function sync_accounts(){
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            $db = DBManagerFactory::getInstance();
            if($this->perpare_sync_process()){
                //get the number of business partners in openbravo
                //based on that number create x amount of loops
                $count = $api->retrieve_webservice_count('client');
                if($count!=false){
                    $loops = ceil($count / 1000);
                }
                //always update the entire sync table.
                //nothing will be deleted and only duplicate matches will be updated, 
                //new ones will be inserted
                $page = 1; 
                while($page <= $loops){
                    ini_set('max_execution_time', 300);
                    $this->execute_sync($page);
                    $page++; 
                    if($page > 100 || $page == false){
                        //used for testing
                        break;
                    }
                }
                
                
                
                
                //finished
                return true;                
            }else{
                //sync prepare failed
                return false;
            }
        }
        
        //do all the locations
        function ext_sync_locations($business_partner_data){
/*            $db = DBManagerFactory::getInstance();
//            //load existing locations
//            $query = "SELECT 
//                            id, name, account_name, account_id,	address1, address2, city, state,
//                            postalcode,	country, isbilling, isshipping,	erp_id,	defaultinvoiceto, defaultshipto
//                        FROM locations 
//                        WHERE deleted is false
//                        AND account_id = '{$this->parent_bean->id}';";
//
//            $result = $db->query($query);
//            $existing = array();
//            if($db->getRowCount($result) > 0) {            
//                while(($existing_location = $db->fetchByAssoc($result)) != null){
//                    foreach($business_partner_data['locations'] as &$location){
//                        $location['duplicate_found'] = false;
//                        
//                        if($existing_location['erp_id'] == $location['id']){
//                            //existing match, i.e skip
//                            //add to existing list
//                            $existing[$location['id']] = $location['id'];                        
//                            break;
//                        }elseif($existing_location['erp_id'] == null){
//                            //needs to be a complete match
//                            if($this->areLocationsEqual($existing_location,$location)){
//                                //link to this location
//                                if(isset($location['match'])){
//                                    //if match is already set, then we have already found 1 match
//                                    //add the duplicate flag to be dealt with later
//                                    $location['duplicate_found'] = true;
//                                    continue;
//                                }
//                                $location['match'] = $existing_location['id'];
//                                $update_flags = array();
//                                foreach($this->address_flags_map as $crm => $erp){
//                                    if(!$existing_location[$crm] && $location[$erp]){
//                                        $update_flags[$crm] =true;
//                                    }
//                                }
//                                if(!empty($update_flags)) {
//                                    $location['additional_fields'] = $update_flags;
//                                }
//                                continue;
//                            }else{
//                                //create
//                            }                            
//                        }else{
//                            //is an existing location with a id
//                            //skip
//                            continue;
//                        }
//                    }
//                    unset($location);
//                }
//                
//                
//                foreach($business_partner_data['locations'] as $location) {
//                    if($location['duplicate_found'] == true) {
//                        //do nothing, this location was found to have more than one potental match.
//                        continue;
//                    }elseif(in_array($location['id'], $existing)) {
//                        continue;                                      
//                    }elseif(isset($location['match'])) {
//                        $this->ext_link_record_to_record(
//                                array(
//                                    'record_module' => 'Locations', 
//                                    'record' => $location['match'], 
//                                    'related_record_id' => $location['id'],
//                                    'additional_fields' => (isset($location['additional_fields']) ? $location['additional_fields'] : null)
//                                ));
//                    }else {
//                        //create new location
//                        $this->set_entry_sugarcrm(BeanFactory::getBean('Locations'),$location); 
//                    }
//                }   
//            }else{
//                 //create all locations when no locations where found for the account
//                 foreach($business_partner_data['locations'] as $location){
//                    $this->set_entry_sugarcrm(BeanFactory::getBean('Locations'),$location); 
//                 }
//            
//            }
 * 
 */
            $related_locations = $this->parent_bean->get_linked_beans('locations', 'Locations');
            
            if(!empty($related_locations)){
                foreach($related_locations as $existing_location) {
                    foreach($business_partner_data['locations'] as &$location){
                        $location['duplicate_found'] = false;
                        if($existing_location->erp_id == $location['id']){
                            //existing match, i.e skip
                            //add to existing list
                            $existing[$location['id']] = $location['id'];                        
                            break;
                        }elseif($existing_location->erp_id == null){
                            //needs to be a complete match
                            if($this->areLocationsEqual($existing_location,$location)){
                                //link to this location
                                if(isset($location['match'])){
                                    //if match is already set, then we have already found 1 match
                                    //add the duplicate flag to be dealt with later
                                    $location['duplicate_found'] = true;
                                    continue;
                                }
                                $location['match'] = $existing_location>id;
                                $update_flags = array();
                                foreach($this->address_flags_map as $crm => $erp){
                                    if(!$existing_location->$crm && $location[$erp]){
                                        $update_flags[$crm] =true;
                                    }
                                }
                                if(!empty($update_flags)) {
                                    $location['additional_fields'] = $update_flags;
                                }
                                continue;
                            }else{
                                //create
                            }                            
                        }else{
                            //is an existing location with a id
                            //skip
                            continue;
                        }
                        unset($location);
                    }
                }
                foreach($business_partner_data['locations'] as $location) {
                    if($location['duplicate_found'] == true) {
                        //do nothing, this location was found to have more than one potental match.
                        continue;
                    }elseif(in_array($location['id'], $existing)) {
                        continue;                                      
                    }elseif(isset($location['match'])) {
                        $this->ext_link_record_to_record(
                                array(
                                    'record_module' => 'Locations', 
                                    'record' => $location['match'], 
                                    'related_record_id' => $location['id'],
                                    'additional_fields' => (isset($location['additional_fields']) ? $location['additional_fields'] : null)
                                ));
                    }else {
                        //create new location
                        $this->set_entry_sugarcrm(BeanFactory::getBean('Locations'),$location); 
                    }
                }   
            }else{
                 //create all locations when no locations where found for the account
                 foreach($business_partner_data['locations'] as $location){
                    $this->set_entry_sugarcrm(BeanFactory::getBean('Locations'),$location); 
                 }
            
            }
            
        }
        
        function areLocationsEqual($existing_location,$location){
            $match = false;
            foreach($this->address_equality_map as $crm => $erp){
                if(strcasecmp(trim($existing_location->$crm),trim($location[$erp])) == 0){
                    $match = true;
                }else{
                    //as soon as the fields dont match
                    //exit and return false
                    return false;
                }
            }
            return $match;
        }
	
            
            

        
        
        /***
         * New Sync Functions
         * 
         * Accounts childern:
         * Contacts &Locations
         * 
         * Contacts childern
         * Locations
         */
        
        
        
        public $master = null;
        public $parent_bean = null;
        public $sync_children = false;
        public $sync_missing_sugarcrm = false;
        public $sync_missing_openbravo = false;
        public $address_fields = array(
                               '_address_street' =>'address1',
                               '_address_city' => 'city',
                               '_address_state' => 'state',
                               '_address_postalcode' => 'postalcode',
                               '_address_country' => 'country'
                           );
        
        //handle the sync process launched via a module UI
        public function ext_sync_data($data){
            $this->master = $data['master'];
            $this->sync_children = ($data['sync_children']=='undefined')?false:$data['sync_children'];
            $this->sync_missing_sugarcrm = ($data['sync_missing_sugarcrm']=='undefined')?false:$data['sync_missing_sugarcrm'];
            $this->sync_missing_openbravo =  ($data['sync_missing_openbravo']=='undefined')?false:$data['sync_missing_openbravo'];
            
            $bean = BeanFactory::getBean($data['record_module'], $data['record']);    
            if(!is_a($bean,'Account')){
                //load primary account bean
                $this->parent_bean = BeanFactory::getBean('Accounts', $bean->account_id);
            }else{
                $this->parent_bean = $bean;
            }
            
            if(($master_data = $this->get_master_data($data))!=null){
                return $this->set_master_data($master_data, $bean);
            }
            
        }
        
        //get the master data from the selected source
        public function get_master_data($data){
            $record = null;
            switch ($this->master){
                case 'openbravo':
                    //$record = $this->ext_retrieve_business_partner_.strtolower($data['record_module'])($data);
                    //return bp or contact
                    if($data['record_module'] == 'Contacts'){
                        $record = $this->ext_retrieve_business_partner_contact($data);
                        break;
                    }
                    $record = $this->ext_retrieve_business_partner($data);
                    //remove children if not needed
                    if(!$this->sync_children){
                        unset($record['contacts']);
                        unset($record['locations']);
                    }
                break;
                case 'sugarcrm':
                    $record = BeanFactory::getBean($data['record_module'], $data['record']);    
                break;
            }
            return $record;        
        }
        
        //load an existing location from an external id
        public function get_location_bean($id) {
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
    
       
        //update destination with source data and handle children
        public function set_master_data($masterData, $bean){
            if(is_a($masterData,'Account') || is_a($masterData,'Contact') ){
                //update openbravo
                if(is_a($masterData,'Account')){
                    $result = $this->set_entry_openbravo($bean);
                    if($result['success']) {
                        //set children data if required
                        if($this->sync_children){
                            $result = $this->handle_children_data($bean);
                        }
                    }
                }
                if(is_a($masterData,'Contact')) {
                    $params = array();
                    //sync location first incease it needs to be created 
                    //as the id is used when the contact is synced
                    if($this->sync_children){
                        if(!empty($bean->primary_location_id)){
                            $result = $this->set_entry_openbravo(BeanFactory::getBean("Locations", $bean->primary_location_id));
                            if($result['success'] && isset($result['data']['id'])){
                                $params['location_id'] = $result['data']['id'];
                            }
                        }
                    }
                    $params['account'] = $this->parent_bean;
                    $result = $this->set_entry_openbravo($bean,$params);
                }
                return $result;
            }else{
                //update sugar ($masterData is an array)
                $result = $this->set_entry_sugarcrm($bean,$masterData);
                if($this->sync_children){
                    if(isset($masterData['contacts'])) {
                        //load current contacts for accounts
                        $related_contacts = $bean->get_linked_beans('contacts', 'Contacts');
                        if($this->sync_missing_sugarcrm || !empty($related_contacts)){
                            foreach($masterData['contacts'] as $contact){
                                //contact web service has a different name for the 
                                //locations(location) array as a contact can only have 1 location
                                $contact['locations'][] = $contact['location'];
                                $contact_bean = BeanFactory::getBean('Contacts');
                                $match = false;
                                foreach($related_contacts as $rel){
                                    if($rel->erp_id === $contact['id']){
                                        $contact_bean = $rel;
                                    }
                                }
                                if(!$this->sync_missing_sugarcrm && !$match){
                                    continue;
                                }
                                //if no contact match is found and sync missing is true
                                //then an empty contact bean is sent and should create a new contact bean
                                $this->set_entry_sugarcrm($contact_bean,$contact);
                            }
                        }
                    }
                }
                return array('success'=>true);
            }
        }
      
        
        //SUGARCRM DATA CHANGE FUNCTIONS (OPENBRAVO MASTER DATA)
        //handle location information for the given bean
        public function handle_location_data($bean,$data,$update) {
            switch (get_class($bean)) {
                case 'Account':
                    //if we only have 1 locations
                    //change the array layout to be treated as if there where multiple locations
                    if(isset($data['locations']['id'])) {
                        $data['locations'][] = $data['locations'];
                    }
                    //must be presumed that the location in the database has already been updated via the location webservice
                    foreach($data['locations'] as $location) {
                        $location_bean = $this->get_location_bean($location['id']);
                        if($location['defaultInvoiceTo'] == true) {
                             if(!empty($location_bean->id)) {
                                $this->set_entry_sugarcrm($location_bean,$location,'_billing'); 
                             }else{
                                 //location does not exist in sugar,check if we are to create it
                                 if($this->sync_missing_sugarcrm){
                                     //new location
                                     $this->set_entry_sugarcrm(BeanFactory::getBean('Locations'),$location,'_billing');
                                     $location_bean = $this->get_location_bean($location['id']);
                                 }
                             }
                             if($bean->billing_location_id !== $location_bean->id){
                                 $update = true;
                                 $bean->billing_location_id = $location_bean->id;
                             }
                             //update bean with address details beacuse when the bean is next saved it will not have the new values that are currently in the database that were updated when the location was saved
                             $this->set_address_entry_sugarcrm($bean,$location_bean,'billing');
                        }else if($location['defaultShipTo'] == true) {
                             //$shipping = $this->get_location_bean($location['id']);
                             if(!empty($location_bean->id)) {
                                $this->set_entry_sugarcrm($location_bean,$location,'_shipping'); 
                                //$bean->shipping_location_id = $shipping->id;
                             }else{
                                 //location does not exist in sugar,check if we are to create it
                                 if($this->sync_missing_sugarcrm){
                                     //new location
                                     $this->set_entry_sugarcrm(BeanFactory::getBean('Locations'),$location,'_shipping');
                                     $location_bean = $this->get_location_bean($location['id']);
                                 }
                             }
                             if($bean->shipping_location_id !== $location_bean->id){
                                 $update = true;
                                 $bean->shipping_location_id = $location_bean->id;
                             }
                             //update bean with address details beacuse when the bean is next saved it will not have the new values that are currently in the database that were updated when the location was saved
                             $this->set_address_entry_sugarcrm($bean,$location_bean,'shipping');
                        }else{
                            if(!empty($location_bean->id)) {
                               $this->set_entry_sugarcrm($location_bean,$location); 
                            }else{
                                //location does not exist in sugar,check if we are to create it
                                if($this->sync_missing_sugarcrm){
                                    //new location
                                    $this->set_entry_sugarcrm(BeanFactory::getBean('Locations'),$location);
                                }
                            }
                        }
                    }
                    break;
                case 'Contact':
                    //as their can only be 1 locations for a contact
                    if(isset($data['locations'][0])){
                        $location = $data['locations'][0];
                        $primary_location = $this->get_location_bean($location['id']);
                        if(!empty($primary_location->id)) {
                           $this->set_entry_sugarcrm($primary_location,$location); 
                        }else{
                            //location does not exist in sugar,check if we are to create it
                            if($this->sync_missing_sugarcrm){
                                //new location
                                $this->set_entry_sugarcrm(BeanFactory::getBean('Locations'),$location);
                                $primary_location = $this->get_location_bean($location['id']);
                            }
                        }
                        if(!empty($primary_location)){
                            if($bean->primary_location_id !== $primary_location->id){
                                $update = true;
                                $bean->primary_location_id = $primary_location->id;
                            }
                            //update bean with address details beacuse when the bean is next saved it will not have the new values that are currently in the database that were updated when the location was saved
                            $this->set_address_entry_sugarcrm($bean,$primary_location,'primary');
                        }
                    }
                    break;
                case 'Location':       
                    break;
                default:
                    break;
            }            
             return $update;           

        } 

        //create/update record in sugarcrm
        public function set_entry_sugarcrm($bean,$data) {
            $update = false;
            if(empty($bean->id)){
                $update = true;
                //new bean, need to create an id to use when creating locations 
                $bean->id = create_guid();
                $bean->new_with_id = true;
            }
            foreach($this->{'sync_map_'.strtolower(get_class($bean))} as $crm => $erp) {
                if(!in_array($crm, array('account_id','account_name','shipping_location_id','billing_locations_id','primary_location_id'))){
                    if(htmlspecialchars_decode($bean->$crm, ENT_QUOTES) !== (isset($data[$erp])?$data[$erp]:'')){
                        if(in_array($bean->$crm,array("0","1"))){
                            //check types bool v string values(true==1)
                            if($bean->$crm == (isset($data[$erp])?$data[$erp]:'')){
                                continue;
                            }
                        }
                        $update = true;
                        $bean->$crm = isset($data[$erp])?$data[$erp]:'';
                    }
                } else{
                    
                }
            }
            //if account id is required, add the account
            //parent_bean will always be the account
            if(isset($this->{'sync_map_'.strtolower(get_class($bean))}['account_id'])) {
                $bean->account_id = $this->parent_bean->id;
                $bean->account_name = $this->parent_bean->name;
            }

            if($this->sync_children) {
                if(!empty($data['locations'])) { 
                    $update = $this->handle_location_data($bean,$data,$update);
                }
            }
            
            if($update){
                //need to trigger bean save but not the hooks
                $bean->in_sync = true;
                $bean->save();
            }
            
            return $update;
        }
         
        //update address of parent record in sugarcrm
        //if not set the address will be overriden with nulls
        public function set_address_entry_sugarcrm($bean,$location,$type) {
            if(is_null($location) || is_null($type)){
                return;
            }
            foreach($this->address_fields as $bean_field => $location_field) {
                if($bean->{$type.$bean_field} !== $location->$location_field){
                    $bean->{$type.$bean_field} = $location->$location_field;
                }
            }
        }
        
        //OPENBRAVO DATA CHANGE FUNCTIONS (SUGARCRM MASTER DATA)
        //create/update record in openbravo
        public function set_entry_openbravo($bean,$attempt_to_match = false,$params = array()){
            
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            if($api != null && $api->checkLogin()){
                if(empty($bean->id)){
                    return array('errorMessage' => 'Error retrieving current record');
                }
                $create = false;
                
                if(empty($bean->erp_id) && $attempt_to_match){
                    if(!($match = $this->match_entry_openbravo($bean))){
                        //set create to true so the bean is saved
                        $create = true;
                        $bean->erp_id = $match['data']['id'];
                    }
                    //this will be null is no match was found then then created 
                    //in the next process if the setting is true
                }
                
                if(empty($bean->erp_id)){
                    //can not update as no external id found,
                    //attempt to create?
                    if($this->sync_missing_openbravo){
                        //only create if missing is set to turn
                        $create = true;
                        $method = 'create_'.strtolower(get_class($bean));
                    }else{
                        //no id so its missing in openbravo?
                        //however the option is not true so
                        //return as successful.
                        return array('success'=>true);
                    }
                }else{
                    $method = 'update_'.strtolower(get_class($bean));
                }
                $related_record = $api->$method($bean,$params);
                
                if($related_record['success']){
                    if(isset($related_record['data']['id'])){
                        if($create){
                            //we have created a record
                            $bean->erp_id = $related_record['data']['id'];
                            $bean->in_sync=true;
                            $bean->save();
                        }
                        return $related_record;
                    }
                }else{
                    if(isset($related_record['errorMessage'])){
                        return $related_record;
                    }
                }
            }
            return array('errorMessage' => 'Can not log in');
        }
      
        //handle then update children data for openbravo
        public function handle_children_data($bean){
            //sync locations first as these are used in the contacts
            //update locations
            $related_locations = $bean->get_linked_beans('locations', 'Locations');
            foreach($related_locations as $rel_l){
                $location_result = $this->set_entry_openbravo($rel_l);
                if(!$location_result['success'] && isset($location_result['errorMessage'])){
                    //error found, stop process
                    return $location_result;
                }
            }
           
            $related_contacts = $bean->get_linked_beans('contacts','Contact');
            foreach($related_contacts as $rel_c){
                $contact_result = $this->set_children_contact_data($rel_c);
                if(!$location_result['success'] && isset($location_result['errorMessage'])){
                    //error found, stop process
                    return $contact_result;
                }
            }
            
            return array('success' => true);
           
            
        }
        
        //update contact in openbravo
        public function set_children_contact_data($bean){
            //as all locations should reside with an account we do not need to update locations for contacts
            //we just need to supply the contact web service with an id
            $location_id = null;
            if($this->sync_children){
                if(!empty($bean->primary_location_id)){
                   $location_id = BeanFactory::getBean("Locations", $bean->primary_location_id)->erp_id;
                }
            }
            $params = array (
                'account' => $this->parent_bean,
                'location_id' => $location_id
            );
            return $this->set_entry_openbravo($bean,$params);            
        }
        
        //not used
        //update locations in openbravo
        public function set_children_location_data($bean){
            if(!empty($bean->primary_location_id)){
                return $this->set_entry_openbravo(BeanFactory::getBean("Locations", $bean->primary_location_id));
            }
        }
        
        //find matching records in openbravo
        public function match_entry_openbravo($bean){
            $api = ExternalAPIFactory::loadAPI('Openbravo');
            switch (get_class($bean)) {
                case 'Account':
                    //this case is a bit point less as the accounts have already been matched by code....
                      
                    //match on code
                    //pass true to match on codes
                    $result = $api->retrieve_business_partner($bean,true);
                    break;
                case 'Location':
                    //exact match//      
                    if(isset($bean->erp_id) && !empty($bean->erp_id)){ 
                        $result = $api->retrieve_business_partner_location($bean);
                        if(isset($result['data'])){
                            foreach($result['data'] as $location){
                                if($this->areLocationsEqual($bean,$location)){
                                    //return match
                                    return $location;
                                }
                            }
                        }
                    }
                    break;
                case 'Contact':
                    //first + last name
                    if(isset($bean->erp_id) && !empty($bean->erp_id)){ 
                        $result = $api->retrieve_contacts_business_partner($bean);
                        foreach($result['data'] as $contact) {
                            if($this->areContantsEqual($bean,$contact)){
                                //return match
                                return $contact;
                            }
                            
                        }
                    }
                    break;
            }
            //no match
            return false;
            
        }
        
        function areContantsEqual($bean,$contact){
            if((!empty($bean->email1) && !empty($contact['email'])) && strcasecmp ($bean->email1,$contact['email'])==0) {
                //match found on email address
                return true;
            }elseif(
                    (!empty($bean->first_name) && !empty($contact['firstName']) 
                        && !empty($bean->last_name) && !empty($contact['lastName'])
                    ) &&
                    (strcasecmp ($bean->first_name,$contact['firstName']) == 0
                        && strcasecmp($bean->last_name, $contact['lastName'])==0)
                    ) {
                //match found on first and last name
                return true;
            }else{
              return false;
            }
        }
}

?>
