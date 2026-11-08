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


class OpenbravoLogicHook {
    var $openbravo_api;
    var $auto_enable_sync ;


    public function __construct(){
        $this->openbravo_api = ExternalAPIFactory::loadAPI('Openbravo',true);
        if($this->openbravo_api == null){
            return null;
        }
        //load api as sync user
        $this->openbravo_api->loadEAPM('',true);
        $this->sourceObj = SourceFactory::getSource('ext_rest_openbravo');
        $this->mapping =  $this->sourceObj->getMapping();
        $this->auto_enable_sync = $this->openbravo_api->getConnectorParam('auto_enable_sync');
    }

    public function connector_enabled($module){
        return key_exists($module, $this->mapping['beans']);
    }
    
    
    /*
     * YUI HOOKS
     * -showFrame
     * -showSummaryFrame
     * --createBalanceDisplay
     * --createFreightDisplay
     * --createPurchaseHistoryDisplay
     * 
     */
    
    public function showFrame($event, $args) {
        if ( $GLOBALS['app']->controller->action != 'DetailView' ) {
            return;
        }
        require_once('include/connectors/utils/ConnectorUtils.php');

        $bean = $GLOBALS['app']->controller->bean;
                
        if ( $this->openbravo_api ) {
            $smarty = new Sugar_Smarty();
            $tplName = 'modules/Connectors/connectors/sources/ext/rest/openbravo/tpls/Openbravo.tpl';
            require_once('include/connectors/utils/ConnectorUtils.php');
            $connector_language = ConnectorUtils::getConnectorStrings('ext_rest_openbravo');
            $smarty->assign('connector_language', $connector_language);
            
            if($this->openbravo_api->checkLogin()) {
                $client_result = $this->openbravo_api->getClient($bean->id,'Accounts');
                if($client_result['success'] == false) {
                    //for now, just don't display the panel if client not found
                    return;
                }
                $c = 1;
                $row = '';
                $html = '';
                foreach($client_result['data'] as $name => $value){
                    if(is_array($value)){
                        continue;
                    }
                    $row .= "<td width='12.5%' scope='row'>{$name}:</td><td width='37.5%'>{$value}</td>";
                    if($c%2 != 1 ){
                        $html .="<tr>$row</tr>";
                        unset($row);
                    }
                    $c++;
                }
                
                if($client_result['success'] == false) {
                    //for now, just don't display the panel if client not found
                    return;
                }

                $smarty->assign('data',$html);
            }
            $smarty->assign('APP', $GLOBALS['app_strings']);
            echo $smarty->fetch($tplName);
        }
    }

