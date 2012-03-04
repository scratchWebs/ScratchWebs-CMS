<?

$uid = $portfolio->getUID();

// Rename gallery. ie: rename to Portfolio
if ($portfolio->portfolio_gallery_rename == "") $galleryRename = "Gallery";
else $galleryRename = $portfolio->portfolio_gallery_rename;

$divGalleryAddId = "divGA" . $uid;
$btnGalleryAddId = "btnGA" . $uid;
$btnGalleryCancelId = "btnGC" . $uid;
$btnGalleryCreateId = "btnGCX" . $uid;
$inputGalleryNameId = "btnGCX" . $uid;

?>
<div id="<?=$uid?>" class="swPortfolio">
	<button class="buttonOptions" onclick="$('#menu_<?=$uid?>').slideToggle();return false"><h3><?= $portfolio->portfolio_name ?></h3></button>
    
    <ul id="menu_<?=$uid?>" class="menu popupMenu">
    	<li><a href="#" onclick="$('#<?= $divGalleryAddId ?>').slideDown(function(){$('#<?= $inputGalleryNameId ?>').focus()})">Create new <?= $galleryRename ?></a></li>
        <!--<li><a href="#" onclick="alert('help')">Help</a></li>-->
    </ul>

    <p>Through this tab you can edit <?= $galleryRename ?>'s and their images.<br />
    You can drag and drop <?= $galleryRename ?>'s and images to change the order that they appear on the website.<br />Clicking on the drop-down buttons will give you more options.</p>
    
    <div id="<?= $divGalleryAddId ?>" class="editSection" style="display:none; margin:10px auto">
        <form id="form_create_gallery<?=$uid?>">
            <p><b>Create new <?= $galleryRename ?></b></p>
            <p>Please enter a name for the new <?= $galleryRename ?></p>
            <input type="text" id="<?= $inputGalleryNameId ?>" name="gallery_name" />
            <button class="buttonSave" onclick="swPortfolio_addGallery('<?=$portfolio->portfolio_id?>',$('#<?=$divGalleryAddId?>'),$('#form_create_gallery<?=$uid?>'),$('#gallery_list_<?=$uid?>')); return false">OK</button>
            <button class="buttonCancel" id="<?= $btnGalleryCancelId ?>" onclick="$('#<?= $divGalleryAddId ?>').slideUp();$('#<?= $btnGalleryAddId ?>').slideDown();return false">Cancel</button>
        </form>
    </div>
</div>

<?
$updateKey = $portfolio->getUpdateKeyByType('sort_galleries');

if (isset($updateKey)) {
	echo '<span class="undoPortfolioSort" data-update-key="' . $updateKey . '">Undo sort</span>';
} else {
	echo '<span class="undoPortfolioSort" style="display:none">Undo sort</span>';
}
?>

<div id="gallery_list_<?=$uid?>" data-portfolioid="<?= $portfolio->portfolio_id ?>" class="portfolioSortableAccordion">
	<?
    // Populate Galleries
    if (count($portfolio->galleries) > 0)
    {
        foreach ($portfolio->galleries as $gallery) {
            include("controls/gallery.php");
        }
    }
    ?>
</div>