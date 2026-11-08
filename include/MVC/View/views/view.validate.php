<?php

class ViewValidate extends SugarView {
    var $type = 'detail';

    function ViewValidate() {
        parent::SugarView();
    }

    function process() {
        $this->display();
    }

    function display() {
        $json = getJSONobj();
        $result = array('errors' => array());
        $result['isvalid'] = $this->bean->validate($result['errors']);
        $out = $json->encode($result, false);
        ob_clean();
        print($out);
        sugar_cleanup(true);
    }
}

?>
