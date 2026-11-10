<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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


class CampaignLog extends SugarBean {

    var $table_name = 'campaign_log';
    var $object_name = 'CampaignLog';
    var $module_dir = 'CampaignLog';

    var $new_schema = true;

    var $campaign_id;
    var $target_tracker_key;
    var $target_id;
    var $target_type;
    var $activity_type;
    var $activity_date;
    var $related_id;
    var $related_type;
    var $deleted;
    var $list_id;
    var $hits;
    var $more_information;
    var $marketing_id;
    function CampaignLog() {
        global $sugar_config;
        parent::SugarBean();

    }

    function get_list_view_data(){
        global $locale;
        $temp_array = $this->get_list_view_array();
        //make sure that both items in array are set to some value, else return null
        if(!(isset($temp_array['TARGET_TYPE']) && $temp_array['TARGET_TYPE']!= '') || !(isset($temp_array['TARGET_ID']) && $temp_array['TARGET_ID']!= ''))
        {   //needed values to construct query are empty/null, so return null
            $GLOBALS['log']->debug("CampaignLog.php:get_list_view_data: temp_array['TARGET_TYPE'] and/or temp_array['TARGET_ID'] are empty, return null");
            $emptyArr = array();
            return $emptyArr;
        }

        $table = strtolower($temp_array['TARGET_TYPE']);

        if($temp_array['TARGET_TYPE']=='Accounts'){
            $query = "select name from $table where id = ".$this->db->quoted($temp_array['TARGET_ID']);
        }else{
            $query = "select first_name, last_name, ".$this->db->concat($table, array('first_name', 'last_name'))." name from $table" .
                " where id = ".$this->db->quoted($temp_array['TARGET_ID']);
        }
        $result=$this->db->query($query);
        $row=$this->db->fetchByAssoc($result);

        if ($row) {
            if($temp_array['TARGET_TYPE']=='Accounts'){
                $temp_array['RECIPIENT_NAME']=$row['name'];
            }else{
                $full_name = $locale->getLocaleFormattedName($row['first_name'], $row['last_name'], '');
                $temp_array['RECIPIENT_NAME']=$full_name;
            }
        }
        // eintel - email is now retrieved via create_new_list_query()
        //$temp_array['RECIPIENT_EMAIL']=$this->retrieve_email_address($temp_array['TARGET_ID']);

        $query = 'select name from email_marketing where id = \'' . $temp_array['MARKETING_ID'] . '\'';
        $result=$this->db->query($query);
        $row=$this->db->fetchByAssoc($result);

        if ($row)
        {
        	$temp_array['MARKETING_NAME'] = $row['name'];
        }

        // SBCC - "Suppressed by Email Address or domain" subpanel: explain why the email wasn't sent
        if ($this->activity_type == 'blocked') {
            // the list view outputs this as-is, so escape it here (the reason itself is plain text)
            $temp_array['SUPPRESSION_REASON'] = htmlspecialchars($this->get_suppression_reason(), ENT_QUOTES, 'UTF-8');
        }

        return $temp_array;
    }