    public function showSummaryFrame($event, $args) {
        if ( $GLOBALS['app']->controller->action != 'DetailView' ) {
            return;
        }
        $bean = $GLOBALS['app']->controller->bean;
        if(!$this->connector_enabled('Accounts')) { 
            //connector is not enabled
            return;
        }
        require_once('include/connectors/utils/ConnectorUtils.php');

        if ( $this->openbravo_api && $this->openbravo_api->checkLogin()) {
            $smarty = new Sugar_Smarty();
            $tplName = 'modules/Connectors/connectors/sources/ext/rest/openbravo/tpls/OpenbravoSummary.tpl';

            require_once('include/connectors/utils/ConnectorUtils.php');
            $connector_language = ConnectorUtils::getConnectorStrings('ext_rest_openbravo');
            $smarty->assign('connector_language', $connector_language);                    

            //set the account id of the current record
            if( is_a($bean,'Account')){
                $account_id = $bean->id;
                $erp_id = $bean->erp_id;
            }elseif($bean->account_id){
                $account_id = $bean->account_id;
                $erp_id = BeanFactory::getBean('Accounts', $bean->account_id)->erp_id;
            }elseif(isset($bean->parent_type) && $bean->parent_type == 'Accounts'){
                $account_id = $bean->parent_id;
                $erp_id = BeanFactory::getBean('Accounts', $bean->parent_id)->erp_id;
            }else{
                $account_id = '';
            }
            //show sales order button even if account is not synced
            if($this->openbravo_api->getConnectorParam('sales_order_button')){
                //assign account id to template
                
                if(is_a($bean,'Account')) {
                    $smarty->assign('show_sales_order_button',true);
                    
                } elseif((is_a($bean,'Contact') || is_a($bean,'Call')) && !empty($account_id)) {
                    $smarty->assign('show_sales_order_button',true);
                } else {
                    //dont display any subpanel
                    return;
                }
                
                $smarty->assign('account_id_for_button',$account_id);
            }else{
                $smarty->assign('show_sales_order_button',false);
            }


            //only show account information for synced accounts s this information does not exist for account that are not synced.
            if(!empty($erp_id)){

                $summaryData = $this->openbravo_api->getSummary($bean);
                if($summaryData['success']){
                    $show_purchase_history = false;
                    if($this->openbravo_api->getConnectorParam('purchase_history') > 0) {
                        if($summaryData['data']['purchasehistory']){
                            $show_purchase_history = true;
                            $smarty->assign('purchase_history_data',$this->createPurchaseHistoryDisplay($summaryData['data']['purchasehistory']));
                        }
                    }
                    $smarty->assign('show_purchase_history',$show_purchase_history);
                    
                    $show_balance = false;
                    if($this->openbravo_api->getConnectorParam('account_balance')) {
                        $show_balance = true;
                        $smarty->assign('balance_data',$this->createBalanceDisplay($summaryData['data']['accountbalances']));
                    }
                    $smarty->assign('show_balance',$show_balance);

                    $show_freight = false;
                    if($this->openbravo_api->getConnectorParam('freight_information')){
                        $show_freight = true;
                        $smarty->assign('freight_data',$this->createFreightDisplay($summaryData['data']['freight']));
                    }
                    $smarty->assign('show_freight',$show_freight);
                } else{
                     $smarty->assign('ERROR', true);
                     $errorMessage = '';
                     if(isset($summaryData['errorMessage'])){
                         $errorMessage = $summaryData['errorMessage']."\n";                         
                     }
                     if(isset($summaryData['bodyMessage'])){
                         $errorMessage .= $summaryData['bodyMessage'];
                     }                     
                     $smarty->assign('errorMessage', $errorMessage);
                }
            }
            //display template
            $smarty->assign('APP', $GLOBALS['app_strings']);
            echo $smarty->fetch($tplName);
            
           
        }
            

    }

    function createBalanceDisplay($data){
        return "<th colspan='2' style='text-align:left'>Account Details Balance</th>
                <tr><td style='text-align:right' scope='col'>Current:</td><td>".$data['current']."</td></tr>
                <tr><td style='text-align:right' scope='col'>30 days:</td><td>".$data['30days']."</td></tr>
                <tr><td style='text-align:right' scope='col'>60days:</td><td>".$data['60days']."</td></tr>
                <tr><td style='text-align:right' scope='col'>90 days:</td><td>".$data['90days']."</td></tr>
                <tr><td style='text-align:right' scope='col'>Total:</td><td>".$data['total']."</td></tr>";
    }

    function createFreightDisplay($data){
        return "<th style='text-align:left'>Freight Details</th><tr><td>".$data['fixedprice']."</td></tr>";
    }

    function createPurchaseHistoryDisplay($data){
        $html = '';
        foreach($data as $row ){
            $html .= "<tr>
                        <td >{$row['product']}</td>
                        <td style='text-align:right'>{$row['qty']}</td>
                        <td style='text-align:right'>{$row['lineamount']}</td>
                        <td >{$row['purchasedate']}</td>
                        <td style='text-align:right'>{$row['price']}</td>
                      </tr>";

        }
        return "<th colspan='5' style='text-align:left'>Purchase Details</th>
                <tr>
                    <td style='text-align:left' scope='col'>Product</td>
                    <td style='text-align:right' scope='col'>QTY</td>
                    <td style='text-align:right' scope='col'>Sales</td>
                    <td style='text-align:left' scope='col'>Last Sale</td>
                    <td style='text-align:right' scope='col'>Price</td>
                    </tr>".$html;
    }

    
    
    
    
    
    /*
     * ACCOUNTS MODULE HOOKS
     * -before save
     * -relationship add
     * -relationship remove
     * -before delete
     */
    
    
    
