<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * "Added To List" column of the SMS Replies subpanel
 * (modules/SMS_Replies/metadata/subpanels/ForSMS.php): a dropdown of the
 * SMS's reply lists, set to the one this reply's contact was added to.
 * Changing it moves the contact to the chosen list - smsReplySetList() in
 * modules/SMS_SMS/javascript/detailview.js posts to
 * SMS_RepliesController::action_setReplyList().
 *
 * Shows plain text instead when there's nothing to choose: the reply has no
 * matched contact, or the user can't edit replies / target lists.
 */
require_once('include/generic/SugarWidgets/SugarWidgetField.php');
require_once('modules/SMS_Replies/ReplyRouting.php');

class SugarWidgetSubPanelReplyListSelect extends SugarWidgetField
{
    // reply lists per SMS id, so a page of rows runs the lookup once
    protected static $options = array();

    function displayList(&$layout_def)
    {
        global $db;

        $fields = $layout_def['fields'];
        $replyId = isset($fields['ID']) ? $fields['ID'] : '';
        $smsId = isset($fields['SMS_ID']) ? $fields['SMS_ID'] : '';
        $contactId = isset($fields['CONTACT_ID']) ? $fields['CONTACT_ID'] : '';
        $listId = isset($fields['REPLY_LIST_ID']) ? $fields['REPLY_LIST_ID'] : '';
        $listName = isset($fields['REPLY_LIST_NAME']) ? $fields['REPLY_LIST_NAME'] : '';

        if (empty($replyId) || empty($smsId) || empty($contactId)
            || !ACLController::checkAccess('SMS_Replies', 'edit', true)
            || !ACLController::checkAccess('ProspectLists', 'edit', true)) {
            return $listName;
        }

        if (!isset(self::$options[$smsId])) {
            self::$options[$smsId] = smsReply_getReplyListOptions($db, $smsId);
        }
        $options = self::$options[$smsId];

        // The reply's current list may no longer be one of the SMS's reply
        // lists - keep it selectable so the dropdown shows the truth.
        if (!empty($listId) && !isset($options[$listId])) {
            $options[$listId] = html_entity_decode($listName, ENT_QUOTES, 'UTF-8');
        }

        $replyIdAttr = htmlspecialchars($replyId, ENT_QUOTES, 'UTF-8');
        $listIdAttr = htmlspecialchars($listId, ENT_QUOTES, 'UTF-8');
        $html = "<select onchange=\"smsReplySetList(this, '{$replyIdAttr}');\" data-current=\"{$listIdAttr}\" style=\"max-width: 100%;\">";
        $html .= '<option value=""></option>';
        foreach ($options as $id => $name) {
            $html .= '<option value="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '"' . ($id === $listId ? ' selected="selected"' : '') . '>'
                . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        $html .= '</select> <span class="sms_reply_list_status"></span>';

        return $html;
    }
}
