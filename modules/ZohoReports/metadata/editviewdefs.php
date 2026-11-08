<?php
$viewdefs ['ZohoReports'] = array(
    'EditView' => array(
        'panels' => array(
            'default' => array(
                array('name'),
                array('description'),
                array('name' => 'url','label' => 'LBL_URL'),
                array('status'),
                array('parameter'),
                array('related_module'),
            ),
        ),
    ),
);

// Experimental json viewdef loading
// $path_info = pathinfo(__FILE__);
// $json = file_get_contents($path_info['dirname'] . '/' . $path_info['filename'] . '.json');
// $viewdefs ['ZohoReports'] = json_decode($json, true); 

?>