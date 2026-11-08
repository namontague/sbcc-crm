<script type='text/javascript'>
    var connector_base_url = "http://{{$config.properties.openbravo_base_url}}{{$openbravo_connector_urls}}";
    var openbravo_id = "{$fields.{{$mapping_name}}.value}";
    var connector_field = "{{$connector_field}}"
</script>
<script type='text/javascript' src='modules/Connectors/connectors/formatters/ext/rest/openbravo/javascript/openbravo.js'></script>
<div style="visibility:hidden;" id="openbravo_popup_div"></div>
<script type='text/javascript'>
    if("{$fields.{{$mapping_name}}.value}" == ""){ldelim}
        $('img#dswidget_img').hide();
    {rdelim}
    $.ajax({ldelim}
        url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=user_auth&parent_module={{$module}}',
        dataType: "json",
        success:  function(result) {ldelim}
                    var user_auth = result;

                    if(user_auth['success'] && "{$fields.{{$mapping_name}}.value}" == ""){ldelim}
                      var method = 'retrieve_business_partner_'+"{{$module}}".toLowerCase();
                      window[method]("{$fields.id.value}", "{{$module}}");
                    {rdelim} else if(user_auth['success'] && "{$fields.{{$mapping_name}}.value}" != ""){ldelim}
                       $('img#dswidget_img').bind('click', function(){ldelim}
                           var url = connector_base_url + "{$fields.{{$mapping_name}}.value}";
                           window.open(url);
                       {rdelim} );
                    {rdelim} else if(!user_auth['success'] && "{$fields.{{$mapping_name}}.value}" != ""){ldelim}
                       $('img#dswidget_img').bind('click', function() {ldelim}
                            alert('Missing/incorrect user auth settings');
                       {rdelim} );
                    {rdelim} else{ldelim}
                       $('img#dswidget_img').hide();
                    {rdelim} 
                {rdelim} 
     {rdelim} );
    
    
    
    
</script>