     //updated account in CRM (YES)
    public function accounts_before_save(&$bean, $event = null, $arguments = null){
        if($this->openbravo_api == null){
            //user is does not have openbravo object
            //main cause due to not having a login
            //return so no hooks are processed
            return;
        }
        if($_REQUEST['action'] == 'Save2' || isset($_REQUEST['rest_data']) || isset($bean->in_sync)){
            return;
        } 
        if($this->connector_enabled('Accounts')){
            //after_save
            if ( $this->openbravo_api && $this->openbravo_api->checkLogin()) {
                if(!empty($bean->erp_id)){
                //updating account
                    $request = $this->openbravo_api->update_business_partner($bean);
                    if(!$request['success']){
                        $GLOBALS['log']->error("Sync Error: {$request['errorMessage']} ");
                        $displayError ='Unable to update Account in Openbravo';
                        if(isset($request['bodyMessage'])){
                            $displayError .= "</br> {$request['bodyMessage']}";
                        }
                    }
                }else if($this->auto_enable_sync) {
                //new account
                    $request = $this->openbravo_api->create_business_partner($bean);
                    if($request['success']){
                        $bean->erp_id = $request['data']['id'];
                        //$bean->save();
                    }else{
                        $GLOBALS['log']->error("Sync Error: {$request['errorMessage']} ");
                        $displayError = "Unable to create Account in Openbravo";
                        if(isset($request['bodyMessage'])){
                            $displayError .= "</br> {$request['bodyMessage']}";
                        }
                    }
                }
            }else{
                SugarApplication::appendErrorMessage("No Connection to Openbravo, Check users external account login details");
            }
        }
            
        if(isset($displayError)) {
            SugarApplication::appendErrorMessage($displayError);
            $url = 'index.php?module=Accounts&action=EditView&record='.$bean->id;
            SugarApplication::redirect($url);             
        }
        
    }
    //after_relationship_add
    //not needed anymore
    public function accounts_on_relationship_add($bean, $event = null, $arguments = null){
        if($this->openbravo_api == null){
            //user is does not have openbravo object
            //main cause due to not having a login
            //return so no hooks are processed
            return;
        }
        //after_relationship_add
        return;
//        if($this->connector_enabled('Contacts') && $this->connector_enabled('Accounts')){
//            if($arguments['relationship'] == 'accounts_contacts'){
//                //make sure related account is a sycned account before attempting to sync any contacts.
//                $account = $this->openbravo_api->retrieve_business_partner(BeanFactory::getBean('Accounts', $arguments['id']));
//                if(isset($account['id'])){
//                    $contact = BeanFactory::getBean('Contacts', $arguments['related_id']);
//                    //need to trigger contact save if coming from subpanel.
//                    //add contact to account from the before save hook thats triggered.
//                    if($_REQUEST['action'] == 'Save2'){
//                        return $contact->save();
//                    }                  
//                    //if contact is a new contact dont call the add to business parnter ws
//                    //the before save hook will add this contact to the account when its called.
//                    if(empty($contact->erp_id)){
//                        return;
//                    }
//                }
//            }
//        }
    }



    
    //after_relationship_delete'
    public function accounts_on_relationship_delete($bean, $event = null, $arguments = null){
        if($this->openbravo_api == null){
            //user is does not have openbravo object
            //main cause due to not having a login
            //return so no hooks are processed
            return;
        }
        if($this->connector_enabled('Contacts') && $this->connector_enabled('Accounts')){
            if($arguments['relationship'] == 'accounts_contacts'){
                if ( $this->openbravo_api && $this->openbravo_api->checkLogin()) {
                    $related_bean = BeanFactory::getBean($arguments['related_module'], $arguments['related_id']);
                    if(!empty($related_bean->erp_id) ){
                        //$primary_account = $related_bean->getPrimaryAccount();
                        //if contact has been removed from its primary account
                        //remove it from the account in openbravo as well
                        if($related_bean->account_id == $bean->id){

                            $request = $this->openbravo_api->remove_contact_from_account($related_bean, $bean);
                            if($request['success']){
                                return true;
                            }else{                          
                                $GLOBALS['log']->error("Sync Error: {$request['errorMessage']} ");
                                $displayError = "Unable to remove Coontact from Account in Openbravo";
                                if(isset($request['bodyMessage'])){
                                    $displayError .= "</br> {$request['bodyMessage']}";
                                }
                            }
                        }else{
                            //do nothing as we are not removing from primary account
                            return;
                        }
                    }
                }else{
                    SugarApplication::appendErrorMessage("No Connection to Openbravo, Check users external account login details");
                }
                if(isset($displayError)) {
                    SugarApplication::appendErrorMessage($displayError);
                    $url = 'index.php?module=Accounts&action=EditView&record='.$bean->id;
                    SugarApplication::redirect($url);             
                }
            }
        }
    }
    
