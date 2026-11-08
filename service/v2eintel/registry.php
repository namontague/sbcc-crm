<?php
if(!defined('sugarEntry'))define('sugarEntry', true);
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
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
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
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
 * This class is responsible for providing all the registration of all the functions and complex types
 *
 */
require_once('service/v2/registry.php');
class registryEintel extends registry {

        public function __construct($serviceClass) {
            parent::__construct($serviceClass);
        }
    
	/**
	 * This mehtod registers all the functions on the service class
	 *
	 */
	protected function registerFunction() {
            // START OF REGISTER FUNCTIONS
            parent::registerFunction();
            $GLOBALS['log']->info('Begin: registryEintel->registerFunction');

            $this->serviceClass->registerFunction(
		  	'portal_login',
		     array('user_auth'=>'tns:user_auth', 'application_name'=>'xsd:string', 'auth_modules'=>'tns:select_modules'),
		     array('return'=>'tns:entry_value')
		     );

            $this->serviceClass->registerFunction(
		  	'portal_change_password',
		     array('session'=>'xsd:string', 'password'=>'xsd:string'),
		     array()
		     );
     		
            
            $this->serviceClass->registerFunction(
                    'sync_ob_new_businessparnter',
		     array(
                         'session'=>'xsd:string',
                         'data_value_list'=>'tns:name_value_list' 
                        ),
		     array(
                         'return' => 'xsd:string'
                        )
		     );
            $this->serviceClass->registerFunction(
                    'sync_ob_businessparnter',
		     array(
                         'session'=>'xsd:string',
                         'data_value_list'=>'tns:name_value_list' 
                        ),
		     array(
                         'return' => 'xsd:string'
                        )
		     );
            $this->serviceClass->registerFunction(
                    'sync_ob_businessparnter_contact',
		     array(
                         'session'=>'xsd:string',
                         'data_value_list'=>'tns:name_value_list'
                         ),
		     array(
                         'return' => 'xsd:string'
                        )
		     );
            $this->serviceClass->registerFunction(
                    'sync_ob_businessparnter_location',
		     array(
                         'session'=>'xsd:string',
                         'data_value_list'=>'tns:name_value_list'),
		     array(
                         'return' => 'xsd:string'
                     )
		     );
            
	    $GLOBALS['log']->info('END: registryEintel->registerFunction');
	        
		// END OF REGISTER FUNCTIONS
	} // fn	

	/**
	 * This method registers all the complex types
	 *
	 */
	protected function registerTypes() {

        parent::registerTypes();
		// START OF REGISTER COMPLEX TYPES
		
		$GLOBALS['log']->info('Begin: registryEintel->registerTypes');

        $this->serviceClass->registerType(
		    'select_modules',
			'complexType',
		   	 'array',
		   	 '',
		  	  'SOAP-ENC:Array',
			array(),
		    array(
		        array('ref'=>'SOAP-ENC:arrayType', 'wsdl:arrayType'=>'xsd:string[]')
		    ),
			'xsd:string'
		);


		$GLOBALS['log']->info('End: registryEintel->registerTypes');

		// END OF REGISTER COMPLEX TYPES
	} // fn
} // clazz
