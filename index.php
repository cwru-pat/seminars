<?php
require_once("assets/includes/site.php");

site_header();
?>

<h1>CERCA Seminar Schedule</h1>

<?php
$result = $mysqli->dbQuery("SELECT seminars.id sid, talks.id id, date, title, name FROM seminars LEFT JOIN talks ON seminars.id=talks.seminar LEFT JOIN presenters ON presenters.id=talks.presenter ORDER BY date, name ASC");
print "<table class='schedule'>";
print "<tr><th>Seminar Date</th><th>Speakers</th></tr>";

$prevdate = "";
$striping = 1;
foreach($result as $seminar) {

    $seminar = get_object_vars($seminar);

    $sid = $seminar['sid'];
    $id = $seminar['id'];

    if($seminar['date'] != $prevdate) {
        $time = strtotime($seminar['date']);
        $prettydate = date("M jS, Y", $time);
        if(date("w", $time)!=5 || date("G", $time)!=12 || date("i", $time)!=30) { $prettydate .= "<br />".date("(D @ G:i)", $time); } // unusual date?
        if($prevdate != "") { print "</td></tr>"; }

        $striping += 1;
        if($striping % 2) {
            print "<tr class='striped'><td>{$prettydate}<br />";
        } else {
            print "<tr><td>{$prettydate}<br />";
        }
        print "</td><td>";
    }
    $prevdate = $seminar[date];

    if($seminar['name']) {
        print "<span class='person'>" . $seminar['name'] . "</span>";
        if($seminar['title']) {
            print " - " . o($seminar['title']);
        }
        print "<br />";
    }
}
print "</td></tr></table>";



site_footer();
?>
