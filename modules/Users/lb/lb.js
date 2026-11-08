/*********************************************************************************
 * This file is an customisation to SugarCRM developed by Enterprise Intelligence
 * Systems Pty Ltd.  Copyright (C) 2009-2010 Enterprise Intelligence Systems Pty Ltd.
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
 * of this program must display Appropriate Legal Notices, as required underGNU Affero General Public Licenseeneral Public License version 3.
 *
 * In accordanGNU Affero General Public Licensee GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/

function viewHeight()
{
    if(window.innerHeight)return(window.innerHeight);
    if(document.documentElement && document.documentElement.clientHeight) 
         return(document.documentElement.clientHeight);
    if(document.body) return(document.body.clientHeight); 
    return 50;
}

function gradient(id, level)
{
	var box = document.getElementById(id);
	box.style.opacity = level;
	box.style.MozOpacity = level;
	box.style.KhtmlOpacity = level;
	box.style.filter = "alpha(opacity=" + level * 100 + ")";
	box.style.display="block";
	return;
}


function fadein(id) 
{
	var level = 0;
	while(level <= 1)
	{
		setTimeout( "gradient('" + id + "'," + level + ")", (level* 1000) + 10);
		level += 0.01;
	}
}


// Open the lightbox

function openbox(did, fadin, divid)
{
  var box = document.getElementById('box_'+did); 
  document.getElementById('filter_'+did).style.display='block';
  
  var title = document.getElementById('boxtitle_'+did);
  //title.innerHTML = did;
  
  var content = document.getElementById('boxcontent_'+did);
  content.style.padding="0";

  //content.innerHTML = document.getElementById('div_'+divid).innerHTML;

  var filter = document.getElementById('filter_'+did);

  if(fadin)
  {
	 gradient("box_"+did, 0);
  }
  else
  { 	
    box.style.display='block';
  }  
  
  if(navigator.appName.substring(0, 3) == "Mic")  // for IE
  {
    x = document.documentElement.scrollTop + document.body.scrollTop + 
    box.offsetHeight / 4;
    box.style.top = x + "px";
    filter.style.top = document.documentElement.scrollTop + document.body.scrollTop;
  }
  else
  {
    var top =  (viewHeight() - box.offsetHeight ) / 2;    
    box.style.top = top + 'px';
    box.style.position='fixed'; // fixed does not work on IE
    filter.style.position='fixed'; 
  }  

  if(fadin)
  {
	 fadein("box_"+did);  
  }
    	
}	

// Close the lightbox

function closebox(did)
{
	document.getElementById('box_'+did).style.display='none';
	document.getElementById('filter_'+did).style.display='none';
	ResetRoles(did);
}
function ResetRoles(did)
{
	var opts_prev = document.getElementById('role_source_prev_'+did).options;
	for(i=0;i<opts_prev.length;i++)
	{
		document.getElementById('role_source_'+did).options[i].selected = opts_prev[i].selected;
	}
	ListSelectedRoles(did);
}
function SetRoles(did)
{
	var opts = document.getElementById('role_source_'+did).options;
	var roles = '';
	for(i=0;i<opts.length;i++)
	{
		document.getElementById('role_source_prev_'+did).options[i].selected = opts[i].selected;
		if(opts[i].selected) roles += opts[i].text+', ';
	}
	document.getElementById('role_td_'+did).innerHTML = roles.slice(0,-2);
	ListSelectedRoles(did);	
}
function GetSelectedRoles(did)
{
	var opts = document.getElementById('role_source_'+did).options;
	var roles = '';
	for(i=0;i<opts.length;i++)
	{
		document.getElementById('role_source_prev_'+did).options[i].selected = opts[i].selected;
		if(opts[i].selected) roles += opts[i].value+',';
	}
	return roles.slice(0,-1);
}
function SaveRole(did)
{
  ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
  var cObj = YAHOO.util.Connect.asyncRequest('GET', 'index.php?module=Users&action=SaveRole&to_pdf=1&project_team_member_id='+did+'&project_id='+document.DetailView.record.value+'&selected_roles='+GetSelectedRoles(did), {success: ajaxResult, failure: ajaxResult});
  SetRoles(did);
  closebox(did);
}
function ajaxResult(o) {
  if (o.status > 0) {
    ajaxStatus.flashStatus(SUGAR.language.get('app_strings', 'LBL_SAVED'), 1000);
  }
}

function AddRole(did)
{
	var sind = document.getElementById('rolelist_'+did).selectedIndex;
	document.getElementById('role_source_'+did).options[sind].selected = true;
	ListSelectedRoles(did);
}
function RemoveRole(sind,did)
{
	document.getElementById('role_source_'+did).options[sind].selected = false;
	ListSelectedRoles(did);
}
function ListSelectedRoles(did)
{
	var opts = document.getElementById('role_source_'+did).options;
	var roles_list = '';
	for(i=0;i<opts.length;i++)
	{
		if(opts[i].selected) roles_list += '<div align="left" style="float:left;width:160px; margin-bottom:5px;">'+ opts[i].text +'</div><div style="float:left;">&nbsp;<img id="rembtn" name="rembtn" src="index.php?entryPoint=getImage&themeName=Sugar&imageName=delete_inline.gif" onclick="RemoveRole(\''+i+'\',\''+did+'\');" /></div><div style="clear:both; height:1px;"></div>';
	}
	document.getElementById('div_role_'+did).innerHTML = roles_list;
}