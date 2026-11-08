<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 *
 *
 *
 * This class overrides SugarView and provides an implementation for the AgentDetails method
 * used to retrieve details about the agent for this account.
 * We take advantage of the MVC framework to provide this action which is invoked from
 * a javascript AJAX request.
 * */

require_once('include/MVC/View/SugarView.php');

class LocationsViewLoad_Account_Locations extends SugarView {

 	function LocationsViewLoad_Account_Locations(){
 		parent::SugarView();
 	}

 	function process() {
		$this->display();
 	}

 	function display() {
            if(empty($_REQUEST['account_id'])){
                
                if($_REQUEST['contact_only']){
                    return $this->getContactLocation($_REQUEST['contact_id']);
                }
                return false;
            }

            if(!module_enabled("Locations")){
                return false;
            }

            $data = array();

           
            $account = BeanFactory::getBean('Accounts',$_REQUEST['account_id']);
            
            $address_list = Location::return_location(null, $_REQUEST['account_id']);
            
            if(!empty($_REQUEST['contact_id'])){
                $contact = BeanFactory::getBean('Contacts', $_REQUEST['contact_id']);   
                if(!empty($contact->primary_location_id)){
                    $contact_primary_location = BeanFactory::getBean('Locations', $contact->primary_location_id);

                    if(empty($contact_primary_location->account_id) || $contact_primary_location->account_id == $_REQUEST['account_id']){
                        //add the address to the end of the array
                        array_push($address_list,$contact_primary_location);
                    }
                }
                $billing_location_id =  empty($contact_primary_location->id)? $account->billing_location_id:$contact_primary_location->id;
                $shipping_location_id =  empty($contact_alt_location->id)? $account->shipping_location_id:$contact_alt_location->id;
            }else{
                //set active location to the accounts
                $billing_location_id = $account->billing_location_id;
                $shipping_location_id = $account->shipping_location_id;
            }
                        
            
            if(!empty($address_list)){
                foreach($address_list as $address){
                     $data['address'][$address->id] =
                        array(
                            'name'=>$address->name,
                            '_address_building_type' => $address->building_type,
                            '_address_number' => $address->number,
                            '_address_street'=>trim($address->address1."\n".$address->address2),
                            '_address_city'=>$address->city,
                            '_address_state'=>$address->state,
                            '_address_postalcode'=>$address->postalcode,                            
                            '_address_country'=>$address->country,
                        );
                     if(!empty($address->address2)){
                        $data['address'][$address->id]['_address_street']  = trim($address->address1)."\n".trim($address->address2);
                    }
                     
                     
                     
                }
                if(isset($billing_location_id)){
                    $data['address']['active_billing'] = $billing_location_id;                   
                }
                if(!empty($shipping_location_id)){
                    $data['address']['active_shipping'] = $shipping_location_id;
                }
            }
            
            if(empty($data)){
                return false;
            }


            ob_clean();
            $json = getJSONobj();
            $decoded = array_map('from_html', $data); // Need to decode from html as the db manager encodes values by default
            $out = $json->encode($decoded);
            echo($out);
 	}
        
        
        function getContactLocation(){
            $data = array();
            $contact = BeanFactory::getBean('Contacts', $_REQUEST['contact_id']);   
            if(!empty($contact->primary_location_id)){
                $contact_primary_location = BeanFactory::getBean('Locations', $contact->primary_location_id);
                if(!empty($contact_primary_location->id)){
                    $data['billing'][$contact_primary_location->id] =
                        array(
                            'name'=>$contact_primary_location->name,
                            'billing_address_building_type' => $contact_primary_location->building_type,
                            'billing_address_number' => $contact_primary_location->number,
                            'billing_address_street'=>trim($contact_primary_location->address1."\n".$contact_primary_location->address2),
                            'billing_address_city'=>$contact_primary_location->city,
                            'billing_address_state'=>$contact_primary_location->state,
                            'billing_address_postalcode'=>$contact_primary_location->postalcode,                            
                            'billing_address_country'=>$contact_primary_location->country,
                        );
                    if(!empty($contact_primary_location->address2)){
                        $data['billing'][$contact_primary_location->id]['billing_address_street']  = trim($contact_primary_location->address1)."\n".trim($contact_primary_location->address2);
                    }
                }
            }
            if(!empty($contact->alt_location_id)){
                $contact_alt_location = BeanFactory::getBean('Locations', $contact->alt_location_id);
                if(!empty($contact_alt_location->id)){
                    $data['shipping'][$contact_alt_location->id] =
                        array(
                            'name'=>$contact_alt_location->name,
                            'shipping_address_building_type' => $contact_alt_location->building_type,
                            'shipping_address_number' => $contact_alt_location->number,
                            'shipping_address_street'=>trim($contact_alt_location->address1),
                            'shipping_address_city'=>$contact_alt_location->city,
                            'shipping_address_state'=>$contact_alt_location->state,
                            'shipping_address_postalcode'=>$contact_alt_location->postalcode,
                            'shipping_address_country'=>$contact_alt_location->country,
                        );
                    if(!empty($contact_alt_location->address2)){
                        $data['shipping'][$contact_alt_location->id]['shipping_address_street']  = trim($contact_alt_location->address1)."\n".trim($contact_alt_location->address2);
                    }
                }
            }
             if(empty($data)){
                return false;
            }


            
              
            
            ob_clean();
            $json = getJSONobj();
            $decoded = array_map('from_html', $data); // Need to decode from html as the db manager encodes values by default
            $out = $json->encode($decoded);
            echo($out);
        }
}

?>
