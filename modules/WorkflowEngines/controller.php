<?php
/*********************************************************************************
 * (C) 2011 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 ********************************************************************************/
require_once('include/MVC/Controller/SugarController.php');

class WorkflowEnginesController extends SugarController
{

    function action_StartProcessWithForm()
    {
		$this->view = 'startprocesswithform';
    }
}
?>