<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/* * *******************************************************************************
 * (C) 2013 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */


$module_name = 'Contacts';
$viewdefs [$module_name]['PopupEditView'] = array(
    'templateMeta' => array(
        'form' => array(
            'hideAudit' => true,
            'headerTpl' => 'include/EditView/PopupEditView/header.tpl',
            'buttons'=>array(
                'POPUPEDITSAVE','POPUPEDITCANCEL'
            )
        ),
        
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30'),
        ),
        
        'javascript' => '<script type="text/javascript" >
            {literal}
            function hideShowRoles() {
                if($("#account_id").val() ) {
                   $("#account_contact_roles_label").show();
                   $("#account_contact_roles_data").show();
                   $("#account_contact_priority_label").show();
                   $("#account_contact_priority_data").show();
                } else {
                   $("#account_contact_roles_label").hide();
                   $("#account_contact_roles_data").hide();
                   $("#account_contact_priority_label").hide();
                   $("#account_contact_priority_data").hide();
                }
            }
            $(function() {
                $("#account_id").change(hideShowRoles);
                hideShowRoles();
            });
            {/literal}
            </script>
            ',
        
    ),
    'panels' =>
    array (
        'lbl_contact_information' =>
        array (
          array (
            array (
              'name' => 'first_name',
              'customCode' => '{html_options name="salutation" id="salutation" options=$fields.salutation.options selected=$fields.salutation.value}' 
                . '&nbsp;<input name="first_name"  id="first_name" size="25" maxlength="25" type="text" value="{$fields.first_name.value}">',
                ),


          ),
          array (
            array (
              'name' => 'last_name',
            ),
            array (
              'name' => 'phone_work',
              'comment' => 'Work phone number of the contact',
              'label' => 'LBL_OFFICE_PHONE',
            ),
          ),

          array (

            array (
              'name' => 'title',
              'comment' => 'The title of the contact',
              'label' => 'LBL_TITLE',
            ),
            array (
              'name' => 'phone_mobile',
              'comment' => 'Mobile phone number of the contact',
              'label' => 'LBL_MOBILE_PHONE',
            ),

          ),

          array (
            'department',
            array (
              'name' => 'phone_fax',
              'comment' => 'Contact fax number',
              'label' => 'LBL_FAX_PHONE',
            ),
          ),
          array (
               'birthdate',
               'phone_home',
          ),

          array (
               '',
               'phone_other',
          ),

          array(
            array (
              'name' => 'account_name',
              'displayParams' =>
              array (
                'key' => 'billing',
                'copy' => 'primary',
                'billingKey' => 'primary',
                'additionalFields' =>
                array (
                  'phone_office' => 'phone_work',
                ),
              ),
            ),
              'phone_other_alternative',
          ),

          array (

            array (
              'name' => 'primary_address_street',
              'hideLabel' => true,
              'type' => 'address',
              'displayParams' =>
              array (
                'key' => 'primary',
                'rows' => 2,
                'cols' => 30,
                'maxlength' => 150,
              ),
            ),

            array (
              'name' => 'alt_address_street',
              'hideLabel' => true,
              'type' => 'address',
              'displayParams' =>
              array (
                'key' => 'alt',
                'copy' => 'primary',
                'rows' => 2,
                'cols' => 30,
                'maxlength' => 150,
              ),
            ),
          ),

          array (

            array (
              'name' => 'email1',
              'studio' => 'false',
              'label' => 'LBL_EMAIL_ADDRESS',
            ),
            'account_contact_priority'
          ),

          array (

            array (
              'name' => 'description',
              'label' => 'LBL_DESCRIPTION',
            ),
            array(
              'name'=>'account_contact_roles',
              'displayParams'=>array('enhancedSelect'=>true)
            ),
          ),
        ),


        'LBL_PANEL_ADVANCED' =>
        array (

          array (

            array (
              'name' => 'report_to_name',
              'label' => 'LBL_REPORTS_TO',
            ),

            array (
              'name' => 'sync_contact',
              'comment' => 'Synch to outlook?  (Meta-Data only)',
              'label' => 'LBL_SYNC_CONTACT',
            ),
          ),

          array (

            array (
              'name' => 'lead_source',
              'comment' => 'How did the contact come about',
              'label' => 'LBL_LEAD_SOURCE',
            ),

            array (
              'name' => 'do_not_call',
              'comment' => 'An indicator of whether contact can be called',
              'label' => 'LBL_DO_NOT_CALL',
            ),
          ),

          array (
                'campaign_name',
                'staff_member',
          ),
          array(
              'last_contact_date'
          ),
        ),



        'LBL_PANEL_ASSIGNMENT' =>
        array (
          array (
            array (
              'name' => 'assigned_user_name',
              'label' => 'LBL_ASSIGNED_TO_NAME',
            ),
          ),
        ),
        'LBL_PORTAL_INFORMATION' =>
        array(
          'displayIf' => '$bean->portalEnabled()',
          array(
              'portal_active',
              array('name' => 'portal_password',
                  'type' => 'password',
                  'customCode' => '<input id="portal_password" name="portal_password" type="password" size="32" maxlength="32" value="{$fields.portal_password.value}">',
                  'label' => 'LBL_PORTAL_PASSWORD'),
          ),
        ),
    ),
    
			
);
?>