    //no webservice for this
    public function accounts_before_delete($bean, $args){
        if($this->openbravo_api == null){
            //user is does not have openbravo object
            //main cause due to not having a login
            //return so no hooks are processed
            return;
        }
        //before_delete
        if($this->connector_enabled('Accounts')){
            if(!empty($bean->erp_id)){
                $request = $this->openbravo_api->delete_business_partner($bean);
                if(!$request['success']){
                    $displayError = 'Unable to delete Business Partner in Openbravo';
                    if(isset($request['bodyMessage'])){
                        $displayError .= "</br> {$request['bodyMessage']}";
                    }
                }
            }
            if(isset($displayError)) {
                SugarApplication::appendErrorMessage($displayError);
                //$url = 'index.php?module=Accounts&action=EditView&record='.$bean->id;
                //SugarApplication::redirect($url);             
            }
        }
        return;
    }

        
    
    /*
     * CONTACTS MODULE HOOKS
     * -before save
     * -relationship add
     * -relationship delete
     * -before delete
     */
    //Updated Contact in CRM (YES)
    public function contacts_before_save($bean,$event, $args){
        if($this->openbravo_api == null){
            //user is does not have openbravo object
            //main cause due to not having a login
            //return so no hooks are processed
            return;
        }
        
        
        
        if(isset($_REQUEST['rest_data']) || isset($bean->in_sync)){
            return;
        }
        //before_save
        if($this->connector_enabled('Contacts') && $this->connector_enabled('Accounts')){
            //make sure related account is a sycned account before syncing any contacts.            
            //if set from after_relationship_add hook use request data to get the account in context
            if($_REQUEST['action'] == 'Save2'){
                $account_id = $_REQUEST['record'];
            }else{      
                $account_id = $bean->account_id;
            }
            
            //get primary account            
            $account = BeanFactory::getBean('Accounts',$account_id);
            
            //get primary location
            $location_id = null;
            if(!empty($bean->primary_location_id)){
                $primary_location = BeanFactory::getBean('Locations', $bean->primary_location_id);
                if(!empty($primary_location->erp_id)){
                    $location_id = array('id' => $primary_location->erp_id);
                }
            }
            
            if ( $this->openbravo_api && $this->openbravo_api->checkLogin()) {
                //make sure this account is a sycned account before syncing any contacts.       
                $primary_business_partner = $this->openbravo_api->retrieve_business_partner($account);

                //if contact has been sycned before check to see if the primary account has changed.
                //if it has changed mark the contact as active = false in the old account
                //and create a new contact and update this contact with the new returned id
                if(!empty($bean->erp_id)){
                    //contact has been synced before.
                    //need to find if the account has changed.
                    //retrieve the current contacts business partner that is set in openbravo
                    $openbravo_business_partner = $this->openbravo_api->retrieve_contacts_business_partner($bean);
                    if(isset($openbravo_business_partner['businessPartner']['id']) && 
                            $openbravo_business_partner['businessPartner']['id'] != $primary_business_partner['id']){
                        //the primary account has changed
                        //need to flag contact as active = false
                       
                        $request = $this->openbravo_api->update_contact($bean,$openbravo_business_partner,null, false);
                        if($request['success']){
                            //contact has been flaged as active = false                    
                            //now create new contact with new account
                            if(!empty($account->erp_id)){                               
                                                                
                                $request = $this->openbravo_api->create_contact($bean,$account,$location_id);
                                if($request['success']){
                                    $bean->erp_id = $request['data']['id'];
                                }else{
                                    //contact was not created
                                    $GLOBALS['log']->error("Sync Error: {$request['errorMessage']} ");
                                    $displayError = "Unable to create Contact in Openbravo";
                                    if(isset($request['bodyMessage'])){
                                        $displayError .= "</br> {$request['bodyMessage']}";
                                    }
                                }        
                            }else{//new account is not sycned
                            }
                            
                        }else{
                            //unable to flag contact as inactive.
                            $GLOBALS['log']->error("Sync Error: {$request['errorMessage']} ");
                            $displayError = "Unable to flag Contact as inactive in Openbravo";
                            if(isset($request['bodyMessage'])){
                                $displayError .= "</br> {$request['bodyMessage']}";
                            }
                        }
                    }else{
                        //primary account has not changed 
                        //or primary account has been added back to contact
                        $request = $this->openbravo_api->update_contact($bean,$account,$location_id,true);
                        if($request['success']){

                        }else{
                            $GLOBALS['log']->error("Sync Error: {$request['errorMessage']} ");
                            $displayError = "Unable to update contact in Openbravo";
                            if(isset($request['bodyMessage'])){
                                $displayError .= "</br> {$request['bodyMessage']}";
                            }
                        }                
                    }
                }else if($this->auto_enable_sync) {
                    //create contact if account has been synced and is valid
                    if(!empty($primary_business_partner['id'])){
                        $request = $this->openbravo_api->create_contact($bean,$account,$location_id);
                        if($request['success']){
                            $bean->erp_id = $request['data']['id'];
                        }else{
                            $GLOBALS['log']->error("Sync Error: {$request['errorMessage']} ");
                            $displayError = "Unable to update Contact in Openbravo";
                            if(isset($request['bodyMessage'])){
                                $displayError .= "</br> {$request['bodyMessage']}";
                            }
                        }
                    }                
                }
            } else{
                SugarApplication::appendErrorMessage("No Connection to Openbravo, Check users external account login details");                
            }  
            
            if(isset($displayError)) {
                SugarApplication::appendErrorMessage($displayError);
                $url = 'index.php?module=Contacts&action=EditView&record='.$bean->id;
                SugarApplication::redirect($url);             
            }
            
            
        }
        
        
    }
    //Deleteing Contact in CRM (YES)
    public function contacts_before_delete(&$bean, $args){
        if($this->openbravo_api == null){
            //user is does not have openbravo object
            //main cause due to not having a login
            //return so no hooks are processed
            return;
        }
        //before_delete
        if($this->connector_enabled('Contacts') && $this->connector_enabled('Accounts')){
            if ( $this->openbravo_api && $this->openbravo_api->checkLogin()) {
                if(!empty($bean->erp_id)){
                    $request = $this->openbravo_api->delete_contact($bean);
                    if($request['success']){
                        $bean->erp_id = null;
                    }else{
                        //get primary location
                        $location_id = null;
                        if(!module_enabled('Locations')){
                            if(!empty($bean->primary_location_id)){
                                $primary_location = BeanFactory::getBean('Locations', $bean->primary_location_id);
                                if(!empty($primary_location->erp_id)){
                                    $location_id = array('id' => $primary_location->erp_id);
                                }
                            }
                        }
                        $openbravo_business_partner = $this->openbravo_api->retrieve_contacts_business_partner($bean);
                        $request = $this->openbravo_api->update_contact($bean,$openbravo_business_partner,$location_id, false);
                        if($request['success']){
                            $bean->erp_id = null;
                        }else{
                            $GLOBALS['log']->error("Sync Error: {$request['errorMessage']} ");
                            $displayError = "Unable to delete Contact in Openbravo";       
                            if(isset($request['bodyMessage'])){
                                $displayError .= "</br> {$request['bodyMessage']}";
                            }
                        }
                    }
                }
            }else{
                SugarApplication::appendErrorMessage("No Connection to Openbravo, Check users external account login details");
            }
            if(isset($displayError)) {
                SugarApplication::appendErrorMessage($displayError);
                $url = 'index.php?module=Contacts&action=EditView&record='.$bean->id;
                SugarApplication::redirect($url);             
            }
        }
    }


    
    
    
    /*
     * LOCATION MODULE HOOKS
     * -before save
     * -relationship add
     * -relationship remove
     * -before delete
     */

