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
 * Contributor(s): Chris Owen <owenc@hubris.net>, Ray Gauss II <rgauss@drscomptech.com>, Scott Eade (DST Fix)
 ********************************************************************************/
/*********************************************************************************

 * Description:
 ********************************************************************************/

require_once('include/utils.php');
require_once('modules/Calendar/Calendar.php');
require_once('modules/vCals/vCal.php');

class iCal extends vCal {

    function iCal() {
        parent::vCal();
    }

    function escapeNls($string) {
        // escape new lines
        $str = str_replace("\r\n", "\\n", $string);
        return $str;
    }

    function get_utc_time($ts) {
        $date_arr = array('ts'=>$ts);
        $time = new DateTimeUtil($date_arr,true);
        return $time->get_utc_date_time();
    }

    function convert_min_to_hr_min($minutes) {
        $hrs = floor(abs($minutes) / 60);
        $remainderMinutes = abs($minutes) - ($hrs * 60);
        $sign = (($minutes < 0) ? "-" : "");
        return $sign . str_pad($hrs, 2, "0", STR_PAD_LEFT) . str_pad($remainderMinutes, 2, "0", STR_PAD_LEFT);
    }

    function create_sugar_ical_todo($user_bean, $task, $moduleName, $dtstamp) {
        global $sugar_config;
        $str = "";
        $str .= "BEGIN:VTODO\n";
        $validDueDate = (isset($task->date_due) && $task->date_due != "" && $task->date_due != "0000-00-00");
        $validDueTime = (isset($task->time_due) && $task->time_due != "");
        $dueYear = 1970;
        $dueMonth = 1;
        $dueDay = 1;
        $dueHour = 0;
        $dueMin = 0;
        if ($validDueDate) {
            $dateDueArr = split("-", $task->date_due);
            $dueYear = (int)$dateDueArr[0];
            $dueMonth = (int)$dateDueArr[1];
            $dueDay = (int)$dateDueArr[2];

            if ($validDueTime) {
                $timeDueArr = split(":", $task->time_due);
                $dueHour = (int)$timeDueArr[0];
                $dueMin = (int)$timeDueArr[1];
            }
        }
        $date_arr = array(
             'day'=>$dueDay,
             'month'=>$dueMonth,
             'hour'=>$dueHour,
             'min'=>$dueMin,
             'year'=>$dueYear);
        $due_date_time = new DateTimeUtil($date_arr,true);
        $str .= "DTSTART;TZID=" . $user_bean->getPreference('timezone') . ":" .
                    str_replace("Z", "", $due_date_time->get_utc_date_time()) . "\n";
        $str .= "DTSTAMP:" . $dtstamp . "\n";
        $str .= "SUMMARY:" . $task->name . "\n";
        $str .= "UID:" . $task->id . "\n";
        if ($validDueDate) {
            $str .= "DUE;VALUE=DATE:" . str_replace("-", "", $task->date_due) . "\n";
        }
        $str .= "DESCRIPTION:Project: " . $task->project_name. "\\n\\n" .
            $this->escapeNls($task->description). "\n";
        $str .= "URL;VALUE=URI:" . $sugar_config['site_url'].
            "/index.php?module=".$moduleName."&action=DetailView&record=". $task->id. "\n";
        if ($task->status == 'Completed') {
            $str .= "STATUS:COMPLETED\n";
            $str .= "COMPLETED:" . $due_date_time->get_utc_date_time() . "\n";
        }
        if ($task->priority == "Low") {
            $str .= "PRIORITY:9\n";
        } else if ($task->priority == "Medium") {
                $str .= "PRIORITY:5\n";
        } else if ($task->priority == "High") {
                $str .= "PRIORITY:1\n";
        }
        $str .= "END:VTODO\n";
        return $str;
    }

