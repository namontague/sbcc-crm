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
                    <a name="openbravo_summary"> </a>
                    <span id="show_link_openbravo_summary" style="display: none">
                        <a class="utilsLink" href="#" onclick="current_child_field = 'openbravo_summary';markSubPanelLoaded('openbravo_summary');showSubPanel('openbravo_summary',null,null,'openbravo_summary');document.getElementById('show_link_openbravo_summary').style.display='none';document.getElementById('hide_link_openbravo_summary').style.display='';return false;">{sugar_getimage name='advanced_search' attr='border="0 align="absmiddle""' ext='.gif' alt=$APP.LBL_SHOW }</a>
                    </span>
                    <span id="hide_link_openbravo_summary" style="display: ">
                        <a class="utilsLink" href="#" onclick="hideSubPanel('openbravo_summary');document.getElementById('hide_link_openbravo_summary').style.display='none';document.getElementById('show_link_openbravo_summary').style.display='';return false;">{sugar_getimage name='basic_search' attr='border="0 align="absmiddle""' ext='.gif' alt=$APP.LBL_HIDE }</a>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
  <div id='subpanel_openbravo_summary' style='width:100%' >
  {if $ERROR}
    <p class="error">{$errorMessage}</p>
  {else}
      <div id='OpenbravoConfirm' class="detail view detail508" style="width: 100%; position: relative;">
          <a href="#" onclick="hideSubPanel('openbravo_summary');document.getElementById('hide_link_openbravo_summary').style.display='none';document.getElementById('show_link_openbravo_summary').style.display='';return false;"></a>

                 {if $show_sales_order_button}
                    <script type='text/javascript' src='modules/Connectors/connectors/formatters/ext/rest/openbravo/javascript/scripts.js'></script>
                    <div id="openbravo_button" style="width: 100%; padding-top: 5px;padding-bottom: 5px;">
                            <input title="Sales Order [Alt+t]" accesskey="t" class="button"
                            onclick="generateSalesOrder('{$account_id_for_button}');"
                            type="button"
                            name="sales_order"
                            id="process_sales_order"
                            value="Create Sales Order">
                </div>
                {/if}



                <div id="op_container" style="width: 100%;">
                  {if $show_balance}
                    <div id='op_balance' style="width: 150px; padding-right:20px; float:left;">
                        <table cellspacing="0" id="detailpanel_997">
                            <tbody>
                                {$balance_data}
                            </tbody>
                        </table>
                    </div>
                  {/if}
                  {if $show_freight}
                    <div id='op_freight' style="width: 150px; padding-right:20px; float:left;">
                        <table cellspacing="0" id="detailpanel_998">
                            <tbody>
                                {$freight_data}
                            </tbody>
                        </table>
                    </div>
                  {/if}
                  {if $show_purchase_history > 0}
                    <div id='op_purchase_history' style="overflow: hidden;" >
                        <table cellspacing="0" id="detailpanel_999" ">
                            <tbody>
                                {$purchase_history_data}
                            </tbody>
                        </table>
                    </div>
                  {/if}

              </div>
         
          <div class="clear"></div>
      </div>
       {/if}
  </div>
</div>

             