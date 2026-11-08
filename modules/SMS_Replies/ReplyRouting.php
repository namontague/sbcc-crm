<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Files an incoming SMS reply into a target list for follow-up, so everyone
 * who gave a similar reply to the same SMS ends up grouped together (e.g.
 * everyone who replied "Yes" in one list, everyone who replied "No" in another).
 *
 * An SMS is sent either from a CAMP or from a plain TARGET LIST (its
 * parent_type is 'Camps' or 'ProspectLists'). Both work the same way, with a
 * "scope" that says which contacts were texted and where that SMS's reply
 * lists live:
 *
 *  Camp:        contacts = members of the camp's master list
 *               (prospect_lists.camp_master_list = 1, linked to the camp);
 *               reply lists = lists linked to the same camp.
 *  Target list: contacts = members of the parent list itself;
 *               reply lists = lists whose "Collects SMS Replies For List"
 *               field (reply_source_list_id) points at the parent list.
 *
 * How a reply is traced back to a contact:
 *  - The reply is linked (via message_id) to the SMS_SMS record that was sent.
 *  - The reply's msisdn is matched against the scope's contacts' phone_mobile,
 *    rather than searching every Contact in the system.
 *
 * How a reply is matched to a reply list:
 *  - Staff tag the reply lists with the reply(ies) they should collect, via
 *    the "SMS Reply Keyword(s)" field (comma-separated, e.g. "1,yes,y"). A
 *    reply goes to a list in the scope whose keywords contain it (exact
 *    match, case-insensitive).
 *  - A list tagged with "*" is the scope's catch-all - it collects any reply
 *    that doesn't match another tagged list.
 *  - If the scope has no matching tagged list and no catch-all, a new list is
 *    created on the fly (tagged with that exact reply) so nothing is silently
 *    lost - but a scope with its lists tagged up front never hits that.
 *
 * Lists made for one particular SMS (its "Create Reply Lists" option - see
 * CreateReplyLists.php) carry that SMS's id in reply_sms_id. If the SMS has
 * any, only those are used for its replies; otherwise the camp's/list's
 * shared reply lists are, ignoring lists that belong to some other SMS.
 *
 * The contact is ADDED to the reply list; they are NOT removed from the
 * source list (so future sends via the transmitsms-synced list are unaffected).
 */

/**
 * @param SugarBean $reply a saved SMS_Replies bean (must have sms_id and msisdn set)
 * @return array(bool $routed, string $reason, string|null $contactId) for logging/diagnostics
 */
function routeSmsReplyToTargetList($reply)
{
    global $db;

    if (empty($reply->sms_id)) {
        return array(false, 'reply is not linked to a sent SMS (no matching message_id)', null);
    }

    $sms = BeanFactory::getBean('SMS_SMS', $reply->sms_id);
    $scope = smsReply_getScope($db, $sms);
    if (isset($scope['error'])) {
        return array(false, $scope['error'], null);
    }

    $contactId = smsReply_matchContactInList($db, $scope['poolListId'], $reply->msisdn);
    if (empty($contactId)) {
        return array(false, "no contact in {$scope['poolLabel']} matches msisdn {$reply->msisdn}", null);
    }

    $normalizedResponse = smsReply_normalizeResponse($reply->response);

    $targetListId = smsReply_findTaggedList($db, $scope, $normalizedResponse);
    $how = 'matched tagged list';

    if (empty($targetListId)) {
        $targetListId = smsReply_findCatchAllList($db, $scope);
        $how = 'matched catch-all list';
    }

    if (empty($targetListId)) {
        $targetListId = smsReply_createTaggedList($scope, $reply->response, $normalizedResponse);
        $how = 'auto-created list (no tagged or catch-all list configured)';
    }

    if (empty($targetListId)) {
        smsReply_recordRouting($db, $reply, $contactId, null);
        return array(false, "could not find or create a target list for reply '{$reply->response}' on {$scope['label']}", $contactId);
    }

    $targetList = BeanFactory::getBean('ProspectLists', $targetListId);
    $targetList->load_relationship('contacts');
    $targetList->contacts->add($contactId);

    smsReply_recordRouting($db, $reply, $contactId, $targetListId);

    return array(true, "added contact {$contactId} to list {$targetListId} ({$how})", $contactId);
}

