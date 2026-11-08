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



function viewHeight() {
    if(window.innerHeight) {
        return(window.innerHeight);
    }
    if(document.documentElement && document.documentElement.clientHeight) {
         return(document.documentElement.clientHeight);
    }
    if(document.body){
        return(document.body.clientHeight);
    }
    return 50;
}

function gradient(id, level) {
    var box = document.getElementById(id);
    box.style.opacity = level;
    box.style.MozOpacity = level;
    box.style.KhtmlOpacity = level;
    box.style.filter = "alpha(opacity=" + level * 100 + ")";
    box.style.display="block";
    return;
}


function fadein(id) {
    var level = 0;
    while(level <= 1) {
        setTimeout( "gradient('" + id + "'," + level + ")", (level* 1000) + 10);
        level += 0.01;
    }
}

///////////////////////
// Open the lightbox //
///////////////////////
function openbox(fadin) {
  var box = document.getElementById('box_create_groups');
  document.getElementById('filter_create_groups').style.display='block';

  var title = document.getElementById('boxtitle_create_groups');
  //title.innerHTML = did;

  var content = document.getElementById('boxcontent_create_groups');
  content.style.padding="0";

  //content.innerHTML = document.getElementById('div_create_groups').innerHTML;

  var filter = document.getElementById('filter_create_groups');

  if(fadin) {
	 gradient("box_create_groups", 0);
  }else  {
    box.style.display='block';
  }

  if(navigator.appName.substring(0, 3) == "Mic")  { // for IE
  
    x = document.documentElement.scrollTop + document.body.scrollTop +
    box.offsetHeight / 4;
    box.style.top = x + "px";
    filter.style.top = document.documentElement.scrollTop + document.body.scrollTop;
  }else {
    //var top =  (viewHeight() - box.offsetHeight ) / 2;
    //box.style.top = top + 'px';
    box.style.position='fixed'; // fixed does not work on IE
    filter.style.position='fixed';
  }

  if(fadin) {
     fadein("box_create_groups");
  }

}

// Close the lightbox

function closebox(data){
    document.getElementById('box_create_groups').style.display='none';
    document.getElementById('filter_create_groups').style.display='none';
    
}



function createGroups() {
    $.ajax({
      url: 'index.php?module=CampGroups&action=createGroups&camp_id='+$("#camp_id").val()+'&sort_by='+$("#sort_by").val() +'&group_size='+$("#group_size").val(),
      dataType: 'json',
      async: false,
      success:  function(data) {
                    if(data['created']){
                        document.getElementById('box_create_groups').style.display='none';
                        document.getElementById('filter_create_groups').style.display='none';
                        showSubPanel('groups',null,true);
                        
                        ajaxStatus.flashStatus(data['created'],1000);
                    }
                },
      error:    function(jqXHR, textStatus, errorThrown) {
                    alert( "Request failed: " + textStatus + errorThrown );
                }
    });
}


function ajaxResult(o) {
  if (o.status > 0) {
    ajaxStatus.flashStatus(SUGAR.language.get('app_strings', 'LBL_SAVED'), 1000);
  }
}