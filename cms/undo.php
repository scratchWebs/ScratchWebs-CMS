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


// output this as xml so we can pass the responseHTML and updateKey to the page. jQuery will do the clever bit.
echo '<?xml version="1.0"?>' .
	 '<sessionUpdate>';

echo '<undoResponse><![CDATA[' . $undoResponse . ']]></undoResponse>';		// send the response back

// return how many this object has ($sessionUpdate->update_object might not be set is we are re-ordering pages)
$noUpdates = (isset($sessionUpdate->update_object)) ? $sessionUpdate->update_object->noUpdates() : 0;

echo '<noUpdates>' . $noUpdates . '</noUpdates>';

echo '</sessionUpdate>';

header("content-type:application/xml;charset=utf-8 .xml");

$sessionObject->saveState();		// always save the sessionState to keep the changes

?>