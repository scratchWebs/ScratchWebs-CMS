<?php
require_once("cms.php");

// Ensure the user is logged in
$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

// Retrieve username
$user = $sessionObject->user->user_name;

// Get $_POST vars
$oldPass = $_GET["old_pass"];
$newPass = $_GET["new_pass"];

// Check correct password
if ($oldPass == $sessionObject->user->user_pass) {
	
	// Set new password
	$sessionObject->user->user_pass = $newPass;
	$sessionObject->user->update();
	
	$changeResponse = "success";
}
else {	
	$changeResponse = "fail";
}

// Return response
echo $changeResponse;

?>