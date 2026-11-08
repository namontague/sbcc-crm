<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('modules/ERPSync/ERPNotify.php');

class OpenbravoERP extends ERPNotify {

    var $url;
    var $password;
    var $username;
    var $json;
    //Setup up connection authentication
    function __construct($url, $username = null, $password = null) {
        global $sugar_config;
        $this->json = getJSONobj();
        $this->username = $username;
        $this->password = $password;
        $this->base_url = $url;
        $this->url = '';
        if(substr($this->base_url, -1) != '/'){
            $this->base_url = $this->base_url.'/';
        }
        $this->businesspartner_url = "{$this->base_url}ws/au.com.eintel.openbravo.service.web.core.businesspartner/";
        $this->service_url = "{$this->base_url}ws/au.com.eintel.openbravo.service.web.core.order/";
        $this->product_url = "{$this->base_url}ws/au.com.eintel.openbravo.service.web.core.product/";

        $this->sales_order_link = "{$this->base_url}?tabId=186&recordId=";


    }
    //END
    function accountRequiredFields() {
      return array('code','account_type');
    }

    function accountCategoryOptions($category) {
        $options = array(
            'Both' => 'B',
            'Customer' => 'D',
            'Employee' => 'E',
            'Supplier' => 'C',
            'Stardard' => 'stardard'
        );

         if(isset($options[$c])){
            return $options[$c];
         }else{
           return 'stardard';
         }
    }

  //ERPSync Fucntions
    function createAccount($bean){
        $this->url = $this->businesspartner_url;

        $account = array(
            'searchKey' => $bean->code,
            'name' => $bean->name,
            'description' => $bean->description,
            'taxID' => $bean->abn,
           // 'category' => $this->accountCategoryOptions($bean->account_type),
            'category' => $bean->account_type,
            'url' => $bean->website
        );
        
        if(module_enabled('Locations')){
            //use the locations module, get erp id
            $billing = BeanFactory::getBean('Locations',$bean->billing_location_id);
            $shipping = BeanFactory::getBean('Locations',$bean->shipping_location_id);

            if($billing->id == $shipping->id){
                 $account['locations'][0] = array(
                    'line1' => $billing->address1,
                    'line2' => $billing->address2,
                    'city' => $billing->city,
                    'region' => strtoupper($billing->state),
                    'postCode' => $billing->postalcode,
                    'country' => $billing->country,
                    'phone' => $billing->phone,
                    'alternativePhone' => $billing->alternate_phone,
                    'fax' => $billing->fax,
                    'shipTo' => true,
                    'invoiceTo' => true,
                );
            }else{
                if(isset($billing)){
                    $account['locations'][0] = array(
                        'line1' => $billing->address1,
                        'line2' => $billing->address2,
                        'city' => $billing->city,
                        'region' => strtoupper($billing->state),
                        'postCode' => $billing->postalcode,
                        'country' => $billing->country,
                        'phone' => $billing->phone,
                        'alternativePhone' => $billing->alternate_phone,
                        'fax' => $billing->fax,
                        'shipTo' => false,
                        'invoiceTo' => true,
                    );
                }
                if(isset($shipping)){
                    $account['locations'][1] = array(
                        'line1' => $shipping->address1,
                        'line2' => $shipping->address2,
                        'city' => $shipping->city,
                        'region' => strtoupper($shipping->state),
                        'postCode' => $shipping->postalcode,
                        'country' => $shipping->country,
                        'phone' => $shipping->phone,
                        'alternativePhone' => $shipping->alternate_phone,
                        'fax' => $shipping->fax,
                        'shipTo' => true,
                        'invoiceTo' => false,
                    );
                }
            }
        }else{
            //Add location
            if($bean->billing_address_street == $bean->shipping_address_street && $bean->billing_address_city == $bean->shipping_address_city &&
                 $bean->billing_address_state == $bean->shipping_address_state && $bean->billing_address_postalcode == $bean->shipping_address_postalcode &&
                   $bean->billing_address_country == $bean->shipping_address_country
                    ){
                //billing and shipping is the same
                $account['locations'][0] = array(
                    'name' => '',
                    'line1'=> $bean->billing_address_street,
                    'line2'=> '',
                    'city'=> $bean->billing_address_city,
                    'region'=> $bean->billing_address_state,//state
                    'postCode'=> $bean->billing_address_postalcode,
                    'country'=> 'AU',
                    'phone'=> $bean->phone_office,
                    'alternativePhone'=> $bean->phone_alternate,
                    'fax'=> $bean->phone_fax,
                    'shipTo'=> true,
                    'invoiceTo'=> true
                );
            }else{
                //billing and shipping are different
                //billing
                $account['locations'][0] = array(
                    'name' => '',
                    'line1'=> $bean->billing_address_street,
                    'line2'=> '',
                    'city'=> $bean->billing_address_city,
                    'region'=> $bean->billing_address_state,//state
                    'postCode'=> $bean->billing_address_postalcode,
                    'country'=> 'AU',
                    'phone'=> $bean->phone_office,
                    'alternativePhone'=> $bean->phone_alternate,
                    'fax'=> $bean->phone_fax,
                    'shipTo'=> false,
                    'invoiceTo'=> true,
                );
                 //ship
                $account['locations'][1] = array(
                    'name' => '',
                    'line1'=> $bean->shipping_address_street,
                    'line2'=> '',
                    'city'=> $bean->shipping_address_city,
                    'region'=> $bean->shipping_address_state,//state
                    'postCode'=> $bean->shipping_address_postalcode,
                    'country'=> 'AU',
                    'phone'=> $bean->phone_office,
                    'alternativePhone'=> $bean->phone_alternate,
                    'fax'=> $bean->phone_fax,
                    'shipTo'=> true,
                    'invoiceTo'=> false
                );

            }
        }

       $this->sendRequest($bean, $account, HTTP_METH_PUT);


    }

