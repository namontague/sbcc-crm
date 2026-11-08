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
$dictionary['GoogleDocument'] = array(
        'table' => 'googledocuments',
        'fields' => array(
                'document_id' => array(
                        'name' => 'document_id',
                        'vname' => 'LBL_DOCID',
                        'type' => 'varchar',
                        'len' => '255',
                        'required'=> true,
                        'importable' => 'required',
                        'comment' => 'resource id of document',
                ),
                'document_title' => array(
                        'name' => 'document_title',
                        'vname' => 'LBL_DOCTITLE',
                        'type' => 'varchar',
                        'len' => '255',
                        'required'=> true,
                        'importable' => 'required',
                        'comment' => 'name of google document retrieved',
                ),
                'document_url' => array(
                        'name' => 'document_url',
                        'vname' => 'LBL_DOCURL',
                        'type' => 'varchar',
                        'len' => '255',
                        'required'=> true,
                        'importable' => 'required',
                        'comment' => 'google document attachment url',
                ),
                'document_type' => array(
                        'name' => 'document_type',
                        'vname' => 'LBL_DOCTYPE',
                        'type' => 'varchar',
                        'len' => '30',
                        'required'=> true,
                        'importable' => 'required',
                        'comment' => 'google document attachment url',
                ),
                'content_type' => array(
                        'name' => 'content_type',
                        'vname' => 'LBL_CONTENTTYPE',
                        'type' => 'varchar',
                        'len' => '50',
                        'required'=> true,
                        'importable' => 'required',
                        'comment' => 'Content type of link',
                ),
                'content_url' => array(
                        'name' => 'content_url',
                        'vname' => 'LBL_CONTENTURL',
                        'type' => 'varchar',
                        'len' => '255',
                        'required'=> true,
                        'importable' => 'required',
                        'comment' => 'url to access content',
                ),
                'export_urls' => array(
                        'name' => 'export_urls',
                        'vname' => 'LBL_EXPORTURLS',
                        'type' => 'array',
                        'source' => 'non-db',
                        'comment' => 'Array of supported export formats & urls ',
                ),                
        ),
);
VardefManager::createVardef('GoogleDocuments','GoogleDocument', array('basic','assignable'));
?>
