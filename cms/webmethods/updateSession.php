<?php 
require_once("../cms.php");

ini_set('memory_limit', '200M');

// Ensure the user is logged in
$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

$post_data = array();

// remove microsoft enabled "curly" quotes from post data
foreach ($_POST as $key => $value){
	$post_data[$key] = swCommon::convert_smart_quotes($value);
}

// Get update object/id/type
if (isset($post_data["update_object"])) $update_object = $post_data["update_object"];
if (isset($post_data["update_object_id"])) $update_object_id = $post_data["update_object_id"];
if (isset($post_data["update_type"])) $update_type = $post_data["update_type"];


$sessionUpdate = new swSessionUpdate($update_type);
$cancelUpdate = false;

// output this as xml so we can pass the responseHTML and updateKey to the page. jQuery will do the clever bit.
header("content-type:application/xml;charset=utf-8 .xml");
echo '<?xml version="1.0"?>' .
	 '<sessionUpdate>' .
		 '<responseHTML><![CDATA[';



// IF updating a web log
if ($update_object == "swWebLog")
{
	$weblog = $sessionObject->findFeatureInSession($update_object_id,swFeature::FEATURE_TYPE_WEBLOG);
	
	
	if ($update_type == "weblog_create")
	{
		$author = $post_data['author'];
		$entry_text = $post_data['text'];
	
		if ($author == '' && $entry_text == '') {
			$cancelUpdate = true;
		} else {
			$wlentry = new swWebLogEntry();
			$wlentry->wlentry_author = $author;
			$wlentry->wlentry_date = new DateTime();
			$wlentry->wlentry_date = $wlentry->wlentry_date->format(swCommon::SQL_DATE_FORMAT);
			$wlentry->wlentry_text = $entry_text;
			
			$weblog->addEntry($wlentry);
			
			echo $wlentry->wlentry_id;	// return the id so we can load the control
			
			// save this update so it can be reviewed/undone later
			$sessionUpdate->update_object = $wlentry;
			$sessionUpdate->is_new = true;
		}
	}
	
	
	elseif ($update_type == "weblog_update")
	{
		$author = $post_data['author'];
		$entry_text = $post_data['text'];
		
		if ($author == '' && $entry_text == '') {
			$cancelUpdate = true;
		} else {
			$wlentry = $weblog->getWebLogEntryById($post_data['wlentry_id']);
			
			// save this update so it can be reviewed/undone later
			$sessionUpdate->update_object = $wlentry;
			$sessionUpdate->updateField('wlentry_author',$author);
			$sessionUpdate->updateField('wlentry_text',$entry_text);
		}
	}
	
	
	elseif ($update_type == "weblog_delete")
	{
		$wlentry = $weblog->getWebLogEntryById($post_data['wlentry_id']);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $wlentry;
		$sessionUpdate->updateField('delete_flag',true);
		$sessionUpdate->is_delete = true;
	}
	
	
	// If we are sorting entries in a weblog...
	elseif ($update_type == "weblog_sort")
	{
		// entries_in_order_by_id comes in like this "wlentryid=2&wlentryid=1&wlentryid=4&wlentryid=3"
		// $entriesInOrderById will now contain... array('2','1','4','3');
		$entriesInOrderById = explode("&",str_replace("wlentryid=","",$post_data["entries_in_order_by_id"]));
	
		$sessionUpdate->update_object = $weblog;
	
		// loop through and set the new wlentry order
		for ($i=0; $i<count($entriesInOrderById); $i++)
		{
			$wlentry_id = $entriesInOrderById[$i];
			$wlentry = $weblog->getWebLogEntryById($wlentry_id);
			
			// save this update so it can be reviewed/undone later
			$additional_update = new swSessionUpdate($update_type,$wlentry);
			$additional_update->updateField('wlentry_order',$i);
			$sessionUpdate->addAdditionalUpdate($additional_update);
		}
	
		$weblog->sortEntries();	// reorder the entries in the session object to reflect the changes
	}
}









