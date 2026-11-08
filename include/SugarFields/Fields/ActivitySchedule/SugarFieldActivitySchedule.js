
$(function() {
    $(".activity_schedule_type select:input").change(activityScheduleOnTypeChange);
    $(".activity_schedule_type select:input").change();
    $(".activity_schedule_numwithinmonth select.mw").change(activityScheduleOnWeekIntervalChange);
});

function activityScheduleOnTypeChange(){
    var scheduleType = $(this).val();
    var parent =$(this).closest(".activitySchedule");
    $(".activitySchedule .scheduleUnit").html(scheduleUnits[scheduleType]);
    if( scheduleType == '') {
        $(".activity_schedule_input", parent).hide();
    } else {
        $(".activity_schedule_updatetemplate", parent).show();
        $(".activity_schedule_interval", parent).show();
        $(".activity_schedule_enddate", parent).show();
        $(".activity_schedule_daysofweek", parent).hide();
        $(".activity_schedule_numwithinmonth", parent).hide();
        if( scheduleType == 'w' ) {
            $(".w", parent).show();
            $(".activity_schedule_daysofweek", parent).insertAfter($(".activity_schedule_interval", parent));
            $(".activity_schedule_daysofweek", parent).show();
        } else if( scheduleType=='md') {
           $(".md", parent).show();
           $(".mw", parent).hide();
            $(".activity_schedule_numwithinmonth", parent).show();
        } else if (scheduleType=='mw') {
            $(".mw", parent).show();
            $(".md", parent).hide();
            $(".w", parent).hide();
            $("select.mw", parent).val($("input.md", parent).val()).change();
            $(".activity_schedule_daysofweek", parent).insertAfter($(".activity_schedule_numwithinmonth", parent));
            $(".activity_schedule_daysofweek", parent).show();
            $(".activity_schedule_numwithinmonth", parent).show();
        }
    }
}


function activityScheduleOnWeekIntervalChange() {
    var interval = $(this).val();
    var parent =$(this).closest(".activity_schedule_numwithinmonth");
    $("input.md",parent).val(interval);
}