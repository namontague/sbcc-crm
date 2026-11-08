<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('include/MVC/Controller/SugarController.php');

class SMS_RepliesController extends SugarController
{
    /**
     * Ajax target of the "Added To List" dropdown in the SMS Replies subpanel
     * (custom/include/generic/SugarWidgets/SugarWidgetSubPanelReplyListSelect.php,
     * smsReplySetList() in modules/SMS_SMS/javascript/detailview.js):
     *
     *   POST index.php?module=SMS_Replies&action=setReplyList&to_pdf=1
     *        record=<reply id>  list_id=<target list id, or empty for none>
     *
     * Responds with JSON: {"success": bool, "message": string}
     */
    function action_setReplyList()
    {
        global $db;

        require_once('modules/SMS_Replies/ReplyRouting.php');

        $result = array(false, 'Reply not found.');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $result = array(false, 'Invalid request.');
        } elseif (!ACLController::checkAccess('SMS_Replies', 'edit', true)
            || !ACLController::checkAccess('ProspectLists', 'edit', true)) {
            $result = array(false, 'You do not have access to change this.');
        } else {
            $reply = BeanFactory::getBean('SMS_Replies', isset($_POST['record']) ? $_POST['record'] : '');
            if (!empty($reply->id)) {
                $result = smsReply_moveReplyToList($db, $reply, isset($_POST['list_id']) ? $_POST['list_id'] : '');
            }
        }

        header('Content-Type: application/json');
        echo json_encode(array('success' => $result[0], 'message' => $result[1]));
        sugar_cleanup(true);
    }
}
