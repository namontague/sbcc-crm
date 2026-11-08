<?php
/**
 * Re-runs rows already logged in sbcc_website_data_sms through the same
 * processing as the live webhook (modules/Camps/wsSMSTech.php): creates the
 * SMS_Replies record, links it to its SMS_SMS record, and routes the contact
 * into the reply target list. Then updates the row's record_status and
 * participant_id exactly as the webhook would have.
 *
 * Intended for replies the webhook marked 'rejected' before
 * processSmsReplyPayload() understood transmitsms's newer payload format
 * (response_id / mobile / datetime_entry ...). Unlike
 * backfillLegacyWebhookData.php, routing is ON by default, because these are
 * current replies that should have been routed when they arrived.
 *
 * Safe to re-run: reply creation is de-duplicated on transmitsms's reply id
 * (transmit_id), and list membership adds are dedup-safe. Rows that still
 * fail are left with their status unchanged and listed in the output.
 *
 * Usage (from the site root):
 *   php modules/SMS_Replies/reprocessWebhookLog.php [options]
 *
 * Options:
 *   --status=NAME  record_status of the rows to pick up (default: pending).
 *                  Use --status=rejected to process them without first
 *                  changing their status by hand.
 *   --ids=1,2,3    only these sbcc_website_data_sms row ids (still must have
 *                  the --status above)
 *   --no-route     log the replies only, don't file contacts into lists
 *   --dry-run      show what would be processed, change nothing
 */

if (!defined('sugarEntry')) define('sugarEntry', true);
require_once('include/entryPoint.php');
require_once('modules/SMS_Replies/ProcessReply.php');

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    sugar_die('reprocessWebhookLog.php is CLI only.');
}

$opts = getopt('', array('status:', 'ids:', 'no-route', 'dry-run'));
$status = isset($opts['status']) ? trim($opts['status']) : 'pending';
$route = !isset($opts['no-route']);
$dryRun = isset($opts['dry-run']);
$ids = array();
if (!empty($opts['ids'])) {
    foreach (explode(',', $opts['ids']) as $id) {
        if ((int) $id > 0) {
            $ids[] = (int) $id;
        }
    }
}

global $current_user, $db;
$current_user = new User();
$current_user->getSystemUser();

$where = "record_status = '" . $db->quote($status) . "'";
if (!empty($ids)) {
    $where .= ' AND id IN (' . implode(',', $ids) . ')';
}

// Oldest first, so replies are routed in the order they arrived. Fetched up
// front rather than looping over a live result set while
// processSmsReplyPayload() runs further queries on the same connection.
$result = $db->query("SELECT id, date_created, file_data FROM sbcc_website_data_sms WHERE {$where} ORDER BY date_created ASC, id ASC");
$rows = array();
// $encode=false: otherwise fetchByAssoc() HTML-encodes the JSON's quotes.
while ($row = $db->fetchByAssoc($result, false)) {
    $rows[] = $row;
}

echo count($rows) . " row(s) with record_status '{$status}'" . ($dryRun ? ' (dry run, nothing will change)' : '') . ', routing ' . ($route ? 'on' : 'off') . "\n\n";

$processed = 0;
$duplicates = 0;
$failed = 0;

foreach ($rows as $row) {
    $rowId = (int) $row['id'];
    $payload = json_decode($row['file_data'], true);

    if (!is_array($payload)) {
        // Rows logged by the old webhook can have raw control characters
        // where the JSON had \n / \r / \t escapes - see
        // backfillLegacyWebhookData.php.
        $repaired = str_replace(array("\r\n", "\r", "\n", "\t"), array('\r\n', '\r', '\n', '\t'), $row['file_data']);
        $payload = json_decode($repaired, true);
    }

    if (!is_array($payload)) {
        echo "[row {$rowId}] FAILED - could not parse file_data as JSON (json error: " . json_last_error_msg() . ")\n";
        $failed++;
        continue;
    }

    if (isset($payload['_note'])) {
        // The webhook couldn't find any reply fields in this request and
        // logged the raw request instead; there's nothing to process.
        echo "[row {$rowId}] FAILED - no reply fields were captured for this request ({$payload['_note']})\n";
        $failed++;
        continue;
    }

    if ($dryRun) {
        $lower = array_change_key_case($payload, CASE_LOWER);
        $mobile = isset($lower['msisdn']) ? $lower['msisdn'] : (isset($lower['mobile']) ? $lower['mobile'] : '?');
        $response = isset($lower['response']) ? $lower['response'] : '';
        echo "[row {$rowId}] {$row['date_created']} from {$mobile}: \"{$response}\"\n";
        continue;
    }

    $outcome = processSmsReplyPayload($payload, $route);

    if (!$outcome['success']) {
        echo "[row {$rowId}] FAILED - {$outcome['error']}\n";
        $failed++;
        continue;
    }

    $reply = $outcome['reply'];
    $newStatus = $outcome['isDuplicate'] ? 'duplicate' : 'processed';
    if ($outcome['isDuplicate']) {
        $duplicates++;
    } else {
        $processed++;
    }

    echo "[row {$rowId}] {$newStatus} - reply {$reply->id} from {$reply->msisdn} (linked=" . (empty($reply->sms_id) ? 'no' : 'yes')
        . ($route ? ', routing ' . ($outcome['routed'] ? 'done' : 'skipped') . " - {$outcome['routeReason']}" : '') . ")\n";

    $set = "record_status = '" . $db->quote($newStatus) . "'";
    if (!empty($outcome['contactId'])) {
        $set .= ", participant_id = '" . $db->quote($outcome['contactId']) . "'";
    }
    $db->query("UPDATE sbcc_website_data_sms SET {$set} WHERE id = {$rowId}");
}

if ($dryRun) {
    echo "\nDry run only. Re-run without --dry-run to process these.\n";
} else {
    echo "\nDone. processed={$processed} duplicates={$duplicates} failed={$failed}\n";
}
