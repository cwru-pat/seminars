<?php
require_once("assets/includes/site.php");

site_header();
check_login();

$fields['talks'] = Array("title", "presenter", "seminar", "edit_key");
$fields['seminars'] = Array("date");
$fields['presenters'] = Array("name", "email", "active");

if(isset($_REQUEST['item'])) { $item = $_REQUEST['item']; } else { $item = ""; }
if(isset($_REQUEST['action'])) { $action = $_REQUEST['action']; } else { $action = ""; }
if(isset($_REQUEST['id'])) { $id = $_REQUEST['id']; } else { $id = ""; }

$safe_item = $mysqli->mysqlEscape($item);
$safe_action = $mysqli->mysqlEscape($action);
$safe_id = $mysqli->mysqlEscape($id);

if($item && !isset($fields[$item])) {
	print "You can't edit this, lol."; site_footer(); die();
}


if($item && $action) {

	$sql_statement = "SET ";
	foreach($fields[$item] as $field) {
		if("edit_key" == $field) {
			if($action == "add") {
				$val = sha1(microtime()*pi());
				$sql_statement .= $field . " = '" . $val . "',";
			}
		} else {
			$val = $mysqli->mysqlEscape( substr(trim($_POST[$field]),0,1000) );
			$sql_statement .= $field . " = '" . $val . "',";
		}
	}
	$sql_statement = substr($sql_statement,0,-1);


	if("edit" == $action) {
		$command = "UPDATE {$safe_item} " . $sql_statement . " WHERE id='{$safe_id}'";
	}
	if("add" == $action) {
		$command = "INSERT INTO {$safe_item} " . $sql_statement;
	}
	if("delete" == $action) {
		$command = "DELETE FROM {$safe_item} WHERE id='{$safe_id}'";
		$mysqli->dbCommand($command);
		header("Location: edit.php?item={$safe_item}"); die();
	}

	$mysqli->dbCommand($command);

}

if($item && $id) {
	$result = $mysqli->dbQuery("SELECT * FROM {$safe_item} WHERE id='{$safe_id}'");
	if(count($result) != 1) { print "Entry not found."; site_footer(); die(); }
	$form_values = get_object_vars($result[0]);
}

?>

<form action="" method="post">
<fieldset>
	<legend>Make Adjustments</legend>
	<?php
	switch ($item) {
		case 'seminars':
		case 'presenters':
			foreach($fields[$item] as $field) {
				if("date" == $field) {
					print "<input data-format='yyyy-MM-dd hh:mm:ss' name='{$field}' value='".o($form_values[$field])."' type='text'></input>";
				  	print "(Enter using a 'YYYY-MM-DD HH:MM:SS' format.)<br />";
				} else {
					print "Edit {$field}: <input type='text' name='{$field}' value='".o($form_values[$field])."' /><br />";
				}
			}
			break;

		case 'talks':

			// seminar field is hidden / set by context
			if($id) {
				$sid = $form_values['seminar'];
			} elseif(isset($_REQUEST['sid'])) {
				$sid = $mysqli->mysqlEscape($_REQUEST['sid']);
			} else {
				break;
			}
			$date = $mysqli->dbQuery("SELECT * FROM seminars WHERE id='{$sid}'");
			print "Adjusting talk on ".($date[0]->date)."<br />";
			print "<input type='hidden' name='seminar' value='{$sid}' />";

			// title is normal field
			print "Edit title: <input type='text' name='title' value='".o($form_values['title'])."' /><br />";

			// presenter needs to be selected from list
			print "Presenter: <select name='presenter'>";
			$people = $mysqli->dbQuery("SELECT * FROM presenters WHERE active>0 ORDER BY name");
			foreach($people as $person) {
				$person = get_object_vars($person);
				$selected = (isset($form_values) && $person['id'] == $form_values['presenter']) ? "selected='selected'" : "";
				print "<option value='$person[id]' {$selected}>";
				print o($person['name']);
				print "</option>";
			}
			print "</select><br /><br />";

			// no edit_key field - this is handled separately.

			break;

		default:
			print "You can edit the following:
					<ul>
						<li><a href='edit.php?item=talks'>Talks - Assign people to talk on seminar dates</a></li>
						<li><a href='edit.php?item=presenters'>People - Add/remove/change people who can present</a></li>
						<li><a href='edit.php?item=seminars'>Dates - Add/remove/change talk dates</a></li>
						<li><a href='settings.php'>General Settings</a></li>
					</ul>";
			site_footer();
			die();
			break;
	}

	if($item && $id) {
		print "<input type='hidden' name='action' value='edit' />";
		print "<input type='submit' value='Make Changes' />";
		print "<input type='button' value='Delete' onclick='javascript:window.location=\"edit.php?item={$safe_item}&id={$safe_id}&action=delete\"' />";
		print "<input type='button' value='Back/Cancel' onclick='javascript:window.location=\"edit.php?item={$safe_item}\"' />";
	} elseif(($item && $item != 'talks') || ('talks' == $item && $sid)) {
		print "<input type='hidden' name='action' value='add' />";
		print "<input type='submit' value='Add New' />";
		print "<input type='button' value='Back/Cancel' onclick='javascript:window.location=\"edit.php\"' />";
	} else {
		print "No changes to make.<br />";
		print "<input type='button' value='Back to Main' onclick='javascript:window.location=\"edit.php\"' />";
	}

	?>
