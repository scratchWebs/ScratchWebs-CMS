<?php
require_once("cms.php");

ini_set('memory_limit', '200M');

// Ensure the user is logged in
$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

$updateKey = $_GET["update_key"];


if (array_key_exists($updateKey, $sessionObject->sessionUpdates))
{
	// get the update from the session
	$sessionUpdate = $sessionObject->sessionUpdates[$updateKey];
	// undo the update (and store the response)
	$undoResponse = $sessionUpdate->undo($sessionObject);
}

echo $undoResponse;		// send the response back


$sessionObject->saveState();		// always save the sessionState to keep the changes

?>