    function updateAccount($bean){
        $this->url = $this->businesspartner_url.$bean->erp_id;
        $account = array(
            'searchKey' => $bean->code,
            'name' => $bean->name,
            'description' => $bean->description,
            'taxID' => $bean->abn,
            'category' => $bean->account_type,//$this->accountCategoryOptions($bean->account_type),
            'url' => $bean->website,
        );
        if(module_enabled('Locations')){
            //use the locations module, get erp id
            $billing = BeanFactory::getBean('Locations',$bean->billing_location_id);
            $shipping = BeanFactory::getBean('Locations',$bean->shipping_location_id);

            if($billing->id == $shipping->id){
                 $account['locations'][0] = array(
                    'line1' => $billing->address1,
                    'line2' => $billing->address2,
                    'city' => $billing->city,
                    'region' => $billing->state,
                    'postCode' => $billing->postalcode,
                    'country' => $billing->country,
                    'phone' => $billing->phone,
                    'alternativePhone' => $billing->alternate_phone,
                    'fax' => $billing->fax,
                    'shipTo' => true,
                    'invoiceTo' => true,
                );
            }else{
                if(isset($billing)){
                    $account['locations'][0] = array(
                        'line1' => $billing->address1,
                        'line2' => $billing->address2,
                        'city' => $billing->city,
                        'region' => $billing->state,
                        'postCode' => $billing->postalcode,
                        'country' => $billing->country,
                        'phone' => $billing->phone,
                        'alternativePhone' => $billing->alternate_phone,
                        'fax' => $billing->fax,
                        'shipTo' => false,
                        'invoiceTo' => true,
                    );
                }
                if(isset($shipping)){
                    $account['locations'][1] = array(
                        'line1' => $shipping->address1,
                        'line2' => $shipping->address2,
                        'city' => $shipping->city,
                        'region' => $shipping->state,
                        'postCode' => $shipping->postalcode,
                        'country' => $shipping->country,
                        'phone' => $shipping->phone,
                        'alternativePhone' => $shipping->alternate_phone,
                        'fax' => $shipping->fax,
                        'shipTo' => true,
                        'invoiceTo' => false,
                    );
                }
            }
        }else{
             //Add location
            if($bean->billing_address_street == $bean->shipping_address_street && $bean->billing_address_city == $bean->shipping_address_city &&
                 $bean->billing_address_state == $bean->shipping_address_state && $bean->billing_address_postalcode == $bean->shipping_address_postalcode &&
                   $bean->billing_address_country == $bean->shipping_address_country
                    ){
                //billing and shipping is the same
                $account['locations'][0] = array(
                    'name' => '',
                    'line1'=> $bean->billing_address_street,
                    'line2'=> '',
                    'city'=> $bean->billing_address_city,
                    'region'=> $bean->billing_address_state,//state
                    'postCode'=> $bean->billing_address_postalcode,
                    'country'=> 'AU',
                    'phone'=> $bean->phone_office,
                    'alternativePhone'=> $bean->phone_alternate,
                    'fax'=> $bean->phone_fax,
                    'shipTo'=> true,
                    'invoiceTo'=> true
                );
            }else{
                //billing and shipping are different
                //billing
                if(!empty($bean->billing_address_street)){
                    $account['locations'][0] = array(
                        'name' => '',
                        'line1'=> $bean->billing_address_street,
                        'line2'=> '',
                        'city'=> $bean->billing_address_city,
                        'region'=> $bean->billing_address_state,//state
                        'postCode'=> $bean->billing_address_postalcode,
                        'country'=> 'AU',
                        'phone'=> $bean->phone_office,
                        'alternativePhone'=> $bean->phone_alternate,
                        'fax'=> $bean->phone_fax,
                        'shipTo'=> false,
                        'invoiceTo'=> true,
                    );
                }
                 //ship
                if(!empty($bean->shipping_address_street)){
                    $account['locations'][1] = array(
                        'name' => '',
                        'line1'=> $bean->shipping_address_street,
                        'line2'=> '',
                        'city'=> $bean->shipping_address_city,
                        'region'=> $bean->shipping_address_state,//state
                        'postCode'=> $bean->shipping_address_postalcode,
                        'country'=> 'AU',
                        'phone'=> $bean->phone_office,
                        'alternativePhone'=> $bean->phone_alternate,
                        'fax'=> $bean->phone_fax,
                        'shipTo'=> true,
                        'invoiceTo'=> false
                    );
                }

            }
        }
        $sent = $this->sendRequest($bean, $account, HTTP_METH_POST);
    }

