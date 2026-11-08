<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * (C) 2010 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 ********************************************************************************/

class SugarWidgetFieldActivitySchedule extends SugarWidgetReportField
{


	function displayListPlain($layout_def)
	{
        global $app_list_strings;
		$value = $this->_get_list_value($layout_def);
		if ( empty($value) ) {
			return '';
		}
        $schedule = '';
        if( $value->type == 'd') {
            $schedule .= $value->repeat_interval;
            $schedule .= ' days';
        } else if( $value->type == 'w') {
            $schedule .= $value->repeat_interval;
            $schedule .= ' weeks';
            if( !empty($value->daysofweek)) {
                $schedule .= ' on ';
                $days = unencodeMultienum($value->daysofweek);
                foreach($days as $d) {
                    $app_list_strings['activity_schedule_daysofweek_list'][$d].' ';
                }
            }
        } else if( $value->type == 'md') {
            $schedule .= $value->repeat_interval;
            $schedule .= ' months';
            if( !empty($value->numwithinmonth)) {
                $schedule .= ' on the '.$value->numwithinmonth.' days';
            }
        } else if( $value->type == 'mw') {
            $schedule .= $value->repeat_interval;
            $schedule .= ' months';
            if( !empty($value->numwithinmonth)) {
                $schedule .= ' on the '.$app_list_strings['activity_schedule_interval_weeks_list'][$value->numwithinmonth].' ';
                $days = unencodeMultienum($value->daysofweek);
                foreach($days as $d) {
                    $app_list_strings['activity_schedule_daysofweek_list'][$d].' ';
                }
            }
        } else if( $value->type == 'y') {
            $schedule .= $value->repeat_interval;
            $schedule .= ' years';
        }
        return $schedule;
	}

}

?>
