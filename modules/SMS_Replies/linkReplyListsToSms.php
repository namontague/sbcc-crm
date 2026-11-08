<?php
/**
 * One-off: ties reply lists that were set up by hand (or auto-created by
 * reply routing) before the SMS "Create Reply Lists" option existed to the
 * SMS they collect replies for, so they look the same as lists that option
 * creates:
 *
 *  - each list's reply_sms_id is set to the SMS
 *  - the SMS gets "Create Reply Lists" ticked and its "Reply List Keywords"
 *    filled in from the lists' keywords (one line per list, catch-all left out)
 *
 * Those older lists are shared by every SMS sent from the same camp or target
 * list, so the SMS is worked out per camp/list:
 *
 *  - only one SMS was sent from it                     -> that SMS
 *  - several were, but only one has replies logged     -> that SMS
 *  - several have replies                              -> skipped and listed,
 *    since the lists hold a mix; pass --sms=ID to choose one yourself
 *
 * Lists already tied to an SMS are left alone, so this is safe to re-run.
 *
 * Usage (from the site root):
 *   php modules/SMS_Replies/linkReplyListsToSms.php [options]
 *
 * Options:
 *   --dry-run   show what would be linked, change nothing
 *   --sms=ID    only do the camp/list this SMS was sent from, and link its
 *               reply lists to this SMS even if other SMS there have replies
 */

if (!defined('sugarEntry')) define('sugarEntry', true);
require_once('include/entryPoint.php');
require_once('modules/SMS_Replies/CreateReplyLists.php');

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    sugar_die('linkReplyListsToSms.php is CLI only.');
}

$opts = getopt('', array('dry-run', 'sms:'));
$dryRun = isset($opts['dry-run']);
$onlySmsId = isset($opts['sms']) ? trim($opts['sms']) : '';

global $current_user, $db;
$current_user = new User();
$current_user->getSystemUser();

/** For output only: collapses line breaks and trims long free-text replies. */
function linkReplyLists_oneLine($text)
{
    $text = trim(preg_replace('/\s+/', ' ', (string) $text));
    return (strlen($text) > 70) ? substr($text, 0, 67) . '...' : $text;
}

$notOwned = "(pl.reply_sms_id IS NULL OR pl.reply_sms_id = '')";

