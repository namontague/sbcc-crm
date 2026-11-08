<?php

  $GLOBALS['log']->debug('Debug Start Booking Webhook:');

  if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

  /**
   * Inbound webhook for transmitsms SMS replies.
   *
   * Registered as the 'wsSMSTech' entry point (see
   * custom/include/MVC/Controller/entry_point_registry.php), so this is
   * reached via:
   *
   *   index.php?entryPoint=wsSMSTech
   *
   * transmitsms posts the reply payload. Two formats have been seen:
   * "ID"/"Message_Id"/"Received_At"/"Msisdn"/"Response"/"Longcode" and the
   * newer response_id/mobile/datetime_entry/message_id/response/longcode/
   * user_id/rate/is_optout - processSmsReplyPayload() accepts both.
   *
   * Every delivery is also logged to sbcc_website_data_sms (raw payload plus
   * a record_status of processed / duplicate / rejected), even when it can't
   * be linked to an SMS, so a gap in that table shows the webhook has stopped.
   *
   * Each reply is stored as an SMS_Replies record and, when its message_id
   * matches an SMS_SMS record's message_id (set when that SMS was sent), is
   * linked to it, filed into a camp reply target list, and shown in the
   * Replies subpanel on that SMS record's detail view. See
   * modules/SMS_Replies/ProcessReply.php and ReplyRouting.php.
   */

  // Work out where the reply fields arrived: a form POST, a JSON body, a
  // form-encoded body sent with an unusual content type (PHP only fills
  // $_POST for the standard form types), or query-string params (GET callback).
  $payload = $_POST;
  $raw = (string) file_get_contents('php://input');
  if (empty($payload) && $raw !== '') {
      $decoded = json_decode($raw, true);
      if (is_array($decoded)) {
          $payload = $decoded;
      } elseif (strpos($raw, '=') !== false) {
          parse_str($raw, $parsed);
          if (!empty($parsed)) {
              $payload = $parsed;
          }
      }
  }
  if (empty($payload)) {
      $query = $_GET;
      unset($query['entryPoint']);
      if (!empty($query)) {
          $payload = $query;
      }
  }

  // If none of that produced any fields, the delivery would be logged as an
  // empty "[]" and the message lost. Log the whole request instead, so the
  // data is there to see what the sender is actually posting.
  $logData = $payload;
  if (empty($payload)) {
      $query = $_GET;
      unset($query['entryPoint']);
      $logData = array(
          '_note' => 'no reply fields could be parsed from this request',
          '_method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '',
          '_content_type' => isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '',
          '_query' => $query,
          '_raw_body' => (mb_check_encoding($raw, 'UTF-8') ? (string) substr($raw, 0, 65536) : 'base64:' . base64_encode(substr($raw, 0, 65536))),
      );
  }

  $GLOBALS['log']->debug('Debug Booking Webhook Data:', var_export($logData, true));

  // Audit log: record every delivery in sbcc_website_data_sms before doing
  // anything else, whether or not it can be linked to an SMS - so a gap in
  // this table's date_created values shows the webhook has stopped receiving.
  // Starts 'pending' and is updated below once processed; a row left
  // 'pending' means processing died part-way (modules/SMS_Replies/
  // backfillLegacyWebhookData.php will pick it up).
  // The JSON must be SQL-escaped: the old version interpolated it raw, and
  // MySQL's backslash handling turned its \n escapes into literal newlines.
  // Use mysqli_real_escape_string directly rather than $db->quote(): quote()
  // runs from_html() first, which turns the &quot; Sugar has already put in
  // $_POST back into a bare " inside the JSON string and invalidates it.
  global $db;
  // JSON_PARTIAL_OUTPUT_ON_ERROR so one badly-encoded value can't make
  // json_encode() return false and leave an empty row.
  $logJson = json_encode($logData, defined('JSON_PARTIAL_OUTPUT_ON_ERROR') ? JSON_PARTIAL_OUTPUT_ON_ERROR : 0);
  $logQuoted = mysqli_real_escape_string($db->getDatabase(), (string) $logJson);
  $db->query("INSERT INTO sbcc_website_data_sms (`date_created`,`record_status`,`file_data`)
              VALUES (now(),'pending','{$logQuoted}')", false, 'Error logging SMS webhook delivery');
  $logRes = $db->query('SELECT LAST_INSERT_ID() AS id');
  $logRow = $db->fetchByAssoc($logRes, false);
  $logId = !empty($logRow['id']) ? (int) $logRow['id'] : 0;

  // No user is loaded for this entry point (auth is disabled for it), but
  // SugarBean::save() needs one for created_by/assigned_user_id - this feature
  // saves new SMS_Replies and (via routeSmsReplyToTargetList) new ProspectLists.
  global $current_user;
  $current_user = new User();
  $current_user->getSystemUser();

  require_once('modules/SMS_Replies/ProcessReply.php');
  $result = processSmsReplyPayload($payload);

  if ($logId) {
      $logStatus = !$result['success'] ? 'rejected' : ($result['isDuplicate'] ? 'duplicate' : 'processed');
      $logSet = "record_status = '" . $db->quote($logStatus) . "'";
      if (!empty($result['contactId'])) {
          $logSet .= ", participant_id = '" . $db->quote($result['contactId']) . "'";
      }
      $db->query("UPDATE sbcc_website_data_sms SET {$logSet} WHERE id = {$logId}", false, 'Error updating SMS webhook log status');
  }

  if (!$result['success']) {
      $GLOBALS['log']->warn("wsSMSTech: rejected request - {$result['error']}");
      header('HTTP/1.1 400 Bad Request');
      $GLOBALS['log']->debug('Debug End Booking Webhook:');
      return;
  }

  $reply = $result['reply'];
  if ($result['isDuplicate']) {
      $GLOBALS['log']->debug("wsSMSTech: duplicate reply ignored, existing id={$reply->id}");
  } else {
      $GLOBALS['log']->debug("wsSMSTech: stored reply {$reply->id} from {$reply->msisdn} (message_id={$reply->message_id}, linked=" . (empty($reply->sms_id) ? 'no' : 'yes') . ')');
  }
  $GLOBALS['log']->debug('wsSMSTech: reply routing ' . ($result['routed'] ? 'succeeded' : 'skipped') . " - {$result['routeReason']}");

  header('Content-Type: application/json');
  echo json_encode(array('status' => 'ok', 'id' => $reply->id, 'linked' => !empty($reply->sms_id)));

  $GLOBALS['log']->debug('Debug End Booking Webhook:');
