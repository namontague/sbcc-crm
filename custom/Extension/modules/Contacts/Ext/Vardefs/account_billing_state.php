<?php
// SBCC - search-only field: filter contacts by the billing state of their linked account(s).
// Options are the unique billing state values on accounts; the where clause is a
// subquery in modules/Contacts/metadata/SearchFields.php.
$dictionary['Contact']['fields']['account_billing_state'] =
    array(
        'name' => 'account_billing_state',
        'vname' => 'LBL_ACCOUNT_BILLING_STATE',
        'type' => 'enum',
        'source' => 'non-db',
        'function' => array(
            'name' => 'get_account_billing_state_options',
            'include' => 'custom/modules/Contacts/AccountAssociationOptions.php',
            'params' => array(),
        ),
        'importable' => 'false',
        'massupdate' => false,
        'reportable' => false,
        'studio' => false,
    );