// IF updating a portfolio
if ($update_object == "swPortfolio")
{
	// get the portfolio from the session
	$portfolio = $sessionObject->findFeatureInSession($update_object_id,swFeature::FEATURE_TYPE_PORTFOLIO);
	
	
	// If we are deleting a gallery from a portfolio...
	if ($update_type == "delete_gallery")
	{
		$gallery = $portfolio->getGalleryById($post_data["gallery_id"]);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $gallery;
		$sessionUpdate->updateField('delete_flag',true);
		$sessionUpdate->is_delete = true;
	}
	
	
	// If we are enabling/disabling a gallery in a portfolio...
	elseif ($update_type == "enable_gallery")
	{
		$gallery = $portfolio->getGalleryById($post_data["gallery_id"]);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $gallery;
		$sessionUpdate->updateField('enabled',(int) $post_data["enable"]);
		
		include '../controls/gallery.php';
	}
	
	
	// if we are about to rename a gallery
	elseif ( $update_type == "rename_gallery" ) {
		$gallery = $portfolio->getGalleryById($post_data["gallery_id"]);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $gallery;
		$sessionUpdate->updateField('gallery_name',$post_data["gallery_name"]);
	}
	
	
	// If we are adding a new gallery to a portfolio...
	elseif ($update_type == "add_gallery")
	{
		if ($post_data["gallery_name"] == '')
			$cancelUpdate = true;
		else {
			$gallery = new swGallery();
			$gallery->gallery_name = $post_data["gallery_name"];
			
			$portfolio->addGallery($gallery);
			
			// save this update so it can be reviewed/undone later
			$sessionUpdate->update_object = $gallery;
			$sessionUpdate->is_new = true;
			
			include "../controls/gallery.php";
		}
	}
	
	
	// If we are sorting galleries in a portfolio...
	elseif ($update_type == "sort_galleries")
	{
		// galleries_in_order_by_id comes in like this "galleryid=2&galleryid=1&galleryid=4&galleryid=3"
		// galleriesInOrderById will now contain... array('2','1','4','3');
		$galleriesInOrderById = explode("&",str_replace("galleryid=","",$post_data["galleries_in_order_by_id"]));
		
		$sessionUpdate->update_object = $portfolio;
		
		 // loop through and set the new gallery order
		for ($i=0; $i<count($galleriesInOrderById); $i++)
		{
			$gallery_id = $galleriesInOrderById[$i];
			$gallery = $portfolio->getGalleryById($gallery_id);
			
			// save this update so it can be reviewed/undone later
			$additional_update = new swSessionUpdate($update_type,$gallery);
			$additional_update->updateField('gallery_order',$i);
			$sessionUpdate->addAdditionalUpdate($additional_update);
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
		$image = $gallery->getImageFromId($post_data["img_id"]);
		
		$gallery->removeImageById($post_data["img_id"]);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $image;
		$sessionUpdate->updateField('delete_flag',true);
		$sessionUpdate->is_delete = true;
	}
	
	
	// if we are about to recrop/update an existing image
	elseif ( $update_type == "update_image" ) {
		$image_id = $post_data["img_id"];
		$image = $gallery->getImageFromId($image_id);
		
		$imageSize = $post_data["img_size"];
		
		// set the image name, url and create the thumbnail
		$image->img_name = $post_data['name'];
		$image->img_URL = $post_data['url'];
		
		$imageData = $image->img_data_original;
		
		// if the image hasn't already been loaded from the session
		if ($imageData == NULL || $imageData == "") {
			if (DATABASE_IMAGE_STOREAGE) {
				$result = mysql_query("SELECT img_data_original FROM tblImages WHERE img_id = " . $image->img_id . ";");
				$imageData = mysql_result($result,0,0);
			} else {
				$imageData = file_get_contents(PATH_IMG . $image_id . '_' . swImage::IMAGE_SIZE_ORIGINAL);
			}
		}
		
		// crop the image
		$imageData = swImage::cropImageFromData($imageData,$image->img_type,
												$post_data['tw'],$post_data['th'],
												$post_data['x'],$post_data['y'],
												$post_data['w'],$post_data['h']);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $image;
		
		switch ($imageSize) {
			case swImage::IMAGE_SIZE_THUMB:
				$sessionUpdate->updateField('img_data_thumb',$imageData);
				break;
			case swImage::IMAGE_SIZE_PREVIEW:
				$sessionUpdate->updateField('img_data_preview',$imageData);
				break;
			case swImage::IMAGE_SIZE_LARGE:
				$sessionUpdate->updateField('img_data_large',$imageData);
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
		
		// return the image control
		$image_has_changes = true;
		include "../controls/image.php";
	}
	
	
	elseif ($update_type == "add_new_image") {
		// we are adding a new image
		$image_id = $post_data["img_id"];
		$image = $sessionObject->images[$image_id];		// get the image from the session
		
		$imageSize = $post_data["img_size"];
		
		$image->img_name = $post_data['name'];				// rename the image
		$image->img_URL = $post_data['url'];				// set the image URL
		$gallery->addImage($image);						// add the image to the gallery
		
		// create the thumbnail
		$imageData = swImage::cropImageFromData($image->img_data_original,$image->img_type,
												$post_data['tw'],$post_data['th'],
												$post_data['x'],$post_data['y'],
												$post_data['w'],$post_data['h']);
		
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
		
		// if this is the first image in the gallery then set it as the 'featured' image 
		// and also ask the client to recrop it
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
		include "../controls/image.php";
		
		//USEFULL FUNCTION: echo "<img src=\"data:" . $image->img_type . ";base64," . base64_encode($image->img_data_thumb) . "\" />";
	}
	
	
	// If we are sorting images in a gallery...
	elseif ($update_type == "sort_images")
	{
		// images_in_order_by_id comes in like this "imageid=2&imageid=1&imageid=4&imageid=3"
		// imagesInOrderById will now contain... array('2','1','4','3');
		$imagesInOrderById = explode("&",str_replace("imageid=","",$post_data["images_in_order_by_id"]));
		
		$sessionUpdate->update_object = $gallery;
		
		 // loop through and set the new image order
		for ($i=0; $i<count($imagesInOrderById); $i++)
		{
			$image_id = $imagesInOrderById[$i];
			$image = $gallery->getImageFromId($image_id);
			
			// save this update so it can be reviewed/undone later
			$additional_update = new swSessionUpdate($update_type,$image);
			$additional_update->updateField('img_order', $i);
			$sessionUpdate->addAdditionalUpdate($additional_update);
		}
		
		$gallery->sortImages();	// reorder the images in the session object to reflect the changes
	}
	
	elseif ($update_type == 'gallery_update_desc_long')
	{
		// make sure the value has actually changed
		if ($gallery->gallery_desc_long == $post_data["value"]) {
			$cancelUpdate = true;
		} else {
			$sessionUpdate->update_object = $gallery;
			$sessionUpdate->updateField('gallery_desc_long',$post_data['value']);
		}
	}
}














// IF updating a page
if ($update_object == "swPage")
{
	if ($update_type == "set_title") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->updateField('pg_title',  $post_data["value"]);
	}
	
	
	elseif ($update_type == "set_linkname") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->updateField('pg_linkname',  $post_data["value"]);
	}
	
	
	elseif ($update_type == "set_description") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->updateField('pg_description',  $post_data["value"]);
	}
	
	
	elseif ($update_type == "set_meta_title") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->updateField('pg_meta_title',  $post_data["value"]);
	}
	
	
	elseif ($update_type == "set_meta_description") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->updateField('pg_meta_description',  $post_data["value"]);
	}
	
	
	elseif ($update_type == "set_meta_keywords") {
		$page = $sessionObject->getPageById($update_object_id);
		
		// save this update so it can be reviewed/undone later
		$sessionUpdate->update_object = $page;
		$sessionUpdate->updateField('pg_meta_keywords',  $post_data["value"]);
	}
	
	
	// If updating page order
	elseif ($update_type == "page_sort")
	{
		// pages_in_order_by_id comes in like this "pageid=2&pageid=1&pageid=4&pageid=3"
		// pagesInOrderById will now contain... array('2','1','4','3');
		$pagesInOrderById = explode("&",str_replace("pageid=","",$post_data["pages_in_order_by_id"]));
		
		 // loop through and set the new page order
		for ($i=0; $i<count($pagesInOrderById); $i++)
		{
			$page_id = $pagesInOrderById[$i];
			$page = $sessionObject->getPageById($page_id);
			
			// pages get added as additional updates
			// because we don't want a seperate update for each page
			$additional_update = new swSessionUpdate($update_type,$page);
			$additional_update->updateField('pg_order',  $i);
			$sessionUpdate->addAdditionalUpdate($additional_update);
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
		if ($section->section_html == $post_data["value"]) {
			$cancelUpdate = true;
		} else {
			// save this update so it can be reviewed/undone later
			$sessionUpdate->update_object = $section;
			$sessionUpdate->updateField('section_html',  $post_data["value"]);
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
		// TODO: this needs to change because there is no parent object to tie this too
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



// we save the session state after changing anything in the session
$sessionObject->saveState();

?>