    public function locations_before_save(&$bean, $event = null, $arguments = null){
        if($this->openbravo_api == null){
            //user is does not have openbravo object
            //main cause due to not having a login
            //return so no hooks are processed
            return;
        }
        //update/create location in openbravo
        if($_REQUEST['action'] == 'Save2' || isset($_REQUEST['rest_data']) || isset($bean->in_sync)){
            return;
        }
        if($this->connector_enabled('Accounts')){
            if ( $this->openbravo_api && $this->openbravo_api->checkLogin()) {
                //if location is not related to an account dont create the business partner location.
                if(empty($bean->account_id)){
                    return;
                }
                if(!empty($bean->erp_id)){
                //updating location
                    $request = $this->openbravo_api->update_business_partner_location($bean);
                    if(!$request['success']){
                        $GLOBALS['log']->error("Sync Error: {$request['errorMessage']}, {$request['bodyMessage']} ");
                        $displayError = "Unable to update location in Openbravo";
                        if(isset($request['bodyMessage'])){
                            $displayError .= "</br> {$request['bodyMessage']}";
                        }

                    }
                }else if($this->auto_enable_sync) {
                //new location
                    $request = $this->openbravo_api->create_business_partner_location($bean);
                    if($request['success']){
                        $bean->erp_id = $request['data']['id'];
                    }else{
                        $GLOBALS['log']->error("Sync Error: {$request['errorMessage']} ");
                        $displayError = "Unable to create location in Openbravo";
                    }
                }
            }else{
                SugarApplication::appendErrorMessage("No Connection to Openbravo, Check users external account login details");
            }

            if(isset($displayError)) {
                SugarApplication::appendErrorMessage($displayError);
                //look at where we are coming from and go back.
                if($_REQUEST['return_module'] == 'Contacts'){
                    //we are coming from the contacts detail view
                    
                    //$url = "index.php?module={$_REQUEST['return_module']}&action=EditView&record={$_REQUEST['return_id']}";
                }
                //$url = 'index.php?module=Locations&action=EditView&record='.$bean->id;
                //SugarApplication::redirect($url);             
            }
            
        }
            
     }
    
