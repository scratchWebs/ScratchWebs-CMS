<?php 
require_once("cms.php");

ini_set('memory_limit', '200M');

// Ensure the user is logged in
$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

// Get update object/id/type
if (isset($_GET["update_object"])) $update_object = $_GET["update_object"];
if (isset($_GET["update_object_id"])) $update_object_id = $_GET["update_object_id"];
if (isset($_GET["update_type"])) $update_type = $_GET["update_type"];


$sessionUpdate = new swSessionUpdate($update_type);
$cancelUpdate = false;

// output this as xml so we can pass the responseHTML and updateKey to the page. jQuery will do the clever bit.
echo '<?xml version="1.0"?>' .
	 '<sessionUpdate>' .
		 '<responseHTML><![CDATA[';




// IF updating a portfolio
if ($update_object == "swPortfolio")
{
	// get the portfolio from the session
	$portfolio = $sessionObject->findFeatureInSession($update_object_id,swFeature::FEATURE_TYPE_PORTFOLIO);
	
	
	// If we are deleting a gallery from a portfolio...
	if ($update_type == "delete_gallery")
	{
		$gallery = $portfolio->getGalleryById($_GET["gallery_id"]);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $gallery;
		$sessionUpdate->old_value = $gallery->delete_flag;
		$sessionUpdate->new_value = true;
		$sessionUpdate->is_delete = true;
		
		$gallery->delete_flag = true;
	}
	
	
	// If we are enabling/disabling a gallery in a portfolio...
	elseif ($update_type == "enable_gallery")
	{
		$gallery = $portfolio->getGalleryById($_GET["gallery_id"]);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $gallery;
		$sessionUpdate->old_value = $gallery->enabled;
		$sessionUpdate->new_value = (int) $_GET["enable"];
		
		$gallery->enabled = (int) $_GET["enable"];
		include 'controls/gallery.php';
	}
	
	
	// if we are about to rename a gallery
	elseif ( $update_type == "rename_gallery" ) {
		$gallery = $portfolio->getGalleryById($_GET["gallery_id"]);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $gallery;
		$sessionUpdate->old_value = $gallery->gallery_name;
		$sessionUpdate->new_value = $_GET["gallery_name"];
		
		$gallery->gallery_name = $_GET["gallery_name"];
	}
	
	
	// If we are adding a new gallery to a portfolio...
	elseif ($update_type == "add_gallery")
	{
		if ($_GET["gallery_name"] == '')
			$cancelUpdate = true;
		else {
			$gallery = new swGallery();
			$gallery->gallery_name = $_GET["gallery_name"];
			
			$portfolio->addGallery($gallery);
			
			// save this update so it can be reviewed/undone later
			$sessionUpdate->update_object = $gallery;
			$sessionUpdate->is_new = true;
			
			include "controls/gallery.php";
		}
	}
	
	
	// If we are sorting galleries in a portfolio...
	elseif ($update_type == "sort_galleries")
	{
		// galleries_in_order_by_id comes in like this "galleryid=2&galleryid=1&galleryid=4&galleryid=3"
		// galleriesInOrderById will now contain... array('2','1','4','3');
		$galleriesInOrderById = explode("&",str_replace("galleryid=","",$_GET["galleries_in_order_by_id"]));
		
		$sessionUpdate->update_object = $portfolio;
		
		 // loop through and set the new gallery order
		for ($i=0; $i<count($galleriesInOrderById); $i++)
		{
			$gallery_id = $galleriesInOrderById[$i];
			$gallery = $portfolio->getGalleryById($gallery_id);
			
			// save this update so it can be reviewed/undone later
			$additional_update = new swSessionUpdate($update_type,$gallery);
			$additional_update->old_value = $gallery->gallery_order;
			$additional_update->new_value = $i;
			$sessionUpdate->addAdditionalUpdate($additional_update);
			
			$gallery->gallery_order = $i;	// SET the new order for this gallery
		}
		
		$portfolio->sortGalleries();	// reorder the galleries in the session object to reflect the changes
	}
}










