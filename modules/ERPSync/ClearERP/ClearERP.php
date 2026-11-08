<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('modules/ERPSync/ERPNotify.php');

class ClearERP extends ERPNotify {

    var $url;
    var $password;
    var $username;

    //Setup up connection authentication
    function __construct($url, $username = null, $password = null) {
        global $sugar_config;
        $this->username = $username;
        $this->password = $password;
        $this->url = $url;
    }
    //END
    function accountRequiredFields() {
      return array('code');
    }

    function account_pre_save($bean){
        if(strtoupper($bean->code) != strtoupper($bean->erp_id)){
            $bean->code = $bean->erp_id;
        }        
    }


  //ERPSync Fucntions
    function createAccount($bean, $updateDescripton = false){
    //setup settings for a new account in clear
        $user = new User();
        $user = $user->retrieve($bean->created_by);

        $assigned_user = new User();
        $assigned_user = $assigned_user->retrieve($bean->assigned_user_id);
        if(!empty($assigned_user->erp_username)) {
            $bean->sales_rep = $assigned_user->erp_username;
        }else{
            $bean->sales_rep = strtoupper($assigned_user->user_name);
        }

        $xmlAccount_details = '';
        if(!empty($bean->code)){
            $xmlAccount_details .= ' cardCode= "'.strtoupper($bean->code).'"';
        }
        $createdDate = date('d/m/Y', strtotime($bean->date_modified));
        
        $xmlAccount_details .= ' createdBy="'.from_html($user->full_name).'"
                            createdDate="'.$createdDate.'" ';

        $xml_string = $this->buildAccountXML($bean, 'createAccount', $xmlAccount_details);
        $sent = $this->sendRequest($bean, $xml_string);

        if($updateDescripton){
            $this->addDescription($bean, $user);
        }
    }

    function updateAccount($bean, $updateDescripton = false, $obsolete = "N"){
    //setup settings for a new account in clear
        $updating_bean = true;
        $user = new User();
        $user = $user->retrieve($bean->modified_user_id);

        $assigned_user = new User();
        $assigned_user = $assigned_user->retrieve($bean->assigned_user_id);
        if(!empty($assigned_user->erp_username)) {
            $bean->sales_rep = $assigned_user->erp_username;
        }else{
            $bean->sales_rep = strtoupper($assigned_user->user_name);
        }

        $changedDate = date('d/m/Y', strtotime($bean->date_modified));
        $xmlAccount_details= ' cardCode="'.strtoupper($bean->erp_id).'"
                            lastChangedBy="'.from_html($user->full_name).'"
                            lastChangedDate="'.$changedDate.'"
                            obsolete="'.$obsolete.'" ';

        $xml_string = $this->buildAccountXML($bean, 'updateAccount', $xmlAccount_details);
        $sent = $this->sendRequest($bean, $xml_string);
        
        if($updateDescripton){
            $this->addDescription($bean, $user);
        }
        if($sent === true){
            return true;
        }
    }

    function createContact($bean){
        $user = new User();
        $user = $user->retrieve($bean->created_by);

        $account = new Account();
        $account = $account->retrieve($bean->account_id);
        $createdDate = date('d/m/Y', strtotime($bean->date_modified));
        if( $account ) {

            $contact_details = 'cardCode="'.strtoupper($account->erp_id).'"
                                createdBy="'.from_html($user->full_name).'"
                                createdDate="'.$createdDate.'"';

            $xml_string = $this->buildContactXML($bean, "createContact", $user, $contact_details);
            $sent = $this->sendRequest($bean, $xml_string);
        }
    }

    function updateContact($bean){
        $user = new User();
        $user = $user->retrieve($bean->modified_user_id);

        

        $account = new Account();
        $account = $account->retrieve($bean->account_id);
        $changeDate = date('d/m/Y', strtotime($bean->date_modified));
        if( $account ) {

            $contact_details = 'cardCode="'.strtoupper($account->erp_id).'"
                                contactID="'.$bean->erp_id.'"
                                lastChangedBy="'.from_html($bean->full_name).'"
                                lastChangeDate="'.$changeDate.'"';

            $xml_string = $this->buildContactXML($bean, "updateContact", $user, $contact_details);
            $sent = $this->sendRequest($bean, $xml_string);
        }
    }

    function addContactToAccount($contact, $account){
        $this->createContact($contact);
    }

    function removeContactFromAccount($contact, $account){
        $xml_string = $this->buildContactRemoveXML($contact, $account);
        $removed = $this->sendRequest($contact, $xml_string);

        if($removed){
           //save the contact without the clear variables, regardless of if its been deleted
           $contact->erp_id = '';
           $contact->save($check_notify=false, $erp_notify = false);
  
           return true;
       }
       

    }