/**
 * Stores on the reply which contact it was matched to and which target list
 * they were added to, for the Contact / Added To List columns of the SMS
 * Replies subpanel. A plain UPDATE rather than $reply->save(), so recording
 * this doesn't touch the reply's date_modified.
 *
 * @param string|null $listId null leaves the reply's list as it is
 */
function smsReply_recordRouting($db, $reply, $contactId, $listId)
{
    $set = "contact_id = '" . $db->quote($contactId) . "'";
    $reply->contact_id = $contactId;
    if (!empty($listId)) {
        $set .= ", reply_list_id = '" . $db->quote($listId) . "'";
        $reply->reply_list_id = $listId;
    }
    $db->query("UPDATE sms_replies SET {$set} WHERE id = '" . $db->quote($reply->id) . "'",
        true, "Error recording contact/list on SMS reply {$reply->id}: ");
}

/**
 * Works out which camp or target list an SMS was sent from.
 *
 * @return array either array('error' => reason) or
 *   array('type' => 'Camps'|'ProspectLists', 'id' => parent id, 'label' => ...,
 *         'poolListId' => list holding the contacts that were texted, 'poolLabel' => ...)
 */
function smsReply_getScope($db, $sms)
{
    if (empty($sms->id) || empty($sms->parent_id)) {
        return array('error' => 'originating SMS has no parent camp or target list');
    }

    // Set when the SMS has reply lists of its own - see smsReply_scopeSql().
    $ownListsSmsId = smsReply_smsHasOwnLists($db, $sms->id) ? $sms->id : null;

    if ($sms->parent_type === 'Camps') {
        $masterListId = smsReply_getCampMasterListId($db, $sms->parent_id);
        if (empty($masterListId)) {
            return array('error' => "camp {$sms->parent_id} has no master target list");
        }
        return array(
            'type' => 'Camps',
            'id' => $sms->parent_id,
            'label' => "camp {$sms->parent_id}",
            'poolListId' => $masterListId,
            'poolLabel' => "the camp's master list",
            'ownListsSmsId' => $ownListsSmsId,
        );
    }

    if ($sms->parent_type === 'ProspectLists') {
        $list = BeanFactory::getBean('ProspectLists', $sms->parent_id);
        if (empty($list->id)) {
            return array('error' => "parent target list {$sms->parent_id} not found");
        }
        return array(
            'type' => 'ProspectLists',
            'id' => $list->id,
            'label' => "target list {$list->id}",
            'poolListId' => $list->id,
            'poolLabel' => 'the parent target list',
            'ownListsSmsId' => $ownListsSmsId,
        );
    }

    return array('error' => 'originating SMS is not linked to a camp or target list');
}

/**
 * Whether any reply lists were created for this particular SMS.
 */
function smsReply_smsHasOwnLists($db, $smsId)
{
    $smsIdQ = $db->quote($smsId);
    $result = $db->query("SELECT id FROM prospect_lists WHERE reply_sms_id = '{$smsIdQ}' AND deleted = 0 LIMIT 1",
        true, "Error looking up reply lists for SMS {$smsId}: ");
    $row = $db->fetchByAssoc($result);
    return !empty($row['id']);
}

/**
 * SQL where condition (on prospect_lists) for the reply lists of an SMS:
 * its own lists if it has any, otherwise its camp's or parent list's shared
 * reply lists, leaving out any made for a different SMS. Also used by the
 * Reply Lists subpanel (ReplyListsSubpanel.php).
 *
 * @param string $table prospect_lists table name or alias
 * @return string|null null if the SMS has no camp or target list parent
 */
