<?php
require_once("assets/includes/site.php");

site_header();
check_login();




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
		print $form_values['description'];
		print "</td><td>";
		print "<input type='text' value='{$form_values[value]}' name='$form_values[name]' />";
		print "</td></tr>";
	}
	?>
	</table>
	<input type="submit" value="Submit Changes" disabled="disabled" />
</fieldset>
</form>


<?php

site_footer();
