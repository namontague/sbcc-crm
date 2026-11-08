<?php

$layout_defs['Contacts']['subpanel_setup']['camps'] = array(
	'order' => 25,
	'sort_order' => 'asc',
	'sort_by' => 'name',
	'module' => 'Camps',
	'subpanel_name' => 'default',
	'get_subpanel_data' => 'camps',
	'add_subpanel_data' => 'camp_id',
	'title_key' => 'LBL_CAMPS_SUBPANEL_TITLE',
	'top_buttons' => array(
		array('widget_class' => 'SubPanelTopButtonQuickCreate'),
		array('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Camps'),
	),
);

$layout_defs['Contacts']['subpanel_setup']['participants'] = array(
	'order' => 25,
	'sort_order' => 'asc',
	'sort_by' => 'participants.name',
	'module' => 'CampParticipants',
	'subpanel_name' => 'default',
	'get_subpanel_data' => 'participants',
	'add_subpanel_data' => 'contact_id',
	'title_key' => 'LBL_CAMPPARTICIPANTS_SUBPANEL_TITLE',
	'top_buttons' => array(
		//array('widget_class' => 'SubPanelTopButtonQuickCreate'),
		//array('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Camps'),
	),
);


?>
