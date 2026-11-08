<?php
/**
 * Demo data for the SMS replies -> target lists feature. Creates a clearly
 * labelled DEMO camp, contacts, master list, reply-tagged lists and a "sent"
 * SMS, then pushes simulated transmitsms replies through the real webhook
 * (index.php?entryPoint=wsSMSTech) so everything flows through the same code
 * path as a genuine reply.
 *
 * Usage (CLI, from the site root):
 *   php modules/SMS_Replies/demoData.php seed
 *   php modules/SMS_Replies/demoData.php replies <site base url>   e.g. https://sbcc.crm.com.au
 *   php modules/SMS_Replies/demoData.php status
 *   php modules/SMS_Replies/demoData.php remove
 *
 * The base URL is deliberately required (not read from site_url in config.php,
 * which can be the production address) so demo replies can't be posted to the
 * wrong site.
 *
 * Records are inserted with SQL rather than saved as beans on purpose: saving a
 * Camp calls addList() against the live transmitsms API, and adding contacts
 * to a master list fires hooks that sync them to transmitsms. Demo records
 * must never reach the real SMS provider. All phone numbers are the 0491 570
 * range the ACMA reserves for fictional use. Do NOT click "Send SMS" on the
 * demo SMS record.
 */
if (!defined('sugarEntry')) define('sugarEntry', true);
require_once('include/entryPoint.php');

if (substr(php_sapi_name(), 0, 3) != 'cli') {
    sugar_die('demoData.php is CLI only.');
}

global $db;

define('DEMO_MARKER', 'DEMO DATA (SMS reply feature) - safe to delete');
define('DEMO_SMS_NAME', 'SMS_REPLY_DEMO_DATA');
define('DEMO_CAMP_NAME', 'DEMO SMS Reply Test Camp');
define('DEMO_LIST_NAME', 'DEMO SMS Reply Test List (no camp)');
define('DEMO_MSG_CAMP', 'DEMO-MSG-1001');
define('DEMO_MSG_LIST', 'DEMO-MSG-2001');

$action = isset($argv[1]) ? $argv[1] : '';
switch ($action) {
    case 'seed':    demoSeed($db); break;
    case 'replies': demoReplies($db, isset($argv[2]) ? $argv[2] : ''); break;
    case 'status':  demoStatus($db); break;
    case 'remove':  demoRemove($db); break;
    default:
        echo "Usage: php modules/SMS_Replies/demoData.php seed|replies <base url>|status|remove\n";
}

function demoQ($sql)
{
    global $db;
    return $db->query($sql, true);
}

function demoRows($sql)
{
    global $db;
    $res = demoQ($sql);
    $rows = array();
    while ($row = $db->fetchByAssoc($res, false)) {
        $rows[] = $row;
    }
    return $rows;
}

