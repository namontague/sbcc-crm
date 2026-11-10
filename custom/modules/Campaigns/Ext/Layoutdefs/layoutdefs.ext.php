<?php 
 //WARNING: The contents of this file are auto-generated


// SBCC - campaign status page: recipients who were sent the email but have not viewed it
$layout_defs['Campaigns']['subpanel_setup']['not_viewed'] = array(
    'order' => 125,
    'module' => 'CampaignLog',
    'get_subpanel_data' => 'function:track_log_not_viewed',
    'function_parameters' => array('EMAIL_MARKETING_ID_VALUE' => ''),
    'subpanel_name' => 'default',
    'title_key' => 'LBL_LOG_ENTRIES_NOT_VIEWED_TITLE',
    'sort_order' => 'desc',
    'sort_by' => 'campaign_log.id',
);

// SBCC - "Suppressed by Email Address or domain" subpanel: use the layout with the Likely Reason column
$layout_defs['Campaigns']['subpanel_setup']['blocked']['subpanel_name'] = 'ForBlocked';

?>