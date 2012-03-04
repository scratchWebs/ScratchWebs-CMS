<?php 
require_once("cms.php");

// Ensure the user is logged in
$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

if (isset($_POST["commit_form"])) {
	$checkedUpdates = array();
	
	// first seperate the the checked updates from the unchecked updates
	foreach ($_POST as $key)
		if (array_key_exists($key,$sessionObject->sessionUpdates)) {
			$checkedUpdates[$key] = $sessionObject->sessionUpdates[$key];
			unset($sessionObject->sessionUpdates[$key]);
		}
	
	// now undo all of the unchecked updates
	$uncheckedUpdates = $sessionObject->sessionUpdates;
	foreach ($uncheckedUpdates as $uncheckedUpdate) {
		$uncheckedUpdate->undo($sessionObject);
		unset($sessionObject->sessionUpdates[$key]);
	}
	
	// now save all of the remaining checked updates
	$savedObjects = array();	// keep track of what get's saved (as we only need to save each dbObject once)
	foreach ($checkedUpdates as $checkedUpdate) {
		$checkedUpdate->commitUpdate($savedObjects);
		
		$log = new swLog();
		
		if (isset($sessionUpdate->update_object)) {
			$log->log_object_type = $sessionUpdate->update_object->getObjectType();
		} else {
			$log->log_object_type = dbObject::OBJECT_TYPE_PAGE;	// this needs to change
		}
		
		$log->log_object_id = -1;	// TODO: THIS NEEDS TO BE THE ID OF THE OBEJCT BUT AT THE MOMENT ALL ID's HAVE DIFFERENT FIELD NAMES
		$log->log_type = swLog::LOG_TYPE_COMMIT_OBJECT;
		$log->log_message = $checkedUpdate->getDesciption();
		$log->log_fk_user_id = $sessionObject->user->user_id;
		$log->saveAsNew();
	}
	
	// save the changes to the session
	$sessionObject->saveState();
}

// redirect so the user cannot re-post
header("location: /cms/");
?>