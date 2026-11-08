<?php
/**
 * One-off: fills in the Contact and Added To List of SMS replies that were
 * stored before those fields existed (new replies get them from
 * routeSmsReplyToTargetList() as they're routed).
 *
 * Works out, without changing any list:
 *  - the contact: the reply's mobile number matched against the contacts the
 *    SMS was sent to, exactly as routing does
 *  - the list: of the SMS's reply lists that contact is actually in, the one
 *    tagged with this reply's text, else the catch-all, else the only one.
 *    Left blank if they're in none (e.g. removed since) or it's ambiguous.
 *
 * Safe to re-run: only replies still missing a contact or list are looked at.
 *
 * Usage (from the site root):
 *   php modules/SMS_Replies/backfillReplyContacts.php [--dry-run]
 */

if (!defined('sugarEntry')) define('sugarEntry', true);
require_once('include/entryPoint.php');
require_once('modules/SMS_Replies/ReplyRouting.php');

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    sugar_die('backfillReplyContacts.php is CLI only.');
}

$opts = getopt('', array('dry-run'));
$dryRun = isset($opts['dry-run']);

global $current_user, $db;
$current_user = new User();
$current_user->getSystemUser();

$result = $db->query("SELECT id, sms_id, msisdn, response, contact_id, reply_list_id FROM sms_replies
                      WHERE deleted = 0 AND sms_id IS NOT NULL AND sms_id != ''
                          AND (contact_id IS NULL OR contact_id = '' OR reply_list_id IS NULL OR reply_list_id = '')
                      ORDER BY sms_id, received_at");
$replies = array();
while ($row = $db->fetchByAssoc($result, false)) {
    $replies[] = $row;
}

echo count($replies) . ' reply(ies) missing a contact or list' . ($dryRun ? ' (dry run, nothing will change)' : '') . "\n";

// Per SMS: its scope, the contacts it was sent to (by normalized mobile) and
// its reply lists with their members - loaded once each.
$smsInfo = array();

$withContact = 0;
$withList = 0;
$noContact = 0;

foreach ($replies as $row) {
    $smsId = $row['sms_id'];
    if (!isset($smsInfo[$smsId])) {
        $smsInfo[$smsId] = backfillReplyContacts_loadSms($db, $smsId);
    }
    $info = $smsInfo[$smsId];
    if (empty($info)) {
        $noContact++;
        continue;
    }

    $contactId = $row['contact_id'];
    if (empty($contactId)) {
        $digits = smsReply_normalizeMsisdn($row['msisdn']);
        $contactId = ($digits !== '' && isset($info['contacts'][$digits])) ? $info['contacts'][$digits] : null;
    }
    if (empty($contactId)) {
        $noContact++;
        continue;
    }
    $withContact++;

    // The SMS's reply lists this contact is in.
    $memberOf = array();
    foreach ($info['lists'] as $listId => $list) {
        if (isset($list['members'][$contactId])) {
            $memberOf[$listId] = $list;
        }
    }

    $listId = null;
    $normalizedResponse = smsReply_normalizeResponse($row['response']);
    foreach ($memberOf as $id => $list) {
        if (trim($list['keywords']) === '*') {
            continue;
        }
        foreach (explode(',', $list['keywords']) as $keyword) {
            if ($normalizedResponse !== '' && strtolower(trim($keyword)) === $normalizedResponse) {
                $listId = $id;
                break 2;
            }
        }
    }
    if (empty($listId)) {
        foreach ($memberOf as $id => $list) {
            if (trim($list['keywords']) === '*') {
                $listId = $id;
                break;
            }
        }
    }
    if (empty($listId) && count($memberOf) == 1) {
        $listId = key($memberOf);
    }
    if (!empty($listId)) {
        $withList++;
    }

    if ($dryRun) {
        continue;
    }

    $set = "contact_id = '" . $db->quote($contactId) . "'";
    if (!empty($listId) && empty($row['reply_list_id'])) {
        $set .= ", reply_list_id = '" . $db->quote($listId) . "'";
    }
    $db->query("UPDATE sms_replies SET {$set} WHERE id = '" . $db->quote($row['id']) . "'");
}

echo ($dryRun ? 'Dry run only. Would set' : 'Done. Set') . " a contact on {$withContact} (and a list on {$withList} of those); no matching contact for {$noContact}.\n";

/**
 * @return array|null array('contacts' => normalized mobile => contact id,
 *   'lists' => list id => array('keywords' => ..., 'members' => contact id => true)),
 *   or null if the SMS has no camp/target list to match contacts against
 */
function backfillReplyContacts_loadSms($db, $smsId)
{
    $sms = BeanFactory::getBean('SMS_SMS', $smsId);
    if (empty($sms->id)) {
        return null;
    }
    $scope = smsReply_getScope($db, $sms);
    if (isset($scope['error'])) {
        return null;
    }

    $info = array('contacts' => array(), 'lists' => array());

    $poolIdQ = $db->quote($scope['poolListId']);
    $result = $db->query("SELECT c.id, c.phone_mobile
                          FROM contacts c
                          INNER JOIN prospect_lists_prospects plp ON plp.related_id = c.id
                              AND plp.related_type = 'Contacts' AND plp.prospect_list_id = '{$poolIdQ}' AND plp.deleted = 0
                          WHERE c.deleted = 0 AND c.phone_mobile IS NOT NULL AND c.phone_mobile != ''");
    while ($row = $db->fetchByAssoc($result, false)) {
        $digits = smsReply_normalizeMsisdn($row['phone_mobile']);
        // first match wins, as in smsReply_matchContactInList()
        if ($digits !== '' && !isset($info['contacts'][$digits])) {
            $info['contacts'][$digits] = $row['id'];
        }
    }

    $where = smsReply_replyListsWhere($db, $sms);
    if (!empty($where)) {
        $result = $db->query("SELECT prospect_lists.id, prospect_lists.reply_keywords FROM prospect_lists
                              WHERE prospect_lists.deleted = 0 AND {$where}");
        while ($row = $db->fetchByAssoc($result, false)) {
            $info['lists'][$row['id']] = array('keywords' => (string) $row['reply_keywords'], 'members' => array());
        }
        foreach ($info['lists'] as $listId => $list) {
            $listIdQ = $db->quote($listId);
            $result = $db->query("SELECT related_id FROM prospect_lists_prospects
                                  WHERE prospect_list_id = '{$listIdQ}' AND related_type = 'Contacts' AND deleted = 0");
            while ($row = $db->fetchByAssoc($result, false)) {
                $info['lists'][$listId]['members'][$row['related_id']] = true;
            }
        }
    }

    return $info;
}
