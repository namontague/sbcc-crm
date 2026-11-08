{{assign var="key" value=$displayParams.key|upper}}
{{assign var="type" value=$displayParams.key|cat:'_type'}}
{{assign var="interval" value=$displayParams.key|cat:'_repeat_interval'}}
{{assign var="enddate" value=$displayParams.key|cat:'_enddate'}}
{{assign var="numwithinmonth" value=$displayParams.key|cat:'_numwithinmonth'}}
{{assign var="daysofweek" value=$displayParams.key|cat:'_daysofweek'}}
{if $fields.{{$type}}.value == '' }
Never
{else}
{if $fields.{{$type}}.value == 'd' }
<span>Every {$fields.{{$interval}}.value} days</span>

{elseif $fields.{{$type}}.value == 'w' }
<span>Every {$fields.{{$interval}}.value} weeks</span>
{if !empty($fields.{{$daysofweek}}.value)}
{multienum_to_array string=$fields.{{$daysofweek}}.value assign="daysofweek_array"}
<span>on {foreach from=$daysofweek_array item=day}{$sep}{$APPLIST.activity_schedule_daysofweek_list.$day}{assign var="sep" value=", "}{/foreach} </span>
{/if}
{elseif $fields.{{$type}}.value == 'md' }
<span>Every {$fields.{{$interval}}.value} months</span>
{if !empty($fields.{{$numwithinmonth}}.value)}
{multienum_to_array string=$fields.{{$numwithinmonth}}.value assign="numwithinmonth_array"}
<span>on the {foreach from=$numwithinmonth_array item=num}{$sep}{$num}{assign var="sep" value=", "}{/foreach} day of the month</span>
{/if}
{elseif $fields.{{$type}}.value == 'mw' }
<span>Every {$fields.{{$interval}}.value} months</span>
{multienum_to_array string=$fields.{{$daysofweek}}.value assign="daysofweek_array"}
{assign var="numwithinmonth_value" value=$fields.{{$numwithinmonth}}.value}
<span>
on the {$APPLIST.activity_schedule_interval_weeks_list.$numwithinmonth_value} {foreach from=$daysofweek_array item=day}{$daysep}{$APPLIST.activity_schedule_daysofweek_list.$day}{assign var="daysep" value=", "}{/foreach} of the month
</span>
{elseif $fields.{{$type}}.value == 'y' }
<span>Every {$fields.{{$interval}}.value} years</span>
{else}
{$fields.{{$type}}.value}
{/if}
{if $fields.{{$enddate}}.value != '' }
<span>until {$fields.{{$enddate}}.value}</span>
{/if}
{/if}