function demoSeed($db)
{
    if (demoRows("SELECT id FROM camps WHERE description = '" . DEMO_MARKER . "' AND deleted = 0")) {
        echo "Demo data already exists - run 'remove' first to recreate it.\n";
        return;
    }

    $now = gmdate('Y-m-d H:i:s');
    $m = DEMO_MARKER;
    $campId = create_guid();
    $start = gmdate('Y-m-d', strtotime('+14 days'));
    $end = gmdate('Y-m-d', strtotime('+16 days'));

    demoQ("INSERT INTO camps (id, name, date_entered, date_modified, modified_user_id, created_by, description, deleted, assigned_user_id, start_date, end_date, type)
           VALUES ('{$campId}', '" . DEMO_CAMP_NAME . "', '{$now}', '{$now}', '1', '1', '{$m}', 0, '1', '{$start}', '{$end}', '[ Ages 6 - 16 ]')");

    // phone numbers are in different formats on purpose, to show msisdn matching
    $contacts = array(
        array('Demo Parent 1', '0491 570 006'),
        array('Demo Parent 2', '+61491570156'),
        array('Demo Parent 3', '0491 570 157'),
        array('Demo Parent 4', '0491570158'),
        array('Demo Parent 5', '0491 570 159'),
        array('Demo Parent 6', '0491 570 007'),
    );
    $contactIds = array();
    foreach ($contacts as $c) {
        $id = create_guid();
        $contactIds[] = $id;
        demoQ("INSERT INTO contacts (id, date_entered, date_modified, modified_user_id, created_by, description, deleted, assigned_user_id, first_name, last_name, phone_mobile, do_not_sms)
               VALUES ('{$id}', '{$now}', '{$now}', '1', '1', '{$m}', 0, '1', '{$c[0]}', '(SMS reply demo)', '{$c[1]}', 0)");
    }

    // master list (camp_master_list=1) holds everyone who was texted. Left
    // WITHOUT an sms_list_id so nothing about it syncs to transmitsms.
    $lists = array(
        'master'  => array(DEMO_CAMP_NAME . ' - Master List (everyone texted)', 1, null),
        'yes'     => array(DEMO_CAMP_NAME . ' - Option 1 YES', 0, '1,yes,y'),
        'no'      => array(DEMO_CAMP_NAME . ' - Option 2 NO', 0, '2,no,n'),
        'unknown' => array(DEMO_CAMP_NAME . ' - Unknown', 0, '*'),
    );
    $listIds = array();
    foreach ($lists as $key => $l) {
        $id = create_guid();
        $listIds[$key] = $id;
        $kw = ($l[2] === null) ? 'NULL' : "'" . $l[2] . "'";
        demoQ("INSERT INTO prospect_lists (id, name, list_type, date_entered, date_modified, modified_user_id, created_by, assigned_user_id, deleted, description, camp_master_list, reply_keywords)
               VALUES ('{$id}', '" . $db->quote($l[0]) . "', 'default', '{$now}', '{$now}', '1', '1', '1', 0, '{$m}', {$l[1]}, {$kw})");
        // link list to camp (direct insert: linking through the relationship API
        // fires a hook that dumps the camp's whole roster into non-master lists)
        demoQ("INSERT INTO prospect_lists_prospects (id, prospect_list_id, related_id, related_type, date_modified, deleted)
               VALUES ('" . create_guid() . "', '{$id}', '{$campId}', 'Camps', '{$now}', 0)");
    }
    foreach ($contactIds as $cid) {
        demoQ("INSERT INTO prospect_lists_prospects (id, prospect_list_id, related_id, related_type, date_modified, deleted)
               VALUES ('" . create_guid() . "', '{$listIds['master']}', '{$cid}', 'Contacts', '{$now}', 0)");
    }

    $smsId = create_guid();
    $msg = 'DEMO: Hi, can your child attend our holiday clinic? Reply YES or NO (or 1 for YES, 2 for NO). - SBCC';
    demoQ("INSERT INTO sms_sms (id, name, date_entered, date_modified, modified_user_id, created_by, description, deleted, date_sent, sent_by_name, status, caller_id, to_list_name, message, response_data, message_id, parent_type, parent_id)
           VALUES ('{$smsId}', '" . DEMO_SMS_NAME . "', '{$now}', '{$now}', '1', '1', 'DEMO: holiday clinic attendance check', 0, '{$now}', 'admin', 'Sent', '0481 074 423',
                   'DEMO master list (not synced to transmitsms)', '" . $db->quote($msg) . "', 'DEMO - never actually sent', 'DEMO-MSG-1001', 'Camps', '{$campId}')");

    // Second scenario: an SMS sent from a plain target list with no camp.
    // Its reply lists point back at it via reply_source_list_id ("Collects SMS
    // Replies For List"). No catch-all list is created on purpose, so a
    // free-text reply shows the auto-create fallback.
    $srcListId = create_guid();
    demoQ("INSERT INTO prospect_lists (id, name, list_type, date_entered, date_modified, modified_user_id, created_by, assigned_user_id, deleted, description, camp_master_list)
           VALUES ('{$srcListId}', '" . DEMO_LIST_NAME . "', 'default', '{$now}', '{$now}', '1', '1', '1', 0, '{$m}', 0)");
    foreach (array_slice($contactIds, 0, 3) as $cid) {
        demoQ("INSERT INTO prospect_lists_prospects (id, prospect_list_id, related_id, related_type, date_modified, deleted)
               VALUES ('" . create_guid() . "', '{$srcListId}', '{$cid}', 'Contacts', '{$now}', 0)");
    }
    $srcReplyLists = array(
        'yes' => array(DEMO_LIST_NAME . ' - Option 1 YES', '1,yes,y'),
        'no'  => array(DEMO_LIST_NAME . ' - Option 2 NO', '2,no,n'),
    );
    $srcListUrls = array();
    foreach ($srcReplyLists as $key => $l) {
        $id = create_guid();
        $srcListUrls[$key] = $id;
        demoQ("INSERT INTO prospect_lists (id, name, list_type, date_entered, date_modified, modified_user_id, created_by, assigned_user_id, deleted, description, camp_master_list, reply_keywords, reply_source_list_id)
               VALUES ('{$id}', '" . $db->quote($l[0]) . "', 'default', '{$now}', '{$now}', '1', '1', '1', 0, '{$m}', 0, '{$l[1]}', '{$srcListId}')");
    }
    $listSmsId = create_guid();
    $listMsg = 'DEMO: Reminder - are you coming to the Saturday practice match? Reply YES or NO. - SBCC';
    demoQ("INSERT INTO sms_sms (id, name, date_entered, date_modified, modified_user_id, created_by, description, deleted, date_sent, sent_by_name, status, caller_id, to_list_name, message, response_data, message_id, parent_type, parent_id)
           VALUES ('{$listSmsId}', '" . DEMO_SMS_NAME . "', '{$now}', '{$now}', '1', '1', 'DEMO: practice match reminder (sent from a target list)', 0, '{$now}', 'admin', 'Sent', '0481 074 423',
                   'DEMO target list (not synced to transmitsms)', '" . $db->quote($listMsg) . "', 'DEMO - never actually sent', '" . DEMO_MSG_LIST . "', 'ProspectLists', '{$srcListId}')");

    echo "Seeded demo data.\n\n";
    echo "Scenario 1 - SMS sent from a camp:\n";
    echo "  Camp:   index.php?module=Camps&action=DetailView&record={$campId}\n";
    echo "  SMS:    index.php?module=SMS_SMS&action=DetailView&record={$smsId}\n";
    foreach ($listIds as $key => $id) {
        echo "  List ({$key}): index.php?module=ProspectLists&action=DetailView&record={$id}\n";
    }
    echo "\nScenario 2 - SMS sent from a target list (no camp):\n";
    echo "  SMS:         index.php?module=SMS_SMS&action=DetailView&record={$listSmsId}\n";
    echo "  Source list: index.php?module=ProspectLists&action=DetailView&record={$srcListId}\n";
    foreach ($srcListUrls as $key => $id) {
        echo "  Reply list ({$key}): index.php?module=ProspectLists&action=DetailView&record={$id}\n";
    }
    echo "\nNext: php modules/SMS_Replies/demoData.php replies <site base url>\n";
}

function demoPostReply($baseUrl, $fields)
{
    $ch = curl_init(rtrim($baseUrl, '/') . '/index.php?entryPoint=wsSMSTech');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $body = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    return $err ? "curl error: {$err}" : $body;
}

function demoReplies($db, $baseUrl)
{
    if ($baseUrl === '') {
        echo "Give the base URL of the site to post the demo replies to, e.g.\n  php modules/SMS_Replies/demoData.php replies https://sbcc.crm.com.au\n";
        return;
    }
    $sms = demoRows("SELECT message_id FROM sms_sms WHERE name = '" . DEMO_SMS_NAME . "' AND deleted = 0");
    if (!$sms) {
        echo "No demo SMS found - run 'seed' first.\n";
        return;
    }
    $t = time();

    // id, minutes ago, first name, msisdn, response, what should happen
    $replies = array(
        array('demo-001', 50, 'Demo Parent 1', '61491570006', 'Yes',   'matches the YES list'),
        array('demo-002', 45, 'Demo Parent 2', '61491570156', '1',     'number is +61... on the contact; "1" matches the YES list'),
        array('demo-003', 40, 'Demo Parent 3', '61491570157', 'NO.',   'trailing punctuation ignored; matches the NO list'),
        array('demo-004', 35, 'Demo Parent 4', '61491570158', '2',     'matches the NO list'),
        array('demo-005', 30, 'Demo Parent 5', '61491570159', 'Can we do Saturday instead?', 'free text; falls into the catch-all Unknown list'),
        array('demo-001', 50, 'Demo Parent 1', '61491570006', 'Yes',   'same delivery sent again (a retry); logged as a duplicate, not stored twice'),
        array('demo-006', 25, '',              '61491570313', 'Yes',   'number is not on the master list; logged but not added to any list'),
        array('demo-007', 10, 'Demo Parent 5', '61491570159', 'yes',   'follows up with "yes"; now in BOTH Unknown and YES'),
        // a seventh element of 'new' = sent in transmitsms's newer payload format
        array('demo-008', 5,  '',              '61491570007', 'NO',    'sent in the newer payload format (mobile/response_id/datetime_entry); matches the NO list', 'new'),
        // scenario 2: replies to the SMS sent from a target list (ids demo-1xx)
        array('demo-101', 20, 'Demo Parent 1', '61491570006', 'yes',          '[target list SMS] matches the list\'s YES reply list'),
        array('demo-102', 15, 'Demo Parent 2', '61491570156', '2',            '[target list SMS] matches the list\'s NO reply list'),
        array('demo-103', 8,  'Demo Parent 3', '61491570157', 'Not sure yet', '[target list SMS] free text and no catch-all on this list, so a new reply list is auto-created for it'),
    );
    foreach ($replies as $r) {
        // demo-1xx replies belong to the target-list SMS, the rest to the camp SMS
        $messageId = (strpos($r[0], 'demo-1') === 0) ? DEMO_MSG_LIST : DEMO_MSG_CAMP;
        if (isset($r[6])) {
            $fields = array(
                'user_id' => '2254',
                'rate' => '10',
                'mobile' => $r[3],
                'response' => $r[4],
                'message_id' => $messageId,
                'response_id' => $r[0],
                'longcode' => '61481074423',
                'datetime_entry' => gmdate('Y-m-d H:i:s', $t - $r[1] * 60),
                'is_optout' => 'no',
            );
        } else {
            $fields = array(
                'ID' => $r[0],
                'Message_Id' => $messageId,
                'Received_At' => gmdate('Y-m-d H:i:s', $t - $r[1] * 60),
                'First_Name' => $r[2],
                'Last_Name' => '',
                'Msisdn' => $r[3],
                'Response' => $r[4],
                'Longcode' => '61481074423',
            );
        }
        $out = demoPostReply($baseUrl, $fields);
        echo sprintf("%-8s %-14s \"%s\"\n   expect: %s\n   webhook: %s\n", $r[0], $r[3], $r[4], $r[5], trim($out));
    }
    echo "\nSee the result with: php modules/SMS_Replies/demoData.php status\n";
}

function demoStatus($db)
{
    $camp = demoRows("SELECT id FROM camps WHERE description = '" . DEMO_MARKER . "' AND deleted = 0");
    if (!$camp) {
        echo "No demo data present.\n";
        return;
    }
    $campId = $camp[0]['id'];
    echo "Target lists on the demo camp:\n";
    $lists = demoRows("SELECT pl.id, pl.name, pl.reply_keywords, pl.camp_master_list
                       FROM prospect_lists pl
                       JOIN prospect_lists_prospects plp ON plp.prospect_list_id = pl.id AND plp.related_type = 'Camps' AND plp.related_id = '{$campId}' AND plp.deleted = 0
                       WHERE pl.deleted = 0 ORDER BY pl.camp_master_list DESC, pl.name");
    foreach ($lists as $l) {
        demoPrintList($l['id'], $l['name'], $l['camp_master_list'] ? 'master list' : "keywords: {$l['reply_keywords']}");
    }

    $src = demoRows("SELECT id, name FROM prospect_lists WHERE name = '" . DEMO_LIST_NAME . "' AND description = '" . DEMO_MARKER . "' AND deleted = 0");
    if ($src) {
        echo "\n\nTarget lists for the SMS sent from a target list (no camp):\n";
        demoPrintList($src[0]['id'], $src[0]['name'], 'the list the SMS was sent to');
        $replyLists = demoRows("SELECT id, name, reply_keywords FROM prospect_lists WHERE reply_source_list_id = '{$src[0]['id']}' AND deleted = 0 ORDER BY name");
        foreach ($replyLists as $l) {
            demoPrintList($l['id'], $l['name'], "keywords: {$l['reply_keywords']}");
        }
    }

    echo "\nReplies logged against the demo SMS:\n";
    $replies = demoRows("SELECT r.transmit_id, r.msisdn, r.response, r.sms_id FROM sms_replies r WHERE r.transmit_id LIKE 'demo-%' AND r.deleted = 0 ORDER BY r.received_at");
    foreach ($replies as $r) {
        echo sprintf("  %-9s %-13s %-30s %s\n", $r['transmit_id'], $r['msisdn'], '"' . $r['response'] . '"', empty($r['sms_id']) ? '(not linked to an SMS)' : 'linked to SMS');
    }
    echo "\nWebhook audit log (sbcc_website_data_sms) for the demo deliveries:\n";
    foreach (demoRows("SELECT id, record_status, participant_id FROM sbcc_website_data_sms WHERE LOWER(file_data) LIKE '%id\":\"demo-%' ORDER BY id") as $l) {
        echo sprintf("  log row %-5s %-10s %s\n", $l['id'], $l['record_status'], empty($l['participant_id']) ? '' : 'matched a contact');
    }
}

function demoPrintList($listId, $name, $tag)
{
    echo "\n  {$name}  [{$tag}]\n";
    $members = demoRows("SELECT c.first_name, c.phone_mobile FROM prospect_lists_prospects plp JOIN contacts c ON c.id = plp.related_id
                         WHERE plp.prospect_list_id = '{$listId}' AND plp.related_type = 'Contacts' AND plp.deleted = 0 ORDER BY c.first_name");
    if (!$members) {
        echo "    (empty)\n";
    }
    foreach ($members as $mem) {
        echo "    - {$mem['first_name']}  ({$mem['phone_mobile']})\n";
    }
}

function demoRemove($db)
{
    $ids = function ($rows) {
        $out = array();
        foreach ($rows as $r) {
            $out[] = "'" . $r['id'] . "'";
        }
        return $out ? implode(',', $out) : "''";
    };

    $camps = demoRows("SELECT id FROM camps WHERE description = '" . DEMO_MARKER . "'");
    $lists = demoRows("SELECT id FROM prospect_lists WHERE description = '" . DEMO_MARKER . "'");
    // lists the routing auto-created for the demo target list have no marker
    // description, but they point back at the demo list
    $autoCreated = demoRows("SELECT id FROM prospect_lists WHERE reply_source_list_id IN (" . $ids($lists) . ")");
    $lists = array_merge($lists, $autoCreated);
    $contacts = demoRows("SELECT id FROM contacts WHERE description = '" . DEMO_MARKER . "'");
    $smsRows = demoRows("SELECT id FROM sms_sms WHERE name = '" . DEMO_SMS_NAME . "'");

    $campIds = $ids($camps);
    $listIds = $ids($lists);
    $contactIds = $ids($contacts);
    $smsIds = $ids($smsRows);

    demoQ("DELETE FROM prospect_lists_prospects WHERE prospect_list_id IN ({$listIds}) OR related_id IN ({$campIds}) OR related_id IN ({$contactIds})");
    demoQ("DELETE FROM sms_replies WHERE transmit_id LIKE 'demo-%' OR sms_id IN ({$smsIds})");
    demoQ("DELETE FROM sbcc_website_data_sms WHERE LOWER(file_data) LIKE '%id\":\"demo-%'");
    demoQ("DELETE FROM prospect_lists WHERE id IN ({$listIds})");
    demoQ("DELETE FROM contacts WHERE id IN ({$contactIds})");
    demoQ("DELETE FROM sms_sms WHERE id IN ({$smsIds})");
    demoQ("DELETE FROM camps WHERE id IN ({$campIds})");

    echo 'Removed demo data: ' . count($camps) . ' camp, ' . count($lists) . ' lists, ' . count($contacts) . ' contacts, ' . count($smsRows) . " SMS, plus its replies and webhook log rows.\n";
}
