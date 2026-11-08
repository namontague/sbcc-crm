<?php
/*********************************************************************************
* This file is an customisation to SugarCRM developed by Enterprise Intelligence
* Systems Pty Ltd.  Copyright (C) 2011 Enterprise Intelligence Systems Pty Ltd.
*
* SugarCRM is a customer relationship management program developed by
* SugarCRM, Inc.
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
* FOR A PARTICULAR PURPOSE.  SGNU Affero General Public LicenseLicense for more
* details.
*
* You should have received GNU Affero General Public LicensePublic License along with
* this program; if not, see http://www.gnu.org/licenses or write to the Free
* Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
* 02110-1301 USA.
*
* You can contact Enterprise Intelligence Systems Pty Ltd. headquarters at
* 25/20 Enterprise Dr, Bundoora, Vic, 3083, Australia or at email address
* info@eintel.com.au
*
* The interactive user interfaces in modified source and object code versions
* of this program must display Appropriate Legal Notices, as required undeGNU Affero General Public Licenseeneral Public License version 3.
*
* In accordanGNU Affero General Public Licensee GNU General Public License version 3,
* these Appropriate Legal Notices must retain the display of the "Powered by
* SugarCRM" logo. If the display of the logo is not reasonably feasible for
* technical reasons, the Appropriate Legal Notices must display the words
* "Powered by SugarCRM".
********************************************************************************/
/**
 * Description of GoogleClient
 * get the HTTP request object to call the google REST API
 * retrieve the google docs credentials of the logged in user
 * to use it for Google Login authorization
 *
 * @author eintel
 */

class GoogleDocsFeed {
    var $httpRequest;

    function getHttpRequestObject() {
        if( empty($this->httpRequest)) {
            $process_url = 'https://docs.google.com/feeds/default/private/full';
            $options = array(
    //            'httpauthtype' => HTTP_AUTH_BASIC,
    //            'protocol' => HTTP_VERSION_1_1
            );

            $this->httpRequest = new HttpRequest($process_url, HTTP_METH_GET, $options);
        }
    }

    /*
     * Get the Post HttpRequest object for creating a new google doc
     */
    function getHttpRequestPostObject() {
        $process_url = 'https://docs.google.com/feeds/default/private/full';

        $options = array(
                // 'httpauthtype' => HTTP_AUTH_BASIC,
                // 'protocol' => HTTP_VERSION_1_1
        );

        $this->httpRequest = new HttpRequest($process_url, HTTP_METH_POST, $options);
        
    }

    /*
     * Get the Post HttpRequest object for uploading a new doc to google
     */
    function getHttpRequestPostObjectForUpload($convert = true) {
        // TODO - Retreive this url the correct way as per google spec
        if( $convert) {
            $process_url = 'https://docs.google.com/feeds/upload/create-session/default/private/full';
        } else {
            $process_url = 'https://docs.google.com/feeds/upload/create-session/default/private/full?convert=false';
        }

        $options = array(
                // 'httpauthtype' => HTTP_AUTH_BASIC,
                // 'protocol' => HTTP_VERSION_1_1
        );

        $this->httpRequest = new HttpRequest($process_url, HTTP_METH_POST, $options);

    }
    
    /*
     * Function to call the google doc API for creating a new document
    */
    function getGoogleDocsFeed($auth_token, $search_title) {
        $this->getHttpRequestObject();

        // set the raw post data with the client credentials
        $googledocs_headers = array(
            "Authorization" => "GoogleLogin Auth=".$auth_token,
            "GData-Version" => "3.0",
        );

        $queryOptions = array();
        if($search_title) {
            $queryOptions['title'] = $search_title;
        }

        $this->httpRequest->setQueryData($queryOptions);
        // set any extra HTTP body contents here.
        // $this->httpRequest->addRawPostData($clientlogin_post);
        $this->httpRequest->addHeaders($googledocs_headers);

        $requestResult ='';
        try {
            $this->httpRequest->send();

            if ($this->httpRequest->getResponseCode() == 200) {
                $requestResult = $this->httpRequest->getResponseBody();
                $xml = simplexml_load_string($requestResult);

                // pass back the generated auth id for fututre references
                return $xml;
            }else {
                $requestResult = 'Failed with Response Code: '.$this->httpRequest->getResponseCode();
            }
        }
        catch (HttpException $ex) {
            $requestResult = (string)$ex;
        }

        error_log($requestResult);
    }


