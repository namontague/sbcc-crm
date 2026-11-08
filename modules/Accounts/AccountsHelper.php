<?php

if (!defined('sugarEntry') || !sugarEntry)
	die('Not A Valid Entry Point');

function getSearchStarRating($focus, $field, $value, $view)
{
	
	$field_type = '_basic';
	if (strpos($view , "_advanced") != false){
		$field_type = "_advanced";
	}

	$html = <<<EOHTML

	<script type="text/javascript" src="modules/Accounts/Account.js"></script>
	<script type="text/javascript" src="include/eintel/jquery/plugins/StarRating/jquery.ui.stars.min.js"></script>
    <link rel="stylesheet" type="text/css" href="include/eintel/jquery/plugins/StarRating/jquery.ui.stars.min.css"/>
	<script type="text/javascript">		
		var sel_inp = $("select[name={$field}{$field_type}]");
		$(sel_inp).before("<input type='hidden' value='{$_REQUEST[$field.$field_type]}' name=\'hiddenrating\' id=\'hiddenrating\' />");
		$(sel_inp).attr("title", "stars");
		$(sel_inp).attr("id", "select-star-rating");		
		$(sel_inp).wrap("<div style=\'display: block;\' class=\'multiField\' id=\'star-rating\' />");

		YAHOO.util.Get.css("include/eintel/jquery/plugins/StarRating/jquery.ui.stars.min.css");
		
		enableStarRating("search_form");
	</script>';

	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	
EOHTML;

	return $html;
}

?>
