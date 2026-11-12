<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/**
 * SBCC - Dropdown options for the Contacts "Association" search filter.
 * Built from the distinct, non-empty association values on active accounts.
 */
function get_account_association_options()
{
    $db = DBManagerFactory::getInstance();
    $options = array('' => '');

    $result = $db->query(
        "SELECT DISTINCT TRIM(association) AS association FROM accounts
         WHERE deleted = 0 AND association IS NOT NULL AND TRIM(association) <> ''
         ORDER BY association"
    );
    while ($row = $db->fetchByAssoc($result, false)) {
        $options[$row['association']] = $row['association'];
    }

    return $options;
}
