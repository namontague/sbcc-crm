<?php
/*********************************************************************************
 * (C) 2010 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 ********************************************************************************/

//require_once('modules/Calendar/DateTimeUtil.php');

/**
 * Encapsulates logic about repeating activitis
 */
class ActivitySchedule extends SugarBean {


    var $module_dir = 'ActivitySchedule';
    var $object_name = 'ActivitySchedule';
    var $table_name = 'activity_schedules';
    var $importable = false;


    var $id;
    var $type;
    var $repeat_interval;
    var $startdate;
    var $nextdate;
    var $completion_offset_secs=0;
    var $enddate;
    var $numwithinmonth;
    var $daysofweek;

    var $parent_type;
    var $parent_name;
    var $parent_id;

    var $window_start;
    var $window_end;
    
    var $scheduleStart;
    var $scheduleEnd;
    var $numwithinmonthArray;
    var $daysofweekArray;

    function ActivitySchedule() {
        parent::SugarBean();
    }

    function addFieldsTo($bean, $key = 'activity_schedule') {
        $exportedFields = array('type', 'repeat_interval', 'startdate', 'nextdate', 'enddate', 'numwithinmonth', 'daysofweek');
        foreach($exportedFields as $f) {
            $bf = $key.'_'.$f;
            $bean->field_defs[$bf] = $this->field_defs[$f];
            $bean->field_defs[$bf]['source'] = 'non-db';
            $bean->$bf = $this->$f;
        }
        $bf = $key.'_updatetemplate';
        $bean->field_defs[$bf] = array(
            'name' => $bf,
            'source' => 'non-db',
            'type' => 'int'
        );
        if( $bean->id == $this->parent_id || empty($this->parent_id)) {
            $bean->$bf = 1;
        } else {
            $bean->$bf = 0;
        }


    }

    function updateValuesFrom($bean, $key = 'activity_schedule') {
        $updatefield = $key.'_updatetemplate';
        $this->updateParent($bean, $updatefield);

        $importedFields= array('type', 'repeat_interval', 'startdate', 'nextdate', 'enddate', 'numwithinmonth', 'daysofweek');
        foreach($importedFields as $f) {
            $bf = $key.'_'.$f;
            $this->$f = $bean->$bf;
        }
        if(!isset($bean->id) || $this->parent_id === $bean->id)  {
            $copiedFields = array('name'=>'name', 'date_start'=>'startdate');
            foreach($copiedFields as $bf => $f) {
                if( empty($this->$f) || !empty($bean->$updatefield)) {
                    $this->$f = $bean->$bf;
                }
            }
            if( empty($this->nextdate) ) {
                $this->nextdate = $this->startdate;
            } else if( !empty($this->startdate)) {
                $next = $this->dateStringToDateTime($this->nextdate);
                $start = $this->dateStringToDateTime($this->startdate);
                if( $this->dateLessThan($next, $start)) {
                    $this->nextdate = $this->startdate;
                }
            }
        }
    }

    function updateParent($bean, $updatefield = null) {
      if( isset($bean->id) && (empty($this->parent_id) || !empty($bean->$updatefield))) {
          $this->parent_type=$bean->object_name;
          $this->parent_id=$bean->id;
      }
    }

    function loadFromRowWithPrefix($row, $prefix) {
        $newrow = array();
        $prefixLength = strlen($prefix);
        foreach($row as $k => $v) {
            if( substr($k, 0,$prefixLength) == $prefix ) {
                $newrow[substr($k,$prefixLength)] = $v;               
            }
        }
        return $this->loadFromRow($newrow);
    }

