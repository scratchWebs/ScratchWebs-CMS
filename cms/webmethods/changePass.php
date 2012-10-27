<?php
require_once("../cms.php");

// Ensure the user is logged in
$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

// Retrieve username
$user = $sessionObject->user->user_name;

// Get $_POST vars
$oldPass = $_POST["old_pass"];
$newPass = $_POST["new_pass"];

// Check correct password
if ($oldPass == $sessionObject->user->user_pass) {
	
	// Set new password
	$sessionObject->user->user_pass = $newPass;
	if ($sessionObject->user->update() == TRUE) $changeResponse = "success";
	else $changeResponse = "dbError";
}
else {	
	$changeResponse = "fail";
}

// Return response
$sessionObject->saveState();
echo $changeResponse;

?>