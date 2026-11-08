
var from_popup_return  = false;
function project_set_return(popup_reply_data) {

   var estimated_start_date = popup_reply_data['name_to_value_array']['estimated_start_date'];
   var estimated_end_date = popup_reply_data['name_to_value_array']['estimated_end_date'];
   var delimiter;
   var now = new Date();

   if(this.cal_date_format == "%Y/%m/%d" || this.cal_date_format == "%m/%d/%Y" || this.cal_date_format == "%d/%m/%Y"){
        delimiter = "/"
   }else if(this.cal_date_format == "%Y-%m-%d" || this.cal_date_format == "%m-%d-%Y" || this.cal_date_format == "%d-%m-%Y"){
        delimiter = "-"
   }else if(this.cal_date_format == "%Y.%m.%d" || this.cal_date_format == "%m.%d.%Y" || this.cal_date_format == "%d.%m.%Y"){
        delimiter = "."
   }

   start_date = create_date_from_arry(estimated_start_date, delimiter, this.cal_date_format)
   end_date = create_date_from_arry(estimated_end_date, delimiter, this.cal_date_format)

   difference_ms = project_new_dates(now, start_date)
   start_date.setTime(start_date.getTime() + difference_ms)
   end_date.setTime(end_date.getTime() + difference_ms)

   popup_reply_data['name_to_value_array']['estimated_start_date'] = set_date(start_date, delimiter, this.cal_date_format)
   popup_reply_data['name_to_value_array']['estimated_end_date'] = set_date(end_date, delimiter, this.cal_date_format)
  
   set_return(popup_reply_data);
	
}

function create_date_from_arry(date, delimiter, format){
   var split_time = date.split(delimiter);

   if(format == "%Y"+delimiter+"%m"+delimiter+"%d"){
       $date = split_time['2']
       $month = split_time['1']
       $year = split_time['0']
   }
   if(format == "%m"+delimiter+"%d"+delimiter+"%Y"){
       $date = split_time['1']
       $month = split_time['0']
       $year = split_time['2']
   }
   if(format == "%d"+delimiter+"%m"+delimiter+"%Y"){
       $date = split_time['0']
       $month = split_time['1']
       $year = split_time['2']
   }
   var jsdate = new Date()
   jsdate.setDate($date)
   jsdate.setMonth($month - 1)
   jsdate.setFullYear($year)
    
   return jsdate    
}

function project_new_dates(now, date){
    var now_ms = now.getTime()
    var date_ms = date.getTime()

    return difference_ms = Math.abs(now_ms - date_ms)
}

function set_date(date, delimiter, format){
   if(format == "%Y"+delimiter+"%m"+delimiter+"%d"){
       return date.getFullYear()+delimiter+(date.getMonth()+1)+delimiter+date.getDate()
   }
   if(format == "%m"+delimiter+"%d"+delimiter+"%Y"){
        return (date.getMonth()+1)+delimiter+date.getDate()+delimiter+date.getFullYear()
   }
   if(format == "%d"+delimiter+"%m"+delimiter+"%Y"){
        return date.getDate()+delimiter+(date.getMonth()+1)+delimiter+date.getFullYear()
   }
}