    function deleteAccount($bean){
        //set obsolete to "Y" when an account if marked as deleted in sugar
        $obsolete = "Y";
        $removed = $this->updateAccount($bean, $updateDescripton = false, $obsolete);
        if($removed){
            return true;
        }else{
            return false;
        }

    }


    function deleteContact($bean){
        $account = new Account();
        $account = $account->retrieve($bean->account_id);
        if( $account ) {
             return  $this->removeContactFromAccount($bean, $account);
        }
        return true;
    }          
    //END ERPSync Functions
    //Extra Functions
    //Add a description if there is one
    function addDescription($bean, $user){
        if(!empty($bean->description)){
            if(!empty($bean->erp_id)){
                $xml_note = $this->buildAccountNoteXML($bean, $updating_bean = true, $user);
                $note_sent = $this->sendRequest($bean, $xml_note);
            }
        }
    }
    //END
    //Set the Primary Contact
    function isPrimaryContact($bean, $account_contacts)
    {
        $isPrimary = 'N';
        if (count($account_contacts) <= 1)
        {
            $isPrimary = 'Y';
        }
        else
        {

            $highest_priority = 999;
            $highest_contact_id = '';
            // Compare all Contacts in Account's subpanel with each other and
            // determine which one has the highest priority
            foreach ($account_contacts as $contact)
            {
                if ($contact->account_contact_priority < $highest_priority)
                {
                    $highest_priority = $contact->account_contact_priority;
                    $highest_contact_id = $contact->id;
                }
            }

            // If bean (Contact) has the highest prioirty
            // compared to its colleagues in the Account
            if ($bean->id == $highest_contact_id)
                $isPrimary = 'Y';
        }

        return $isPrimary;
    }
    //END
    //Split up address on /r/n
    function splitStreetAddress($address){
        $addressArray = explode("\r\n",$address);
        $formatAddress = array();
        $addresses = array();

        //remove empty lines
        foreach($addressArray as $address){
            if(empty($address)){
                continue;
            }else{
                $addresses[] = $address;
            }
        }

        //rebuild address array to a max of 3 entrys with the 3rd concated
        //with any entrys that are in the array deeper than index 2
        for($i = 0; $i < count($addresses); $i++){
            if($i > 2){
                $formatAddress[2] = $formatAddress[2].' '.$addresses[$i];
            }else{
                $formatAddress[$i] = $addresses[$i];
            }
        }

        return $formatAddress;
    }
    //END
    //Get First contact role or use title field
    function contactRole($bean){
        if(!empty($bean->account_contact_roles)){
            $roles = unencodeMultienum($bean->account_contact_roles);
            if(isset($roles[0])){
                return $roles[0];
            }
        }else{
            if(!empty($bean->title)){
                return $bean->title;
            }
        }
     }

