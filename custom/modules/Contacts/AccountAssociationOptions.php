<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/**
 * SBCC - Dropdown options for the Contacts "Association" search filter.
 * Built from the distinct, non-empty association values on active accounts.
 */
function get_account_association_options()
{
    return sbcc_distinct_account_values('association');
}

/**
 * SBCC - Dropdown options for the Contacts "Account Billing State" search filter.
 */
function get_account_billing_state_options()
{
    return sbcc_distinct_account_values('billing_address_state');
}

/**
 * Distinct, trimmed, non-empty values of an accounts column (column name is
 * hard-coded by the callers above, never user input).
 */
function sbcc_distinct_account_values($column)
{
    $db = DBManagerFactory::getInstance();
    $options = array('' => '');

    $result = $db->query(
        "SELECT DISTINCT TRIM($column) AS val FROM accounts
         WHERE deleted = 0 AND $column IS NOT NULL AND TRIM($column) <> ''
         ORDER BY val"
    );
    while ($row = $db->fetchByAssoc($result, false)) {
        $options[$row['val']] = $row['val'];
    }

    return $options;
}
