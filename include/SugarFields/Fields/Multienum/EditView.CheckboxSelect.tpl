{*
*}
<input type="hidden" id="{{sugarvar key='name'}}_multiselect" name="{{sugarvar key='name'}}_multiselect" value="true">
{multienum_to_array string={{sugarvar key='value' string=true}} default={{sugarvar key='default' string=true}} assign="values"}
{html_checkboxes name={{sugarvar key='name' string=true}} options={{sugarvar key='options' string=true}} selected=$values separator='<br/>'}

