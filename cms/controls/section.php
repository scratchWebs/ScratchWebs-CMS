<?
/*
 * Script assumes $section is set to an instance of swSection
*/
$section = (isset($section)) ? $section : null;
if (!isset($section)) throw new Exception('$section is not set to an instance of swSection');

$updateKey = $section->getUpdateKeyByType('section_update_html')
?>

<h3 class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>"><a href="#"><?= $section->section_name; ?></a></h3>
<div class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>" style="position:relative;padding:5px">
    <textarea class="editRTE ui-corner-all" id="divSectionHTML<?= $section->section_id ?>">
		<?= $section->section_html; ?>
    </textarea>
    <div class="editRTEbuttons" data-pageid="<?= $page->pg_id ?>">
        <span class="RTEbutton RTEsave" onclick="swSection_html_update('<?= $section->section_id ?>',$('#btn_undo_section<?= $section->section_id ?>'))"></span>
		<span id="btn_undo_section<?= $section->section_id ?>" class="RTEbutton undoChange<? if (!isset($updateKey)) echo " ui-state-disabled" ?>" onclick="swSection_html_undo('<?= $section->section_id ?>',$(this))" data-update-key="<?= $section->getUpdateKeyByType("section_update_html") ?>" style="margin-left:0px !important"></span>
    </div>
</div>