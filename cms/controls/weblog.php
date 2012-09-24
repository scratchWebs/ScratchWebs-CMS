<?
/*
 * Script assumes $weblog is set to an instance of swWebLog
*/
$weblog = (isset($weblog)) ? $weblog : new swWebLog();
if (!isset($weblog)) throw new Exception('$weblog is not set to an instance of swWebLog');

$uid = $weblog->getUID();

?>

<h3><?= $weblog->weblog_name ?></h3>
<button id="btnCreateNew<?= $uid ?>" class="buttonAdd" onclick="$(this).hide();$('#divAddNew<?= $uid ?>').show();$('#btnAuthor<?= $uid ?>').focus()">Add New</button>
<div id="divAddNew<?= $uid ?>" class="editSection" style="display:none; margin:10px auto">
	<form id="frmCreate<?= $uid ?>">
		<p><b>Create new <?= $weblog->weblog_entry_name ?></b></p>
	    <p>Author<br /><input id="btnAuthor<?= $uid ?>" type="text" name="author" /></p>
	    <p>Text<br /><textarea name="text" style="width:500px; height:80px"></textarea></p>
    </form>
    <button class="buttonSave" onclick="swLogEntry_create('<?= $weblog->weblog_id ?>',$('#divAddNew<?= $uid ?>'),$('#frmCreate<?= $uid ?>'),$('#weblog_list_<?=$uid?>'),$('#btnCreateNew<?= $uid ?>'))">Create</button>
    <button class="buttonCancel" id="" onclick="$('#divAddNew<?= $uid ?>').hide();$('#btnCreateNew<?= $uid ?>').show();">Cancel</button>
</div>

<?
$updateKey = $weblog->getUpdateKeyByType('weblog_sort');

if (isset($updateKey)) {
	echo '<span class="undoWeblogSort" data-update-key="' . $updateKey . '">Undo sort</span>';
} else {
	echo '<span class="undoWeblogSort" style="display:none">Undo sort</span>';
}

?>

<div id="weblog_list_<?=$uid?>" data-weblogid="<?= $weblog->weblog_id ?>" class="weblog_sortable">
	<?
    // Populate Entries
    foreach ($weblog->weblog_entries as $wlentry) {
        include("controls/weblogentry.php");
    }
    ?>
</div>