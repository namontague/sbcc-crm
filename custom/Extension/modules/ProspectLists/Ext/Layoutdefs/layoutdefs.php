<?php

$layout_defs['ProspectLists']['subpanel_setup']['camps'] = array(
			'order' => 40,
			'module' => 'Camps',
			'sort_order' => 'asc',
			'sort_by' => 'name',
			'subpanel_name' => 'ForProspectLists',
			'get_subpanel_data' => 'camps',
			'add_subpanel_data' => 'camp_id',
			'title_key' => 'LBL_CAMPS_SUBPANEL_TITLE',
                        'refresh_page'=>1,
                        'top_buttons' => array(
                            array('widget_class' => 'SubPanelTopButtonQuickCreate'),
                            array('widget_class'=>'SubPanelTopSelectButton','mode'=>'MultiSelect', 'metadata'=>'popupdefs_prospect_lists'),
                         ),
		);
$layout_defs['ProspectLists']['subpanel_setup']['contacts'] =array(
			'order' => 20,
			'module' => 'Contacts',
			'sort_by' => 'last_name, first_name',
			'sort_order' => 'asc',
			'subpanel_name' => 'ForProspectLists',
			'get_subpanel_data' => 'contacts',
			'title_key' => 'LBL_CONTACTS_SUBPANEL_TITLE',
			'top_buttons' => array(
				array('widget_class' => 'SubPanelTopButtonQuickCreate'),
                                array('widget_class' => 'SubPanelTopComposeEmailButton_SBCC','linked_field' => 'contacts'),
				array('widget_class'=>'SubPanelTopSelectButton','mode'=>'MultiSelect'),
			),
		);

$layout_defs['ProspectLists']['subpanel_setup']['email'] = array(
                    'order' => 20,
                    'sort_order' => 'desc',
                    'sort_by' => 'date_sent',
                    'title_key' => 'LBL_EMAILS_SUBPANEL_TITLE',
                    'type' => 'collection',
                    'subpanel_name' => 'email',   //this values is not associated with a physical file.
                    'module'=>'Emails',
                    'collection_list' => array(
                        'emails' => array(
                            'module' => 'Emails',
                            'subpanel_name' => 'ForAll',
                            'get_subpanel_data' => 'emails',
                        ),
                    ),
		);


$layout_defs['ProspectLists']['subpanel_setup']['sms'] = array(
                'order' => -10,
                'module' => 'SMS_SMS',
                'subpanel_name' => 'default',
                'get_subpanel_data' => 'sms',
                //'add_subpanel_data' => 'camp_id',
                'title_key' => 'LBL_SMS_SMS_SUBPANEL_TITLE',
                'top_buttons' => array(
                    array(
                        'widget_class' => 'SubPanelTopCreateButton',
                    ),
                ),
            );



?>