    function createContact($bean){
        $this->loadAccount($bean);

        $this->url = "{$this->businesspartner_url}{$bean->account_bp->erp_id}/contact";

        $contact = array(
                'firstName' => $bean->first_name,
                'lastName' => $bean->last_name,
                'phone' => $bean->phone_work,
                'alternativePhone' => $bean->phone_other,
                'email' => $bean->email1,
                'position' => $bean->title,
                'comments' => $bean->description,
                'fax' => $bean->phone_fax,
                'mobile' => $bean->phone_mobile,
            );
        $this->sendRequest($bean, $contact, HTTP_METH_PUT);

    }

    function addContactToAccount($contact, $account){
        $this->createContact($contact);
    }

    function updateContact($bean){
        $this->loadAccount($bean);

        $this->url = "{$this->businesspartner_url}{$bean->account_bp->erp_id}/contact/{$bean->erp_id}";
        $contact = array(
                'firstName' => $bean->first_name,
                'lastName' => $bean->last_name,
                'phone' => $bean->phone_work,
                'alternativePhone' => $bean->phone_other,
                'email' => $bean->email1,
                'position' => $bean->title,
                'comments' => $bean->description,
                'fax' => $bean->phone_fax,
                'mobile' => $bean->phone_mobile,
            );
        $this->sendRequest($bean, $contact, HTTP_METH_POST);
        $bean->save(false, false);
    }


    function deleteContact($bean){
        $this->loadAccount($bean);

        if(isset($bean->account_bp->erp_id)){
            $this->url = "{$this->businesspartner_url}{$bean->account_bp->erp_id}/contact/{$bean->erp_id}";
            $contact = array();
            return $this->sendRequest($bean, $contact, HTTP_METH_DELETE);
        }
    }

    function removeContactFromAccount($bean, $account){
        $this->url = "{$this->businesspartner_url}{$account->erp_id}/contact/{$bean->erp_id}";
        $contact = array();
        return $this->sendRequest($bean, $contact, HTTP_METH_DELETE);

    }

    function loadAccount($bean){
        $account = new Account();
        $account = $account->retrieve($bean->account_id);
        $bean->account_bp = $account;
    }







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

    /*
      Retrieve a Business Partner
        URI : <business partner url>/<business partner identifier>
        Operation: GET
    */

    function retrieve_business_partner($bean){
        $this->loadAccount($bean);

        $this->url = $this->businesspartner_url.$bean->account_bp->erp_id;

        return $this->sendRequest($bean, null, HTTP_METH_GET,true);

    }



    //Create a Business Partner
    //URI : <business partner url>
    //Operation: PUT


    //Update an existing Business Partner
    //URI : <business partner url>/<business partner identifier>
    //Operation: POST


