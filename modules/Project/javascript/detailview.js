
var dhxWins;

    $(function(){
        dhxWins= new dhtmlXWindows();
        dhxWins.setSkin(dhtmlxSkin);
        dhxWins.setImagePath(dhtmlxImagePaths['windows']);
    });


    function runGanttChart(id) {
        //Get the browser page size
        //then set the window to a % of that size
        var viewportWidth = $(window).width();
    	var viewportHeight = $(window).height();
        //being the width and height in to allow for a border
        viewportWidth = viewportWidth - 50;
        viewportHeight = viewportHeight - 100;

        var ganttWindow = dhxWins.createWindow("ganttWindow", 200, 200, viewportWidth, viewportHeight);
        ganttWindow.setText("Loading Chart");
        dhxWins.attachEvent("onContentLoaded", function(ganttWindow){
            ganttWindow.setText('Gantt Chart');
        });
        ganttWindow.centerOnScreen();
        ganttWindow.attachURL('?module=Project&action=GanttChart&id='+id+'');      
    }

    function closeGantt() {


        dhxWins.window("ganttWindow").close();
        
    }

              
