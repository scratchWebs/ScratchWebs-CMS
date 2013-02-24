<?php
require_once("../cms.php");

// get the required info either from previously saved values OR from $_GET data
if (!isset($uid)) $uid = uniqid();										// uid
if (!isset($galleryID)) $galleryID = $_POST["gallery_id"];				// gallery id
if (!isset($imageID)) {													// image id
	if (isset($_POST["image_id"])) $imageID = $_POST["image_id"];
	else $imageID = "";
}

if (isset($_GET["image_size"])) $imageSize = $_POST["image_size"];		// image_size
else $imageSize = swImage::IMAGE_SIZE_THUMB;							// edit thumb by default

if (!isset($update_type)) $update_type = "update_image";

if ($imageID !== "" && $imageID !== NULL) $imgSrc = swImage::getOriginalSrc($imageID);
else $imgSrc = "";

$cropFeatured = (isset($_POST["crop_featured"])) ? $_POST["crop_featured"] : false;	// show the cropping featured image message

?>

<div id="div_initializing<?=$uid?>" class="cloud" style="clear:both; padding:100px; text-align:center">
    <img src="<?= URI_CMS ?>images/green-tick.jpg" style="margin-right:15px; width:16px; height:16px;" />Loading image... Complete<br />
    <img src="<?= URI_CMS ?>images/ajax-loader2.gif" style="position:relative; top:4px; margin-right:5px; width:16px; height:16px;" />Initalizing... Please wait
</div>

<form id="upload_form<?=$uid?>" class="box gallery_image_upload" method="post" action="updateSession.php" style="clear:both;display:none; overflow:hidden">
    <input type="hidden" name="uid" value="<?=$uid?>" />
    <input type="hidden" name="update_object" value="swGallery" />
    <input type="hidden" name="update_object_id" value="<?=$galleryID?>" />
    <input type="hidden" name="update_type" value="<?=$update_type?>" />
    <input type="hidden" name="image_src" value="<?=$imgSrc?>" />
    <input type="hidden" id="x_<?=$uid?>" name="x" />
    <input type="hidden" id="y_<?=$uid?>" name="y" />
    <input type="hidden" id="w_<?=$uid?>" name="w" />
    <input type="hidden" id="h_<?=$uid?>" name="h" />
    <input type="hidden" id="tw_<?=$uid?>" name="tw" />
    <input type="hidden" id="th_<?=$uid?>" name="th" />
    <input type="hidden" id="img_id_<?=$uid?>" name="img_id" value="<?=$imageID?>" />
    <input type="hidden" id="img_size_<?=$uid?>" name="img_size" value="<?=$imageSize?>" />
    
    <b>name:</b> <input type="text" id="name_<?=$uid?>" name="name" value="" style="width:250px" /><p />
    
    <? if ($cropFeatured) { ?>
    	<b>Please create the main image</b><br />Use the mouse to select an area for the main image.
    <? } else { ?>
    	<b>Create image thumbnail</b><br />Use the mouse to select an area for the thumbnail.
    <? } ?>
    <div id="div_crop<?=$uid?>" style="margin:10px">
        <img id="img_preview<?=$uid?>" class="imgAreaSelect" src="<?= $imgSrc ?>" style="margin:0px; padding:0px; border:1px solid #000000" />
        <div id="div_thumb<?=$uid?>" style="float:right; position:relative; overflow:hidden; width:170px; height:100px; margin:0px; padding:0px; border:1px solid #000000">
            <img id="img_thumb<?=$uid?>" src="<?= $imgSrc ?>" style="position: relative" />
        </div>
    </div>
</form>
