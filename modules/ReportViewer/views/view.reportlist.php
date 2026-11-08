<?php
/*********************************************************************************
 *
 * (C) 2009 Enterprise Intelligence Pty Ltd
 * Not to be used or redistributed without permission.
 *
 ********************************************************************************/



require_once('include/MVC/View/SugarView.php');
require_once('modules/ReportServer/ReportRestClient.php');

class ReportViewerViewReportList extends SugarView {

 	function ReportViewerViewReportList(){
 	  	parent::SugarView();
 	}

        function process(){
            // This will bypass the sugar header and footer so only the required text is sent
            $this->display();
           
        }

        function display() {
            $id = $_GET['id'];

            $report= new ReportRestClient();
            $basepath =  $report->getBasePath();

            if ($id != '') {
                $base =  $basepath;
                $url_id = "$base$id";
                $xml = new SimpleXMLElement($report->listResources($url_id));
            }
            else {
                $id = 0;
                $xml = new SimpleXMLElement($report->listResources($basepath));
            }

            $json = array();
            $json['id'] = $id;
            $json['item'] = array();
            $json['child'] = 1;

            foreach($xml->resourceDescriptor as $res) {

                if((string)$res['wsType'] == "folder") {
                    $json['item'][] = array('id' => substr((string)$res['uriString'], strlen($basepath)), 'text' => (string)$res->label, 'child'=>1,
                        'userdata' =>array(array('name'=>'type', 'content' =>(string)$res['wsType']),));
                }
                if((string)$res['wsType'] == "reportUnit") {
                    $json['item'][] = array('id' => substr((string)$res['uriString'], strlen($basepath)), 'text' => (string)$res->label,
                        'userdata' =>array(array('name'=>'type', 'content' =>(string)$res['wsType'])));
                }

            }

            echo json_encode($json);
        }
  
}
?>




