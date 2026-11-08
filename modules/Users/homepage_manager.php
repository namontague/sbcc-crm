<?PHP
//KBRILL DEFAULT DASHBOARD
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once('modules/Users/User.php');
class defaultHomepage extends User  {
	function resetConfig() {
		global $current_user;
		global $sugar_config;
		if($current_user->hm_lockhomepage) {
			$sugar_config['lock_homepage'] = true;
		}
	}
	function addTab(&$bean, $event) {
		global $current_user;
		if(isset($_REQUEST['record'])) {
			$mod_strings = return_module_language($GLOBALS['current_language'], 'Users');
			require_once('modules/Users/homepage_manager.php');
			$focus=new User();
			$focus->retrieve($_REQUEST['record']);
			$dd=new defaultHomepage();
			$default_homepage = $focus->hm_default_homepage;
			$only_once = $focus->hm_only_once;
			$lock = $focus->hm_lockhomepage;
			if($only_once=='1') {
				$only_once="checked";
			} else {
				$only_once="";
			}
			if($lock=='1') {
				$lock="checked";
			} else {
				$lock="";
			}
			$default_homepage_options = $dd->getAllDashboardOptions($focus->id,$default_homepage);
			if($_REQUEST['action']=='EditView' && $current_user->is_admin) {
				echo "
                                <script type='text/javascript' src='include/javascript/yui/build/utilities/utilities.js'></script>
                                <script type='text/javascript' src='include/javascript/yui/build/tabview/tabview.js'></script>
                                <script type='text/javascript' src='include/javascript/yui/build/event/event.js'></script>
                                <script type='text/javascript' src='modules/Connectors/Connector.js'></script>
                                <script type=\"text/javascript\">
				var EditView_tabs = new YAHOO.widget.TabView(\"EditView\");
				EditView_tabs.on('contentReady', function(e){
                                    EditView_tabs.addTab( new YAHOO.widget.Tab({
                                        label: 'Dashlet Manager',
                                        content: '".$this->createFormHTML($only_once,$lock,$default_homepage_options)."',
                                        cacheData: true,
                                        active : true
                                    }));
				});
				</script> \n";
			}
			if($_REQUEST['action']=='DetailView' && $current_user->is_admin) {
				echo "
                                <script type='text/javascript' src='include/javascript/yui/build/utilities/utilities.js'></script>
                                <script type='text/javascript' src='include/javascript/yui/build/tabview/tabview.js'></script>
                                <script type='text/javascript' src='include/javascript/yui/build/event/event.js'></script>
                                <script type='text/javascript' src='modules/Connectors/Connector.js'></script>
                                <script type=\"text/javascript\">
				var DetailView_tabs = new YAHOO.widget.TabView(\"form\");
				DetailView_tabs.on('contentReady', function(e){
                            	DetailView_tabs.addTab( new YAHOO.widget.Tab({
				label: 'Dashlet Manager',
				content: '".$this->createFormHTML($only_once,$lock,$default_homepage_options)."',
				cacheData: true,
                                active : true
				}));
				});
				</script> \n";
			}
		}
	}

	function getAllDashboardOptions($removeThisID="",$default="") {
		global $mod_strings;
		$options="<option value=\"\">".$mod_strings['LBL_NONE']."</option>";

		$options .= "<option disabled=disabled>-----".$mod_strings['LBL_SAVED_TEMPLATES']."------</option>";
		//Add all custom dashboards
		$sql="SELECT id,assigned_user_id FROM user_preferences WHERE assigned_user_id LIKE 'DD_%' AND category='Home' AND deleted=0;";
		$result=$this->db->query($sql,true);
		while($hash=$this->db->fetchByAssoc($result)) {
			if($hash['assigned_user_id']==$default) {
				$select="SELECTED /";
			} else {
				$select="/";
			}
			$name=substr($hash['assigned_user_id'],3);
			$options.="<option value=\"{$hash['assigned_user_id']}\" $select>{$name}</option>";
		}

		$options .= "<option disabled=disabled>-----".$mod_strings['LBL_USER_TEMPLATES']."------</option>";
		//Add all users dashboards
		$sql="SELECT id,first_name,last_name FROM users WHERE status='Active' AND portal_only=0 AND is_group=0;";
		$result=$this->db->query($sql,true);
		while($hash=$this->db->fetchByAssoc($result)) {
			if($hash['id']!=$removeThisID){
				if($hash['id']==$default) {
					$select="SELECTED /";
				} else {
					$select="/";
				}
				$options.="<option value=\"{$hash['id']}\" $select>{$hash['first_name']} {$hash['last_name']}</option>";
			}
		}
		return $options;
	}

	function getCustomDashboardOptions($default="",$omit_none=false) {
		global $mod_strings;
		if($omit_none) {
			$options="<option value=\"\">".$mod_strings['LBL_NONE']."</option>";
		}

		//Add all custom dashboards
		$sql="SELECT id,assigned_user_id FROM user_preferences WHERE assigned_user_id LIKE 'DD_%' AND deleted=0;";
		$result=$this->db->query($sql,true);
		while($hash=$this->db->fetchByAssoc($result)) {
			if($hash['id']==$default) {
				$select="SELECTED /";
			} else {
				$select="/";
			}
			$name=substr($hash['assigned_user_id'],3);
			$options.="<option value='{$hash['assigned_user_id']}' $select>{$name}</option>";
		}
		return $options;
	}

	function getDashboardName($dashboard_id) {
		global $mod_strings;
		$sql="SELECT id,first_name,last_name FROM users WHERE id='{$dashboard_id}' AND deleted=0;";
		$result=$this->db->query($sql,true);
		$hash=$this->db->fetchByAssoc($result);
		if(!empty($hash['id'])) {
			return $mod_strings['LBL_LINK_TO']." ".$hash['first_name'] . " " . $hash['last_name'] ;
		} else {
			$sql="SELECT id,assigned_user_id FROM user_preferences WHERE assigned_user_id='{$dashboard_id}';";
			$result=$this->db->query($sql,true);
			$hash=$this->db->fetchByAssoc($result);
			return substr($hash['assigned_user_id'],3);
		}
	}

	function afterLogin() {
		global $current_user, $current_language;
		$mod_strings = return_module_language($current_language, 'Users');
		//$current_user->retrieve($current_user->id);
		$default_homepage = $current_user->hm_default_homepage;
		$only_once = $current_user->hm_only_once;
		$lock = $current_user->hm_lockhomepage;
		$only_once_toggle = $current_user->hm_toggle;
		if(!empty($default_homepage) && $only_once_toggle!='on') {
			$sql="SELECT id,contents FROM user_preferences WHERE assigned_user_id='{$default_homepage}' AND deleted=0 AND category='Home'";
			$result=$this->db->query($sql,true);
			$hash=$this->db->fetchByAssoc($result);
			$contents=unserialize(base64_decode($hash['contents']));
			$dashlets=$contents['dashlets'];
			$pages=$contents['pages'];

                        if(substr($lock,0,5)=='merge') {
                            $dashlets2 = $current_user->getPreference('dashlets', 'Home'); // load user's dashlets config
                            $pages2 = $current_user->getPreference('pages', 'Home');
                            //merge pages
                            //scan $pages for names and store them in an array
                            //it would be nice if tabs each had a unique ID like dashlets do
                            $tabNameArray=array();
                            foreach ($pages as $index=>$content) {
                                    $tabNameArray[$index]=$content['pageTitle'];
                            }
                            //Now scan through pages2 and see what tabs are missing
                            foreach ($pages2 as $index2=>$content2) {
                                    $test=array_search($content2['pageTitle'],$tabNameArray);
                                    if($test!==false) {
                                            //OK, this tab needs to be overridden from $pages
                                            $pages2[$index2]=$pages[$test];
                                            $tabNameArray[$test]="";  //set it to null so we know its used
                                    }
                            }
                            if($lock!="merge_delete") {
                            //add back in any deleted tabs
                                    foreach ($tabNameArray as $index=>$content) {
                                            if(!empty($content)) {
                                                    $pages2[]=$pages[$index];
                                            }
                                    }
                            }
                            $pages=$pages2;
                            //merge dashlets
                            $dashlets3=array_merge_recursive($dashlets,$dashlets2);
                            $dashlets=$dashlets3;
                        }

                        if(!empty($dashlets)) {
                            $current_user->setPreference('dashlets', $dashlets, 0, 'Home');
			}
			if(!empty($pages)) {
                            $current_user->setPreference('pages', $pages, 0, 'Home');
			}
                        
			$GLOBALS['log']->info("User {$current_user->user_name}'s Home page set to {$default_homepage}");
			$current_user->savePreferencesToDB();
		}
		if($only_once=="1") {
			$current_user->hm_default_homepage="";
			$current_user->hm_only_once='0';
			$current_user->hm_toggle='0';
			$current_user->save(false);
		}
	}

	function createFormHTML($only_once,$lock,$default_homepage_options) {
		global $mod_strings;
		if($_REQUEST['action']=='DetailView') {
			$disabled='DISABLED';
		} else {
			$disabled='';
		}
		$html="<div>
		<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"5\" class=\"edit view\">
		<tr>
			<th align=\"left\" scope=\"row\" colspan=\"4\">
				<h4>
				{$mod_strings["LBL_HOMEPAGE_OPTIONS"]}
				</h4>
			</th>
		</tr>
		<tr>
			<td width=\"20%\" class=\"dataLabel\" scope=\"row\">
				<slot>
					{$mod_strings["LBL_DEFAULT_HOMEPAGE"]}
				</slot>
			</td>
			<td class=\"dataField\">
				<slot>
					<select name=\"hm_default_homepage\" {$disabled}>
					{$default_homepage_options}
					</select>
					<br>
				</slot>
			</td>
			<td class=\"dataField\" scope=\"row\">
				<slot>
					{$mod_strings["LBL_DEFAULT_HOMEPAGE_TEXT"]}
				</slot>
			</td>
		</tr>
		<tr>
			<td width=\"20%\" class=\"dataLabel\" scope=\"row\">
				<slot>
					{$mod_strings["LBL_HOMEPAGE_MANAGER_ONLY_ONCE"]}
				</slot>
			</td>
			<td valign=\"top\" class=\"dataField\" scope=\"row\">
				<slot>
					<INPUT class=\"checkbox\" type=\"checkbox\" name=\"hm_only_once\" value=\"1\" {$disabled} {$only_once}>
					&nbsp;
				</slot>
			</td>
			<td class=\"dataField\" scope=\"row\">
				<slot>
					{$mod_strings["LBL_HOMEPAGE_MANAGER_ONCE_ONLY_TEXT"]}
				</slot>
			</td>
		</tr>
		<tr>
			<td width=\"20%\" class=\"dataLabel\" scope=\"row\">
				<slot>
					{$mod_strings["LBL_LOCK_HOMEPAGE"]}
				</slot>
			</td>
			<td valign=\"top\" class=\"dataField\" scope=\"row\">
				<slot>
					<INPUT class=\"checkbox\" type=\"checkbox\" name=\"hm_lockhomepage\" value=\"1\" {$disabled} {$lock}>
					&nbsp;
				</slot>
			</td>
			<td class=\"dataField\" scope=\"row\">
				<slot>
					{$mod_strings["LBL_LOCK_HOMEPAGE_TEXT"]}
				</slot>
			</td>
		</tr>
		</table>
		</div>";
		$html_lines=explode('\n',$html);
		$html="";
		foreach ($html_lines as $value) {
			$html.=trim($value);
		}
		return trim( preg_replace( '/\s+/', ' ', $html ) );
	}
}
?>