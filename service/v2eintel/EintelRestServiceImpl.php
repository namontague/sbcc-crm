<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * This class is an implemenatation class for all the rest services
 */
require_once('service/v2eintel/EintelWebServiceImpl.php');
// Need this so that our $helperObject is not overwritten later
require_once('service/core/SugarRestServiceImpl.php');
class EintelRestServiceImpl extends EintelWebServiceImpl {
	
	function md5($string){
		return md5($string);
	}
}
require_once('service/v2eintel/EintelHelperRest.php');
SugarWebServiceImpl::$helperObject = new EintelHelperRest();
