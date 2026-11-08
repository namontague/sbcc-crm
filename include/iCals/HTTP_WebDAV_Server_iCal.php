<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
 /*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2008 SugarCRM Inc.
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
  * Contributor(s): Chris Owen <owenc@hubris.net>, Ray Gauss II <rgauss@drscomptech.com> patches for iCal support
 ********************************************************************************/


require_once 'config.php';
require_once 'include/utils.php';
require_once 'modules/Users/User.php';
require_once 'modules/Calendar/Calendar.php';
require_once 'include/iCals/iCal.php';
require_once 'modules/vCals/HTTP_WebDAV_Server_vCal.php';


    /**
     * Filesystem access using WebDAV
     *
     * @access public
     */
    class HTTP_WebDAV_Server_iCal extends HTTP_WebDAV_Server_vCal
    {

        var $cal_encoding = "";

        function HTTP_WebDAV_Server_iCal()
        {
           $this->vcal_focus = new iCal();
           $this->user_focus = new User();
        }

        /**
         * Serve a webdav request
         *
         * @access public
         * @param  string
         */
        function ServeICalRequest($base = false) {

            if (empty($_REQUEST['type'])) {
              $_REQUEST['type'] = 'ics';
            }

            if (empty($_REQUEST['encoding'])) {
                $this->cal_encoding = 'utf-8';
            } else {
                $this->cal_encoding = $_REQUEST['encoding'];
            }

            // check the HTTP auth headers for a user
            if (empty($_REQUEST['user_name']) && !empty($_SERVER['PHP_AUTH_USER'])) {
              $_REQUEST['user_name'] = $_SERVER['PHP_AUTH_USER'];
              $_REQUEST['key'] = $_SERVER['PHP_AUTH_PW'];
            }

            // if username is still empty, then return 401
            if (empty($_REQUEST['user_name']) && empty($_REQUEST['email'])) {
                if ($_REQUEST['type'] == 'ics') {
                    $this->http_status("401 not authorized");
                    header('WWW-Authenticate: Basic realm="SugarCRM iCal"');
                    echo 'Authorization required';
                } else {
                    $this->http_status("404 Not Found");
                }
                return;
            }
            parent::ServeRequest();
        }


        /**
        * GET method handler
        *
        * @param void
        * @returns void
        */
        function http_GET() {

           if ($this->vcal_type == 'vfb') {
             $this->http_status("200 OK");
             echo $this->vcal_focus->get_vcal_freebusy($this->user_focus);
           } else if ($this->vcal_type == 'ics') {
             // DO HTTP AUTHORIZATION for iCal:
             if ( $this->user_focus->getPreference('calendar_publish_key') &&
                $this->publish_key != $this->user_focus->getPreference('calendar_publish_key' )) {
                    $this->http_status("401 not authorized");
                    header('WWW-Authenticate: Basic realm="SugarCRM iCal"');
                    echo 'Authorization required';
                    return;
             }

             $this->http_status("200 OK");
             header('Content-Type: text/calendar; charset="utf-8"');
             echo mb_convert_encoding(html_entity_decode($this->vcal_focus->get_vcal_ical($this->user_focus), ENT_QUOTES), $this->cal_encoding);
           } else {
             $this->http_status("404 Not Found");
           }

        }

    }


?>