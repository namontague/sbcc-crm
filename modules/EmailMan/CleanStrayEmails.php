<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/*
 * (C) 2012 Enterprise Intelligence Systems Pty Ltd
 *
 * Script to clean redundant emails in the system
 *
 */

// Query limit for process
define("CSE_LIMIT", 10000);

$GLOBALS['log']->info("Cleaning stray emails");

require_once('include/TimeDate.php');
require_once('include/utils.php');

global $sugar_config, $timedate;

/* Process inbound emails */
if ($sugar_config['stray_inbound_period'] != 0)
{
    $GLOBALS['log']->info("Processing stray inbound emails");

    // Calculate inbound time
    $inbound_months = $sugar_config['stray_inbound_period'];
    $date_sent = $timedate->getNow();
    $date_sent->sub(new DateInterval("P{$inbound_months}M"));
    $date_sent =  $date_sent->format($timedate->get_db_date_time_format());

    /* Get inbound emails that are not associated to any beans in sugar */
    $query_string = <<< EOQ

    SELECT id FROM emails
    WHERE id NOT IN
    (
        SELECT e.id
            /*, e.date_sent, e.parent_id, e.parent_type,
            eabr.bean_id, eabr. bean_module,
            ea.email_address,
            et.from_addr, et.to_addrs, et.cc_addrs, et.bcc_addrs
            */
        FROM emails e

        /* Join email details */
        INNER JOIN emails_text et ON et.email_id = e.id AND et.deleted = 0

        /* Join to, from, cc and bcc  */
        INNER JOIN email_addresses ea ON (INSTR(et.to_addrs, ea.email_address) > 0
                OR INSTR(et.from_addr, ea.email_address) > 0
                OR INSTR(et.cc_addrs, ea.email_address) > 0
                OR INSTR(et.bcc_addrs, ea.email_address) > 0)
            AND ea.deleted = 0

        /* join beans email addresses are related to */
        INNER JOIN email_addr_bean_rel eabr ON eabr.email_address_id = ea.id AND eabr.deleted = 0

        WHERE e.type = 'inbound'
            AND e.date_sent < '{$date_sent}'
            AND e.deleted = 0
    )
    AND type = 'inbound'
    AND date_sent < '{$date_sent}'
    AND deleted = 0

EOQ;

    process_stray_emails($query_string);
}

/* Process outbound emails */
if ($sugar_config['stray_outbound_period'] != 0)
{
    $GLOBALS['log']->info("Processing stray outbound emails");

    $outbound_months = $sugar_config['stray_outbound_period'];
    $date_sent = $timedate->getNow();
    $date_sent->sub(new DateInterval("P{$outbound_months}M"));
    $date_sent =  $date_sent->format($timedate->get_db_date_time_format());

    $query_string = <<< EOQ

    SELECT id FROM emails
    WHERE id NOT IN
    (

        SELECT e.id
            /*, e.date_sent, e.parent_id, e.parent_type, e.type, e.status,
            eabr.bean_id, eabr. bean_module,
            ea.email_address,
            et.from_addr, et.to_addrs, et.cc_addrs, et.bcc_addrs */

        FROM emails e

        /* Join email details */
        INNER JOIN emails_text et ON et.email_id = e.id AND et.deleted = 0

        /* Join to, cc and bcc  */
        INNER JOIN email_addresses ea ON (INSTR(et.to_addrs, ea.email_address) > 0
                OR INSTR(et.cc_addrs, ea.email_address) > 0
                OR INSTR(et.bcc_addrs, ea.email_address) > 0)
            AND ea.deleted = 0

        /* join beans email addresses are related to */
        INNER JOIN email_addr_bean_rel eabr ON eabr.email_address_id = ea.id AND eabr.deleted = 0

        WHERE e.type = 'out'
            AND e.date_sent < '{$date_sent}'
            AND e.deleted = 0

    )
    AND type = 'out'
    AND date_sent < '{$date_sent}'
    AND deleted = 0

EOQ;

    process_stray_emails($query_string);
}

/* Deletes emails and their attatchments
 * of the result set returned by the query
 */
function process_stray_emails($query_string) {
    global $sugar_config;
    $db = DBManagerFactory::getInstance();

    $emails_rset = $db->limitQuery($query_string, 0, CSE_LIMIT, true, "Error retrieving candidate emails");
    $GLOBALS['log']->info("Stray emails to be removed: {$db->getRowCount($emails_rset)}");
    while ($email_row = $db->fetchByAssoc($emails_rset))
    {
        if (isset($sugar_config['email_default_delete_attachments']) && $sugar_config['email_default_delete_attachments'] == true)
        {
            // Get attatchments
            $query_string = "SELECT id FROM notes WHERE parent_type = 'Emails' AND parent_id = '{$email_row['id']}'";
            $notes_rset = $db->query($query_string);
            if ($db->getRowCount($notes_rset) > 0)
            {
                while ($note_row = $db->fetchByAssoc($notes_rset))
                {
                    // Delete attatchment
                    $removeFile = getcwd() . "/{$sugar_config['upload_dir']}{$note_row['id']}";
                    if (file_exists($removeFile)) {
                        if (!unlink($removeFile)) {
                            $GLOBALS['log']->error("*** Could not unlink() file: [ {$removeFile} ]");
                        }
                    }
                }
                // Delete note in database
                $query_string = "UPDATE notes SET deleted = 1 WHERE parent_type = 'Emails' AND parent_id = '{$email_row['id']}'";
                $db->query($query_string);
            }
        }

        // Delete email
        $query_string = "UPDATE emails SET deleted = 1 WHERE id = '{$email_row['id']}'";
        $db->query($query_string);
        $query_string = "UPDATE emails_text SET deleted = 1 WHERE email_id = '{$email_row['id']}'";
        $db->query($query_string);
    }

}

?>
