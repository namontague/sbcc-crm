<?php
/**
 * One-off backfill: processes rows already sitting in sbcc_website_data_sms
 * (captured by the old wsSMSTech.php, which just logged raw webhook posts to
 * that table instead of creating SMS_Replies records) into proper SMS_Replies
 * records, linked to their originating SMS_SMS record where possible, then
 * marks each row 'processed'.
 *
 * Deliberately does NOT run target-list routing (unlike the live webhook):
 * this backlog is largely free-text customer replies rather than terse
 * 1/2/3 or yes/no answers, and none of the real camps involved have
 * tagged/catch-all lists configured yet, so routing as designed would spawn
 * a one-off list per uniquely-worded reply. This just gets every historical
 * reply logged and visible on its SMS's Replies subpanel; routing can be
 * run per-camp later (via routeSmsReplyToTargetList()) once lists are set up.
 *
 * Safe to re-run: only 'pending' rows are picked up, and reply creation is
 * de-duplicated on transmitsms's own reply id (transmit_id), same as the
 * live webhook.
 *
 * Usage (from the site root):
 *   php modules/SMS_Replies/backfillLegacyWebhookData.php
 */

if (!defined('sugarEntry')) define('sugarEntry', true);
require_once('include/entryPoint.php');
require_once('modules/SMS_Replies/ProcessReply.php');

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    sugar_die('backfillLegacyWebhookData.php is CLI only.');
}

global $current_user, $db;
$current_user = new User();
$current_user->getSystemUser();

// Fetch every pending row into memory up front, rather than looping over a
// live result set while processSmsReplyPayload() issues further queries on
// the same connection - simplest to reason about, and this is a small,
// one-off table.
$result = $db->query("SELECT id, date_created, file_data FROM sbcc_website_data_sms WHERE record_status = 'pending' ORDER BY date_created ASC");
$rows = array();
// $encode=false: fetchByAssoc() HTML-encodes fields by default (e.g. " -> &quot;),
// which corrupts file_data's literal JSON quotes and breaks json_decode() below.
while ($row = $db->fetchByAssoc($result, false)) {
    $rows[] = $row;
}

$processed = 0;
$duplicates = 0;
$failed = 0;

foreach ($rows as $row) {
    $rowId = $row['id'];
    $payload = json_decode($row['file_data'], true);

    if (!is_array($payload)) {
        // The old wsSMSTech.php inserted this JSON via raw SQL string
        // interpolation with no SQL escaping, so MySQL's own backslash-escape
        // processing silently turned the JSON's \n / \r / \t escapes into
        // literal control-character bytes at insert time, corrupting it.
        // json_encode() never emits a real control character in its output,
        // so any raw one found now is unambiguously a casualty of that bug -
        // safe to re-escape and retry.
        $repaired = str_replace(array("\r\n", "\r", "\n", "\t"), array('\r\n', '\r', '\n', '\t'), $row['file_data']);
        $payload = json_decode($repaired, true);
    }

    if (!is_array($payload)) {
        echo "[row {$rowId}] FAILED - could not parse file_data as JSON (json error: " . json_last_error_msg() . ")\n";
        $failed++;
        continue;
    }

    $outcome = processSmsReplyPayload($payload, false); // false = log only, no target-list routing

    if (!$outcome['success']) {
        echo "[row {$rowId}] FAILED - {$outcome['error']}\n";
        $failed++;
        continue;
    }

    $reply = $outcome['reply'];
    $status = $outcome['isDuplicate'] ? 'duplicate' : 'processed';
    if ($outcome['isDuplicate']) {
        $duplicates++;
    } else {
        $processed++;
    }

    echo "[row {$rowId}] {$status} - reply {$reply->id} from {$reply->msisdn} (linked=" . (empty($reply->sms_id) ? 'no' : 'yes') . ")\n";

    $statusQ = $db->quote($status);
    $db->query("UPDATE sbcc_website_data_sms SET record_status = '{$statusQ}' WHERE id = " . (int) $rowId);
}

echo "\nDone. processed={$processed} duplicates={$duplicates} failed={$failed}\n";
