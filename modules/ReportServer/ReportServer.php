<?php

require_once('data/SugarBean.php'); /*Include this file since we are extending SugarBean*/
require_once('include/utils.php'); /* Include this file if you want access to Utility methods such as return_module_language,return_mod_list_strings_language, etc ..*/

class ReportServer extends SugarBean {
    var $module_dir = 'ReportServer';
    var $object_name = 'ReportServer';
    //var $table_name = '';

	function bean_implements($interface){
		switch($interface){
			case 'ACL':return true;
		}
		return false;
	}  
}