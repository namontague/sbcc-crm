<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');


require_once('include/MVC/View/SugarView.php');

class SMS_SMSViewGetList extends SugarView {

 	function SMS_SMSViewGetList(){
            parent::SugarView();
 	}

 	function process() {
            $this->display();
 	}

 	function display(){
        require_once('modules/SMS_SMS/SMSFunctions.php');
        if(isset($_REQUEST['record'])) {
            $focus = BeanFactory::getBean('SMS_SMS',$_REQUEST['record']);
            $_REQUEST['list_id'] = $focus->to_list_id;
        }
        $data = getList($_REQUEST['list_id'],true);
        if($data == null){
            return false;
        }
        
        echo json_encode($data);
 	}
}

?>
