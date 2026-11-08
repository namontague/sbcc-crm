{{if $displayParams.firstCall }}
<script type="text/javascript">
var scheduleUnits = {ldelim}{foreach from=$APPLIST.activity_schedule_units_list key=k  item=v name=scheduleUnits}'{$k}':'{$v}'{if !$smarty.foreach.scheduleUnits.last},{/if} {/foreach} {rdelim};
</script>
<script type="text/javascript" src='{sugar_getjspath file="include/SugarFields/Fields/ActivitySchedule/SugarFieldActivitySchedule.js"}'></script>
{{/if}}
{{assign var="key" value=$displayParams.key|upper}}
{{assign var="type" value=$displayParams.key|cat:'_type'}}
{{assign var="interval" value=$displayParams.key|cat:'_repeat_interval'}}
{{assign var="enddate" value=$displayParams.key|cat:'_enddate'}}
{{assign var="numwithinmonth" value=$displayParams.key|cat:'_numwithinmonth'}}
{{assign var="daysofweek" value=$displayParams.key|cat:'_daysofweek'}}
{{assign var="updatetemplate" value=$displayParams.key|cat:'_updatetemplate'}}
<div class="activitySchedule" id="{{$displayParams.key}}">
<div class='activity_schedule_type'>
<select name='{{$type}}' tabindex="{{$tabindex}}" id='{{$type}}' title='{{$vardef.help}}'>
{{assign var="typeOptionsMember" value=`$parentFieldArray+'.'+$type+'.name'}}
{html_options options={{sugarvar memberName='type' key='options' string=true}} selected=$fields.{{$type}}.value}
</select>
<label class='activity_schedule_input activity_schedule_updatetemplate'>
{if strval($fields.{{$updatetemplate}}.value) == "1" }
{assign var="checked" value="CHECKED"}
{else}
{assign var="checked" value=""}
{/if}
<input type="hidden" id="{{$updatetemplate}}" name="{{$updatetemplate}}" value="0">
<input type="checkbox" id="{{$updatetemplate}}" name="{{$updatetemplate}}" {$checked} value="1">
{sugar_translate label='LBL_{{$key}}_UPDATETEMPLATE' module='{{$module}}'}
</label>
</div>

<div class='activity_schedule_numwithinmonth activity_schedule_input'>
<span class="md">On day</span>
<span class="mw">The
<select name='activity_schedule_interval_week' tabindex="{{$tabindex}}" id='activity_schedule_interval_week' class="mw">
{{assign var="typeOptionsMember" value=`$parentFieldArray+'.'+$type+'.name'}}
{html_options options=$APPLIST.activity_schedule_interval_weeks_list selected=$fields.{{$interval}}.value}
</select>
</span> <input type="text" name="{{$numwithinmonth}}" id="{{$numwithinmonth}}" size="10" value='{$fields.{{$numwithinmonth}}.value}' tabindex="{{$tabindex}}" class="md"><span class="md"> of</span>
</div>


<div class='activity_schedule_interval activity_schedule_input'>
Every <input type="text" name="{{$interval}}" id="{{$interval}}" size="10" value='{$fields.{{$interval}}.value}' tabindex="{{$tabindex}}">
 <span class="scheduleUnit"></span>
</div>


<div class='activity_schedule_daysofweek activity_schedule_input'>
{multienum_to_array string=$fields.{{$daysofweek}}.value assign="daysofweek_array"}
<span class="w">On </span>{html_checkboxes name="{{$daysofweek}}" selected=$daysofweek_array options=$APPLIST.activity_schedule_daysofweek_list separator="&nbsp;"}
</div>

<div class='activity_schedule_enddate activity_schedule_input'>
Ending by 
{assign var=date_value value={{sugarvar key='value' string=true}} }
<input autocomplete="off" type="text" name="{{$enddate}}" id="{{$enddate}}" value="{$fields.{{$enddate}}.value}" title='{{$vardef.help}}' {{$displayParams.field}} tabindex='{{$tabindex}}' size="11" maxlength="10">
{{if !$displayParams.hideCalendar}}
<img border="0" src="{sugar_getimagepath file='jscalendar.gif'}" alt="{$APP.LBL_ENTER_DATE}" id="{{$enddate}}_trigger" align="absmiddle" />
{{/if}}
{{if $displayParams.showFormats}}
&nbsp;(<span class="dateFormat">{$USER_DATEFORMAT}</span>)
{{/if}}
{{if !$displayParams.hideCalendar}}
<script type="text/javascript">
Calendar.setup ({ldelim}
inputField : "{{$enddate}}",
daFormat : "{$CALENDAR_FORMAT}",
button : "{{$enddate}}_trigger",
singleClick : true,
dateStr : "{$fields.{{$enddate}}.value}",
step : 1,
weekNumbers:false
{rdelim}
);
</script>
{{/if}}
</div>

<script type="text/javascript">
$(".activity_schedule_type select:input").change();
</script>

</div>
