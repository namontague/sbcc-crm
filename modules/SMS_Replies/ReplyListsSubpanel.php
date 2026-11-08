<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Query for the "Reply Lists" subpanel on an SMS_SMS detail view (see
 * modules/SMS_SMS/metadata/subpaneldefs.php): the target lists that collect
 * replies to that SMS, found the same way as when routing a reply - see
 * smsReply_replyListsWhere() in ReplyRouting.php:
 *
 *  - Lists created for this SMS by its "Create Reply Lists" option, if any.
 *  - Otherwise, for an SMS sent from a target list: lists whose "Collects SMS
 *    Replies For List" (reply_source_list_id) is that list.
 *  - Otherwise, for an SMS sent from a camp: lists linked to the camp that
 *    have reply keywords set (the camp's other lists aren't reply lists).
 *
 * There's no Sugar relationship between an SMS and these lists, so this is a
 * function data source ('generate_select' => true): Sugar builds the select
 * from the ProspectLists subpanel fields and adds this where clause.
 */
require_once('modules/SMS_Replies/ReplyRouting.php');

/**
 * @param array $params function_parameters from the subpanel definition (unused)
 * @return array query parts for SugarBean::build_sub_queries_for_union()
 */
function get_sms_reply_lists_query($params)
{
    global $db;

    $sms = null;
    if (!empty($GLOBALS['app']->controller->bean) && $GLOBALS['app']->controller->bean->module_dir === 'SMS_SMS') {
        $sms = $GLOBALS['app']->controller->bean;
    } elseif (!empty($_REQUEST['record'])) {
        $sms = BeanFactory::getBean('SMS_SMS', $_REQUEST['record']);
    }

    $where = smsReply_replyListsWhere($db, $sms);
    if (empty($where)) {
        // Matches nothing - the SMS has no camp or target list parent.
        $where = '1 = 0';
    }

    return array(
        'select' => '',
        'from' => '',
        'join' => '',
        'join_tables' => array(),
        'where' => $where,
    );
}