    function saveScheduleFor($bean, $check_notify) {
        if( $this->new_with_id == true ) {
          // Doing a resave, update the parent details but leave the rest alone so that the date formatting
          // is not stuffed up.
          $this->updateParent($bean);
          $bean->activity_schedule_id = $this->save($check_notify);

        } else {
          $this->updateValuesFrom($bean);
          if( empty($this->type)) {
              $bean->activity_schedule_id ='';
              if(!empty($this->id)) {
                  $this->mark_deleted($this->id);
              }
          } else {
              if(empty($this->id)) {
                  $bean->activity_schedule_id = create_guid();
                  $this->id = $bean->activity_schedule_id;
                  $this->new_with_id=true;
                  return true;
              }
              $bean->activity_schedule_id = $this->save($check_notify);
          }
        }
        if ($bean->activity_schedule_id) {
            $this->createNextInstance($bean);
        }
        return false;
    }

    static function loadScheduleFor($bean, $scheduleField='activity_schedule') {
        if(empty($bean->$scheduleField)) {
            $bean->$scheduleField = new ActivitySchedule();
            $scheduleIdField = "${scheduleField}_id";
            if(!empty($bean->$scheduleIdField) ) {
                $bean->$scheduleField->retrieve($bean->$scheduleIdField);
            }
        }
        $bean->$scheduleField->addFieldsTo($bean);

    }

    function setCompletionOffsetFromEndDate($endDate) {
        if( !empty($endDate) && !empty($this->startdate)) {
            $start = $this->dateStringToDateTime($this->startdate);
            $end = $this->dateStringToDateTime($endDate);
            $this->completion_offset_secs = $this->dateSecondsSubtract($end,$start);
        } else {
            $this->completion_offset_secs = -1;
        }
    }

    function setWindow($start, $end) {
        if( is_string($start)) {
            $this->window_start = $this->dateStringToDateTime($start);
        } else if( is_a($start, 'DateTime')) {
            $this->window_start = $start;
        }
        if( is_string($end)) {
            $this->window_end = $this->dateStringToDateTime($end);
        } else if( is_a($end, 'DateTime')) {
            $this->window_end = $end;
        }

    }

    function dateStringToDateTime($date, $inDisplayFormat = true) {
        global $timedate;
        $date = trim($date);
        if (empty($date)) {
            return null;
        }
        if (strlen($date) <= 10) {
            $date .= ' ' . $timedate->get_default_midnight();
        }

        if ($timedate->check_matching_format($date, TimeDate::DB_DATETIME_FORMAT)) {
            // Ok , no need to convert
        } else {
            $date = $timedate->to_db($date);
        }

        preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $date, $matches);

