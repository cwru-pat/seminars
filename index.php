<?php
require_once("assets/includes/site.php");

site_header();
?>
<h1>CERCA Seminar Schedule</h1>
<div>
<p style="color:#220000">
We meet most Fridays in the Miller Room from 12:45 to 1:45 for two 25-30 minute talks. <br />
Good pizza is generally served!  (Bad pizza is never served.) But if there is a particle astrophysics seminar that
Friday then we sometimes meet on Tuesday, but sometimes we don't.
</p>

<p style="color:#220000">
<strong>Tate Deskins</strong> is running the show.
To subscribe or unsubscribe to emails, please visit the <a href="https://groups.google.com/a/case.edu/forum/#!forum/cerca">CERCA Google Groups</a> page.
</p>

<?php

if(isset($_REQUEST['key'])) {

    if(isset($_REQUEST['key'])) {
        $safe_key = $mysqli->mysqlEscape($_REQUEST['key']);
        // check to make sure they have a valid key
        $result = $mysqli->dbQuery("SELECT title, name, date FROM (SELECT * FROM talks WHERE talks.edit_key='{$safe_key}') AS t LEFT JOIN seminars ON t.seminar=seminars.id LEFT JOIN presenters ON presenters.id=t.presenter");
        if(count($result) != 1) {
            warning("Invalid access key - you can't change anything."); page_bottom(); die();
        }
        // only let them change titles if now is before their talk
        $result = get_object_vars($result[0]);
        if(strtotime($result['date']) < time()) {
            warning("Invalid access key - you can't change anything."); page_bottom(); die();
        }
    }

    // submit key if new one submitted
    if(isset($_REQUEST['submit']) && isset($safe_key) && $safe_key) {
        $result = $mysqli->dbQuery("SELECT * FROM talks WHERE talks.edit_key='{$safe_key}'");
        if(count($result) != 1) { warning("Invalid access key - you can't change anything."); page_bottom(); die(); }
        $safe_title = $mysqli->mysqlEscape($_REQUEST['title']);
        $mysqli->dbCommand("UPDATE talks SET title='{$safe_title}' WHERE talks.edit_key='{$safe_key}'");
        warning("Title Updated", "success");
    }

    // pull stored title back out in case changed
    $result = $mysqli->dbQuery("SELECT title, name, date FROM (SELECT * FROM talks WHERE talks.edit_key='{$safe_key}') AS t LEFT JOIN seminars ON t.seminar=seminars.id LEFT JOIN presenters ON presenters.id=t.presenter");
    $result = get_object_vars($result[0]);

    ?>
    <form action="" method="post">
    <fieldset>
        <legend>Title Submission  for <?php print $result['name']; ?> | Talk on <?php print $result['date']; ?>  </legend>
        Here you can edit the title of your talk that will be sent out via email.  You can change this up until the time of your talk.<br />
        <input type="text" name="title" maxlength="1000" value="<?php print o($result['title']); ?>" /><br />
        <input type="submit" name="submit" value="Submit Title" />
    </fieldset>
    </form>
    <a href="index.php">View Talk Schedule</a>
    <?php

} else {

    $result = $mysqli->dbQuery("SELECT value FROM settings WHERE name='seminartime' LIMIT 1");
    $seminar_time = explode('.',$result[0]->value);

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
            if(date("w", $time)!=$seminar_time[0] || date("G", $time)!=$seminar_time[1] || date("i", $time)!=$seminar_time[2]) { $prettydate .= "<br />".date("(D @ G:i)", $time); } // unusual date?
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

}

?>
</div>
<?php
site_footer();
?>
