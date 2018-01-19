<?php
require_once("assets/includes/site.php");

site_header();
check_login();

if(isset($_REQUEST['submit']) && isset($_REQUEST['CSRFToken'])
	&& $token->validateToken($_REQUEST['CSRFToken'])) {
		// $safe_title = $mysqli->mysqlEscape($_REQUEST['title']);
		foreach($_REQUEST as $name => $value) {
			$safe_name = $mysqli->mysqlEscape($name);
			$safe_value = $mysqli->mysqlEscape($value);
			$mysqli->dbCommand("UPDATE settings SET value='{$safe_value}' WHERE name='{$safe_name}'");
		}
}
?>

<form action="" method="post">
<fieldset>
	<legend>Make Adjustments</legend>
	<table>
	<?php
	$result = $mysqli->dbQuery("SELECT * FROM settings");
	foreach($result as $row) {
		$form_values = get_object_vars($row);
		print "<tr><td>";
		print o($form_values['description']);
		print "</td><td>";
		print "<input type='text' value='" . o($form_values[value]) . "' name='" . o($form_values[name]) . "' />";
		print "</td></tr>";
	}
	?>
	</table>
	<input type='hidden' name='CSRFToken' value='<?php print o($token->getToken()) ?>'>
	<input type="submit" name="submit" value="Submit Changes"/>
</fieldset>
</form>


<?php

site_footer();
