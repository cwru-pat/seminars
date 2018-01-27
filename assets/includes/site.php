<?php session_start();

set_include_path(dirname(__FILE__));

define("PHP_LOG_FILE",__DIR__.'/../jobs/log/error.log');
define("CRON_LOG_FILE",__DIR__.'/../jobs/log/cron.log');
define("MAIL_LOG_FILE",__DIR__.'/../jobs/log/mail.log');
define("UPDATE_LOG_FILE",__DIR__.'/../jobs/log/update.log');
define("LOG_LOG_FILE",__DIR__.'/../jobs/log/logrotate.log');

error_reporting(E_ALL);
ini_set("display_errors", '0');
ini_set("log_errors", '1');
ini_set("error_log", PHP_LOG_FILE);

require_once("template.php");
require_once("mysqli_conn.class.php");
require_once("CSRFToken.class.php");
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

//reusable CSRFToken
global $token;
$token = new CSRFToken();