function smsReply_replyListsWhere($db, $sms, $table = 'prospect_lists')
{
    if (empty($sms->id) || empty($sms->parent_id)) {
        return null;
    }

    if (smsReply_smsHasOwnLists($db, $sms->id)) {
        return "{$table}.reply_sms_id = '" . $db->quote($sms->id) . "'";
    }

    $notOwned = "({$table}.reply_sms_id IS NULL OR {$table}.reply_sms_id = '')";
    $parentIdQ = $db->quote($sms->parent_id);
    if ($sms->parent_type === 'ProspectLists') {
        return "{$table}.reply_source_list_id = '{$parentIdQ}' AND {$notOwned}";
    }
    if ($sms->parent_type === 'Camps') {
        return "{$table}.id IN (SELECT plp.prospect_list_id FROM prospect_lists_prospects plp
                    WHERE plp.related_type = 'Camps' AND plp.related_id = '{$parentIdQ}' AND plp.deleted = 0)
                AND {$table}.reply_keywords IS NOT NULL AND TRIM({$table}.reply_keywords) != '' AND {$notOwned}";
    }
    return null;
}

/**
 * SQL fragments restricting prospect_lists (aliased pl) to this scope's reply lists.
 * @return array('join' => ..., 'where' => ...)
 */
function smsReply_scopeSql($db, $scope)
{
    if (!empty($scope['ownListsSmsId'])) {
        return array('join' => '', 'where' => " AND pl.reply_sms_id = '" . $db->quote($scope['ownListsSmsId']) . "'");
    }

    $notOwned = " AND (pl.reply_sms_id IS NULL OR pl.reply_sms_id = '')";
    $idQ = $db->quote($scope['id']);
    if ($scope['type'] === 'Camps') {
        return array(
            'join' => "INNER JOIN prospect_lists_prospects plp ON plp.prospect_list_id = pl.id
                           AND plp.related_type = 'Camps' AND plp.related_id = '{$idQ}' AND plp.deleted = 0",
            'where' => $notOwned,
        );
    }
    return array('join' => '', 'where' => " AND pl.reply_source_list_id = '{$idQ}'" . $notOwned);
}

function smsReply_normalizeMsisdn($number)
{
    $digits = preg_replace('/\D+/', '', (string) $number);
    if ($digits === '') {
        return '';
    }
    if (strlen($digits) > 9 && substr($digits, 0, 2) === '61') {
        $digits = substr($digits, 2);
    } elseif (strlen($digits) > 9 && substr($digits, 0, 1) === '0') {
        $digits = substr($digits, 1);
    }
    return $digits;
}

function smsReply_normalizeResponse($response)
{
    $normalized = strtolower(trim($response));
    $normalized = rtrim($normalized, ".!? \t\n\r");
    return $normalized;
}

function smsReply_getCampMasterListId($db, $campId)
{
    $campIdQ = $db->quote($campId);
    $query = "SELECT pl.id
              FROM prospect_lists pl
              INNER JOIN prospect_lists_prospects plp ON plp.prospect_list_id = pl.id
                  AND plp.related_type = 'Camps' AND plp.related_id = '{$campIdQ}' AND plp.deleted = 0
              WHERE pl.camp_master_list = 1 AND pl.deleted = 0
              LIMIT 1";
    $result = $db->query($query, true, "Error looking up camp master list for camp {$campId}: ");
    $row = $db->fetchByAssoc($result);
    return !empty($row['id']) ? $row['id'] : null;
}

function smsReply_matchContactInList($db, $listId, $msisdn)
{
    $targetDigits = smsReply_normalizeMsisdn($msisdn);
    if ($targetDigits === '') {
        return null;
    }

    $listIdQ = $db->quote($listId);
    $query = "SELECT c.id, c.phone_mobile
              FROM contacts c
              INNER JOIN prospect_lists_prospects plp ON plp.related_id = c.id
                  AND plp.related_type = 'Contacts' AND plp.prospect_list_id = '{$listIdQ}' AND plp.deleted = 0
              WHERE c.deleted = 0 AND c.phone_mobile IS NOT NULL AND c.phone_mobile != ''";
    $result = $db->query($query, true, "Error matching msisdn to contacts in list {$listId}: ");

    while ($row = $db->fetchByAssoc($result)) {
        if (smsReply_normalizeMsisdn($row['phone_mobile']) === $targetDigits) {
            return $row['id'];
        }
    }
    return null;
}

