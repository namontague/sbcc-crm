<?php

if (!defined('sugarEntry') || !sugarEntry)
  die('Not A Valid Entry Point');


require_once('include/MVC/View/SugarView.php');

class SMS_SMSViewSyncList extends SugarView {

  function SMS_SMSViewSyncList() {
    parent::SugarView();
  }

  function process() {
    $this->display();
  }

  function display() {
    ini_set('memory_limit', '512M');
    require_once('modules/SMS_SMS/SMSFunctions.php');
    $data = null;
    $data_return = array();
    if (isset($_REQUEST['parent_id'], $_REQUEST['parent_module'])) {
      $focus = BeanFactory::getBean($_REQUEST['parent_module'], $_REQUEST['parent_id']);


      //create a sms list for this camp
      //$this = camp/target list
      $list = null;
      if (!isset($focus->sms_list_id) || empty($focus->sms_list_id)) {
        $data_return = array(
            'error' => array(
                'msg' => 'no sms list id found'
            )
        );
        echo json_encode($data_return);
        return;
      } else {
        if ($_REQUEST['parent_module'] == 'Camps') {
          //add all targets to this list
          $prospect_list = $focus->get_linked_beans('prospect_lists', 'ProspectList');
          if (isset($prospect_list)) {
            foreach ($prospect_list as $p_list) {
              if ($p_list->camp_master_list) {
                $list = $p_list;
                break;
              }
            }
            if (is_null($list)) {
              $data_return = array(
                  'error' => array(
                      'msg' => 'no master list found'
                  )
              );
              echo json_encode($data_return);
              return;
            }
          }
        } elseif ($_REQUEST['parent_module'] == 'ProspectLists') {
          //load all targets 
          //foreach camp load participants 


          $list = $focus;
        }

        $targets = $list->get_linked_beans('contacts', 'Contacts');
        if(!empty($targets)) {
          $target_data = array();
          foreach ($targets as $person) {
            if($person->do_not_sms == false) {
              $number = trim(str_replace(' ', '', $person->phone_mobile));
              if (substr($number, 0, 2) == '04') {
                $number = "61" . substr($number, 1);
              }
              if (!isset($target_data[$number])) {
                $target_data[$number] = array(
                    'number' => $number,
                    'first_name' => $person->first_name,
                    'last_name' => $person->last_name,                    
                    'person' => $person
                );
              } else {
                $data_return = array(
                    'error' => array(
                        'msg' => "</br>Error: {$number} found twice in list",
                    )
                );
              }
            }
          }
          if (!empty($target_data)) {
            if (($data = syncFullListFromButton($focus->sms_list_id, $target_data)) != null) {
              if (isset($data['result'])) {
                $message = "</br>" . implode("</br>", $data['result']);
                $data_return = array(
                    'success' => array(
                        'msg' => $message
                    )
                );
              } else if (isset($data['error'])) {
                $message = "</br>" . implode("</br>", $data['error']);
                $data_return = array(
                    'error' => array(
                        'msg' => $message
                    )
                );
              } else {
                $data_return = array(
                    'error' => array(
                        'msg' => "</br>Error",
                    )
                );
              }
            }
          }
        }else{
          $data_return = array(
                'error' => array(
                    'msg' => "</br>Error: No targets in list.",
                )
            );
        }
      }
    }
    if (empty($data_return)) {
      $data_return = array(
          'error' => array(
              'msg' => "</br>Error",
          )
      );
    }
    $json = getJSONobj();
    $decoded = array_map('from_html', $data_return); // Need to decode from html as the db manager encodes values by default
    $out = $json->encode($decoded);
    echo($out);
  }

}

?>