    // query and create the iCal Events for SugarCRM Meetings and Calls and
    // return the string
    function create_sugar_ical(&$user_bean,&$start_date_time,&$end_date_time, $dtstamp) {
        $str = '';
        global $DO_USER_TIME_OFFSET, $sugar_config;

        $acts_arr = CalendarActivity::get_activities($user_bean->id,
            false,
            $start_date_time,
            $end_date_time,
            'week');

        // loop thru each activity, get start/end time in UTC, and return iCal strings
        for ($i = 0;$i < count($acts_arr);$i++)
        {
            $act =$acts_arr[$i];

            $event = $act->sugar_bean;
            $str .= "BEGIN:VEVENT\n";
            $str .= "SUMMARY:" . $event->name . "\n";
            $str .= "DTSTART;TZID=" . $user_bean->getPreference('timezone') . ":" .
                    str_replace("Z", "", $this->get_utc_time($act->start_time->ts)) . "\n";
            $str .= "DTEND;TZID=" . $user_bean->getPreference('timezone') . ":" .
                    str_replace("Z", "", $this->get_utc_time($act->end_time->ts)) . "\n";
            $str .= "DTSTAMP:" . $dtstamp . "\n";
            $str .= "DESCRIPTION:" . $this->escapeNls($event->description) . "\n";
            $str .= "URL;VALUE=URI:" . $sugar_config['site_url'].
                "/index.php?module=".$event->module_dir."&action=DetailView&record=". $event->id. "\n";
            $str .= "UID:" . $event->id . "\n";
            if ($event->object_name == "Meeting") {
                $str .= "LOCATION:" . $event->location . "\n";
                $eventUsers = $event->get_meeting_users();
                $query = "SELECT contact_id as id from meetings_contacts where meeting_id='$event->id' AND deleted=0";
                $eventContacts = $event->build_related_list($query, new Contact());
                $eventAttendees = array_merge($eventUsers, $eventContacts);
                if (is_array($eventAttendees)) {
                    foreach($eventAttendees as $attendee) {
                        if ($attendee->id != $user_bean->id) {
                            $str .= 'ATTENDEE;CN="'.$attendee->get_summary_text().'":mailto:'. $attendee->email1 . "\n";
                        }
                    }
                }
            }
            if ($event->object_name == "Call") {
                $eventUsers = $event->get_call_users();
                $eventContacts = $event->get_contacts();
                $eventAttendees = array_merge($eventUsers, $eventContacts);
                if (is_array($eventAttendees)) {
                    foreach($eventAttendees as $attendee) {
                        if ($attendee->id != $user_bean->id) {
                            $str .= 'ATTENDEE;CN="'.$attendee->get_summary_text().'":mailto:'. $attendee->email1 . "\n";
                        }
                    }
                }
            }
            if ($event->reminder_time > 0) {
                $str .= "BEGIN:VALARM\n";
                $str .= "TRIGGER:-PT" . $event->reminder_time/60 . "M\n";
                $str .= "ACTION:DISPLAY\n";
                $str .= "DESCRIPTION:" . $event->name . "\n";
                $str .= "END:VALARM\n";
            }
            $str .= "END:VEVENT\n";

        }

        require_once('include/TimeDate.php');
        $timedate = new TimeDate();
        $today = gmdate("Y-m-d");
        $today = $timedate->handle_offset($today, $timedate->dbDayFormat, false);

        require_once('modules/ProjectTask/ProjectTask.php');
        $where = "project_task.assigned_user_id='{$user_bean->id}' ".
            "AND (project_task.status IS NULL OR (project_task.status!='Deferred')) ".
            "AND (project_task.date_start IS NULL OR project_task.date_start <= '$today')";
        $seedProjectTask = new ProjectTask();
        $projectTaskList = $seedProjectTask->get_full_list("", $where);
        if (is_array($projectTaskList)) {
            foreach($projectTaskList as $projectTask) {
                $str .= $this->create_sugar_ical_todo($user_bean, $projectTask, "ProjectTask", $dtstamp);
            }
        }

        require_once('modules/Tasks/Task.php');
        $where = "tasks.assigned_user_id='{$user_bean->id}' ".
            "AND (tasks.status IS NULL OR (tasks.status!='Deferred')) ".
            "AND (tasks.date_start IS NULL OR tasks.date_start <= '$today')";
        $seedTask = new Task();
        $taskList = $seedTask->get_full_list("", $where);
        if (is_array($taskList)) {
            foreach($taskList as $task) {
                $str .= $this->create_sugar_ical_todo($user_bean, $task, "Tasks", $dtstamp);
            }
        }

        return $str;
    }

