<?php


?>
<h3>CMS Admin (Restricted Access)</h3>

<form id="frmAdmin">
	<h4>Status</h4>
    <p><?= "Session object is currently using " . $sessionObject->memory_in_use . " of memory." ?></p>
    <h4>Custom Header</h4>
    <p>
        <input type="checkbox" id="chkCustomHeader" /><label for="chkCustomHeader">Use custom header</label> &nbsp; 
        <input type="text" value="header.php" />
    </p>
    <h4>Change UI Theme</h4>
    <div class="uiButtonSet">
        <input type="radio" name="theme" id="rdoThemeDefault" checked="checked" /><label for="rdoThemeDefault">Default theme</label>
        <input type="radio" name="theme" id="rdoThemeLight" /><label for="rdoThemeLight">Light theme</label>
        <input type="radio" name="theme" id="rdoThemeDark" /><label for="rdoThemeDark">Dark theme</label>
    </div>
    <h4>Helper Functions</h4>
    <p>
		<button class="buttonNext" onclick="window.location='helpers/refreshImageSizes.php';return false">Refresh image sizes</button>
    </p>
    <h4>Edit pages</h4><button class="buttonAdd">Add Page</button>
    
	<div class="accordionSortable">
		<?
            // CREATE EDIT PAGE CONTROLS
    /*
            foreach ($pages as $page) {
                
                if (isFirstPage($page)) $ButtonStyleHighlight = " ui-state-highlight";
                                   else $ButtonStyleHighlight = "";
                
                ?>
				
                <div>
                    <h3><a href="#"><?= $page->pg_linkname; ?></a></h3>
                    <div>
                        <table cellpadding="5" cellspacing="0" border="0">
                            <tr><th>
                                Code Ref<br /><input type="text" size="20" value="<?= $page->pg_code_ref; ?>" />
                            </th><th>
                                Link name<br /><input type="text" size="20" value="<?= $page->pg_linkname; ?>" />
                            </th><th>
                                Title<br /><input type="text" size="20" value="<?= $page->pg_title; ?>" />
                            </th></tr>
                        </table>
                        <h4>Sections</h4>
                        <ul>
                        <?
						
							foreach ($page->pg_sections as $section) {
								
								?>
                                <li>
									<?= $section->section_name; ?>
                                    <small>(size: <?= $section->section_max_size; ?>)</small>
                                	<button class="buttonEdit">Edit</button>
                                </li>
                                <?
								
							}
						
						?>
                        </ul>
                        <p><button class="buttonAdd">Add Section</button></p>
                        <h4>Features</h4>
                        <?
						
						
						?>
                        <p><button class="buttonAdd">Add Feature</button></p>
					    <p><button class="buttonDelete">Delete Page</button></p>
                    </div>
                </div>
        
                <?
                
            }
            */
        ?>
    </div>
</form>