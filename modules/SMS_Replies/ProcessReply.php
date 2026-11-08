<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Shared logic for turning one transmitsms reply payload into an SMS_Replies
 * record and routing it to a target list. Used by both the live webhook
 * (modules/Camps/wsSMSTech.php) and the one-off backfill of rows already
 * captured in sbcc_website_data_sms (modules/SMS_Replies/backfillLegacyWebhookData.php).
 *
 * Two payload formats are accepted, with keys matched case-insensitively:
 *  - original: "ID", "Message_Id", "Received_At", "First_Name", "Last_Name",
 *    "Msisdn", "Response", "Longcode" (seen in sbcc_website_data_sms, in
 *    both capitalized and lowercase forms)
 *  - current: user_id, rate, mobile, response, message_id, response_id,
 *    longcode, datetime_entry, is_optout
 */
require_once('modules/SMS_Replies/ReplyRouting.php');

/**
 * @param array $payload raw associative array of reply fields (any key case)
 * @param bool $route whether to also run target-list routing (default true).
 *   Pass false to just log the reply (create/link the SMS_Replies record)
 *   without touching any ProspectList - used by the historical backfill,
 *   where routing as designed would spawn a one-off list per uniquely-worded
 *   free-text reply on camps with no tagged/catch-all list configured.
 * @return array {
 *   success: bool,
 *   error: string|null,
 *   reply: SugarBean|null,       the SMS_Replies bean (new or pre-existing duplicate)
 *   isDuplicate: bool,
 *   routed: bool,
 *   routeReason: string|null,
 *   contactId: string|null,
 * }
 */
function processSmsReplyPayload($payload, $route = true)
{
    $payload = array_change_key_case((array) $payload, CASE_LOWER);

    // transmitsms's current reply callback uses different field names
    // (response_id / mobile / datetime_entry, plus user_id, rate, is_optout)
    // from the earlier format (id / msisdn / received_at). Map the new names
    // onto the old ones; note user_id is the account id, not a reply id.
    $aliases = array('response_id' => 'id', 'mobile' => 'msisdn', 'datetime_entry' => 'received_at');
    foreach ($aliases as $from => $to) {
        if ((!isset($payload[$to]) || $payload[$to] === '') && isset($payload[$from])) {
            $payload[$to] = $payload[$from];
        }
    }

    $transmitId = isset($payload['id']) ? trim($payload['id']) : '';
    $messageId  = isset($payload['message_id']) ? trim($payload['message_id']) : '';
    $msisdn     = isset($payload['msisdn']) ? trim($payload['msisdn']) : '';
    $response   = isset($payload['response']) ? trim($payload['response']) : '';
    $firstName  = isset($payload['first_name']) ? trim($payload['first_name']) : '';
    $lastName   = isset($payload['last_name']) ? trim($payload['last_name']) : '';
    $longcode   = isset($payload['longcode']) ? trim($payload['longcode']) : '';
    $receivedAt = isset($payload['received_at']) ? trim($payload['received_at']) : '';

    if ($msisdn === '') {
        return array(
            'success' => false, 'error' => 'missing msisdn', 'reply' => null,
            'isDuplicate' => false, 'routed' => false, 'routeReason' => null, 'contactId' => null,
        );
    }

    // transmitsms may retry webhook delivery - de-duplicate on their reply id.
    $reply = null;
    $isDuplicate = false;
    if ($transmitId !== '') {
        $existing = BeanFactory::newBean('SMS_Replies');
        if ($existing->retrieve_by_string_fields(array('transmit_id' => $transmitId))) {
            $reply = $existing;
            $isDuplicate = true;
        }
    }

    if (empty($reply)) {
        $reply = BeanFactory::newBean('SMS_Replies');
        $reply->transmit_id = $transmitId;
        $reply->message_id = $messageId;
        $reply->msisdn = $msisdn;
        $reply->response = $response;
        $reply->first_name = $firstName;
        $reply->last_name = $lastName;
        $reply->longcode = $longcode;
        $reply->received_at = ($receivedAt !== '') ? $receivedAt : gmdate('Y-m-d H:i:s');

        $fullName = trim($firstName . ' ' . $lastName);
        $reply->name = ($fullName !== '') ? $fullName : $msisdn;

        // Link back to the SMS_SMS record that was sent with this message_id.
        if ($messageId !== '') {
            $sms = BeanFactory::newBean('SMS_SMS');
            if ($sms->retrieve_by_string_fields(array('message_id' => $messageId))) {
                $reply->sms_id = $sms->id;
            }
        }

        $reply->save();
    }

    // File the participant into a camp/reply target list for follow-up. Safe
    // to run even for a duplicate reply - it just re-confirms the same
    // list membership (set_relationship/Link2::add() are dedup-safe).
    if ($route) {
        list($routed, $routeReason, $contactId) = routeSmsReplyToTargetList($reply);
    } else {
        $routed = false;
        $routeReason = 'routing skipped';
        $contactId = null;
    }

    return array(
        'success' => true,
        'error' => null,
        'reply' => $reply,
        'isDuplicate' => $isDuplicate,
        'routed' => $routed,
        'routeReason' => $routeReason,
        'contactId' => $contactId,
    );
}