    //Retrieve a list of Business Partners
    //URI : <business partner url>
    //Operation: GET

    //Retrieve  Business Partner Contacts
    //URI : <business partner url>/<business partner identifier>/contact
    //Operation: GET


    //Create a Business Partner Contact
    //URI : <business partner url>/<business partner identifier>/contact
    //Operation: PUT


    //Update an existing Business Partner Contact
    //URI : <business partner url>/<business partner identifier>/contact/<contact id>
    //Operation: POST


    //Remove an existing Business Partner Contact
    //URI : <business partner url>/<business partner identifier>/contact/<contact id>
    //Operation: DELETE






    /*
      Orders Web Service
         * Retrieve a Sales Order
         * Create a Sales Order
         * Update an existing Sales Order
         * Retrieve a list of  Sales Order
     */

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
    function create_sales_order($bean, $line_items){
        $bean->bp = $this->retrieve_business_partner($bean);
        $this->url = "{$this->service_url}sales";

        if(!isset($bean->bp->id)){
            //no pricelist cannot create sales order
            return false;
        }
        if(!isset($bean->bp->customer->priceList->id)){
            //no pricelist cannot create sales order
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

        if(!empty($lines)){
            $sales_order = array(
              'transactionDocument' => 'Standard Order',
              'orderDate' => date('Y-m-d',time()),
              'scheduledDeliveryDate' => date('Y-m-d',time()),
              'businessPartner' => $bean->bp->id,
              'currency' => 'AUD',
              'invoiceAddress' => array(
                  'id' => $invoice_id
              ),
              'deliveryAddress' => array(
                  'id' => $deliver_id
              ),
              'priceList' => $bean->bp->customer->priceList->id,
              'paymentMethod'=> $bean->bp->customer->paymentMethod->id,
              'paymentTerms' => $bean->bp->customer->paymentTerms->id,
              'lines' =>  $lines,
            );
        }else{
            //no lines, no sales order created
            return false;
        }


       return $this->sendRequest($bean, $sales_order, HTTP_METH_PUT,false,false);
    }

    function add_invoice_lines($line_items){
        global $app_list_strings;
        $line_number = 1;
        $lines = array();

        foreach($line_items as $item){
            $lines["$line_number"] =
                    array(
                       'product' => $item->product_code, //COPYCOST//LABOUR //PART
                       'description' => $item->description,
                       'UOM' => '101',
                       'unitPrice' => (!empty($item->unit_price))?$item->unit_price:0,
                       'quantity' => (!empty($item->quantity))?$item->quantity:0,
                       'lineNetAmount' => (!empty($item->quantity) && !empty($item->unit_price))? $item->quantity * $item->unit_price:0.00,
                       'tax' => '4028C9B8301AFBAA01301B1DF8B70616' //GST
                    );
            $line_number++;
        }
        return $lines;
    }


    //Update an existing Sales Order
    //URI : <order url>/sales/<order identifier>
    //Operation: POST
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

        $this->sendRequest($bean, $sales_order, HTTP_METH_POST);
    }



    //Retrieve a list of  Sales Order
    //URI : <order url>/sales
    //Operation: GET
    function retrieve_list_sale_order($bean){
        $this->url = "{$this->service_url}sales";

        $sales_order = array();

        $this->sendRequest($bean, $sales_order, HTTP_METH_GET, true);
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
        $this->url = "{$this->product_url}{$bean->erp_id}";

        $product_list = array(
            // 'searchKey' => $bean->code,
            //'name' => $bean->name,
        );

        return $this->sendRequest($bean, $product_list, HTTP_METH_GET, true);
    }



    //Retrieve a list of Products
    //URI : <product url>
    //Operation: GET
    function retrieve_list_of_product($bean = null, $parameters = array()){
        if(substr($this->product_url, -1) == '/'){
            $this->product_url = substr($this->product_url,0,-1);
        }
        $this->url = "{$this->product_url}";

        if($parameters){
            $param_str = "?";
            $first = true;
            foreach($parameters as $name => $value){
                $separator = '&';
                if($first){
                    $separator = '';
                    $first = false;
                }
                $param_str .= "{$separator}{$name}={$value}";
            }
            $this->url = "{$this->url}{$param_str}";
        }

        $product_list = array();

        return $this->sendRequest($bean, $product_list, HTTP_METH_GET, true);
    }



