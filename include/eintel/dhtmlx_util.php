<?php
/*********************************************************************************
* This file is an customisation to SugarCRM developed by Enterprise Intelligence
* Systems Pty Ltd.  Copyright (C) 2009-2011 Enterprise Intelligence Systems Pty Ltd.
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
* FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
* details.
*
* You should have received a copy of the GNU Affero General Public License along with
* this program; if not, see http://www.gnu.org/licenses or write to the Free
* Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
* 02110-1301 USA.
*
* You can contact Enterprise Intelligence Systems Pty Ltd. headquarters at
* 25/20 Enterprise Dr, Bundoora, Vic, 3083, Australia or at email address
* info@eintel.com.au
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

define('DHTMLX_JS', 'js');
define('DHTMLX_FOLDER', 'folder');
define('DHTMLX_CSS', 'css');
define('DHTMLX_SKIN', 'skin');
define('DHTMLX_IMGS', 'imgs');

function dhtmlxModuleInfo() {
    static $moduleInfo=array(
        'common' => array(),
        'form' =>array(
            DHTMLX_FOLDER => 'dhtmlxForm',
            DHTMLX_JS=> array('dhtmlxform.js'),
            DHTMLX_SKIN => array(
                'dhx_black' => 'dhtmlxform_dhx_black.css',
                'dhx_blue' => 'dhtmlxform_dhx_blue.css',
                'dhx_skyblue' => 'dhtmlxform_dhx_skyblue.css',
                'dhx_web' => 'dhtmlxform_dhx_web.css',
            ),
            DHTMLX_IMGS => 'imgs',
        ),
        'gantt' =>array(
            DHTMLX_FOLDER => 'dhtmlxGantt',
            DHTMLX_JS=> array('dhtmlxgantt.js'),
            DHTMLX_CSS => array('dhtmlxgantt.css'),
            DHTMLX_IMGS => 'imgs',
        ),
        'grid' =>array(
            DHTMLX_FOLDER => 'dhtmlxGrid',
            DHTMLX_JS=> array('dhtmlxgrid.js', 'dhtmlxgridcell.js'),
            DHTMLX_CSS => array('dhtmlxgrid.css','dhtmlxgrid_skins.css'),
            DHTMLX_SKIN => array(
                'dhx_black' => 'dhtmlxgrid_dhx_black.css',
                'dhx_blue' => 'dhtmlxgrid_dhx_blue.css',
                'dhx_skyblue' => 'dhtmlxgrid_dhx_skyblue.css',
                'dhx_web' => 'dhtmlxgrid_dhx_web.css',
            ),
            DHTMLX_IMGS => 'imgs',
        ),
        'tabbar' =>array(
            DHTMLX_FOLDER => 'dhtmlxTabbar',
            DHTMLX_JS=> array('dhtmlxtabbar.js','dhtmlxtabbar_start.js'),
            DHTMLX_CSS => array('dhtmlxtabbar.css'),
            DHTMLX_IMGS => 'imgs',
        ),
        'tree' =>array(
            DHTMLX_FOLDER => 'dhtmlxTree',
            DHTMLX_JS=> array('dhtmlxtree.js','ext/dhtmlxtree_json.js'),
            DHTMLX_CSS => array('dhtmlxtree.css'),
            DHTMLX_IMGS => 'imgs',
        ),
        'windows' =>array(
            DHTMLX_FOLDER => 'dhtmlxWindows',
            DHTMLX_JS=> array('dhtmlxwindows.js'),
            DHTMLX_CSS => array('dhtmlxwindows.css'),
            DHTMLX_SKIN => array(
                'dhx_black' => 'dhtmlxwindows_dhx_black.css',
                'dhx_blue' => 'dhtmlxwindows_dhx_blue.css',
                'dhx_skyblue' => 'dhtmlxwindows_dhx_skyblue.css',
                'dhx_web' => 'dhtmlxwindows_dhx_web.css',
            ),
            DHTMLX_IMGS => 'imgs',
        ),
    );
    return $moduleInfo;
}

function dhtmlxJavascriptFor($modules = array('common','windows','tree', 'grid','form','tabbar'),
                                $skin='dhx_web',$debug = false) {
    $base_path = 'include/eintel/dhtmlx/';
    $css_folder = '/codebase/';
    $js_folder = '/codebase/';
    if( $debug ) {
        $js_folder = '/sources/';
    }

    $script = '';

    if(in_array('common',$modules)) {
        $script .= '<script type="text/javascript" src="' . getJSPath($base_path.'dhtmlxWindows'.$js_folder.'dhtmlxcommon.js') . '"></script>'."\n";
        $script .= '<script type="text/javascript" src="' . getJSPath($base_path.'dhtmlxWindows'.$js_folder.'dhtmlxcontainer.js') . '"></script>'."\n";
        $script .= '<script type="text/javascript">var dhtmlxImagePaths = new Array();var dhtmlxSkin="'.$skin.'";</script>'."\n";
    }
    $moduleInfo = dhtmlxModuleInfo();

    $imagePaths ='';
    foreach($modules as $m) {
        $info = $moduleInfo[$m];
        if( isset($info[DHTMLX_JS]) && is_array($info[DHTMLX_JS])) {
            foreach($info[DHTMLX_JS] as $j) {
                $script .=  '<script type="text/javascript" src="' . getJSPath($base_path.$info[DHTMLX_FOLDER].$js_folder.$j) . '"></script>'."\n";
            }
        }
        if( isset($info[DHTMLX_CSS]) && is_array($info[DHTMLX_CSS])) {
            foreach($info[DHTMLX_CSS] as $c) {
                $script .=  '<link  rel="stylesheet" type="text/css" href="' . getJSPath($base_path.$info[DHTMLX_FOLDER].$css_folder.$c) . '"></link>'."\n";
            }
        }
        if(isset($info[DHTMLX_SKIN]) && is_array($info[DHTMLX_SKIN]) && isset($info[DHTMLX_SKIN][$skin])) {
            $script .=  '<link  rel="stylesheet" type="text/css" href="' . getJSPath($base_path.$info[DHTMLX_FOLDER].$css_folder.'skins/'.$info[DHTMLX_SKIN][$skin]) . '"></link>'."\n";
        }
        if( isset($info[DHTMLX_IMGS])) {
            $imagePaths .= 'dhtmlxImagePaths["'.$m.'"]="'.$base_path.$info[DHTMLX_FOLDER].$css_folder.$info[DHTMLX_IMGS].'/";'."\n";
        }
    }
    if(!empty($imagePaths)) {
        $script .= '<script type="text/javascript">'.$imagePaths.'</script>'."\n";
    }
    return $script;
}

