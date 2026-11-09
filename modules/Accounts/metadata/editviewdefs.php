<?php
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/

$viewdefs['Accounts']['EditView'] = array(
    'templateMeta' => array(
                            'form' => array('buttons'=>array('SAVE', 'CANCEL'),
                                // EINTEL Addition
                                'hidden' => array(
                                    '<input type="hidden" name="old_portal_password" value="{$fields.portal_password.value}">',
                                // EINTEL End
                                ),
                            ),
                            'maxColumns' => '2', 
                            'widths' => array(
                                            array('label' => '10', 'field' => '30'),
                                            array('label' => '10', 'field' => '30'),
                                            ),
                            'includes'=> array(
                                            array('file'=>'modules/Accounts/Account.js'),
                                             array('file' => 'include/eintel/jquery/plugins/StarRating/jquery.ui.stars.min.js'),
                            ),
                            'javascript' => '<link rel="stylesheet" type="text/css" href="include/eintel/jquery/plugins/StarRating/jquery.ui.stars.min.css"/>
                                  <script type="text/javascript">
                                    enableStarRating("EditView");
                                  </script>
                                  <script type="text/javascript" src="' . getJSPath('modules/Accounts/javascript/locations_js.js') . '"></script>'
                    ),
    'panels' => array(
    
      'lbl_account_information' => 
      array (
        array(
          'code',
          'association',
        ),
        array (
          array (
            'name' => 'name',
            'label' => 'LBL_NAME',
            'displayParams' => 
            array (
              'required' => true,
            ),
          ),
          array (
            'name' => 'phone_office',
            'label' => 'LBL_PHONE_OFFICE',
          ),
        ),

        array (

          array (
            'name' => 'website',
            'type' => 'link',
            'label' => 'LBL_WEBSITE',
          ),

          array (
            'name' => 'phone_fax',
            'label' => 'LBL_FAX',
          ),
        ),



        array(
            array(
                'name'=> 'billing_location_id',
                'hideIf' => '!module_enabled("Locations")'
            ),
            array(
                'name'=> 'shipping_location_id',
                'hideIf' => '!module_enabled("Locations")'
            ),
        ),

        array (

          array (
            'name' => 'billing_address_street',
            'hideLabel' => true,
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'billing',
              'rows' => 2,
              'cols' => 30,
              'maxlength' => 150,
            ),
          ),

          array (
            'name' => 'shipping_address_street',
            'hideLabel' => true,
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'shipping',
              'copy' => 'billing',
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
            'label' => 'LBL_EMAIL',
          ),
          array(
           'name'=>'tags',
           'displayParams'=>array('enhancedSelect'=>true)
          ),
        ),

        array (

          array (
            'name' => 'description',
            'label' => 'LBL_DESCRIPTION',
          ),
        ),
      ),
      'LBL_PANEL_ADVANCED' => 
      array (

        array (
          'account_type',
          'industry'
        ),

        array (
          'annual_revenue',
          'employees'
        ),

//        array (
//          'sic_code',
//          'ticker_symbol'
//        ),

        array(
          'abn',
          'acn'
        ),


        array (
          'parent_name',
          'ownership'
        ),

        array (
          'campaign_name',
           array (
              'name' => 'rating',
              'comment' => 'An arbitrary rating for this company for use in comparisons with others',
              'label' => 'LBL_RATING',
              'customCode' => '<input type="hidden" value="{$fields.rating.value}" name="hiddenrating" id="hiddenrating">
                 <div class="multiField" id="star-rating">
                    <select tabindex="110" title="stars" id="select-star-rating" name="rating">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                  </div>'
           ),
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
            'label' => 'LBL_ASSIGNED_TO',
          ),
        ),
      ),
      'LBL_PORTAL_INFORMATION' =>
      array(
        array(
            'portal_active',
            array('name' => 'portal_password',
                'type' => 'password',
                'customCode' => '<input id="portal_password" name="portal_password" type="password" size="32" maxlength="32" value="{$fields.portal_password.value}">',
                'label' => 'LBL_PORTAL_PASSWORD'),
        ),
      ),

    )
);
?>