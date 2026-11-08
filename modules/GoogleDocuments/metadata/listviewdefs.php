<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
* This file is an customisation to SugarCRM developed by Enterprise Intelligence
* Systems Pty Ltd.  Copyright (C) 2011 Enterprise Intelligence Systems Pty Ltd.
*
* SugarCRM is a customer relationship management program developed by
* SugarCRM, Inc.
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
* FOR A PARTICULAR PURPOSE.  SGNU Affero General Public LicenseLicense for more
* details.
*
* You should have received GNU Affero General Public LicensePublic License along with
* this program; if not, see http://www.gnu.org/licenses or write to the Free
* Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
* 02110-1301 USA.
*
* You can contact Enterprise Intelligence Systems Pty Ltd. headquarters at
* 25/20 Enterprise Dr, Bundoora, Vic, 3083, Australia or at email address
* info@eintel.com.au
*
* The interactive user interfaces in modified source and object code versions
* of this program must display Appropriate Legal Notices, as required undeGNU Affero General Public Licenseeneral Public License version 3.
*
* In accordanGNU Affero General Public Licensee GNU General Public License version 3,
* these Appropriate Legal Notices must retain the display of the "Powered by
* SugarCRM" logo. If the display of the logo is not reasonably feasible for
* technical reasons, the Appropriate Legal Notices must display the words
* "Powered by SugarCRM".
********************************************************************************/

$listViewDefs['GoogleDocuments'] = array(
    'NAME' => array(
        'width' => '40',
        'label' => 'LBL_NAME',
        'link' => true,
        'default' => true,
    ),
    'DOCUMENT_TITLE' => array(
        'width' => '40',
        'label' => 'LBL_DOCTITLE',
        'link' =>  true,
        'default' => true,
    ),
    'EXPORT_URLS' => array(
        'width' => '40',
        'label' => 'LBL_EXPORTURLS',
        'link' => false,
        'customCode' => '{$EXPORT_URLS}',
        //'customCode' =>'{foreach from=$fields.export_urls.value key="lbl" item="url"}<a href="{$url}" target="_blank">{$lbl}</a> {/foreach}',
        'default' => true,
        'related_fields' => array('content_url','document_type'),
    ),
    'DOCUMENT_URL' => array(
        'width' => '20',
        'label' => 'LBL_DOCURL',
        'link' => false,
        'customCode' =>'<a href="{$DOCUMENT_URL}" target="_blank">Edit</a>',
        'default' => true,
    ),
);
?>