    //XML FUNCTIONS
        //Build Xml for an account
        function buildAccountXML($bean, $request_type, $xmlAccount_details){
            $email = new SugarEmailAddress();
            $email = $email->getPrimaryAddress($bean);

            $shipaddressArray = $this->splitStreetAddress(from_html($bean->shipping_address_street));
            $billaddressArray = $this->splitStreetAddress(from_html($bean->billing_address_street));

            $billing_address_street = empty($billaddressArray[0]) ? '' : $billaddressArray[0];
            $billing_address_street_2 = empty($billaddressArray[1]) ? '' : $billaddressArray[1];
            $billing_address_street_3 = empty($billaddressArray[2]) ? '' : $billaddressArray[2];

            $shipping_address_street = empty($shipaddressArray[0]) ? '' : $shipaddressArray[0];
            $shipping_address_street_2 = empty($shipaddressArray[1]) ? '' : $shipaddressArray[1];
            $shipping_address_street_3 = empty($shipaddressArray[2]) ? '' : $shipaddressArray[2];
            
            $xml_string = '<?xml version="1.0" ?>
                          <message xmlns="urn:CESUGAR">
                           <request requestID="'.$request_type.'">
                            <account
                                '.$xmlAccount_details.'
                                name=           "'.from_html($bean->name).'"
                                billAddress1=	"'.$billing_address_street.'"
                                billAddress2=	"'.$billing_address_street_2.'"
                                billAddress3=	"'.$billing_address_street_3.'"
                                billSuburb=     "'.from_html($bean->billing_address_city).'"
                                billState=      "'.from_html($bean->billing_address_state).'"
                                billPostCode=   "'.$bean->billing_address_postalcode.'"
                                billCountry=	"'.from_html($bean->billing_address_country).'"
                                delAddress1=	"'.$shipping_address_street.'"
                                delAddress2=	"'.$shipping_address_street_2.'"
                                delAddress3=	"'.$shipping_address_street_3.'"
                                delSuburb=      "'.from_html($bean->shipping_address_city).'"
                                delState=	"'.from_html($bean->shipping_address_state).'"
                                delPostCode="'.$bean->shipping_address_postalcode.'"
                                delCountry=	"'.from_html($bean->shipping_address_country).'"
                                email=		"'.from_html($email).'"
                                website=	"'.from_html($bean->website).'"
                                phone=		"'.$bean->phone_office.'"
                                fax=        "'.$bean->phone_fax.'"
                                direct=     "'.$bean->phone_alternate.'"
                                other=      "'.$bean->phone_alternate_2.'"
                                mobile=     "'.$bean->phone_mobile.'"
                                acn=     "'.$bean->acn.'"
                                abn=     "'.$bean->abn.'"'
                                .(isset($bean->sales_rep)? ' debsalesrep= "'.$bean->sales_rep.'"' : '').'
                            />
                            </request>
                          </message>';
            return $xml_string;
    }    
        //END XML Build
        //Build Xml for an account note
        function buildAccountNoteXML($bean, $updating_bean, $user){
            $segment = '';
            $segment .= '<segment>'.wordwrap($bean->description, 1000, '</segment><segment>', false);
            $segment .= '</segment>';
            $createdDate = date('d/m/Y', strtotime($bean->date_modified));
            $xml_note ='<?xml version="1.0" ?><message xmlns="urn:CESUGAR">
                            <request requestID="addAccountNote">
                                <note
                                cardCode=   "'.strtoupper($bean->erp_id).'"
                                createdBy=    "'.from_html($user->full_name).'"
                                createdDate=    "'.$createdDate.'">
                                '.$segment.'
                                </note>
                            </request>
                            </message>';
            return $xml_note;
        }
        //END XML Build
        //Build Xml for a Contact creation or update
        function buildContactXML($bean, $request_type, $user, $contact_details){
            if(isset($bean->account_id) && !empty($bean->account_id)){
                $account = new Account();
                $account = $account->retrieve($bean->account_id);
                //adding ordering of contacts to attempt to maintain some consistency 
                //when comparing contacts that have the same priorty.
                $account_contacts = $account->get_linked_beans('contacts','Contact');
                //sugar bean no longer supports the sort parameter
                //apply our own sorting function
                usort($account_contacts, function($a, $b) {
                    return strcasecmp($a->last_name, $b->last_name);
                });
            }
            
            $isPrimary = $this->isPrimaryContact($bean, $account_contacts);
            if(!isset($isPrimary)){
                $isPrimary = 'N';
            }

            $role = $this->contactRole($bean);

            $email = new SugarEmailAddress();
            $email = $email->getPrimaryAddress($bean);

            $addressArray = $this->splitStreetAddress(from_html($bean->primary_address_street));

            $primary_address_street = empty($addressArray[0]) ? '' : $addressArray[0];
            $primary_address_street_2 = empty($addressArray[1]) ? '' : $addressArray[1];
            $primary_address_street_3 = empty($addressArray[2]) ? '' : $addressArray[2];

            $xml_string = '<?xml version="1.0"><message xmlns="urn:CESUGAR">
                            <request requestID="'.$request_type.'">
                            <contact
                                '.$contact_details.'
                                title=          "'.$role.'"
                                salutation=     "'.from_html($bean->salutation).'"
                                firstName=      "'.from_html($bean->first_name).'"
                                lastName=       "'.from_html($bean->last_name).'"
                                address1=       "'.$primary_address_street.'"
                                address2=       "'.$primary_address_street_2.'"
                                address3=       "'.$primary_address_street_3.'"
                                suburb=         "'.from_html($bean->primary_address_city).'"
                                state=          "'.from_html($bean->primary_address_state).'"
                                postCode=       "'.$bean->primary_address_postalcode.'"
                                country=        "'.from_html($bean->primary_address_country).'"
                                email=          "'.from_html($email).'"
                                isPrimary=      "'.$isPrimary.'"
                                phone=          "'.$bean->phone_work.'"
                                fax=            "'.$bean->phone_fax.'"
                                direct=         "'.$bean->phone_other.'"
                                other=          "'.$bean->phone_home.'"
                                mobile=         "'.$bean->phone_mobile.'"/>
                                </request>
                           </message>';
            return $xml_string;
        }
        //END XML Build       
        //Build Xml for removing a contact
        function buildContactRemoveXML($contact, $account){
            $xml_string = '<?xml version="1.0" ?>
                            <message xmlns="urn:CESUGAR">
                                <request requestID="removeContact">
                                <contact
                                    cardCode="'.strtoupper($account->erp_id).'"
                                    contactID="'.$contact->erp_id.'"
                                    title="'.from_html($contact->salutation).'"
                                    firstName="'.from_html($contact->first_name).'"
                                    lastName="'.from_html($contact->last_name).'"
                                />
                                </request>
                             </message>';
            return $xml_string;
        }
        //END XML Build
    //END XML FUNCTIONS
    //Send Post to ClearERP for both accounts and contacts
    function sendRequest($bean, $xml_string){
        if(isset($xml_string) && !empty($xml_string)){
            $GLOBALS['log']->debug('XML to be Passed to Clear: '.$xml_string);
            //sugar_die("XML to be Passed to Clear: '.$xml_string");
            $httpRequest = new httpRequest($this->url, HTTP_METH_POST);
            $httpRequest->setContentType = 'Content-Type: text/xml';
            $httpRequest->setRawPostData($xml_string);
            //check Responsecode to make sure the post was sent correctly
            try {
                $result = $httpRequest->send();
                if ($httpRequest->getResponseCode() == 200) {
                    //successful;
                    $requestResult = true;
                }else{
                    $requestResult = 'Fail '.$httpRequest->getResponseCode().')';
                }
            } catch (HttpException $ex){
                $requestResult = (string)$ex;
            }
            if($requestResult === true){
                //Request was successful: continue
                $GLOBALS['log']->debug("XML HttpRequest: successful.");
                //sugar_die("HttpRequest: successful");
                $xml = $httpRequest->getResponseBody();
                $GLOBALS['log']->debug("XML Http Response: $xml");
                //turn respone body into xml object
                $xml = simplexml_load_string($xml);
                //retrieve all the attributes
                $attributes = $xml->result->attributes();
                $attri = array();

                foreach($attributes as $name => $value){
                    $attri[$name] = $value;
                }

                if(isset($attri['desc'])){
                    $desc = $attri['desc'];
                }

                $status = $attri['status'];
                $statusLong = $attri['statusLong'];

                if($status == 'OK'){
                    if(empty($bean->erp_id) ){
                        //get the cardcode/contactID to then be used in the update
                        if($bean->object_name == 'Account' && isset($attri['cardCode'])){
                            $bean->erp_id = $attri['cardCode'];
                            if(!empty($bean->erp_id)){
                                $bean->code = $bean->erp_id;
                            }
                        } else if ($bean->object_name == 'Contact' && isset($attri['contactID'])){
                            $bean->erp_id = $attri['contactID'];
                        }

                        $GLOBALS['log']->debug("XML Post status ($bean->object_name : $bean->name): $status : $statusLong");
                        //sugar_die("XML Post status: $status : $statusLong");
                        //resaving of bean with new values
                        $bean->save($check_notify=false, $erp_notify = false);
                    }else{
                             $GLOBALS['log']->debug("XML Post status ($bean->object_name : $bean->name): $status : $statusLong");
                    }
                    return true;
                }else{
                    if(isset($desc)){
                      $GLOBALS['log']->debug("XML Error: $desc");
                      sugar_die("XML Error: $desc");
                    }
                    $GLOBALS['log']->debug("XML Post status: $status : Able to connect to ClearERP but the following stoped the XML request being successful ($bean->object_name : $bean->name): $statusLong");
                    if($statusLong == 'General Failure creating new record in database - possible duplicate cardCode'){
                        //resave with account code empty as its a duplicate and we dont want it to have that code
                        $bean->erp_id = '';
                        $bean->code = '';
                        $bean->save($check_notify=false, $erp_notify = false);
                    }
                    sugar_die("XMLPost status: $status : Able to connect to ClearERP but the following stoped the XML request being successful ($bean->object_name : $bean->name): $statusLong");


                }
                //return true;
            }else{
                 $GLOBALS['log']->debug("XML HttpRequest Failed: {$requestResult}. ($bean->object_name) {$bean->name} was still created in Sugar.");
                 sugar_die("HttpRequest Failed: {$requestResult}. ($bean->object_name) {$bean->name} was still created in Sugar.");
                 return false;
            }
        }else{
            $GLOBALS['log']->debug('XML ERROR: XML string was not created');
             sugar_die("XML string was not created");
            return false;
        }
    }
    //END


}

