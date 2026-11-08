<?php

$dictionary['camp_staff'] = array(
	'table' => 'camp_staff',
	'fields' => array(
		array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
		array('name' => 'contact_id', 'type' => 'varchar', 'len' => '36'),
		array('name' => 'camp_id', 'type' => 'varchar', 'len' => '36'),
		array('name' => 'date_modified', 'type' => 'datetime'),
		array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => true),
	),
	'indices' => array(
		array('name' => 'camp_contacts_pk', 'type' => 'primary', 'fields' => array('id')),
		array('name' => 'idx_camp_cnct_camp', 'type' => 'index', 'fields' => array('camp_id')),
		array('name' => 'idx_camp_cnct_cnct', 'type' => 'index', 'fields' => array('contact_id')),
		array('name' => 'camp_contacts_alt', 'type' => 'alternate_key', 'fields' => array('camp_id', 'contact_id')),
	),
	'relationships' => array(
		'camp_staff' => array(
			'lhs_module' => 'Camps',
			'lhs_table' => 'camps',
			'lhs_key' => 'id',
			'rhs_module' => 'Contacts',
			'rhs_table' => 'contacts',
			'rhs_key' => 'id',
			'relationship_type' => 'many-to-many',
			'join_table' => 'camp_staff',
			'join_key_lhs' => 'camp_id',
			'join_key_rhs' => 'contact_id',
		),
	),
);
?>