    public function locations_on_relationship_add($bean, $event = null, $arguments = null){
        if($this->openbravo_api == null || isset($bean->in_sync)){
            //user is does not have openbravo object
            //main cause due to not having a login
            //return so no hooks are processed
            return;
        }

        //after_relationship_add
    //   $arguments['id'] parent record
    //   $arguments['module'] parent module
    //   $arguments['related_id'] linked id
    //   $arguments['related_module'] linked module
    //   $arguments['link'] field def of link
    //   $arguments['relationship'] relationship
        if($this->connector_enabled($arguments['module'])){
            if($arguments['relationship'] == 'account_location'){
                if ( $this->openbravo_api && $this->openbravo_api->checkLogin()) {
                    if(!empty($bean->erp_id)){
                        $person = new Contact();
                        if(!is_array($arguments['related_id'])){
                            $rel_key = array($arguments['related_id']);
                        }
                        foreach($rel_key as $contact){
                            $person = $person->retrieve($contact);
                            //$this->openbravo_api->addContactToAccount($person, $this);
                        }
                    }
                }else{
                    SugarApplication::appendErrorMessage("No Connection to Openbravo, Check users external account login details");
                }
            }
        }
    }

    public function locations_on_relationship_delete($bean, $event = null, $arguments = null){
        if($this->openbravo_api == null){
            //user is does not have openbravo object
            //main cause due to not having a login
            //return so no hooks are processed
            return;
        }
        //after_relationship_delete
        if($this->connector_enabled($arguments['module'])){
            if($arguments['relationship'] == 'account_location'){
                
            }
        }
    }
  
    public function locations_before_delete($bean, $args){
        if($this->openbravo_api == null){
            //user is does not have openbravo object
            //main cause due to not having a login
            //return so no hooks are processed
            return;
        }
        //before_delete
        if($this->connector_enabled('Contacts') && $this->connector_enabled('Accounts')){
            if ( $this->openbravo_api && $this->openbravo_api->checkLogin()) {
                if(!empty($bean->erp_id)){
                    $request = $this->openbravo_api->delete_business_partner_location($bean);
                    if(!$request['success']){
                        $bean->deleted = false;
                        $request_update = $this->openbravo_api->update_business_partner_location($bean,false);
                        if($request_update['success']){
                            $displayError = "Location: {$bean->name} May have linked records, Location has been marked inactive not been removed from Openbravo";
                        }else{
                            $displayError = 'Unabled to delete location or mark location as inactive.';
                        }   
                        if(isset($request_update['bodyMessage'])){
                            $displayError .= "</br> {$request['bodyMessage']}";
                        }
                    }
                }
                if(isset($displayError)) {
                    SugarApplication::appendErrorMessage($displayError);
                }
            }else {
                SugarApplication::appendErrorMessage("No Connection to Openbravo, Check users external account login details");
                return false;
            }
        }
    }
  
     
 
}
