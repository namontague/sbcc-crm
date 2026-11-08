<?php
require_once('include/MVC/View/views/view.edit.php');
require_once('include/json_config.php');



class SMS_SMSViewEdit extends ViewEdit {

 	function SMS_SMSViewEdit(){ 		 		
 	    parent::ViewEdit();
        $this->useForSubpanel = true;

 	}
 	
	function display(){
        if(empty($this->bean->id)){
            $parent = BeanFactory::getBean($_REQUEST['return_module'], $_REQUEST['return_id']);
            if(isset($parent->sms_list_id)) {
                $this->ss->assign('sms_list_id',$parent->sms_list_id);
            }
        }
        parent::display();
	} 	

}

?>
