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



if (isset($_GET["update_object_id"])) $update_object_id = $_GET["update_object_id"];
$page = $sessionObject->getPageById($update_object_id);


// output this as xml so we can pass the responseHTML and updateKey to the page. jQuery will do the clever bit.
echo '<?xml version="1.0"?>' .
	 '<sessionUpdate>';

echo '<undoResponse>' . $undoResponse . '</undoResponse>';		// send the response back

echo '<noUpdates>' . $page->noUpdates() . '</noUpdates>';			// DOESNT WORK WITH SECTIONS

echo '</sessionUpdate>';

header("content-type:application/xml;charset=utf-8 .xml");

$sessionObject->saveState();		// always save the sessionState to keep the changes

?>