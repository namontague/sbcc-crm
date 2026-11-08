{*
*}
{multienum_to_array string={{sugarvar key='value' string=true}} default={{sugarvar key='default' string=true}} assign="values"}
{html_checkboxes name={{sugarvar key='name' string=true}} options={{sugarvar key='options' string=true}} selected=$values separator='<br/>' disabled='true'}
{{if !empty($displayParams.enableConnectors)}}
{{sugarvar_connector view='DetailView'}}
{{/if}}

