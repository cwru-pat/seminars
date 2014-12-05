<?php

function site_header() {

print <<<HEADER
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>CERCA - Scheduling System</title>
	<link type="text/css" rel="stylesheet" media="all" href="assets/styles/styles.css" />
    <link type="text/css" rel="stylesheet" media="all" href="assets/styles/bootstrap-datetimepicker.min.css" />

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/seminars.js"></script>

</head>
<body class="cerca-content">
<div id="cerca">
HEADER;

}

function site_footer() {

print <<<FOOTER
</div>
</body>
</html>
FOOTER;

}
