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
 * of this program must display Appropriate Legal Notices, as required underGNU Affero General Public Licenseeneral Public License version 3.
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

class GoogleClientLogin {
    var $httpRequest;

    function getHttpRequestObject() {

        $process_url = $GLOBALS['sugar_config']['GoogleDocClientLogin'];

        $options = array(
            'httpauthtype' => HTTP_AUTH_BASIC,
            'protocol' => HTTP_VERSION_1_1
        );

        $this->httpRequest = new HttpRequest($process_url, HTTP_METH_POST, $options);
        $this->httpRequest->setContentType('application/x-www-form-urlencoded');

    }

    /*
     * Function to start a process on google and return back the process id
     * which is used to store as part of the WorkflowEvent record.
     */
    function startGoogleLoginProcess() {

        global $current_user;
        // google login username and password
        $google_username = $current_user->google_username;
        $google_password = $current_user->getGooglePassword();

        // set the raw post data with the client credentials
        $clientlogin_post = array(
            "accountType" => "HOSTED_OR_GOOGLE",
            "Email" => $google_username,
            "Passwd" => $google_password,
            "service" => "writely",
            "source" => "browser"
        );
        // set the raw post data with the client credentials

        $this->httpRequest->addPostFields($clientlogin_post);

        $requestResult = '';
        try {
            $this->httpRequest->send();

            if ($this->httpRequest->getResponseCode() == 200) {
                $requestResult = $this->httpRequest->getResponseBody();
                $lines = preg_split('/[\n\r]+/',$requestResult);
                foreach($lines as $l) {
                    if( ($p = strpos($l,'Auth=')) !== FALSE ) {
                        return substr($l, $p+5);
                    }
                }

                $valueString = explode("Auth=", $requestResult);

                // pass back the generated auth id for fututre references
                return $valueString[1];
            } else {
                $requestResult = 'Failed with Response Code: ' . $this->httpRequest->getResponseCode();
            }
        }
        catch (HttpException $ex) {
            $requestResult = (string)$ex;
        }
        //error_log($requestResult);
    }

}
?>
