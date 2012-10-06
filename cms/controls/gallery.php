<?
/*
* Script assumes $gallery is set to an instance of swGallery
*/
$gallery = (isset($gallery)) ? $gallery : null;
if (!isset($gallery)) throw new Exception('$gallery is not set to an instance of swGallery');

if (!$gallery->delete_flag) {								// only continue if the gallery hasn't been deleted
	
	$uid = $gallery->getUID();	// unique id for this gallery (must not be the same as any other element on the page)
	
	$isEmptyGallery = (count($gallery->gallery_images) == 0);	// confiure the control differently when it has no images

?>
<div id="<?=$uid?>" data-id="<?=$gallery->gallery_id?>" class="swGallery" data-enabled="<?= ($gallery->enabled) ? 'true' : 'false' ?>">
    <h3><a href="#"><?= $gallery->gallery_name ?><? if (!$gallery->enabled) echo ' (Disabled)' ?></a></h3>
    <div>
        <div id="div_gallery<?= $uid ?>"<? if ($isEmptyGallery) echo ' style="display:none"' ?>>
	    	<?
	        //gallery options menu
	        ?>
            <div class="buttonControls">
                <button class="buttonOptions imageContextMenu" onclick="$('#gallery_menu<?=$uid?>').css('top','28px').css('right','15px');$('.popupMenu').not($('#gallery_menu<?=$uid?>')).slideUp();$('#gallery_menu<?=$uid?>').slideToggle();return false">Options</button>
                <ul id="gallery_menu<?=$uid?>" class="menu popupMenu" style="position:absolute">
                  <li><a href="#" onclick="_addImageFromLocal('<?=$uid?>','<?=$gallery->gallery_id?>')">Add images</a></li>
                  <li><a href="#" onclick="$('#divGalleryDescLong<?=$gallery->gallery_id?>').slideUp();$('#divGalleryDescLongEdit<?=$gallery->gallery_id?>').slideDown()">Edit Description</a></li>
                  <? // if this gallery is part of a portfolio
				  	 if (isset($gallery->gallery_fk_portfolio_id)) { ?>
                      <li><a href="#" onclick="$('#gallery_rename<?= $uid ?>').slideDown(function(){$('#gallery_rename_name<?= $uid ?>').focus()});">Rename</a></li>
                      <li><a href="#" onclick="swPortfolio_enableGallery('<?=$gallery->gallery_fk_portfolio_id?>','<?=$gallery->gallery_id?>',<? if ($gallery->enabled) echo "0"; else echo "1"; ?>)"><? if ($gallery->enabled) echo "Disable"; else echo "Enable" ?></a></li>
	                  <li><a href="#" onclick="swPortfolio_deleteGallery('<?=$gallery->gallery_fk_portfolio_id?>','<?=$gallery->gallery_id?>')">Delete</a></li>
                  <? } ?>
                </ul>
            </div>
            <? if (!$gallery->enabled) echo '<b>This ' . $gallery->gallery_type . ' has been disabled and will not appear on the website</b><br />'; ?>
            <? if ($gallery->gallery_desc_short != "") echo $gallery->gallery_desc_short . '<br />' ?>
			
			<?
	    	// gallery description
	    	?>
	    	<div id="divGalleryDescLong<?= $gallery->gallery_id ?>"><?= $gallery->gallery_desc_long ?></div>
	    	<div id="divGalleryDescLongEdit<?= $gallery->gallery_id ?>" class="editSection hidden" style="margin:10px auto">
	    		<p><b>Edit Description</b></p>
	    		<p>Edit the description in the box below, When you have finished press Update</p>
	    	    <textarea class="editRTE ui-corner-all" id="txtGalleryDescLongHTML<?= $gallery->gallery_id ?>">
					<?= $gallery->gallery_desc_long ?>
			    </textarea>
			    <button class="buttonSave" onclick="swGallery_editDescriptionLong('<?= $gallery->gallery_id ?>'); return false">Update</button>
			    <button class="buttonCancel" onclick="$('#divGalleryDescLong<?=$gallery->gallery_id?>').slideDown();$('#divGalleryDescLongEdit<?=$gallery->gallery_id?>').slideUp();return false">Cancel</button>
	    	</div>
	    	
			<? // if this gallery is part of a portfolio
            	if (isset($gallery->gallery_fk_portfolio_id)) { ?>
                    <div id="gallery_rename<?= $uid ?>" class="editSection hidden" style="margin:10px auto">
                        <form id="form_rename_gallery<?=$uid?>">
                            <p><b>Rename <?= $gallery->gallery_type ?></b></p>
                            <p>Please enter the new name for this <?= $gallery->gallery_type ?></p>
                            <input type="text" id="gallery_rename_name<?= $uid ?>" name="gallery_name" value="<?= $gallery->gallery_name ?>" />
                            <button class="buttonSave" onclick="swPortfolio_renameGallery('<?=$gallery->gallery_fk_portfolio_id?>','<?=$gallery->gallery_id?>',$('#form_rename_gallery<?=$uid?>'),$('#gallery_rename<?= $uid ?>'));return false">Update</button>
                            <button class="buttonCancel" onclick="$('#gallery_rename<?= $uid ?>').slideUp();return false">Cancel</button>
                        </form>
                    </div>
			<? }
			
			
			
			
            $updateKey = $gallery->getUpdateKeyByType('sort_images');
            
            if (isset($updateKey)) {
                echo '<span class="undoImageSort" data-update-key="' . $updateKey . '">Undo sort</span>';
            } else {
                echo '<span class="undoImageSort" style="display:none">Undo sort</span>';
            }
            ?>
            <ul class="swGalleryImageList" data-galleryid="<?=$gallery->gallery_id?>">
			<?
            // if this gallery is empty then start adding images straight away
            if ($isEmptyGallery) {
                //echo "<b>Click on the drop-down button to start adding images</b>";
				echo "<script type=\"text/javascript\">" . 
						"$(document).ready(function(){" .
							"_addImageFromLocal('" . $uid . "','" . $gallery->gallery_id . "')" .
						"})</script>";
            }
            ?>
            
			<?
            foreach ($gallery->gallery_images as $image)
            {
                include("image.php");
            }
            ?>
            </ul>
        </div>
        
        
        <div id="div_uploadbox<?=$uid?>" style="display:none">
            <p><b>Click on the "Add Images" button bellow to get started</b>
            <br />Once you have added your images you can give them a name and create custom thumbnails.
			</p>
            <div id="div_crop_area<?=$uid?>" style="clear:both"></div>
            <div id="div_status<?=$uid?>" ></div>
            
            <div class="div_buttons" style="clear:both; padding-top:20px">
                <div class="buttonControls">
                    <button class="buttonAdd" id="btnAddMore<?=$uid?>" onclick="return false" >Add images</button>
                    <button class="buttonCancel" onclick="swImage_uploadCancel('<?= $uid ?>');return false">Cancel</button>
                    <button class="buttonSave" onclick="swImage_SaveToSession('div_crop_area<?=$uid?>','<?= $uid ?>');return false">OK</button>
                </div>
            </div>
            
        </div>
        
        <div id="div_loading<?=$uid?>" class="cloud" style="clear:both; padding:100px; text-align:center; display:none">
            <img src="images/ajax-loader2.gif" style="margin-right:15px" />Loading image... Please wait
        </div>
        
        <div id="div_edit_box<?=$uid?>" style="display:none">
            <div id="div_edit_crop_area<?=$uid?>" style="clear:both"></div>
            
            <div class="buttonControls">
                <button class="buttonCancel" id="cancel_edit_image<?=$uid?>" onclick="swImage_uploadCancel('<?= $uid ?>');return false">Cancel</button>
                <button class="buttonSave" onclick="swImage_SaveToSession('div_edit_box<?=$uid?>','<?= $uid ?>');return false">OK</button>
            </div>
        </div>
    </div>
</div>
<? } ?>