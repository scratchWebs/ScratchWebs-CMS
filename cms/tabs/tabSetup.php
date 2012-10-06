<?php

/* Site information
 * Assumes $pages is an array of swPage objects
 */

// ensure include has required information
$pages = (isset($pages)) ? $pages : null;
if (!isset($pages)) throw new Exception('$pages must be an array of swPage objects in: ' . __FILE__);

?>
<h3>Website Setup</h3>
<p style="margin-bottom:10px">Use this section to update your site. &nbsp; You can rearrange the order of your pages, change text and image content. &nbsp; Use the commit button when done (top left) to immediately save your changes to the website.</p>
<?

// Page Setup

?>
<table class="thinTable" style="width:100%"><tr valign="top"><td width="165">
    <ul id="sortable">
		<?
        // CREATE SORTABLE PAGE MENU
        foreach ($pages as $page) {
            if ($page->isFirstPage()) $ButtonStyleHighlight = " ui-state-highlight";
                               else $ButtonStyleHighlight = "";
            
            if ($page->enabled) $ButtonStyleEnabled = "ui-state-default";
                              else $ButtonStyleEnabled = "ui-state-disabled";
            
            // the following line of code HTML needs to all be on one line for jquery to replace the text
            ?>
            <li style="position:relative" class="<?= $ButtonStyleEnabled; ?><?= $ButtonStyleHighlight; ?> sortItem" data-pageid="<?= $page->pg_id ?>" id="pgID_<?= $page->pg_id ?>"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><?= $page->pg_linkname; ?><? //if ($page->noUpdates() > 0) echo '<span class="changeIndicator ui-state-error">'.$page->noUpdates().'</span>';?></li>
            <?
        }
		?>
    </ul>
    <? if (array_key_exists('page_sort',$sessionObject->sessionUpdates)) { ?>
        <span id="undoPageSort" data-update-key="<?= $sessionObject->sessionUpdates['page_sort']->key ?>">Undo sort</span>
    <? } else { ?>
        <span id="undoPageSort" style="display:none">Undo sort</span>
    <? } ?>
</td><td>
	<?
    // CREATE EDITABLE PAGE CONTENTS
    foreach ($pages as $page) {
        require("controls/page.php");
    }
    ?>
</td></tr></table>