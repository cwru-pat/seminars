<?php

include("../includes/site.php");
if(!$jobaccess) { die("POOPER SCOOPER"); }

$now = time();
$settings = $mysqli->dbQuery("SELECT * FROM settings");
$conf = Array();
foreach($settings as $setting) {
    $conf[$setting->name] = trim($setting->value);
}

// all notifications that go out will be for talks that happen after now.
$seminars = $mysqli->dbQuery("SELECT * FROM seminars WHERE date > FROM_UNIXTIME({$now}) ORDER BY date ASC");
if(count($seminars) == 0) { die("No upcoming talks."); }

foreach($seminars as $seminar) {
    $seminar = get_object_vars($seminar);

    // times after which notifications should be mailed
    $seminar_time = strtotime($seminar['date']);
    $title_mail_time = $seminar_time - $conf['titlemail']*3600;
    $title_reminder_time = $seminar_time - $conf['titleminder']*3600;
    $announce_time = $seminar_time - $conf['announcetime']*3600;


/**

HANDLE TITLE REQUESTS

**/


    $talks = $mysqli->dbQuery("SELECT title, keymailed, name, email, edit_key, t.id FROM (SELECT * FROM talks WHERE seminar='" . $seminar['id'] . "') as t LEFT JOIN presenters as p ON t.presenter=p.id");
    foreach($talks as $talk) {
        $talk = get_object_vars($talk);
        $email = "";

        // if (notifytime < now  && notified == 0)
        // send notify email
        if($now > $title_mail_time && $talk['keymailed'] == 0) {
            $email = "
Hey there!

You're scheduled to give a talk at the CERCA seminar on ".date("M jS, Y", $seminar_time).".  Before then, you'll need to submit a title that will go out in the CERCA reminder email.

You should submit your title here:
http://" . $conf['server'] . "/index.php?key=" . $talk['edit_key'] . "
You can change this title as often as you like up until the time of your talk, but the CERCA announcement will be sent out at ".date("g:i a \\o\\n M jS", $announce_time).", so try to finalize before then.

--This is an automated message; please reply to " . $conf['email'] . " if you need help or if you need to reschedule your talk.
";
        }

        // if (secondtime < now  && notified <= 1 && no title yet)
        // send followup email
        if($now > $title_reminder_time && $talk['keymailed'] == 1 && trim($talk['title']) == "") {
            $email = "
Hey there!

This is a reminder email - you're scheduled to give a talk at the CERCA seminar on ".date("M jS, Y", $seminar_time).".

Don't forget to submit your title here:
http://" . $conf['server'] . "/index.php?key=" . $talk['edit_key'] . "
The CERCA announcement will be sent out at ".date("g:i a \\o\\n M jS", $announce_time).", so try to finalize before then!

--This is an automated message; please reply to " . $conf['email'] . " if you need help or if you need to reschedule your talk.
";
        }

        if($email) {
            // send email

            $subject = "CERCA Seminar: Submit your talk title!";
            $org_email = $conf['email'];
            $to = $talk['email'];
            $headers = 'From: "CERCA Notices" <no-reply@anduril.phys.cwru.edu>' . "\r\n" .
                        'Reply-To: ' . $org_email . "\r\n" .
                        'Cc: ' . $org_email . "\r\n";

            $success = mail($to, $subject, $email, $headers);
            //$success = TRUE;
            if($success) {
                // print "Mail sent! ";
                $mysqli->dbCommand("UPDATE talks SET keymailed = keymailed+1 WHERE id='".$talk['id']."'");
            } else {
                print "Mail failed! ";
            }
        }
    }
/**

HANDLE SEMINAR ANNOUNCEMENTS

**/

    if($seminar_time-4800 < $now && $seminar['announced'] == 1) {
        $email = "Hello,\n\nJust a reminder that the next CERCA seminar is at ";
        $email .= date("g:i a", $seminar_time);
        $email .= " today (about an hour from now). We'll be hearing from the following:\n";

        $talks = $mysqli->dbQuery("SELECT * FROM (SELECT * FROM talks WHERE seminar='".$seminar['id']."') AS t LEFT JOIN presenters ON t.presenter=presenters.id");
        foreach($talks as $talk) {
            $email .= "* " . ($talk->name) . ($talk->title ? (" on '" . $talk->title . "'") : "") . "\n";
        }
        $email .= "\nYou can see the schedule of talks at http://" . $conf["server"] . "/index.php.";
        $email .= "\nHope to see you there!\n";


        // make sure there are actually talks this week.
        foreach($talks as $talk) {
            if($talk->name == "NO CERCA" || "Seminar") {
                $email = 0;
            }
        }


        $subject = "CERCA Seminar Soon";
        $org_email = $conf['email'];
        $to = "CERCA List <cerca@case.edu>";
        $headers = 'From: "CERCA Notices" <no-reply@anduril.phys.cwru.edu>' . "\r\n" .
                    'Reply-To: ' . $org_email . "\r\n" .
                    'Cc: ' . $org_email . "\r\n";
        if($email) {
            $success = mail($to, $subject, $email, $headers);
            if($success) {
                // print "Mail sent! ";
                $mysqli->dbCommand("UPDATE seminars SET announced=2 WHERE id='" . $seminar['id'] . "'");
            } else {
                print "Mail failed! ";
            }
        } else {
            print "No day of announcment! ";
        }
    }


    // if (cercannounce < now && announced == 0)
        // announce seminar talks
    if($announce_time < $now && $seminar['announced'] == 0) {
        $email = "Hello,\n\n For the next CERCA seminar (";
        $email .= date("M jS \\a\\t G:i", $seminar_time);
        $email .= "), we'll be hearing from the following:\n";

        $talks = $mysqli->dbQuery("SELECT * FROM (SELECT * FROM talks WHERE seminar='".$seminar['id']."') AS t LEFT JOIN presenters ON t.presenter=presenters.id");
        foreach($talks as $talk) {
            $email .= "* " . ($talk->name) . ($talk->title ? (" on '" . $talk->title . "'") : "") . "\n";
        }
        $email .= "\nYou can see the schedule of talks at http://" . $conf["server"] . "/index.php.";
        $email .= "\nHope to see you there!\n";


        // make sure there are actually talks this week.
        foreach($talks as $talk) {
            if($talk->name == "NO CERCA") {
                $email = "Hello - Just a reminder that there will be NO CERCA this week";
                $email .= ($talk->title ? (" due to the following: '" . $talk->title . "'") : ".");
                $email .= "\nStay tuned for future talks!\n";
            }
        }


        $subject = "CERCA Seminar";
        $org_email = $conf['email'];
        $to = "CERCA List <cerca@case.edu>";
        $headers = 'From: "CERCA Notices" <no-reply@anduril.phys.cwru.edu>' . "\r\n" .
                    'Reply-To: ' . $org_email . "\r\n" .
                    'Cc: ' . $org_email . "\r\n";

        $success = mail($to, $subject, $email, $headers);
        if($success) {
            // print "Mail sent! ";
            $mysqli->dbCommand("UPDATE seminars SET announced=1 WHERE id='".$seminar['id']."'");
        } else {
            print "Mail failed! ";
        }
    }


}

// print "Mail run complete.\n";
