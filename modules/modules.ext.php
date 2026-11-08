<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/* * *******************************************************************************
 * (C) 2013 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */


$moduleList[] = 'Camps';
$beanList['Camps'] = 'Camp';
$beanFiles['Camp'] = 'modules/Camps/Camp.php';

$moduleList[] = 'CampParticipants';
$beanList['CampParticipants'] = 'CampParticipant';
$beanFiles['CampParticipant'] = 'modules/CampParticipants/CampParticipant.php';

$moduleList[] = 'CampGroups';
$beanList['CampGroups'] = 'CampGroup';
$beanFiles['CampGroup'] = 'modules/CampGroups/CampGroup.php';

$moduleList[] = 'CampGroupBookings';
$beanList['CampGroupBookings'] = 'CampGroupBooking';
$beanFiles['CampGroupBooking'] = 'modules/CampGroupBookings/CampGroupBooking.php';

$moduleList[] = 'AOS_PDF_Templates';
$beanList['AOS_PDF_Templates'] = 'AOS_PDF_Templates';
$beanFiles['AOS_PDF_Templates'] = 'modules/AOS_PDF_Templates/AOS_PDF_Templates.php';


$moduleList[] = 'AOR_Charts';
$beanList['AOR_Charts'] = 'AOR_Chart';
$beanFiles['AOR_Chart'] = 'modules/AOR_Charts/AOR_Chart.php';

$moduleList[] = 'AOR_Conditions';
$beanList['AOR_Conditions'] = 'AOR_Condition';
$beanFiles['AOR_Condition'] = 'modules/AOR_Conditions/AOR_Condition.php';

$moduleList[] = 'AOR_Fields';
$beanList['AOR_Fields'] = 'AOR_Field';
$beanFiles['AOR_Field'] = 'modules/AOR_Fields/AOR_Field.php';

$moduleList[] = 'AOR_Reports';
$beanList['AOR_Reports'] = 'AOR_Report';
$beanFiles['AOR_Report'] = 'modules/AOR_Reports/AOR_Report.php';

$moduleList[] = 'SMS_SMS';
$beanList['SMS_SMS'] = 'SMS_SMS';
$beanFiles['SMS_SMS'] = 'modules/SMS_SMS/SMS_SMS.php';

$moduleList[] = 'SMS_Replies';
$beanList['SMS_Replies'] = 'SMS_Replies';
$beanFiles['SMS_Replies'] = 'modules/SMS_Replies/SMS_Replies.php';


?>