</fieldset>
</form>

<?php
if('presenters' == $item || 'seminars' == $item) {
?>
	<table id="item-list">
		<?php
			if($item == 'presenters') {
				$result = $mysqli->dbQuery("SELECT * FROM {$safe_item} ORDER BY active DESC, name");
			} elseif($item == 'seminars') {
				$result = $mysqli->dbQuery("SELECT * FROM {$safe_item} ORDER BY date");
			}

			if(count($result) == 0) {
				print "No data here."; site_footer(); die();
			}

			print "<tr><th></th>";
			foreach($result[0] as $header => $val) {
				print "<th>{$header}</th>";
			}
			print "</tr>";

			foreach($result as $object) {
				print "<tr>";
				$form_values = get_object_vars($object);
				print "<td><a href='edit.php?item={$item}&id={$form_values[id]}' class='edit-link'>EDIT</a></td>";
				foreach($form_values as $key => $value) {
					print "<td>{$value}</td>";
				}
				print "</tr>";
			}
		?>
	</table>
<?php
} elseif('talks' == $item) {

$result = $mysqli->dbQuery("SELECT seminars.id sid, talks.id id, announced, keymailed, date, title, name FROM seminars LEFT JOIN talks ON seminars.id=talks.seminar LEFT JOIN presenters ON presenters.id=talks.presenter ORDER BY date, name ASC");

print "<table>";
$prevdate = "";
foreach($result as $seminar) {

	$seminar = get_object_vars($seminar);

	$sid = $seminar['sid'];
	$id = $seminar['id'];

	if($seminar['date'] != $prevdate) {
		if($prevdate != "") { print "</td></tr>"; }
		print "<tr><td>{$seminar['date']}<br />";
		print "<a href='edit.php?item=seminars&id={$sid}'>edit</a>";
		print " | <a href='edit.php?item=talks&sid={$sid}'>add speaker</a>";
        if($seminar['announced']) {
          print "<br /><em class='green'>Announcement Sent</em>";
        }
		print "</td><td>";
	}
	$prevdate = $seminar['date'];

	if($seminar['name']) {
		print $seminar['name'] . ($seminar['title'] ? " - " . o($seminar['title']) : "");
		print " | <a href='edit.php?item=talks&id={$id}'>change</a>";
        if($seminar['keymailed']) {
            print " | <em class='green'>Talk Notify Email Sent</em>";
        }
		print "<br />";
	}
}
print "</td></tr></table>";

?>



<?php
}

site_footer();