// IF updating gallery
if ($update_object == "swGallery")
{
	$galleryID = $update_object_id;
	$gallery = $sessionObject->findGalleryInSession($galleryID);
	
	// if we are about to delete an image from a gallery
	if ( $update_type == "delete_image" ) {
		$image = $gallery->getImageFromId($_GET["img_id"]);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $image;
		$sessionUpdate->old_value = $image->delete_flag;
		$sessionUpdate->new_value = true;
		$sessionUpdate->is_delete = true;
		
		$image->delete_flag = true;
		$gallery->removeImageById($_GET["img_id"]);
	}
	
	
	// if we are about to recrop/update an existing image
	elseif ( $update_type == "update_image" ) {
		$image_id = $_GET["img_id"];
		$image = $gallery->getImageFromId($image_id);
		
		$imageSize = $_GET["img_size"];
		
		// set the image name and create the thumbnail
		$image->has_changed = true;
		$image->img_name = $_GET['name'];
		
		$imageData = $image->img_data_original;
		
		// if the image hasn't already been loaded from the session
		if ($imageData == NULL || $imageData == "") {
			$result = mysql_query("SELECT img_data_original FROM tblImages WHERE img_id = " . $image->img_id . ";");
			$imageData = mysql_result($result,0,0);
		}
		
		// crop the image
		$imageData = swImage::cropImageFromData($imageData,$image->img_type,
												$_GET['tw'],$_GET['th'],
												$_GET['x'],$_GET['y'],
												$_GET['w'],$_GET['h']);
		
		// put the cropped image in the appropriate place
		switch ($imageSize) {
			case swImage::IMAGE_SIZE_THUMB:
				$image->img_data_thumb = $imageData;
				break;
			case swImage::IMAGE_SIZE_PREVIEW:
				$image->img_data_preview = $imageData;
				break;
			case swImage::IMAGE_SIZE_LARGE:
				$image->img_data_large = $imageData;
				break;
		}
		
		// if setting a new preview image - ensure we refresh the existing 
		// featured images when the ajax call finishes (except this one)
		if ($imageSize == swImage::IMAGE_SIZE_PREVIEW) {
			$gallery->setFeaturedImage($image->img_id);
			
			echo "<script type=\"text/javascript\">
					swImage_refreshFeaturedImages($('#div_gallery" . $gallery->getUID() . "'),'" . $image->img_id . "');
				  </script>";
		}
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $image;
		$sessionUpdate->new_value = $imageData;
		
		// return the image control
		$image_has_changes = true;
		include "controls/image.php";
	}
	
	
	elseif ($update_type == "add_new_image") {
		// we are adding a new image
		$image_id = $_GET["img_id"];
		$image = $sessionObject->images[$image_id];		// get the image from the session
		
		$imageSize = $_GET["img_size"];
		
		$image->img_name = $_GET['name'];				// rename the image
		$gallery->addImage($image);						// add the image to the gallery
		
		// create the thumbnail
		$imageData = swImage::cropImageFromData($image->img_data_original,$image->img_type,
												$_GET['tw'],$_GET['th'],
												$_GET['x'],$_GET['y'],
												$_GET['w'],$_GET['h']);
		
		// Apply the cropped image to the relative image
		switch ($imageSize) {
			case swImage::IMAGE_SIZE_THUMB:
				$image->img_data_thumb = $imageData;
				break;
			case swImage::IMAGE_SIZE_PREVIEW:
				$image->img_data_preview = $imageData;
				break;
			case swImage::IMAGE_SIZE_LARGE:
				$image->img_data_large = $imageData;
				break;
		}
		
		// if we are creating a new image image then set it and also reset any other featured images
		if (count($gallery->gallery_images) == 1) {
			$gallery->setFeaturedImage($image->img_id);
			
			// if this is the only image in the gallery then force the user to crop a preview image
			echo "<script type=\"text/javascript\">
					swImage_initReCrop('" . $image->img_id . "','" . 
											   $image->img_name . "'," . 
											   swImage::IMAGE_SIZE_PREVIEW . ",'" . 
											   $image->img_fk_gallery_id . "','g_" . 
											   $gallery->gallery_id . "',280,350,true,true);
				  </script>";
		}
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $image;
		$sessionUpdate->is_new = true;
		
		// return the image control
		$image_has_changes = true;
		include "controls/image.php";
		
		//USEFULL FUNCTION: echo "<img src=\"data:" . $image->img_type . ";base64," . base64_encode($image->img_data_thumb) . "\" />";
	}
	
	
	// If we are sorting images in a gallery...
	elseif ($update_type == "sort_images")
	{
		// images_in_order_by_id comes in like this "imageid=2&imageid=1&imageid=4&imageid=3"
		// imagesInOrderById will now contain... array('2','1','4','3');
		$imagesInOrderById = explode("&",str_replace("imageid=","",$_GET["images_in_order_by_id"]));
		
		$sessionUpdate->update_object = $gallery;
		
		 // loop through and set the new image order
		for ($i=0; $i<count($imagesInOrderById); $i++)
		{
			$image_id = $imagesInOrderById[$i];
			$image = $gallery->getImageFromId($image_id);
			
			// save this update so it can be reviewed/undone later
			$additional_update = new swSessionUpdate($update_type,$image);
			$additional_update->old_value = $image->img_order;
			$additional_update->new_value = $i;
			$sessionUpdate->addAdditionalUpdate($additional_update);
			
			$image->img_order = $i;	// SET the new order for this image
		}
		
		$gallery->sortImages();	// reorder the images in the session object to reflect the changes
	}
}














// IF updating a page
if ($update_object == "swPage")
{
	if ($update_type == "set_title") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->old_value = $page->pg_title;
		$sessionUpdate->new_value = $_GET["value"];
		
		$page->pg_title = $_GET["value"];
	}
	
	
	elseif ($update_type == "set_linkname") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->old_value = $page->pg_linkname;
		$sessionUpdate->new_value = $_GET["value"];
		
		$page->pg_linkname = $_GET["value"];
	}
	
	
	elseif ($update_type == "set_description") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->old_value = $page->pg_description;
		$sessionUpdate->new_value = $_GET["value"];
		
		$page->pg_description = $_GET["value"];
	}
	
	
	elseif ($update_type == "set_meta_title") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->old_value = $page->pg_meta_title;
		$sessionUpdate->new_value = $_GET["value"];
		
		$page->pg_meta_title = $_GET["value"];
	}
	
	
	
	elseif ($update_type == "set_meta_description") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->old_value = $page->pg_meta_description;
		$sessionUpdate->new_value = $_GET["value"];
		
		$page->pg_meta_description = $_GET["value"];
	}
	
	
	elseif ($update_type == "set_meta_keywords") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->old_value = $page->pg_meta_keywords;
		$sessionUpdate->new_value = $_GET["value"];
		
		$page->pg_meta_keywords = $_GET["value"];
	}
	
	
	// If updating page order
	elseif ($update_type == "page_sort")
	{
		// pages_in_order_by_id comes in like this "pageid=2&pageid=1&pageid=4&pageid=3"
		// pagesInOrderById will now contain... array('2','1','4','3');
		$pagesInOrderById = explode("&",str_replace("pageid=","",$_GET["pages_in_order_by_id"]));
		
		 // loop through and set the new page order
		for ($i=0; $i<count($pagesInOrderById); $i++)
		{
			$page_id = $pagesInOrderById[$i];
			$page = $sessionObject->getPageById($page_id);
			
			// pages get added as additional updates
			// because we don't want a seperate update for each page
			$additional_update = new swSessionUpdate($update_type,$page);
			$additional_update->old_value = $page->pg_order;
			$additional_update->new_value = $i;
			$sessionUpdate->addAdditionalUpdate($additional_update);
			
			$page->pg_order = $i;	// SET the new order for this page
		}
		
		$sessionObject->sortPages();	// reorder the pages in the session object to reflect the changes
	}
}











