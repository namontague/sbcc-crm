
var dhxWins;


    $(function(){
        dhxWins= new dhtmlXWindows();
        dhxWins.setSkin(dhtmlxSkin);
        dhxWins.setImagePath(dhtmlxImagePaths['windows']);

//        dhxWins.attachEvent("onContentLoaded", function(win){
//            win.setText('Enter Parameters');
//        });

    });


    function showReportsPopup(bean, record_id) {
        var reportWindow = dhxWins.createWindow("reportWindow", 200, 200, 525, 400);
        reportWindow.setText("Loading");
        dhxWins.attachEvent("onContentLoaded", function(reportWindow){
            reportWindow.setText('Request Report');
        });
        reportWindow.centerOnScreen();
        var pos = reportWindow.getPosition();
        var scroll = 0;
        if(parent.document.body.scrollTop ) {
            scroll = parent.document.body.scrollTop;
        } else if (parent.window.pageYOffset) {
            scroll = parent.window.pageYOffset;
        }
        reportWindow.setPosition(pos[0],pos[1]+scroll);

        reportWindow.attachURL('?module=ReportViewer&action=PopupList&bean='+bean+'&record_id='+record_id);
        var loadingImage = document.getElementById('loading_image');
        if( loadingImage ) {
            loadingImage.style.display="none";
        }

    }
