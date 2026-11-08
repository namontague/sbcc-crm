{*
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2012 SugarCRM Inc.
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

*}
<script type="text/javascript">
function allowOpenbravo() {ldelim}

document.getElementById('OpenbravoFrame').src = "{$AJAX_URL}";
document.getElementById('OpenbravoConfirm').style.display = 'none';
document.getElementById('OpenbravoFrame').style.display = 'block';

YAHOO.util.Connect.asyncRequest('GET', 'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=allowOpenbravo', {ldelim}{rdelim}, null);

{rdelim}
SUGAR.util.doWhen("typeof(markSubPanelLoaded) != 'undefined' && document.getElementById('subpanel_openbravo')", function() {ldelim}
	markSubPanelLoaded('openbravo');
	var OpenbravoSubPanel = document.getElementById("subpanel_openbravo");
	OpenbravoSubPanel.cookie_name="openbravo_v";
	if(div_cookies['openbravo_v']){ldelim}
		if(div_cookies['openbravo_v'] == 'none')
		{ldelim}
            hideOpenbravoSubPanel();
		{rdelim}
	{rdelim}
    else
    {ldelim}
        if(typeof(DCMenu) == 'undefined')
            return;
        var cookieKey = DCMenu.module + '_divs';
        var tmpCookie = Get_Cookie(cookieKey);
        if(tmpCookie)
        {ldelim}
            var subCookies = tmpCookie.split('#');
            var foundOpenbravoCookie = false;
            for(var x=0;x<subCookies.length;x++ )
            {ldelim}
                var subCookie = subCookies[x];
                var pars = subCookie.split('=');
                if(pars.length == 2)
                {ldelim}
                    if(pars[0] == 'openbravo_v')
                        foundOpenbravoCookie = true;
                {rdelim}
            {rdelim}
            if(!foundOpenbravoCookie)
            {ldelim}
                hideOpenbravoSubPanel()
            {rdelim}
        {rdelim}
        else
        {ldelim}
            hideOpenbravoSubPanel();
        {rdelim}

    {rdelim}

{rdelim});

function hideOpenbravoSubPanel(){ldelim}
    hideSubPanel('openbravo');
    document.getElementById('hide_link_openbravo').style.display='none';
    document.getElementById('show_link_openbravo').style.display='';
{rdelim}

</script>
<div id='OpenbravoDiv' style='width:100%' class="doNotPrint">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="formHeader h3Row">
        <tbody>
            <tr>
                <td nowrap="">
                    <h3>
                        <span>Openbravo</span>
                    </h3>
                </td>
                <td width="20">
                 
                    <img  src="modules/Connectors/connectors/formatters/ext/rest/openbravo/tpls/openbravo.png" alt="Openbravo logo">
                </td>
                <td width="100%" valign="middle" nowrap="">
                    <a name="openbravo"> </a>
                    <span id="show_link_openbravo" style="display: ">
                        <a class="utilsLink" href="#" onclick="current_child_field = 'openbravo';markSubPanelLoaded('openbravo');showSubPanel('openbravo',null,null,'openbravo');document.getElementById('show_link_openbravo').style.display='none';document.getElementById('hide_link_openbravo').style.display='';return false;">{sugar_getimage name='advanced_search' attr='border="0 align="absmiddle""' ext='.gif' alt=$APP.LBL_SHOW }</a>
                    </span>
                    <span id="hide_link_openbravo" style="display: ">
                        <a class="utilsLink" href="#" onclick="hideSubPanel('openbravo');document.getElementById('hide_link_openbravo').style.display='none';document.getElementById('show_link_openbravo').style.display='';return false;">{sugar_getimage name='basic_search' attr='border="0 align="absmiddle""' ext='.gif' alt=$APP.LBL_HIDE }</a>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
  <div id='subpanel_openbravo' style='width:100%' {if !$showOpenbravo}align="center"{/if}>
      <div id='OpenbravoConfirm' class="detail view detail508" style="width: 100%; position: relative;{if $showOpenbravo}display:none;{/if}">
          <a href="#" onclick="hideSubPanel('openbravo');document.getElementById('hide_link_openbravo').style.display='none';document.getElementById('show_link_openbravo').style.display='';return false;"></a>
        
          <form>
              
                
                <table cellspacing="0" id="detailpanel_99">
                        <tbody>
                            {$data}

                        </tbody>
                </table>
             
              
          </form>
          <div class="clear"></div>
      </div>
      <iframe id='openbravoFrame' src='{$URL}' title='{$URL}' scrolling="no" style='border:0px; width:100%;height:400px;overflow:hidden;{if !$showOpenbravo}display:none;{else}display:block;{/if}'></iframe>
   </div>
</div>