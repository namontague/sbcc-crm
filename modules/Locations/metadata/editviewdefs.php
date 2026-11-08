<?php
/* * *******************************************************************************
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */

$module_name = 'Locations';
$module_table = 'locations';
$object_name = 'Location';

$viewdefs[$module_name]['EditView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),

    ),

    'panels' => array(
        'default' => array(
            array(
                'building_type'
            ),
             array(
                'number'
            ),
            array(
                'address1',
                array(
                    'name' => 'account_name',
                    'displayParams' =>  array(
                        'readOnlyIf' => '!empty($fields.account_id.value)' // && !locationIsPrimary();
                    ),
                ),
            ),
            array(
                'address2',
                'phone'
            ),
            array(
                'city',
                'alternate_phone',
            ),
            array(
                'state',
                'fax',
            ),
            array(
                'postalcode',
                array(
                    'name' => 'isbilling',
                    'customCode' => '
                        {if !empty($fields.account_id.value)}
                            <input type="checkbox" class="checkbox" name="isshipping" id="isshipping" value="{$fields.isbilling.value}" disabled="true"
                            {if $fields.isbilling.value == true}checked{/if}>
                        {else}
                            <input type="hidden" name="{$fields.isbilling.name}" value="0">
                            <input type="checkbox" id="isbilling" name="{$fields.isbilling.name}" value="1" title="" tabindex="0" {if $fields.isbilling.value == true}checked{/if}>
                        {/if}',
                    
                ),
            ),
            array(
                'country',
                array(
                    'name' => 'isshipping',
                    'customCode' => '
                        {if !empty($fields.account_id.value)}
                            <input type="checkbox" class="checkbox" name="isshipping" id="isshipping" value="{$fields.isshipping.value}" disabled="true"
                            {if $fields.isshipping.value == true}checked{/if}>
                        {else}
                            <input type="hidden" name="{$fields.isshipping.name}" value="0">
                            <input type="checkbox" id="isbilling" name="{$fields.isshipping.name}" value="1" title="" tabindex="0" {if $fields.isshipping.value == true}checked{/if}>
                        {/if}',
                    
    
                ),                
            ),
    
         
        )

    ),

);
?>
