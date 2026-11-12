<?php
// SBCC - search-only field: filter contacts by the association of their linked account(s).
// Options are the unique association values on accounts; the where clause is a
// subquery in modules/Contacts/metadata/SearchFields.php.
$dictionary['Contact']['fields']['account_association'] =
    array(
        'name' => 'account_association',
        'vname' => 'LBL_ACCOUNT_ASSOCIATION',
        'type' => 'enum',
        'source' => 'non-db',
        'function' => array(
            'name' => 'get_account_association_options',
            'include' => 'custom/modules/Contacts/AccountAssociationOptions.php',
            'params' => array(),
        ),
        'importable' => 'false',
        'massupdate' => false,
        'reportable' => false,
        'studio' => false,
    );