// IF updating a section
if ($update_object == "swSection")
{
	$section = $sessionObject->findSectionInSession($update_object_id);
	
	if ($update_type == "section_update_html") {
		// make sure the value has actually changed
		if ($section->section_html == $_GET["value"]) {
			$cancelUpdate = true;
		} else {
			// save this update so it can be reviewed/undone later
			$sessionUpdate->update_object = $section;
			$sessionUpdate->old_value = $section->section_html;
			$sessionUpdate->new_value = $_GET["value"];
			
			$section->section_html = $_GET["value"];
		}
	}
}









echo ']]></responseHTML>';



// Now the update has finished we can save it
// if the field being updated is back to it's original value
// OR a newly added item has been deleted then the update will not have been saved
if ( !$cancelUpdate && $sessionUpdate->save($sessionObject) ) {
	echo '<updateKey>' . $sessionUpdate->key . '</updateKey>';
	
	$log = new swLog();
	
	if (isset($sessionUpdate->update_object)) {
		$log->log_object_type = $sessionUpdate->update_object->getObjectType();
		$log->log_object_id = $sessionUpdate->update_object->getObjectID();
	} else {
		// At the moment this only happens for pages 
		// this needs to change because there is no parent object to tie this too
		$log->log_object_type = dbObject::OBJECT_TYPE_PAGE;
		$log->log_object_id = -1;
	}
	
	$log->log_type = swLog::LOG_TYPE_SESSION_UPDATE;
	$log->log_message = $sessionUpdate->getDesciption();
	$log->log_fk_user_id = $sessionObject->user->user_id;
	$log->saveAsNew();
}

// return how many this object has ($sessionUpdate->update_object might not be set is we are re-ordering pages)
$noUpdates = (isset($sessionUpdate->update_object)) ? $sessionUpdate->update_object->noUpdates() : 0;

echo '<noUpdates>' . $noUpdates . '</noUpdates>';

echo '</sessionUpdate>';

header("content-type:application/xml;charset=utf-8 .xml");




// we save the session state after changing anything in the session
$sessionObject->saveState();

?>