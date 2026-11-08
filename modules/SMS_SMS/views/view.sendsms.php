<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');


require_once('include/MVC/View/SugarView.php');

class SMS_SMSViewSendSMS extends SugarView {

 	function SMS_SMSViewSendSMS(){
            parent::SugarView();
 	}

 	function process() {
            $this->display();
 	}

 	function display(){
        global $timedate, $current_user;
        require_once('modules/SMS_SMS/SMSFunctions.php');
        $focus = BeanFactory::getBean('SMS_SMS',$_REQUEST['record']);
        $data = sendSMS($focus);
        
        if($data == null){
            return false;
        }
        
        if($data['success']){
           $focus->status = 'Sent';
           $focus->date_sent = $timedate->asUser($timedate->getNow());
           $focus->sent_by_name = $current_user->user_name;
           if(!empty($data['message_id'])){
               $focus->message_id = $data['message_id'];
           }
        }else{
            $focus->status = 'Failed';
        }
        //update sms record with response data
        $focus->response_data = isset($data['success'])?$data['success']:$data['data'];
        $focus->save();
        
        echo json_encode($data);
 	}
}

?>
