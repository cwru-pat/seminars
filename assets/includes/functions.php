<?php

function o($text) {
	return htmlspecialchars($text, ENT_QUOTES);
}

function warning($text, $level="warning") {
    print "<div class='notice {$level}'>";
    print $text;
    print "</div>";
}

function check_login() {
  phpCAS::client(CAS_VERSION_2_0, 'login.case.edu', 443, '/cas');
  phpCAS::setNoCasServerValidation();
  phpCAS::forceAuthentication();

  $user = phpCAS::getUser();
  global $mysqli;
  $result = $mysqli->dbQuery("SELECT * FROM settings WHERE name='admins'");
  $admins = array_map(trim, explode(',', $result[0]->value));

  if(array_search($user, $admins) === FALSE) {
    print "<p>Only cool people get to edit CERCA stuff.</p>";
    site_footer();
    die();
  } else {
    print "<p class='loggedinas'><em>Logged in as </em><span class='monospaced'>".$user."</span>.</p>";
  }
}
