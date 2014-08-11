<?php session_start();

set_include_path(dirname(__FILE__));

require_once("template.php");
require_once("mysqli_conn.class.php");
require_once("functions.php");

require_once("/usr/share/php/CAS.php");

global $config;
require_once("config.php");

global $mysqli;
$mysqli = new DBConn(array(
		'user' => $config["db"]["user"],
		'pass' => $config["db"]["pass"],
		'host' => $config["db"]["host"],
		'name' => $config["db"]["name"]
	));
// $mysqli->setDebug(TRUE);

// allow general access to run jobs?
$jobaccess = TRUE;
