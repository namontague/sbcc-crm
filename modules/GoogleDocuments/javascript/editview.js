
var dhxWins;

$(function(){
    dhxWins= new dhtmlXWindows();
    dhxWins.setSkin(dhtmlxSkin);
    dhxWins.setImagePath(dhtmlxImagePaths['windows']);

    $('#document_title').attr('readonly','true');
    $('#document_type').attr('readonly','true');
});
    

function onExecute() {
    var win, mygrid;
    win = dhxWins.createWindow('GoogleDocWin', 200, 200, 500, 500);
    win.centerOnScreen();

    win.setText(" Google Documents ");
    var pos = win.getPosition();
    var scroll = 0;
    if(parent.document.body.scrollTop ) {
        scroll = parent.document.body.scrollTop;
    } else if (parent.window.pageYOffset) {
        scroll = parent.window.pageYOffset;
    }

    win.setPosition(pos[0],pos[1]+scroll);


    var tabbar = win.attachTabbar("top");
    tabbar.setSkin(dhtmlxSkin);
    tabbar.setImagePath(dhtmlxImagePaths['tabbar']);
    tabbar.addTab("select", "Select");
    tabbar.addTab("create", "Create");
    tabbar.setTabActive('select');


    var searchFormData = [
        {type:"input", name:"search_criteria"},
        {type:"button", name:"search", value:"Search", command:"search"},
        {type:"label", name:"test",label:"<div id='document_grid' style='width:95%'></div>" },
        {type:"button", name:"select", value:"Add Document", command:"select"}
    ];
    var createFormData = [
        {type:"input", name:"document_title", label:"Enter Document Title"},
        {type:"select", name:"document_kind", label:"Select type of document",
        options: [
            {value:'document', text:'Document',selected:true},
            {value:'drawing', text:'Drawing'},
            //{value:'folder', text:'Folder'},
            {value:'pdf', text:'PDF'},
            {value:'presentation', text:'Presentation'},
            {value:'spreadsheet', text:'Spreadsheet'},
            {value:'form', text:'Form'},
        ]},
        {type:"button", name:"create", value:"Create a Google Doc", command:"createNew"}
    ];



    var searchForm= tabbar.cells('select').attachForm(searchFormData);
    searchForm.attachEvent("onButtonClick", function(name, command) {
        if(command=='search') {
            var search_title = searchForm.getItemValue('search_criteria');
            mygrid.clearAndLoad("index.php?module=GoogleDocuments&action=GoogleDocsXml&search_title="+search_title);
        }  else if (command=='select') {
            var selectedRow = mygrid.getSelectedRowId();
            if( selectedRow ) {
                
                $("input[name=document_id]").val(selectedRow);
                $("input[name=document_title]").val(mygrid.cellById(selectedRow,0).getValue());
                $("input[name=googledoc_name]").val(mygrid.cellById(selectedRow,0).getValue());                
                $("input[name=document_type]").val(mygrid.cellById(selectedRow,2).getValue());
                $("input[name=document_url]").val(mygrid.getUserData(selectedRow,'document_url'));
                $("input[name=content_type]").val(mygrid.getUserData(selectedRow,'content_type'));
                $("input[name=content_url]").val(mygrid.getUserData(selectedRow,'content_url'));
                $("input[name=googledoc_name]").change();
                win.close();
            } else {
                alert('You must select a document from the list to add it');
            }
        }
    });
    var createForm= tabbar.cells('create').attachForm(createFormData);
    createForm.attachEvent("onButtonClick", function(name, command) {
        if(command=='createNew') {
            var title = createForm.getItemValue('document_title');
            var kind = createForm.getItemValue('document_kind');
            var http = new XMLHttpRequest();
            var url = "index.php?module=GoogleDocuments&action=CreateGoogleDocLink&title="+title+"&kind="+kind;
            http.open("GET", url, false);
            http.send(null);
            if (http.responseText) {

                $("input[name=document_id]").val($(http.responseXML).find("id").text());
                $("input[name=document_title]").val($(http.responseXML).find("title").text());
                $("input[name=googledoc_name]").val($(http.responseXML).find("title").text());
                $("input[name=document_type]").val($(http.responseXML).find("docType").text());
                $("input[name=document_url]").val($(http.responseXML).find("document_url").text());
                $("input[name=content_type]").val($(http.responseXML).find("content_type").text());
                $("input[name=content_url]").val($(http.responseXML).find("content_url").text());
                $("input[name=googledoc_name]").change()
                win.close();
            }
        }
    });
    //myform.setSkin(dhtmlxSkin);
    //myform.setImagePath(dhtmlxImagePaths['form']);

    //mygrid = myform.attachGrid();
    mygrid = new dhtmlXGridObject('document_grid');
    mygrid.setSkin(dhtmlxSkin);
    mygrid.setImagePath(dhtmlxImagePaths['grid']);
    mygrid.setHeader("Title,Author, Type");
    mygrid.setInitWidthsP("*,40,20");
    mygrid.setColTypes("ro,ro,ro");

    mygrid.setColAlign("left,left,center");
    mygrid.enableAutoHeight(true);
    //mygrid.enableAutoWidth(true);


    mygrid.init();

    mygrid.load("index.php?module=GoogleDocuments&action=GoogleDocsXml");

    win.attachEvent('onResizeFinish', function() {
        mygrid.setSizes();
    })
}

function onUpload(element) {

    var form = $(element).parents('form:first');
    var data = new FormData(document.getElementById(form.attr('id')));
    data.append("module", "GoogleDocuments");
    data.append("action", "UploadGoogleDocLink");
    
    var http = new XMLHttpRequest();
    var url = "index.php?module=GoogleDocuments&action=UploadGoogleDocLink";
    http.open("POST", url, false);
    http.send(data);
    if (http.responseText) {

        $("input[name=document_id]").val($(http.responseXML).find("id").text());
        $("input[name=document_title]").val($(http.responseXML).find("title").text());
        $("input[name=googledoc_name]").val($(http.responseXML).find("title").text());
        $("input[name=document_type]").val($(http.responseXML).find("docType").text());
        $("input[name=document_url]").val($(http.responseXML).find("document_url").text());
        $("input[name=content_type]").val($(http.responseXML).find("content_type").text());
        $("input[name=content_url]").val($(http.responseXML).find("content_url").text());
        $("input[name=googledoc_name]").change();
    }    
}