    //Retrieve  Product Attachment
    //URI : <product url>/<product identifier>/attachment/<attachment id>
    //Operation: GET










//Retrieve  Business Partner Locations
    //API
    //URI : <business partner url>/<business partner identifier>/location
    //Operation: GET
    //Returns:
    function retrieve_business_partner_location($bean){
        $this->url = "{$this->businesspartner_url}{$bean->erp_id}/location";
        $location_list = array();
        return $this->sendRequest($bean, $location_list, HTTP_METH_GET, true);
    }

    //Create a Business Partner Location
    //API
    //URI : <business partner url>/<business partner identifier>/location
    //Operation: PUT
    function create_business_partner_location($bean){
        $this->loadAccount($bean);
        $this->url = "{$this->businesspartner_url}{$bean->account_bp->erp_id}/location";

        $location = array(
            'line1' => $bean->address1,
            'line2' => $bean->address2,
            'city' => $bean->city,
            'region' => strtoupper($bean->state),
            'postCode' => $bean->postalcode,
            'country' => $bean->country,
            'phone' => $bean->phone,
            'alternativePhone' => $bean->alternate_phone,
            'fax' => $bean->fax,
            'shipTo' => ($bean->isshipping)? true:false,
            'invoiceTo' => ($bean->isbilling)?true:false,
        );

         return $this->sendRequest($bean, $location, HTTP_METH_PUT, false, false);



    }


    //Update an existing Business Partner Location
    //API
    //URI : <business partner url>/<business partner identifier>/location/<location id>
    //Operation: POST
    //Data:
    function update_business_partner_location($bean){
        $this->loadAccount($bean);
        $this->url = "{$this->businesspartner_url}{$bean->account_bp->erp_id}/location/{$bean->erp_id}";

        $location = array(
            'line1' => $bean->address1,
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
        );
        return $this->sendRequest($bean, $location, HTTP_METH_POST, true, true);
    }

    //Remove an existing Business Partner Location
    //API
    //URI : <business partner url>/<business partner identifier>/location/<location id>
    //Operation: DELETE
    //Data:
    function delete_business_partner_location($bean){
        $this->loadAccount($bean);
        $this->url = "{$this->businesspartner_url}{$bean->account_bp->erp_id}/location/{$bean->erp_id}";

        return $this->sendRequest($bean, $location, HTTP_METH_DELETE);
    }









    function sendRequest($bean, $json_string, $request_type, $retrieve = false, $save = true){
            $GLOBALS['log']->debug('JSON to be Passed to Openbravo: '.$json_string);
            $httpRequest = new HttpRequest($this->url, $request_type);
            $httpRequest->setOptions(array('httpauth' => "{$this->username}:{$this->password}",'httpauthtype'=>HTTP_AUTH_BASIC));
            $httpRequest->setContentType('Content-Type: text/json');

            if($request_type == HTTP_METH_PUT){
                //create
                $httpRequest->setPutData($this->json->encode($json_string));
            }
            if($request_type == HTTP_METH_POST){
                //update
                $httpRequest->setRawPostData($this->json->encode($json_string));
            }
            if($request_type == HTTP_METH_DELETE){
                //delete
                $httpRequest->setRawPostData($this->json->encode($json_string));
            }
            //check Responsecode to make sure the post was sent correctly
            try {
                $result = $httpRequest->send();
                if ($httpRequest->getResponseCode() == 200) {
                    //successful;
                    $requestResult = true;
                    $json = $httpRequest->getResponseBody();
                    $json = json_decode($json);

                    if($retrieve){
                        return $json;
                    }

                    if($request_type == HTTP_METH_DELETE){
                         $bean->erp_id = null;
                    }else{
                        //update with returned OB ids
                        $bean->erp_id = $json->id;
                        if($bean->object_name == "ServiceInvoice"){
                            $bean->document_no = $json->documentNo;
                            //$bean->erp_id = $json->id;
                        }
                        if($save){
                            $bean->save($check_notify=false, $erp_notify = false);
                        }
                    }

                    return true;
                }else{
                    $xml = simplexml_load_string($httpRequest->getResponseBody());
                    $requestResult = "ERROR: {$httpRequest->getResponseCode()}, {$xml->message}";
                    $GLOBALS['log']->debug("{$requestResult}");
                    return false;
                }
            } catch (HttpException $ex){
                $requestResult = (string)$ex;
            }
    }
    //END


}

