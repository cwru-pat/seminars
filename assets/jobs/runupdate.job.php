<?php

include("../includes/site.php");
if(!$jobaccess) { die("POOPER SCOOPER"); }

$time = time();
$mysqli->dbCommand("UPDATE settings SET value={$time} WHERE name='lastupdate'");
print("Attempt made to set 'lastupdate'.");
