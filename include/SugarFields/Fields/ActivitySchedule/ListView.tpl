{assign var="key" value=$displayParams.key|upper}
{assign var="type" value=$displayParams.key|cat:'_type'|upper}
{assign var="interval" value=$displayParams.key|cat:'_repeat_interval'|upper}
{assign var="enddate" value=$displayParams.key|cat:'_enddate'|upper}
{assign var="numwithinmonth" value=$displayParams.key|cat:'_numwithinmonth'|upper}
{assign var="daysofweek" value=$displayParams.key|cat:'_daysofweek'|upper}



{if $parentFieldArray.$type == '' }
Never
{else}
{if $parentFieldArray.$type == 'd' }
<span>Every {$fields.$interval} days</span>

{elseif $parentFieldArray.$type == 'w' }
<span>Every {$parentFieldArray.$interval} weeks</span>
{if !empty($parentFieldArray.$daysofweek)}
{multienum_to_array string=$fields.$daysofweek assign="daysofweek_array"}
<span>on {foreach from=$daysofweek_array item=day}{$sep}{$APPLIST.activity_schedule_daysofweek_list.$day}{assign var="sep" value=", "}{/foreach} </span>
{/if}
{elseif $parentFieldArray.$type == 'md' }
<span>Every {$parentFieldArray.$interval} months</span>
{if !empty($parentFieldArray.$numwithinmonth)}
{multienum_to_array string=$parentFieldArray.$numwithinmonth assign="numwithinmonth_array"}
<span>on the {foreach from=$numwithinmonth_array item=num}{$sep}{$num}{assign var="sep" value=", "}{/foreach} day of the month</span>
{/if}
{elseif $parentFieldArray.$type == 'mw' }
<span>Every {$parentFieldArray.$interval} months</span>
{multienum_to_array string=$parentFieldArray.$daysofweek assign="daysofweek_array"}
{assign var="numwithinmonth_value" value=$fields.$numwithinmonth}
<span>
on the {$APPLIST.activity_schedule_interval_weeks_list.$numwithinmonth_value} {foreach from=$daysofweek_array item=day}{$daysep}{$APPLIST.activity_schedule_daysofweek_list.$day}{assign var="daysep" value=", "}{/foreach} of the month
</span>
{elseif $parentFieldArray.$type == 'y' }
<span>Every {$parentFieldArray.$interval} years</span>
{else}
{$parentFieldArray.$type} 
{/if}
{if $parentFieldArray.$enddate != '' }
<span>until {$parentFieldArray.$enddate}</span>
{/if}
{/if}

