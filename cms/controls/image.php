<?php
/*
	this script assumes that $image is an instance of swImage
*/

$uid = swGallery::UID . $image->img_fk_gallery_id;

// decide what colour the box should be
if ($image->hasUpdates() || $image_has_changes)
	$className = "ui-state-error";		// edited and uncommited colour
else if ($image->img_featured)
	$className = "ui-state-highlight";	// unedited and commited - featured image
else
	$className = "ui-state-default";	// unedited and commited (normal image)

// set the text of the image
$imageText = $image->img_name;
if ($image->img_featured) {
	$imageText = "*" . $imageText . " (Main image)";
	$className .= " featured";
}

?>
<li id="img_<?=$image->img_id?>" data-id="<?=$image->img_id?>" class="swImage <?=$className?>">
	<img src="<?= $image->getImageSrc(swImage::IMAGE_SIZE_THUMB) ?>" title="<?=$imageText?>" width="100" />
	<h5 title="<?=$imageText?>"><?=$imageText?></h5>
    
    <ul id="img_menu<?=$image->img_id?>" class="menu popupMenu">
      <li><a href="#" onclick="swGallery_deleteImage('<?=$image->img_fk_gallery_id?>','<?=$image->img_id?>')">Delete</a></li>
      <? //<li><a href="#" onclick="alert('Rename!')">Rename</a></li> ?>
      <li><a href="#" onclick="swImage_initReCrop('<?=$image->img_id?>','<?=$image->img_name?>',<?=swImage::IMAGE_SIZE_PREVIEW?>,'<?=$image->img_fk_gallery_id?>','<?=$uid?>',280,350,true)">
      <? if ($image->img_featured) echo "Edit Main image"; else echo "Set as Main image"; ?></a></li>
      <li><a href="#" onclick="swImage_initReCrop('<?=$image->img_id?>','<?=$image->img_name?>',<?=swImage::IMAGE_SIZE_THUMB?>,'<?=$image->img_fk_gallery_id?>','<?=$uid?>',170,100)">Edit Thumbnail</a></li>
    </ul>
    
	<button class="buttonOptions imageContextMenu" onclick="$('.popupMenu').slideUp();$('#img_menu<?=$image->img_id?>').slideToggle();return false">Options</button>
</li>