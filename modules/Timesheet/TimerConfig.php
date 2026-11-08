<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
global $current_user, $mod_strings, $app_strings, $locale;
require_once 'modules/Timesheet/TimesheetTimer.php';

if (isset($_REQUEST['do']) && $_REQUEST['do'] == 'save') {
  if (isset($_REQUEST['timerOn'])) {
    $current_user->setPreference('timerReminder', 'on');
  }
  else {
    $current_user->setPreference('timerReminder', 'off');
  }

  if (isset($_REQUEST['number'])) {
    $number = unformat_number($_REQUEST['number']);
    $literal = '';
    if (isset($_REQUEST['literal'])) {
      $literal = $_REQUEST['literal'];
    }
    $current_user->setPreference('timerReminder_interval', $number.$literal);
  }
}

$interval = $current_user->getPreference('timerReminder_interval');

$number = '30';
$literal = 'min';

if (!is_null($interval)) {
  $interval = TimesheetTimer::resolveInterval($interval);
  $number = format_number($interval['number'],  $locale->getPrecedentPreference('default_currency_significant_digits'), $locale->getPrecedentPreference('default_currency_significant_digits'));
  $literal = $interval['literal'];
}
$timer = $current_user->getPreference('timerReminder');
if (is_null($timer))
  $timer = 'on';
?>
<form method="POST" action="index.php" id="TimerConfig">
<input type="hidden" name="module" value="Timesheet">
<input type="hidden" name="action" value="TimerConfig">
<!--input type="hidden" name="sugar_body_only" value="true"-->
<input type="hidden" name="do" value="save">
Switch reminder on: <input type="checkbox" name="timerOn" value="1" <?php echo $timer == 'on' ? 'checked' : ''?>><br />
Show reminder every: <input type="text" size="3" name="number" value="<?php echo $number?>"> 
<select name="literal">
  <option value="sec" <?php echo $literal == 'sec' ? 'selected' : ''?>>sec</option>
  <option value="min" <?php echo $literal == 'min' ? 'selected' : ''?>>min</option>
  <option value="hr" <?php echo $literal == 'hr' ? 'selected' : ''?>>hour</option>
</select>
<br />
<input type="submit" class="button" value=" Save ">
</form>
<?php
/*
?>
<script language="javascript">
<!--
// ajaxStatus object to display status text
var ajaxStatus = new SUGAR.ajaxStatusClass;

function ajaxResult(data) {
  if (data.status > 0) {
    ajaxStatus.flashStatus('<?php echo $app_strings['LBL_SAVED']?>', 1000);
  }
}

function sendForm() {
  ajaxStatus.showStatus('<?php echo $app_strings['LBL_SAVING']?>');
  YAHOO.util.Connect.setForm(document.getElementById('TimerConfig'));
  YAHOO.util.Connect.asyncRequest('POST', 'index.php', {success: ajaxResult, failure: ajaxResult});
}
//-->
</script>
<?php 
*/ 
?>