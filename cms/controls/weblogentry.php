<?php
/*
 * Script assumes $wlentry is set to an instance of an swWeblogEntry
*/
$wlentry = (isset($wlentry)) ? $wlentry : new swWebLogEntry();
if (!isset($wlentry)) throw new Exception('$wlentry is not set to an instance of swWeblogEntry');

if ($wlentry->delete_flag == false)
{
	?>
	<div id="wle<?= $wlentry->wlentry_id ?>" data-id="<?= $wlentry->getObjectID() ?>" data-weblogid="<?= $wlentry->wlentry_fk_weblog_id ?>" class="box" <? if ($wlentry->hasUpdates()) { echo 'style="border:1px solid #f00"'; } ?>>
		<div style="float:left">
			<span class="buttonSortSmall" style="margin-right:10px"></span>
		</div>
		<div style="float:right">
	        <span class="buttonSaveSmall" onclick="swWebLog_update('<?= $wlentry->wlentry_fk_weblog_id ?>','<?= $wlentry->wlentry_id ?>',$('#frm<?= $wlentry->getUID() ?>'))"></span>
			<span class="buttonDeleteSmall" onclick="swWebLog_delete('<?= $wlentry->wlentry_id ?>')"></span>
			<? if (!$wlentry->isNew()) { ?>
				<span class="undoChange<? if (!$wlentry->hasUpdates()) echo " ui-state-disabled" ?>" onclick="swWebLog_undo('<?= $wlentry->wlentry_id ?>',$(this))" data-update-key="<?= $wlentry->getUpdateKeyByType("weblog_update") ?>"></span>
			<? } ?>
		</div>
		<form id="frm<?= $wlentry->getUID() ?>">
			Author: <input name="author" type="text" value="<?= $wlentry->wlentry_author ?>" /><br />
			<textarea name="text" rows="8" cols="100"><?= $wlentry->wlentry_text ?></textarea>
		</form>
	</div>
	<?
	
}
?>