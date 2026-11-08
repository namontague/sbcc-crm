<?php 
 //WARNING: The contents of this file are auto-generated




$layout_defs['Accounts']['subpanel_setup']['contacts'] = array(
			'order' => 30,
			'module' => 'Contacts',
			'sort_order' => 'asc',
			'sort_by' => 'account_contact_priority, last_name, first_name',
			'subpanel_name' => 'ForAccounts',
			'get_subpanel_data' => 'contacts',
			'add_subpanel_data' => 'contact_id',
			'title_key' => 'LBL_CONTACTS_SUBPANEL_TITLE',
			'top_buttons' => array(
				array('widget_class' => 'SubPanelTopCreateAccountNameButton'),
				array('widget_class' => 'SubPanelTopSelectButton', 'mode'=>'MultiSelect'),
        array('widget_class' => 'SubPanelTopComposeEmailButton_SBCC','linked_field' => 'contacts'),  
			)
);




?>