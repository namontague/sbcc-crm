<?php
require_once('service/v2eintel/EintelHelperWebService.php');
class EintelHelperRest extends EintelHelperWebService {

	function get_name_value($field,$value){
		return  $value;
	}
}