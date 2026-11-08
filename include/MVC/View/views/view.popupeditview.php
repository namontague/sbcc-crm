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
require_once('include/EditView/PopupEditView/PopupEditView.php');
class ViewPopupEditView extends ViewEdit{
    protected $override_popup = array();
    var $type ='edit';
    function ViewPopupEditView(){
        parent::ViewEdit();
        $this->options['show_subpanels'] = false;
        $this->options['show_title'] = false;
        $this->options['show_header'] = true;
        $this->options['show_footer'] = false;
        $this->options['show_javascript'] = true;

    }

    public function displayHeader($retModTabs=false)
    {
        global $theme;
        global $app_strings;
        global $mod_strings;

        $GLOBALS['app']->headerDisplayed = true;

        $themeObject = SugarThemeRegistry::current();
        $theme = $themeObject->__toString();

        $ss = new Sugar_Smarty();
        $ss->assign("APP", $app_strings);
        $ss->assign("THEME", $theme);
        $ss->assign("THEME_IE6COMPAT", $themeObject->ie6compat ? 'true':'false');
        $ss->assign("MODULE_NAME", $this->module);
        $ss->assign("langHeader", get_language_header());

        // get browser title
        $ss->assign("SYSTEM_NAME", $this->getBrowserTitle());

        // get css
        $css = $themeObject->getCSS();
        if ($this->_getOption('view_print')) {
            $css .= '<link rel="stylesheet" type="text/css" href="'.$themeObject->getCSSURL('print.css').'" media="all" />';
        }
        $ss->assign("SUGAR_CSS",$css);

        // get javascript
        ob_start();
        $this->renderJavascript();

        $ss->assign("SUGAR_JS",ob_get_contents().$themeObject->getJS());
        ob_end_clean();

        // get favicon
        if(isset($GLOBALS['sugar_config']['default_module_favicon']))
            $module_favicon = $GLOBALS['sugar_config']['default_module_favicon'];
        else
            $module_favicon = false;

        $favicon = $this->getFavicon();
        $ss->assign('FAVICON_URL', $favicon['url']);


        $imageURL = SugarThemeRegistry::current()->getImageURL("dashboard.png");
        $homeImage = "<img src='$imageURL'>";
		$ss->assign("homeImage",$homeImage);
        //global $mod_strings;
        //$mod_strings = $bakModStrings;
        $headerTpl = 'include/EditView/PopupEditView/_header.tpl';
        if (inDeveloperMode() )
            $ss->clear_compiled_tpl($headerTpl);

        if ($retModTabs)
        {
            return $ss->fetch($themeObject->getTemplate('_headerModuleList.tpl'));
        } else {
            $ss->display($headerTpl);

            $this->includeClassicFile('modules/Administration/DisplayWarnings.php');

            $errorMessages = SugarApplication::getErrorMessages();
            if ( !empty($errorMessages)) {
                foreach ( $errorMessages as $error_message ) {
                    echo('<p class="error">' . $error_message.'</p>');
                }
            }
        }

    }


    public function getModuleTitle($show_help = true)   {
        global $sugar_version, $sugar_flavor, $server_unique_key, $current_language, $action;

        $theTitle = "<div class='moduleTitle'>\n";

        $module = preg_replace("/ /","",$this->module);

        $params = $this->_getModuleTitleParams();
        $index = 0;

        if(SugarThemeRegistry::current()->directionality == "rtl") {
            $params = array_reverse($params);
        }

        $count = count($params);
        $paramString = '';
        foreach($params as $parm){
            $index++;
            $paramString .= $parm;
            if($index < $count){
                $paramString .= $this->getBreadCrumbSymbol();
            }
        }

        if(!empty($paramString)){
            $theTitle .= "<h2> $paramString </h2>\n";
        }

        $theTitle .= "<div class='clear'></div></div>\n";
        return $theTitle;
    }

    public function getMetaDataFile() {
        $metadataFile = null;
        $foundViewDefs = false;
        $viewDef = 'popupeditviewdefs';
        $coreMetaPath = 'modules/'.$this->module.'/metadata/' . $viewDef . '.php';
        if(file_exists('custom/' .$coreMetaPath )){
            $metadataFile = 'custom/' . $coreMetaPath;
            $foundViewDefs = true;
        }else{
            if(file_exists('custom/modules/'.$this->module.'/metadata/metafiles.php')){
                require_once('custom/modules/'.$this->module.'/metadata/metafiles.php');
                if(!empty($metafiles[$this->module][$viewDef])){
                    $metadataFile = $metafiles[$this->module][$viewDef];
                    $foundViewDefs = true;
                }
            }elseif(file_exists('modules/'.$this->module.'/metadata/metafiles.php')){
                require_once('modules/'.$this->module.'/metadata/metafiles.php');
                if(!empty($metafiles[$this->module][$viewDef])){
                    $metadataFile = $metafiles[$this->module][$viewDef];
                    $foundViewDefs = true;
                }
            }
        }

        if(!$foundViewDefs && file_exists($coreMetaPath)){
                $metadataFile = $coreMetaPath;
        }
        $GLOBALS['log']->debug("metadatafile=". $metadataFile);

        return $metadataFile;
    }

    public function preDisplay()  {
        /* BEGIN - SECURITY GROUPS */
        $metadataFile = null;
        $foundViewDefs = false;

        $metadataFile = $this->getMetaDataFile();

        $this->ev = $this->getEditView();
        $this->ev->ss =& $this->ss;
        $this->ev->setup($this->module, $this->bean, $metadataFile, get_custom_file_if_exists('include/EditView/PopupEditView/PopupEditView.tpl'));
    }

    /**
     * Get EditView object
     * @return EditView
     */
    protected function getEditView()  {
        return new PopupEditView();
    }

    function display(){
        $this->ev->process();
        echo $this->ev->display($this->showTitle);
    }




}
?>