    function get_timezone_string() {
        global $current_user, $timedate;
        $timezoneName = $current_user->getPreference('timezone');
        $timezone = $timedate->getUserTimeZone();

        // dstOffset is sometimes not present.
        $dstOffset = 0;
        if (array_key_exists('dstOffset', $timezone)) {
            $dstOffset = $timezone['dstOffset'];
        }

        $dstRange = $timedate->getDSTRange(date('Y'), $timezone);
        $startstamp = strtotime($dstRange['start']);
        $endstamp = strtotime($dstRange['end']);

        $timezoneString = "BEGIN:VTIMEZONE\n";
        $timezoneString .= "TZID:" . $timezoneName . "\n";
        $timezoneString .= "X-LIC-LOCATION:" . $timezoneName . "\n";

        $timezoneString .= "BEGIN:DAYLIGHT\n";
        $timezoneString .= "TZOFFSETFROM:" . $this->convert_min_to_hr_min($timezone['gmtOffset']) . "\n";
        $timezoneString .= "TZOFFSETTO:" . $this->convert_min_to_hr_min($timezone['gmtOffset'] + $dstOffset) . "\n";
        $timezoneString .= "DTSTART:" . str_replace("Z", "", $this->get_utc_time($startstamp)) . "\n";
        $timezoneString .= "END:DAYLIGHT\n";

        $timezoneString .= "BEGIN:STANDARD\n";
        $timezoneString .= "TZOFFSETFROM:" . $this->convert_min_to_hr_min($timezone['gmtOffset'] + $dstOffset) . "\n";
        $timezoneString .= "TZOFFSETTO:" . $this->convert_min_to_hr_min($timezone['gmtOffset']) . "\n";
        $timezoneString .= "DTSTART:" . str_replace("Z", "", $this->get_utc_time($endstamp)) . "\n";
        $timezoneString .= "END:STANDARD\n";

        $timezoneString .= "END:VTIMEZONE\n";

        return $timezoneString;
    }

    // return a iCal vcal string
    function get_vcal_ical(&$user_focus) {
           global $current_user;
           $current_user = $user_focus;

           $cal_name = $user_focus->first_name. " ". $user_focus->last_name;

           $str = "BEGIN:VCALENDAR\n";
           $str .= "VERSION:2.0\n";
           $str .= "METHOD:PUBLISH\n";
           $str .= "X-WR-CALNAME:$cal_name (SugarCRM)\n";
           $str .= "PRODID:-//SugarCRM//SugarCRM Calendar//EN\n";
           $str .= $this->get_timezone_string();
           $str .= "CALSCALE:GREGORIAN\n";

           // get current time local
           $date_arr = array();
           $now_date_time_local = new DateTimeUtil($date_arr,true);

           // get current time GMT
           $date_arr = array('ts'=>$now_date_time_local->ts - $now_date_time_local->tz_offset);
           $now_date_time = new DateTimeUtil($date_arr,true);

           // get start date GMT ( 1 month ago )
           $date_arr = array(
             'day'=>$now_date_time->day,
             'month'=>($now_date_time->month - 1),
             'hour'=>($now_date_time->hour),
             'min'=>($now_date_time->min),
             'year'=>$now_date_time->year);

           $start_date_time = new DateTimeUtil($date_arr,true);

           // get date 2 months from start date
           $date_arr = array(
             'day'=>$start_date_time->day,
             'month'=>($start_date_time->month + 2),
             'hour'=>($start_date_time->hour),
             'min'=>($start_date_time->min),
             'year'=>$start_date_time->year);

           $end_date_time = new DateTimeUtil($date_arr,true);

           // get UTC time format
           $utc_start_time = $start_date_time->get_utc_date_time();
           $utc_end_time = $end_date_time->get_utc_date_time();
           $utc_now_time = $now_date_time->get_utc_date_time();

           $str .= $this->create_sugar_ical($user_focus,$start_date_time,$end_date_time,$utc_now_time);

           $str .= "DTSTAMP:" . $utc_now_time . "\n";
           $str .= "END:VCALENDAR\n";

           return $str;
    }

}

?>