        global $timedate;
        $dt = $timedate->tzGMT(new SugarDateTime());
        $dt->setDate($matches[1],$matches[2],$matches[3]);
        $dt->setTime($matches[4],$matches[5],0);
        return $dt;
    }



    function prepare() {
        global $timedate;
        if( !isset($this->scheduleStart) && !empty($this->nextdate)) {
            $this->scheduleStart = $timedate->tzUser($this->dateStringToDateTime($this->nextdate));

        }
        if( !isset($this->scheduleEnd) && !empty($this->enddate)) {
            $this->scheduleEnd = $timedate->tzUser($this->dateStringToDateTime($this->enddate));
        }
        if (empty($this->repeat_interval)) {
            $this->repeat_interval = 1;
        }


        if( !isset($this->numwithinmonthArray)) {
            if (empty($this->numwithinmonth)) {
                if ($this->type == 'md') {
                    $this->numwithinmonthArray = array($this->scheduleStart->day);
                } else {
                    $this->numwithinmonthArray = array($this->scheduleStart->week);
                }
            } else {
                $this->numwithinmonthArray = explode(',', $this->numwithinmonth);
            }
        }
        
        if(!isset($this->daysofweekArray)) {
            if (empty($this->daysofweek)) {
                $this->daysofweekArray = array($this->scheduleStart->day_of_week);
            } else {
                $this->daysofweekArray = unencodeMultienum( $this->daysofweek);
            }
        }
    }

    function nextInstance() {
        $this->prepare();
        switch ($this->type) {
            case 'd':
                $next =  $this->nextInstanceDay();
                break;
            case 'w':
                $next = $this->nextInstanceWeek();
                break;
            case 'md':
                $next =  $this->nextInstanceDateInMonth();
                break;
            case 'mw':
                $next =  $this->nextInstanceNthDayOfMonth();
                break;
            case 'y':
                $next =  $this->nextInstanceYear();
                break;
        }
        if( !isset($this->scheduleEnd) || $this->dateLessThanOrEqual($next, $this->scheduleEnd)) {
            return $next;
        } else {
            return null;
        }

    }

    function instancesInWindow() {
        $this->prepare();
        switch ($this->type) {
            case 'd':
                return $this->windowInstancesDay();
                break;
            case 'w':
                return $this->windowInstancesWeek();
                break;
            case 'md':
                return $this->windowInstancesDateInMonth();
                break;
            case 'mw':
                return $this->windowInstancesNthDayOfMonth();
                break;
            case 'y':
                return $this->windowInstancesYear();
                break;
        }
    }


    private function nextInstanceDay() {
        $year = $this->scheduleStart->year;
        $month = $this->scheduleStart->month;
        $day = $this->scheduleStart->day + $this->repeat_interval;

        return $this->newDateTime($day, $month, $year);
    }

    private function windowInstancesDay() {
        $first = $this->getWindowFirst();
        $last = $this->getWindowLast();

        $offset = $this->dateDaySubtract($first, $this->scheduleStart);
        $intervalCount = floor($offset / $this->repeat_interval);
        $result = array();

        $candidate = $this->addDays($this->scheduleStart, $this->repeat_interval * $intervalCount);
        while($candidate && $this->dateLessThanOrEqual($candidate, $first)) {
            $candidate = $this->addDays($candidate, $this->repeat_interval);
        }

        while($candidate && $this->dateLessThan($candidate, $last)) {
            $result[] = $candidate;
            $candidate = $this->addDays($candidate, $this->repeat_interval);
        }

        return $result;
    }

    private function nextInstanceWeek() {
        return $this->nextInstanceWeekFrom($this->scheduleStart);
    }

    private function nextInstanceWeekFrom($date) {
        $week_offset = 0;
        $offset = 0;
        while ($offset <= 0) {
            foreach ($this->daysofweekArray as $w) {
                $offset = $w - $date->day_of_week;
                $offset += ($this->repeat_interval * $week_offset);
                if ($offset > 0) {
                    break;
                }
            }
            $week_offset += 7;
        }

        $day = $date->day + $offset;

        return $this->newDateTime($day, $date->month, $date->year);
    }

    private function windowInstancesWeek() {
        $first = $this->getWindowFirst();
        $last = $this->getWindowLast();

        $result = array();

        $candidate = $this->nextInstanceWeekFrom($first);
        while($candidate && $this->dateLessThan($candidate, $last)) {
            $result[] = $candidate;
            $candidate = $this->nextInstanceWeekFrom($candidate);
        }
        return $result;
    }

    private function nextInstanceDateInMonth() {
        return $this->nextInstanceDateInMonthFrom($this->scheduleStart);
    }

    private function nextInstanceDateInMonthFrom($date) {
        $year = $date->year;
        $month = $date->month;

        $i = 0;
        while ($i < 12) {
            foreach ($this->numwithinmonthArray as $d) {
                if (checkdate($month, $d, $year)) {
                    $next_date = $this->newDateTime($d, $month, $year);
                    if ($this->dateGreaterThan($next_date, $date)) {
                        return $next_date;
                    }
                }
            }
            $month += $this->repeat_interval;
            while ($month > 12) {
                $month -= 12;
                $year += 1;
            }
            $i++;
        }
        return null;
    }

    private function windowInstancesDateInMonth() {
        $first = $this->getWindowFirst();
        $last = $this->getWindowLast();

        $result = array();

        $candidate = $this->nextInstanceDateInMonthFrom($first);
        while($candidate && $this->dateLessThan($candidate, $last)) {
            $result[] = $candidate;
            $candidate = $this->nextInstanceDateInMonthFrom($candidate);
        }
        return $result;
    }


    private function nextInstanceNthDayOfMonth() {
         return  $this->nextInstanceNthDayOfMonthFrom($this->scheduleStart);;
    }

    private function nextInstanceNthDayOfMonthFrom($date) {
        $year = $date->year;
        $month = $date->month;

        $i = 0;
        while ($i < 12) {
            foreach ($this->numwithinmonthArray as $n) {
                foreach( $this->daysofweekArray as $dow) {
                    $d = $this->nthDayOfMonth($n, $dow, $month, $year);
                    if (checkdate($month, $d, $year)) {
                        $next_date = $this->newDateTime($d, $month, $year);
                        if ($this->dateGreaterThan($next_date, $date)) {
                            return $next_date;
                        }
                    }

                }
            }
            $month += $this->repeat_interval;
            while ($month > 12) {
                $month -= 12;
                $year += 1;
            }
            $i++;
        }
        return null;
    }


    private function windowInstancesNthDayOfMonth() {
        $first = $this->getWindowFirst();
        $last = $this->getWindowLast();

        $result = array();

        $candidate = $this->nextInstanceNthDayOfMonthFrom($first);
        while($candidate && $this->dateLessThan($candidate, $last)) {
            $result[] = $candidate;
            $candidate = $this->nextInstanceNthDayOfMonthFrom($candidate);
        }
        return $result;
    }


    private function nextInstanceYear() {
        $year = $this->scheduleStart->year + $this->repeat_interval;
        $month = $this->scheduleStart->month;
        $day = $this->scheduleStart->day;
        return $this->newDateTime($day, $month, $year);
    }

    private function windowInstancesYear() {
        $first = $this->getWindowFirst();
        $last = $this->getWindowLast();

        $offset = $this->dateYearSubtract($first, $this->scheduleStart);
        $intervalCount = floor($offset / $this->repeat_interval);
        $result = array();

        $candidate = $this->addYears($this->scheduleStart, $this->repeat_interval * $intervalCount);
        while($candidate && $this->dateLessThanOrEqual($candidate, $first)) {
            $candidate = $this->addYears($candidate, $this->repeat_interval);
        }

        while($candidate && $this->dateLessThan($candidate, $last)) {
            $result[] = $candidate;
            $candidate = $this->addYears($candidate, $this->repeat_interval);
        }

        return $result;
    }



    private function getWindowFirst() {
        $first = $this->window_start;
        if( $this->dateLessThan($first, $this->scheduleStart ) ) {
            $first = $this->scheduleStart;
        }
        return $first;
    }
    private function getWindowLast() {
        $last = $this->window_end;
        if( isset($this->scheduleEnd) && $this->dateGreaterThan($last, $this->scheduleEnd ) ) {
            $last = $this->scheduleEnd;
        }
        return $last;

    }
    /*
     * nthDayOfMonth($n,$dow,$month,$year) will compute the Nth day of the given
     * month.  For example, the first Monday in April 2001.
     *
     * Parameters:
     *
     *  $n - the Nth day you want, i.e. 2 for 2nd
     *  $dow - The day of week you want, 0=Sunday, 1=Monday, etc. [0-6]
     *  $month - The month you want [1-12]
     *  $year - The full year [1900? - ??]
     *
     * Returns:
     *
     *  The date, in the month you passed, that fits the criteria.
     *  Here we return to error conditions:
     *    -1 = invalid date
     *    -2 = There is no Nth day of that month, like no 5th Tuesday
     *         of the specified month
     *
     * Example:
     *
     *  To find the first Tuesday in June 2001:
     *     $day = nthDayInMonth(1,2,6,2001);
     *  will return 4, which means the first Tuesday in June is 4/6/2001.
     *
     */
    private function nthDayOfMonth($n, $dow, $month, $year) {
        /* Check the date */
        if ($month > 12) {
            return -1;
        }
        if ($dow > 6) {
            return -1;
        }
        /* Valid Nth day, should be no more than 5 */
        if (($n <= 0) || ($n > 5)) {
            return -1;
        }


        $first_day_of_month = $this->newDateTime(1,$month,$year);

        $retval = (7 * $n) - 6 + $this->phpdow_mod($dow - $first_day_of_month->format('w'), 7);
        /* Make sure the date is not greater than the number of days
           in the month */
        if (!checkdate($month, $retval, $year)) {
            return -2;
        }
        /* Otherwise we return the date */
        return $retval;
    }


    /*
    * @returns will deal negative numbers.
    * @param $a first day.
    * @param $b second value.
    */
    private function phpdow_mod($a,$b) {
        if ($a <= 0) {
            return (int)$this->phpdow_mod($b-abs($a), $b);
        } else {
           return (int)($a % $b);
        }
    }


    private function newDateTime($day, $month, $year) {
        global $timedate;
        $dt = $timedate->tzUser(new SugarDateTime());
        $dt->setDate($year, $month, $day);
        $dt->setTime($this->scheduleStart->hour, $this->scheduleStart->min, $this->scheduleStart->sec);
        return $dt;
    }

    private function addDays(SugarDateTime $date, $days) {
        return $date->add(new DateInterval("P{$days}D"));
    }

    private function addYears(SugarDateTime $date, $years) {
        return $date->add(new DateInterval("P{$years}Y"));
    }


    private function dateLessThan(SugarDateTime $lhs, SugarDateTime $rhs) {
        return $lhs->getTimestamp() < $rhs->getTimestamp();
    }
    private function dateLessThanOrEqual(SugarDateTime $lhs, SugarDateTime $rhs) {
        return $lhs->getTimestamp() <= $rhs->getTimestamp();
    }

    private function dateGreaterThan(SugarDateTime $lhs, SugarDateTime $rhs) {
        return $lhs->getTimestamp() > $rhs->getTimestamp();
    }
    private function dateGreaterThanOrEqual(SugarDateTime $lhs, SugarDateTime $rhs) {
        return $lhs->getTimestamp() >= $rhs->getTimestamp();
    }

    private function dateSecondsSubtract(SugarDateTime $lhs, SugarDateTime $rhs) {
        return $lhs->getTimestamp()  - $rhs->getTimestamp();
    }

    private function dateDaySubtract(SugarDateTime $lhs, SugarDateTime $rhs) {
        return round(($lhs->getTimestamp()  - $rhs->getTimestamp()) / 86400);
    }

    private function dateYearSubtract(SugarDateTime $lhs, SugarDateTime $rhs) {
        return round(($lhs->getTimestamp()  - $rhs->getTimestamp()) / (86400*365));
    }

    private function toDateOnly(SugarDateTime $datetime) {
        $datetime->setDate(0,0,0);
        return $datetime;
    }

    public function getSchedulesBefore($date) {
        $query = "SELECT * FROM {$this->table_name} WHERE nextdate < '{$date}' and deleted =0";
        $result = $this->db->query($query, true);

        $list = array();
        while($row = $this->db->fetchByAssoc($result)) {
            $sched = new ActivitySchedule();
            $sched->loadFromRow($row);
            $list[] = $sched;
        }
        return $list;

    }
 
    public function correctSchedules() {
        $query = "UPDATE {$this->table_name} SET nextdate=startdate WHERE nextdate < startdate and deleted =0";
        $result = $this->db->query($query, true);
    }

    public function createNextInstance($bean)
    {
        if($bean) {
            global $app_list_strings;
            $_module_name = strtolower($bean->module_name);
            // if bean still active
            if (in_array($bean->status, $app_list_strings["active_{$_module_name}_only"])) {
                return false;
            }

            $latest_inst = $this->getLatestInstance($bean);
            // if bean is not the latest
            if ($bean->id != $latest_inst->id) {
                return false;
            }
        }

        global $timedate, $beanFiles;

        $next = $this->nextInstance();
        if ($next != null) {
            require_once($beanFiles[$this->parent_type]);
            if( $bean && $bean->id == $this->parent_id ) {
                $parent = $bean;
            } else {
                $parent = new $this->parent_type;
                $parent = $parent->retrieve($this->parent_id, true, false);
            }
            $newbean = clone($parent);
            unset($newbean->id);
            unset($newbean->fetched_row);
            unset($newbean->rel_fields_before_value);
            global $sugar_config;
            if(isset($sugar_config['activity_schedule'][$bean->object_name]['ignore'])) {
                foreach($sugar_config['activity_schedule'][$bean->object_name]['ignore'] as $f ) {
                    unset($newbean->$f);
                }
            }
            // Links keep back references to original bean.. causes issues later
            foreach($newbean as $f => $v) {
                if($v instanceof Link || $v instanceof Link2) {
                    unset($newbean->$f);
                }
            }
            $nextdate = $timedate->asDb($next);
            $newbean->date_start = $nextdate;
            $newbean->name = $this->name;

            $newbean->status = '';
            unset($newbean->date_modified);
            unset($newbean->modified_user_id);
            unset($newbean->date_entered);
            unset($newbean->created_by);
            if ($newbean->object_name == 'Task' && $this->completion_offset_secs >= 0) {
                $newbean->date_due = $timedate->asDb($next->add(new DateInterval("PT{$this->completion_offset_secs}S")));
            }

            $newbean->save();
            $this->nextdate = $nextdate;
            $this->save();

            $parent->load_relationships();
            $addedRelationships = false;
            foreach ($parent->field_defs as $def) {
                if ($def['type'] == 'link') {
                    $field = $def['name'];
                    if (isset($parent->$field) && $parent->$field->_relationship->relationship_type == 'many-to-many') {
                        $newbean->load_relationship($field);
                        $rows = null;
                        if($parent->$field instanceof Link2) {
                            $rows = $parent->$field->rows;
                        } else {
                            $rows = $parent->$field->get(true);
                        }
                        foreach ($rows as $vals) {
                            $id = $vals['id'];
                            unset($vals['id']);
                            $newbean->$field->add($id, $vals);
                            $addedRelationships = true;
                        }
                    }
                }
            }
            if( $addedRelationships) {
                $this->save_relationship_changes(true);
            }
        }
    }

    /*
     * Get latest bean related to this Activity Schedule
     * @return the retrieved bean
     */
    public function getLatestInstance($current_bean= null)
    {
        $bean = new $this->parent_type;
        $query = "SELECT * FROM {$bean->table_name} WHERE activity_schedule_id = '{$this->id}' " .
                "AND deleted = 0 " ;
        if( $current_bean && !empty($current_bean->id)) {
            $query .= "AND id <> '{$current_bean->id}' ";
        }
        $query .= "ORDER BY date_start DESC";

        $latest_bean = false;
        // Only get first row
        $bean_id = $this->db->getOne($query, true, 'Error retrieving latest bean');
        if ($bean_id) {
            $latest_bean = $bean->retrieve($bean_id);
        }
        if($current_bean) {
            if($latest_bean) {
                $currentStart = $this->dateStringToDateTime($current_bean->date_start);
                $beanStart= $this->dateStringToDateTime($latest_bean->date_start);
                if( $this->dateLessThanOrEqual($beanStart, $currentStart)) {
                    $latest_bean = $current_bean;
                }
            } else {
                $latest_bean =  $current_bean;
            }
        }
        return $latest_bean;
    }

}


