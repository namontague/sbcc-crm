<?php

$dictionary['projects_team_members'] = array (
    'table' => 'projects_team_members',
    'fields' => array (
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'project_team_member_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'project_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' =>'user_project_roles', 'type' =>'varchar', 'len'=>'255'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => true),
    ),
    'indices' => array (
        array('name' => 'projects_users_pk', 'type' =>'primary', 'fields'=>array('id')),
        array('name' => 'idx_proj_user_proj', 'type' =>'index', 'fields'=>array('project_id')),
        array('name' => 'idx_proj_user_user', 'type' =>'index', 'fields'=>array('project_team_member_id')),
        array('name' => 'projects_users_alt', 'type'=>'alternate_key', 'fields'=>array('project_id','project_team_member_id')),
    ),
    'relationships' => array (
        'projects_team_members' => array(
            'lhs_module' => 'Project',
            'lhs_table' => 'project',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'projects_team_members',
            'join_key_lhs' => 'project_id',
            'join_key_rhs' => 'project_team_member_id',
        ),
    ),
);
?>