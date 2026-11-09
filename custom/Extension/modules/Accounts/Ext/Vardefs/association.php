<?php
// SBCC - Cricket association the club (account) belongs to
$dictionary['Account']['fields']['association'] =
    array(
        'name' => 'association',
        'vname' => 'LBL_ASSOCIATION',
        'type' => 'varchar',
        'len' => 150,
        'comment' => 'Cricket association the club is part of',
        'merge_filter' => 'enabled',
        'importable' => 'true',
        'massupdate' => false,
    );
