<?php
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
require_once('include/MVC/View/SugarView.php');
require_once('modules/Project/gantt.php');

class GoogleDocumentsViewCreateGoogleDocLink extends SugarView {

    function GoogleDocumentsViewCreateGoogleDocLink() {
        parent::SugarView();
    }

    function process() {
        // This will bypass the sugar header and footer so only the required text is sent
        $this->display();

    }

    function display() {

        $out = "<?xml version='1.0' encoding='UTF-8'?>
                    <rows>";
        $title='';
        if(isset($_REQUEST['title'])) {
            $title=$_REQUEST['title'];
        }
        $kind ='document';
        if(isset($_REQUEST['kind'])) {
            $kind=$_REQUEST['kind'];
        }


        $googleDocument = new GoogleDocument();
        $xml = $googleDocument->createEmptyGoogleDoc($title, $kind);

        $title = (string)$xml->title;
        $id = (string)$xml->id;
        $author = (string)$xml->author->name;
        $resourceId = (string)($xml->children('http://schemas.google.com/g/2005')->resourceId);

        foreach($xml->link as $link) {
            if($link['rel'] == 'alternate') {
                $googleDocLink = (string)$link['href'];
                break;
            }
        }

        foreach($xml->category as $category) {
            if($category['scheme'] == 'http://schemas.google.com/g/2005#kind') {
                $docType = (string)$category['label'];
                break;
            }
        }
        
        $content_type = (string)$xml->content['type'];
        $content_url = (string)$xml->content['src'];
        
        $out = $out."<row>
            <id>{$resourceId}</id>
            <title>{$title}</title>
            <author>{$author}</author>
            <docType>$docType</docType>
            <content_type>$content_type</content_type>
            <content_url><![CDATA[$content_url]]></content_url>
            <document_url><![CDATA[$googleDocLink]]></document_url>
        </row>";
        $out = $out."</rows>";
        
        ob_clean();
        header('Content-Type: text/xml');
        print($out);
        sugar_cleanup(true);
    }

}

?>
