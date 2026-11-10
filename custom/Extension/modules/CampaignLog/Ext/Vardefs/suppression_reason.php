<?php
// SBCC - likely reason a campaign email was suppressed (worked out in CampaignLog::get_suppression_reason())
$dictionary['CampaignLog']['fields']['suppression_reason'] =
    array(
        'name' => 'suppression_reason',
        'vname' => 'LBL_SUPPRESSION_REASON',
        'type' => 'varchar',
        'len' => 255,
        'source' => 'non-db',
        'reportable' => false,
    );

// SBCC - fast duplicate-address lookup for the suppression reason (some placeholder addresses have 25k+ log rows)
$dictionary['CampaignLog']['indices'][] = array(
    'name' => 'idx_camp_more_info_mkt',
    'type' => 'index',
    'fields' => array('more_information', 'marketing_id'),
);
