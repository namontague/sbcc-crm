<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('service/core/SoapHelperWebService.php');
class EintelHelperWebService extends SoapHelperWebServices {

	/**
	 * Validate the provided session information is correct and current.  Load the session.
     * Override base validation to allow portal_xxx logons
	 *
	 * @param String $session_id -- The session ID that was returned by a call to login.
	 * @return true -- If the session is valid and loaded.
	 * @return false -- if the session is not valid.
	 */
	function validate_authenticated($session_id){
		$GLOBALS['log']->info('Begin: EintelHelperWebService->validate_authenticated');
		if(!empty($session_id) ){
			session_id($session_id);
			session_start();
            $session_types = array('user','portal_user','portal_contact','portal_account');
			if(!empty($_SESSION['is_valid_session']) && $_SESSION['ip_address'] == query_client_ip() && in_array($_SESSION['type'], $session_types)){
				global $current_user;
				require_once('modules/Users/User.php');
				$current_user = new User();
				$current_user->retrieve($_SESSION['user_id']);
				$this->login_success();
				$GLOBALS['log']->info('End: EintelHelperWebService->validate_authenticated - passed');
				return true;
			}

			$GLOBALS['log']->debug("calling destroy");
			session_destroy();
		}
		LogicHook::initialize();
		$GLOBALS['logic_hook']->call_custom_logic('Users', 'login_failed');
		$GLOBALS['log']->info('End: EintelHelperWebService->validate_authenticated - validation failed');
		return false;
	}


}