
// Function used to display report selection dialog.

    function onReportSelectGetListofReports() {

        var dhxWins= new dhtmlXWindows();
        dhxWins.setSkin(dhtmlxSkin);
        dhxWins.setImagePath(dhtmlxImagePaths['windows']);

        var w1 = dhxWins.createWindow("w1", 200, 200, 300, 350);
        w1.setText("Report Server");
        w1.centerOnScreen();

        tree = w1.attachTree();
        tree.setSkin(dhtmlxSkin);
        tree.setImagePath(dhtmlxImagePaths["tree"]);
        tree.setDataMode("json");
        tree.setXMLAutoLoading("?module=ReportViewer&action=ReportList");
        tree.loadJSON("?module=ReportViewer&action=ReportList");
        tree.attachEvent("onDblClick",onDoubleClickCloseTree);

        function onDoubleClickCloseTree(id) {

            if(tree.getUserData(id,"type") == "reportUnit") {
                var tfield = document.getElementById('report_location');
                tfield.value = id;
                dhxWins.window("w1").close();
            }


        }
    }



