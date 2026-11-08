var dhxWins;

$( function() {
	dhxWins= new dhtmlXWindows();
	dhxWins.setSkin(dhtmlxSkin);
	dhxWins.setImagePath(dhtmlxImagePaths['windows']);
});


function zohoReportExecute(id, record_id){
	var reportParamsUrl = '?module=ZohoReports&action=ReportParams&id='+id+'&record_id='+record_id;
	$.get(reportParamsUrl, function(report_url) {
		displayWindow(report_url);
	});	
}

function displayWindow(report_url) {
	var reportWindow = dhxWins.createWindow("reportWindow", 200, 200, 800, 600);
	reportWindow.setText("Loading");
	dhxWins.attachEvent("onContentLoaded", function(reportWindow) {
		reportWindow.setText('Request ZohoReport');
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
	reportWindow.attachURL(report_url);
	
	var loadingImage = document.getElementById('loading_image');
	if( loadingImage ) {
		loadingImage.style.display="none";
	}
}


