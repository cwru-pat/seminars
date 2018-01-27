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
			if($safe_name != "lastupdate") {
				$mysqli->dbCommand("UPDATE settings SET value='{$safe_value}' WHERE name='{$safe_name}'");
			}
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
		print "<input type='text' value='" . o($form_values['value']) . "' name='" . o($form_values['name']) . ($form_values['name'] == "lastupdate" ? "' disabled'":"''") . "' />";
		print "</td></tr>";
	}
	?>
	</table>
	<input type='hidden' name='CSRFToken' value='<?php print o($token->getToken()) ?>'>
	<input type="submit" name="submit" value="Submit Changes"/>
</fieldset>
</form>

<h2>Error Logs</h2>

<?php
$logfiles = array(PHP_LOG_FILE,CRON_LOG_FILE,MAIL_LOG_FILE,UPDATE_LOG_FILE,LOG_LOG_FILE);
foreach($logfiles as $logfile){
	print "<p>"
	if (file_exists($LOG_FILE) && $log = trim(file_get_contents($LOG_FILE))) {
		print "<div class='form-group'>";
		print "<label for='errors'>$LOG_FILE Content</label>";
		print "<textarea class='form-control' rows='10' readonly id='errors'>";
		print o($log);
		print "</textarea>";
		print "</div>";
	} else {
		print "Currently there are no errors in $LOG_FILE.";
	}
	print "</p>"
}
?>
<?php
site_footer();
