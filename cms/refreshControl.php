<?
require_once("cms.php");

$type = $_GET["type"];
$id = $_GET["id"];

$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

if ($type == "swImage") {
	$image = $sessionObject->findImageInSession($id);

	if (isset($image)) {
		include('controls/image.php');
	}
}
elseif ($type == "swGallery") {
	$gallery = $sessionObject->findGalleryInSession($id);

	if (isset($gallery)) {
		include('controls/gallery.php');
	}
}
elseif ($type == "swWebLogEntry") {
	$wlentry = $sessionObject->findWebLogEntryInSession($id);

	if (isset($wlentry)) {
		include('controls/weblogentry.php');
	}
} 
?>