// Shared reply lists, grouped by the camp or target list they collect for.
$groups = array();
$result = $db->query("SELECT pl.id, pl.name, pl.reply_keywords, pl.reply_source_list_id AS parent_id
                      FROM prospect_lists pl
                      WHERE pl.deleted = 0 AND pl.reply_source_list_id IS NOT NULL AND pl.reply_source_list_id != '' AND {$notOwned}
                      ORDER BY pl.name");
while ($row = $db->fetchByAssoc($result, false)) {
    $groups['ProspectLists:' . $row['parent_id']][] = $row;
}
$result = $db->query("SELECT pl.id, pl.name, pl.reply_keywords, plp.related_id AS parent_id
                      FROM prospect_lists pl
                      INNER JOIN prospect_lists_prospects plp ON plp.prospect_list_id = pl.id
                          AND plp.related_type = 'Camps' AND plp.deleted = 0
                      WHERE pl.deleted = 0 AND pl.reply_keywords IS NOT NULL AND TRIM(pl.reply_keywords) != '' AND {$notOwned}
                      ORDER BY pl.name");
while ($row = $db->fetchByAssoc($result, false)) {
    $groups['Camps:' . $row['parent_id']][] = $row;
}

if ($onlySmsId !== '') {
    $only = BeanFactory::getBean('SMS_SMS', $onlySmsId);
    if (empty($only->id)) {
        sugar_die("SMS {$onlySmsId} not found.\n");
    }
    $key = $only->parent_type . ':' . $only->parent_id;
    $groups = isset($groups[$key]) ? array($key => $groups[$key]) : array();
}

echo count($groups) . ' camp(s)/target list(s) with reply lists not yet tied to an SMS' . ($dryRun ? ' (dry run, nothing will change)' : '') . "\n";

$linked = 0;
$skipped = 0;

foreach ($groups as $key => $lists) {
    list($parentType, $parentId) = explode(':', $key, 2);
    $parent = BeanFactory::getBean($parentType, $parentId);
    $parentLabel = ($parentType === 'Camps' ? 'Camp' : 'Target list') . ' "' . (empty($parent->id) ? $parentId : $parent->name) . '"';
    echo "\n{$parentLabel}\n";

    // SMS sent from this camp/list, with how many replies each has logged.
    $candidates = array();
    $parentIdQ = $db->quote($parentId);
    $parentTypeQ = $db->quote($parentType);
    $result = $db->query("SELECT s.id, s.description, s.date_entered, s.status,
                              (SELECT COUNT(*) FROM sms_replies r WHERE r.sms_id = s.id AND r.deleted = 0) AS replies
                          FROM sms_sms s
                          WHERE s.deleted = 0 AND s.parent_type = '{$parentTypeQ}' AND s.parent_id = '{$parentIdQ}'
                          ORDER BY s.date_entered");
    while ($row = $db->fetchByAssoc($result, false)) {
        $candidates[$row['id']] = $row;
    }

    $smsId = null;
    if ($onlySmsId !== '') {
        $smsId = $onlySmsId;
    } elseif (count($candidates) == 1) {
        $smsId = key($candidates);
    } else {
        $withReplies = array();
        foreach ($candidates as $candidate) {
            if ($candidate['replies'] > 0) {
                $withReplies[] = $candidate['id'];
            }
        }
        if (count($withReplies) == 1) {
            $smsId = $withReplies[0];
        }
    }

    if (empty($smsId)) {
        $skipped++;
        if (empty($candidates)) {
            echo "  SKIPPED - no SMS has been sent from it\n";
        } else {
            echo "  SKIPPED - can't tell which SMS these lists belong to. Re-run with --sms=ID for the right one:\n";
            foreach ($candidates as $candidate) {
                echo "    {$candidate['id']}  {$candidate['date_entered']}  {$candidate['status']}  replies={$candidate['replies']}  \"{$candidate['description']}\"\n";
            }
        }
        foreach ($lists as $list) {
            echo '    list: ' . linkReplyLists_oneLine($list['name']) . ' [' . linkReplyLists_oneLine($list['reply_keywords']) . "]\n";
        }
        continue;
    }

    $sms = $candidates[$smsId];
    echo "  -> SMS {$smsId} \"{$sms['description']}\" ({$sms['date_entered']}, replies={$sms['replies']})\n";

    $lines = array();
    $listIds = array();
    foreach ($lists as $list) {
        echo '    list: ' . linkReplyLists_oneLine($list['name']) . ' [' . linkReplyLists_oneLine($list['reply_keywords']) . "]\n";
        $listIds[] = "'" . $db->quote($list['id']) . "'";
        $keywords = smsReply_normalizeKeywords($list['reply_keywords']);
        // A list auto-created from a multi-line reply has line breaks in its
        // keywords, which can't be written as one line of the SMS's field -
        // it's still tied to the SMS, just not listed there.
        if ($keywords !== '' && $keywords !== '*' && !preg_match('/[\r\n]/', $keywords) && !in_array($keywords, $lines)) {
            $lines[] = $keywords;
        }
    }

    $linked++;
    if ($dryRun) {
        continue;
    }

    // Plain SQL rather than bean saves: saving an SMS_SMS looks up its
    // transmitsms list, and would create any missing lists.
    $smsIdQ = $db->quote($smsId);
    $db->query("UPDATE prospect_lists SET reply_sms_id = '{$smsIdQ}' WHERE id IN (" . implode(',', $listIds) . ')');

    // Keep any lines already on the SMS, and add the ones from these lists.
    $current = $db->fetchByAssoc($db->query("SELECT reply_list_keywords FROM sms_sms WHERE id = '{$smsIdQ}'"), false);
    $allLines = array();
    foreach (preg_split('/\r\n|\r|\n/', (string) $current['reply_list_keywords']) as $line) {
        $line = smsReply_normalizeKeywords($line);
        if ($line !== '' && $line !== '*') {
            $allLines[] = $line;
        }
    }
    $allLines = array_unique(array_merge($allLines, $lines));
    $keywordsQ = $db->quote(implode("\n", $allLines));
    $db->query("UPDATE sms_sms SET create_reply_lists = 1, reply_list_keywords = '{$keywordsQ}' WHERE id = '{$smsIdQ}'");
}

echo "\n" . ($dryRun ? 'Dry run only. Would link' : 'Done. Linked') . " {$linked}, skipped {$skipped}.\n";
