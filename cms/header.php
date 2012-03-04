<?php
require_once("cms.php");

// Ensure the user is logged in
$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

// Set the title if one hasn't already been entered
if (!isset($title)) {
	$title = "ScratchWebs CMS";
}

// attempt to load in a custom header to match the website style
//$custom_header = PATH_HTML . "header.php";
//
//if (file_exists($custom_header)) {
//	$CMSMode = true;
//	require_once($custom_header);
//	exit;
//} else {

// Use this default header if no template exists
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?=$title?></title>

<!-- meta -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- script -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>

</head>
<body>
<?
//}
?>