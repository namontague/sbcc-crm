<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Contact count for a target list in the SMS "Reply Lists" subpanel
 * (custom/modules/ProspectLists/metadata/subpanels/ForSMSReplies.php).
 *
 * ProspectList::get_entry_count() counts every prospect_lists_prospects row,
 * which for a camp reply list includes the row linking it to the camp - so
 * it would show one more than the number of people who replied. This counts
 * contacts only.
 */
require_once('include/generic/SugarWidgets/SugarWidgetField.php');

class SugarWidgetSubPanelReplyListContactCount extends SugarWidgetField
{
    function displayList(&$layout_def)
    {
        global $db;

        if (empty($layout_def['fields']['ID'])) {
            return '';
        }
        $listIdQ = $db->quote($layout_def['fields']['ID']);
        $result = $db->query("SELECT COUNT(*) AS num FROM prospect_lists_prospects
                              WHERE prospect_list_id = '{$listIdQ}' AND related_type = 'Contacts' AND deleted = 0");
        $row = $db->fetchByAssoc($result);
        return !empty($row['num']) ? (int) $row['num'] : 0;
    }
}