/**
 * Finds a reply list in this scope explicitly tagged (via reply_keywords) with
 * the given normalized reply text. Ignores catch-all ("*") lists.
 */
function smsReply_findTaggedList($db, $scope, $normalizedResponse)
{
    if ($normalizedResponse === '') {
        return null;
    }

    $sql = smsReply_scopeSql($db, $scope);
    $query = "SELECT pl.id, pl.reply_keywords
              FROM prospect_lists pl
              {$sql['join']}
              WHERE pl.deleted = 0 AND pl.reply_keywords IS NOT NULL AND TRIM(pl.reply_keywords) != ''
                  AND TRIM(pl.reply_keywords) != '*'{$sql['where']}";
    $result = $db->query($query, true, "Error looking up reply-keyword lists for {$scope['label']}: ");

    while ($row = $db->fetchByAssoc($result)) {
        foreach (explode(',', $row['reply_keywords']) as $keyword) {
            if (strtolower(trim($keyword)) === $normalizedResponse) {
                return $row['id'];
            }
        }
    }
    return null;
}

/**
 * Finds this scope's catch-all list (reply_keywords = "*"), if configured.
 */
function smsReply_findCatchAllList($db, $scope)
{
    $sql = smsReply_scopeSql($db, $scope);
    $query = "SELECT pl.id
              FROM prospect_lists pl
              {$sql['join']}
              WHERE pl.deleted = 0 AND TRIM(pl.reply_keywords) = '*'{$sql['where']}
              LIMIT 1";
    $result = $db->query($query, true, "Error looking up catch-all reply list for {$scope['label']}: ");
    $row = $db->fetchByAssoc($result);
    return !empty($row['id']) ? $row['id'] : null;
}

/**
 * Last-resort fallback: creates a new reply list in this scope tagged with the
 * exact reply text, so a reply is never silently dropped even if staff haven't
 * set up tagged/catch-all lists yet.
 */
function smsReply_createTaggedList($scope, $rawResponse, $normalizedResponse)
{
    $parent = BeanFactory::getBean($scope['type'], $scope['id']);
    if (empty($parent->id)) {
        return null;
    }

    $label = (trim($rawResponse) !== '') ? trim($rawResponse) : 'Unrecognised';

    return smsReply_createReplyList(
        $scope['type'],
        $scope['id'],
        trim($parent->name) . ' - Replied: ' . $label,
        ($normalizedResponse !== '') ? $normalizedResponse : '*',
        !empty($scope['ownListsSmsId']) ? $scope['ownListsSmsId'] : null
    );
}

/**
 * Creates an empty reply list collecting replies for a camp or target list,
 * or for one SMS sent from it when $smsId is given.
 *
 * @param string $parentType 'Camps' or 'ProspectLists' (the SMS's parent)
 * @param string $keywords comma-separated reply keywords, or '*' for a catch-all
 * @return string the new list's id
 */
function smsReply_createReplyList($parentType, $parentId, $name, $keywords, $smsId = null)
{
    $newList = BeanFactory::newBean('ProspectLists');
    $newList->name = mb_substr($name, 0, 255);
    $newList->list_type = 'default';
    $newList->reply_keywords = $keywords;
    if ($parentType === 'ProspectLists') {
        $newList->reply_source_list_id = $parentId;
    }
    if (!empty($smsId)) {
        $newList->reply_sms_id = $smsId;
    }
    $newList->save();

    if ($parentType === 'Camps') {
        // Direct relationship-table insert, not $camp->prospect_lists->add() -
        // that goes through Sugar's relationship/hook framework, which for the
        // 'prospect_list_camps' relationship triggers the existing
        // SBCC_ProspectLists_hooks::AddtoList after_relationship_add hook (see
        // custom/modules/ProspectLists/sbcc_logic_hooks.php): it auto-populates
        // any newly camp-linked, non-master list with the camp's ENTIRE
        // participant roster. Right for a staff-created sub-list, wrong here -
        // this list should start empty and only ever gain the contacts who
        // gave this reply.
        $newList->set_relationship('prospect_lists_prospects', array(
            'related_id' => $parentId,
            'related_type' => 'Camps',
            'prospect_list_id' => $newList->id,
        ));
    }

    return $newList->id;
}