    /*
     * Function to create a new google document with the provided title and
     * return the link to the new google document created
     */
    function getCreatedGoogleDocLink($auth_token, $title,$kind='document') {
        $this->getHttpRequestPostObject();

        // set the raw post data with the client credentials
        $googledocs_headers = array(
                "Authorization" => "GoogleLogin Auth=".$auth_token,
                "GData-Version" => "3.0",
                "Content-Type" => "application/atom+xml",
        );
        $this->httpRequest->addHeaders($googledocs_headers);

        $newGoogleDocPostData =
                "<?xml version='1.0' encoding='UTF-8'?>
            <entry xmlns='http://www.w3.org/2005/Atom'>
                <category scheme='http://schemas.google.com/g/2005#kind'
                    term='http://schemas.google.com/docs/2007#{$kind}'/>
                <title>";

        if($title) {
            $newGoogleDocPostData = $newGoogleDocPostData.$title;
        } else {
            $newGoogleDocPostData = $newGoogleDocPostData."test document";
        }
        $newGoogleDocPostData = $newGoogleDocPostData."</title>
            </entry>";

        // set any extra HTTP body contents here.
        $this->httpRequest->setBody($newGoogleDocPostData);


        $requestResult ='';
        try {
            $this->httpRequest->send();

            if ($this->httpRequest->getResponseCode() == 201) {
                $requestResult = $this->httpRequest->getResponseBody();
                $xml = simplexml_load_string($requestResult);
                return $xml;
            }else {
                $requestResult = 'Failed with Response Code: '.$this->httpRequest->getResponseCode();
            }
        }
        catch (HttpException $ex) {
            $requestResult = (string)$ex;
        }

        error_log($requestResult);
    }

    /*
     * Function to upload a new document to google
     * return the link to the new google document created
     */
    function getUploadedGoogleDocLink($auth_token, $fileName, $fileMimeType, $fileLocation, $convert) {

        if(!file_exists($fileLocation)) {
          return false;
        }

        $fileSize = filesize($fileLocation);
        $this->getHttpRequestPostObjectForUpload($convert);

        // set the raw post data with the client credentials
        $googledocs_headers = array(
                "Authorization" => "GoogleLogin Auth=".$auth_token,
                "GData-Version" => "3",
                "Content-Type" => "application/atom+xml",
                "X-Upload-Content-Type" => $fileMimeType,
                "X-Upload-Content-Length" => $fileSize,
        );
        
        $this->httpRequest->addHeaders($googledocs_headers);

        $newGoogleDocPostData ="<?xml version='1.0' encoding='UTF-8'?>
            <entry xmlns='http://www.w3.org/2005/Atom'>
                <title>$fileName</title>
            </entry>";

        // set any extra HTTP body contents here.
        $this->httpRequest->setBody($newGoogleDocPostData);

        try {
            $this->httpRequest->send();


            if ($this->httpRequest->getResponseCode() == 200) {
                $uploadUrl = $this->httpRequest->getResponseHeader('Location');
                return $this->doResumableUpload($auth_token,$uploadUrl, $fileLocation, $fileSize,$fileMimeType);
            }else {
                $requestResult = 'Failed with Response Code: '.$this->httpRequest->getResponseCode();
            }
        }
        catch (HttpException $ex) {
            $requestResult = (string)$ex;
        }

        error_log($requestResult);
    }

    private function doResumableUpload($auth_token, $uploadUrl, $fileLocation, $fileSize,$fileMimeType) {
        $offset = 0;
        $chunkSize = 524288;
        //$chunkSize = 131072;
        $resumeRequest = new HttpRequest($uploadUrl,HTTP_METH_PUT);
        // set the raw post data with the client credentials
        $googledocs_headers = array(
                "Authorization" => "GoogleLogin Auth=".$auth_token,
                "GData-Version" => "3.0",
                "Content-Length" => 0,
                "Content-Type" => $fileMimeType,
          //      "Content-Range" => '',
        );

        $failCount =0;
        do{
          $nextOffset = $offset+$chunkSize;
          if( $nextOffset > $fileSize) {
            $nextOffset = $fileSize;
          }
          $contentRange = $offset."-".($nextOffset-1);
          $googledocs_headers['Content-Length'] = $nextOffset - $offset;
          if($chunkSize < $fileSize) {
            // Only set content range when sending partial request
            $googledocs_headers['Content-Range'] = "bytes ".$contentRange."/".$fileSize;;
          }
          $data = file_get_contents($fileLocation, false, null, $offset, $chunkSize);
          $resumeRequest->setHeaders($googledocs_headers);
          $resumeRequest->setUrl($uploadUrl);
          $resumeRequest->setPutData($data);
          $resumeRequest->send();

          $responseCode = $resumeRequest->getResponseCode();
          if($responseCode == 201) {
            $requestResult = $resumeRequest->getResponseBody();
            $xml = simplexml_load_string($requestResult);
            return $xml;
          } else if( $responseCode== 308) {
            $offset = $nextOffset;
            if( $resumeRequest->getResponseHeader('Location') ) {
              $uploadUrl = $resumeRequest->getResponseHeader('Location');
            }
            $receivedRange = $resumeRequest->getResponseHeader('Range');
          } else if ($responseCode == 503 ) {
            if($failCount > 5) {
              return false;
            }
//            $resumeRequest->setHeaders(array(
//                  "Authorization" => "GoogleLogin Auth=".$auth_token,
//                  "GData-Version" => "3.0",
//                  "Content-Length" => 0,
//                  "Content-Range" => 'bytes */'.$fileSize,
//            ));
//            $resumeRequest->setPutData();
//            $resumeRequest->send();
//            if( $resumeRequest->getResponseHeader('Location') ) {
//              $uploadUrl = $resumeRequest->getResponseHeader('Location');
//            }
//            $receivedRange = $resumeRequest->getResponseHeader('Range');
            $failCount++;
            //try again
          } else {
            // if ($responseCode == 410 || $responseCode == 404 ) {
            return false;
          }
        }while($offset < $fileSize);
    }
}
?>
