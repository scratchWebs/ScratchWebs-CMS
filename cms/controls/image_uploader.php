<?php
require_once("../cms.php");

// Ensure the user is logged in
$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

$galleryID = $_POST["gallery_id"];

$no_of_uploads = count($_FILES["uploadfile"]["name"]);

/// do upload here and place into session, then reference the image below from the session
for ($i = 0;$i < $no_of_uploads;$i++)
{
	$name = $_FILES["uploadfile"]["name"][$i];
	$tmp_name = $_FILES["uploadfile"]["tmp_name"][$i];
	
	$image = new swImage();					// create image object
	$image->createFromUploadedFile($tmp_name,$name);
	
	$sessionObject->addImage($image);		// add the image to the session
	$sessionObject->saveState();
	
	$imageID = $image->img_id;
	$update_type = "add_new_image";
	include("image_editor.php");
}
?>