    /**
     * SBCC - work out the likely reason a 'blocked' campaign log entry was suppressed.
     * Mirrors the checks in EmailMan::sendEmail(), in the same order: address suppression lists,
     * domain suppression lists, then an email address already used for this email marketing message.
     * Worked out when displayed, so suppression lists changed since sending can change the answer.
     */
    function get_suppression_reason()
    {
        // bean values are HTML-encoded when loaded; work with the raw address. Returns plain text.
        $email = strtolower(trim(html_entity_decode((string)$this->more_information, ENT_QUOTES, 'UTF-8')));
        if ($email === '') {
            return 'Unknown (no email address recorded)';
        }
        $suppression = $this->get_campaign_suppression_lists($this->campaign_id);

        // 1. Address is on a "Suppression List - By Email Address" attached to the campaign
        if (isset($suppression['addresses'][$email])) {
            return 'Email address is on suppression list "' . $suppression['addresses'][$email] . '"';
        }

        // 2. Domain is on a "Suppression List - By Domain" attached to the campaign
        $at_pos = strrpos($email, '@');
        if ($at_pos !== false) {
            foreach ($suppression['domains'] as $domain => $list_name) {
                $pos = strrpos($email, $domain);
                if ($pos !== false && $pos > $at_pos) {
                    return 'Domain "' . $domain . '" is on suppression list "' . $list_name . '"';
                }
            }
        }

        // 3. Same email address already logged for this email marketing message
        $query = "SELECT target_id, target_type, activity_type FROM campaign_log"
            . " WHERE deleted = 0 AND more_information = " . $this->db->quoted($email)
            . " AND marketing_id = " . $this->db->quoted($this->marketing_id)
            . " AND id <> " . $this->db->quoted($this->id)
            . " ORDER BY CASE WHEN activity_type = 'targeted' THEN 0 ELSE 1 END, activity_date";
        $row = $this->db->fetchByAssoc($this->db->limitQuery($query, 0, 1));
        if ($row) {
            if ($row['target_id'] == $this->target_id && $row['target_type'] == $this->target_type) {
                return 'Duplicate - this recipient is on more than one of the campaign\'s target lists';
            }
            $name = $this->get_related_name($row['target_id'], $row['target_type']);
            if ($name == $row['target_id'] . $row['target_type']) {
                $name = 'another recipient';
            }
            $name = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
            $own_name = html_entity_decode($this->get_related_name($this->target_id, $this->target_type), ENT_QUOTES, 'UTF-8');
            // compare names ignoring case and spacing differences (double / non-breaking spaces)
            $same_name = strcasecmp(trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $name)), trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $own_name))) == 0;
            if ($row['activity_type'] == 'targeted' && $same_name) {
                return 'Duplicate record - another ' . strtolower(preg_replace('/s$/', '', $row['target_type'])) . ' record with the same name and email was already sent it';
            }
            if ($row['activity_type'] == 'targeted') {
                return 'Duplicate email address - already sent to ' . $name;
            }
            return 'Duplicate email address - shared with ' . $name;
        }

        return 'Unknown - no matching suppression list or duplicate found (lists may have changed since sending)';
    }

    /**
     * SBCC - the campaign's suppression lists, loaded once per request (as EmailManDelivery.php does when sending).
     * Joining from a single email address instead is very slow for addresses shared by thousands of records.
     * @return array('addresses' => array(lower email => list name), 'domains' => array(lower domain => list name))
     */
    function get_campaign_suppression_lists($campaign_id)
    {
        static $cache = array();
        if (isset($cache[$campaign_id])) {
            return $cache[$campaign_id];
        }
        $lists = array('addresses' => array(), 'domains' => array());
        $query = "SELECT pl.id, pl.name, pl.list_type, pl.domain_name FROM prospect_list_campaigns plc"
            . " JOIN prospect_lists pl ON pl.id = plc.prospect_list_id AND pl.deleted = 0"
            . " WHERE plc.campaign_id = " . $this->db->quoted($campaign_id) . " AND plc.deleted = 0"
            . " AND pl.list_type IN ('exempt_address', 'exempt_domain')";
        $result = $this->db->query($query);
        while ($row = $this->db->fetchByAssoc($result, false)) {
            if ($row['list_type'] == 'exempt_domain') {
                $domain = strtolower(trim((string)$row['domain_name']));
                if ($domain !== '' && !isset($lists['domains'][$domain])) {
                    $lists['domains'][$domain] = $row['name'];
                }
                continue;
            }
            $email_query = "SELECT ea.email_address FROM prospect_lists_prospects plp"
                . " JOIN email_addr_bean_rel eabr ON eabr.bean_id = plp.related_id AND eabr.bean_module = plp.related_type AND eabr.deleted = 0"
                . " JOIN email_addresses ea ON ea.id = eabr.email_address_id AND ea.deleted = 0"
                . " WHERE plp.prospect_list_id = " . $this->db->quoted($row['id']) . " AND plp.deleted = 0";
            $email_result = $this->db->query($email_query);
            while ($email_row = $this->db->fetchByAssoc($email_result, false)) {
                $address = strtolower(trim((string)$email_row['email_address']));
                if ($address !== '' && !isset($lists['addresses'][$address])) {
                    $lists['addresses'][$address] = $row['name'];
                }
            }
        }
        return $cache[$campaign_id] = $lists;
    }

    //this function is called statically by the campaign_log subpanel.
    function get_related_name($related_id, $related_type) {
        global $locale;
        $db= DBManagerFactory::getInstance();
        if ($related_type == 'Emails') {
            $query="SELECT name from emails where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $row['name'];
            }
        }
        if ($related_type == 'Contacts') {
            $query="SELECT first_name, last_name from contacts where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $full_name = $locale->getLocaleFormattedName($row['first_name'], $row['last_name']);
            }
        }
        if ($related_type == 'Leads') {
            $query="SELECT first_name, last_name from leads where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $full_name = $locale->getLocaleFormattedName($row['first_name'], $row['last_name']);
            }
        }
        if ($related_type == 'Prospects') {
            $query="SELECT first_name, last_name from prospects where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $full_name = $locale->getLocaleFormattedName($row['first_name'], $row['last_name']);
            }
        }
        if ($related_type == 'CampaignTrackers') {
            $query="SELECT tracker_url from campaign_trkrs where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $row['tracker_url'] ;
            }
        }
        if ($related_type == 'Accounts') {
            $query="SELECT name from accounts where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $row['name'];
            }
        }
		return $related_id.$related_type;
	}

	function create_new_list_query($order_by, $where, $filter = array(), $params = array(), $show_deleted = 0, $join_type = '', $return_array = false, $parentbean = null, $singleSelect = false)
    {
        // eintel - join email to query so that sorting via email is possible
        $query_arr = parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, $return_array, $parentbean, $singleSelect);
        $query_arr['select'] .= ", email_address as recipient_email";
        // SBCC - the recipient is target_id/target_type; related_id/related_type is the sent email or tracker
        $query_arr['from'] .= " LEFT JOIN email_addr_bean_rel eabr ON eabr.deleted = 0 AND eabr.bean_module = campaign_log.target_type"
                . " AND eabr.bean_id = campaign_log.target_id AND eabr.primary_address = 1"
                . " LEFT JOIN email_addresses ea ON ea.deleted = 0 AND ea.id = eabr.email_address_id";
        return $query_arr;
    }
}

?>
