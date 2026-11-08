<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * The SMS "Create Reply Lists" option: when an SMS is saved with it ticked,
 * creates target lists to collect replies to that SMS (called from
 * SMS_SMS::save()).
 *
 *  - One list per line of "Reply List Keywords", tagged with that line's
 *    comma-separated keywords, e.g. "yes,y,1" -> a list named "... - Yes".
 *  - Always a catch-all list ("*") for replies matching no other list.
 *
 * The lists are tied to the SMS (reply_sms_id), so its replies go only into
 * them - see ReplyRouting.php. They're also linked to the SMS's camp, or point
 * at its parent target list, like hand-made reply lists, so they show up there.
 *
 * Safe to run on every save: a line whose keywords already have a list for
 * this SMS is skipped, as is the catch-all once it exists. Lines removed from
 * the field don't delete their lists.
 */
require_once('modules/SMS_Replies/ReplyRouting.php');

/**
 * @param SugarBean $sms a saved SMS_SMS bean
 * @return array names of the lists created
 */
function createSmsReplyLists($sms)
{
    global $db;

    if (empty($sms->create_reply_lists) || empty($sms->id) || empty($sms->parent_id)
        || !in_array($sms->parent_type, array('Camps', 'ProspectLists'))) {
        return array();
    }

    $parent = BeanFactory::getBean($sms->parent_type, $sms->parent_id);
    if (empty($parent->id)) {
        return array();
    }

    $baseName = trim($parent->name);
    if (trim($sms->description) !== '') {
        $baseName .= ' - ' . trim($sms->description);
    }

    // Keyword sets this SMS already has lists for.
    $existing = array();
    $smsIdQ = $db->quote($sms->id);
    $result = $db->query("SELECT reply_keywords FROM prospect_lists WHERE reply_sms_id = '{$smsIdQ}' AND deleted = 0");
    while ($row = $db->fetchByAssoc($result, false)) {
        $existing[smsReply_normalizeKeywords($row['reply_keywords'])] = true;
    }

    $created = array();
    foreach (preg_split('/\r\n|\r|\n/', (string) $sms->reply_list_keywords) as $line) {
        $keywords = smsReply_normalizeKeywords($line);
        if ($keywords === '' || $keywords === '*' || isset($existing[$keywords])) {
            continue;
        }
        $first = explode(',', $keywords);
        $name = $baseName . ' - ' . ucfirst($first[0]);
        smsReply_createReplyList($sms->parent_type, $parent->id, $name, $keywords, $sms->id);
        $existing[$keywords] = true;
        $created[] = $name;
    }

    if (!isset($existing['*'])) {
        $name = $baseName . ' - Other replies';
        smsReply_createReplyList($sms->parent_type, $parent->id, $name, '*', $sms->id);
        $created[] = $name;
    }

    return $created;
}

/**
 * "Yes, Y ,1,,yes" -> "yes,y,1": lower case, trimmed, no blanks or repeats.
 * A "*" on its own (the catch-all) is kept as "*"; mixed in with other
 * keywords it's dropped, since the catch-all list is created separately.
 */
function smsReply_normalizeKeywords($keywords)
{
    $parts = array();
    foreach (explode(',', strtolower((string) $keywords)) as $keyword) {
        $keyword = trim($keyword);
        if ($keyword !== '' && !in_array($keyword, $parts)) {
            $parts[] = $keyword;
        }
    }
    if ($parts === array('*')) {
        return '*';
    }
    $parts = array_diff($parts, array('*'));
    return implode(',', $parts);
}
