<?php

require_once('modules/ERPSync/ERPNotify.php');
require_once('data/SugarBean.php'); /*Include this file since we are extending SugarBean*/
require_once('include/utils.php'); /* Include this file if you want access to Utility methods such as return_module_language,return_mod_list_strings_language, etc ..*/
require_once('modules/ERPSync/ClearERP/ClearERP.php');
require_once('modules/ERPSync/OpenbravoERP/OpenbravoERP.php');

class ERPSync extends SugarBean {
    var $module_dir = 'ERPSync';
    var $object_name = 'ERPSync';
//    var $table_name = '';

    function ERPSync() {
        $this->disable_vardefs = true;
        $this->disable_custom_fields = true;
        parent::SugarBean();
    }

    static function getERPNotify(){
        static $erp;
        if( $erp ) {
            return $erp;
        }
        global $sugar_config;
        $erp_type = $sugar_config['erpsync_type'];


        if(!empty($erp_type)){
            if( empty($sugar_config['erpsync_url'])){
                sugar_die('Must set server URL to enable ERP synchronisation');
            }
            if(class_exists($erp_type)){
                $erp =  new $erp_type($sugar_config['erpsync_url'],
                                   $sugar_config['erpsync_username'],
                                   $sugar_config['erpsync_password']);
            }else{
                $erp = new ERPNotify();
            }
        } else {
            $erp = new ERPNotify();
        }
        return $erp;
    }
}