/**
 * Moves a reply's contact to a different one of its SMS's reply lists - the
 * "Added To List" dropdown in the SMS Replies subpanel (see
 * SMS_RepliesController::action_setReplyList()).
 *
 * The contact is added to the new list and taken out of the one the reply
 * had them in - unless another of their replies to the same SMS still has
 * them in that list. Goes through the normal list relationship, so the same
 * hooks run as when a contact is added/removed on the list's own screen.
 *
 * @param SugarBean $reply a saved SMS_Replies bean
 * @param string $newListId one of the SMS's reply lists, or '' for none
 * @return array(bool $success, string $message)
 */
function smsReply_moveReplyToList($db, $reply, $newListId)
{
    if (empty($reply->id) || empty($reply->sms_id)) {
        return array(false, 'This reply is not linked to an SMS.');
    }
    if (empty($reply->contact_id)) {
        return array(false, 'This reply has no contact to add to a list.');
    }

    $newListId = trim((string) $newListId);
    $oldListId = trim((string) $reply->reply_list_id);
    if ($newListId === $oldListId) {
        return array(true, 'No change.');
    }

    if ($newListId !== '') {
        $allowed = smsReply_getReplyListOptions($db, $reply->sms_id);
        if (!isset($allowed[$newListId])) {
            return array(false, 'That list is not one of this SMS\'s reply lists.');
        }
        $newList = BeanFactory::getBean('ProspectLists', $newListId);
        $newList->load_relationship('contacts');
        $newList->contacts->add($reply->contact_id);
    }

    if ($oldListId !== '') {
        $replyIdQ = $db->quote($reply->id);
        $contactIdQ = $db->quote($reply->contact_id);
        $oldListIdQ = $db->quote($oldListId);
        $result = $db->query("SELECT id FROM sms_replies
                              WHERE deleted = 0 AND id != '{$replyIdQ}'
                                  AND contact_id = '{$contactIdQ}' AND reply_list_id = '{$oldListIdQ}'
                              LIMIT 1");
        $other = $db->fetchByAssoc($result);
        if (empty($other['id'])) {
            $oldList = BeanFactory::getBean('ProspectLists', $oldListId);
            if (!empty($oldList->id)) {
                $oldList->load_relationship('contacts');
                $oldList->contacts->delete($oldListId, $reply->contact_id);
            }
        }
    }

    $listSql = ($newListId !== '') ? "'" . $db->quote($newListId) . "'" : 'NULL';
    $db->query("UPDATE sms_replies SET reply_list_id = {$listSql} WHERE id = '" . $db->quote($reply->id) . "'",
        true, "Error changing the list on SMS reply {$reply->id}: ");
    $reply->reply_list_id = $newListId;

    return array(true, 'Saved.');
}

/**
 * The reply lists of an SMS, for the "Added To List" dropdown.
 * @return array list id => list name, ordered by name
 */
function smsReply_getReplyListOptions($db, $smsId)
{
    $options = array();
    $sms = BeanFactory::getBean('SMS_SMS', $smsId);
    $where = smsReply_replyListsWhere($db, $sms);
    if (empty($where)) {
        return $options;
    }
    $result = $db->query("SELECT prospect_lists.id, prospect_lists.name FROM prospect_lists
                          WHERE prospect_lists.deleted = 0 AND {$where}
                          ORDER BY prospect_lists.name");
    while ($row = $db->fetchByAssoc($result, false)) {
        $options[$row['id']] = $row['name'];
    }
    return $options;
}
