<?php
/*
 * Script assumes $page is set to an instance of swPage
 * 
 * */
$page = (isset($page)) ? $page : null;
if (!isset($page)) throw new Exception('$page is not set to an instance of swPage');

$divID = $page->getUID();		// unique div id to tie the sortable menu to the relevant page

if (!$page->isFirstPage()) $className = "ui-helper-hidden";		// hide all but the first page
?>

<div id="<?= $divID; ?>" class="<?= $className; ?>">
	
	<div class="tabs">
		<ul>
			<li><a href="#General"><span class="ui-icon ui-icon-document"></span>General</a></li>
			<?
				// Draw a seperate tab for each feature
				foreach ($page->pg_features as $feature) {
					$feature_name = "";
					$feature_image = "";
					
					switch ($feature->getFeatureType()) {
						case swFeature::FEATURE_TYPE_GALLERY:
							$feature_name = $feature->gallery_name;
							$feature_image = "ui-icon-image";
							break;
						case swFeature::FEATURE_TYPE_PORTFOLIO:
							$feature_name = $feature->portfolio_name;
							$feature_image = "ui-icon-image";
							break;
					}
					?>
					  <li><a href="#feature_<?= $feature->getUID() ?>">
						<span class="ui-icon ui-icon-image"></span><?= $feature_name ?></a>
					  </li>
					<?
				}
			?>
		</ul>
		
		<?
		
		
// GENERAL TAB /////////////////////////////////////////////////////////////////////
		?>
        
		<div id="General">
			<div class="buttonControls">
            	<? if ($user->user_type == swUser::USER_TYPE_ADMIN) { ?>
				<button class="buttonEnable">Disable Page</button>
                <button class="buttonEdit">Edit Title</button>
				<button class="buttonEdit">Edit Link</button>
                <button class="buttonEdit">Edit Meta Description</button>
                <? } ?>
                
			</div>
			
			<h3 id="set_page_title<?= $page->pg_id ?>"><?= $page->pg_title ?></h3>
            
            <div class="accordion">
            
      			<div>
					<? $updateKey = $page->getUpdateKeyByType('set_title') ?>
                    <h3 class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>"><a href="#">Page Title</a></h3>
                    <div class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>" style="position:relative;padding:5px">
                        <p>The Page Title is displayed at the top of the respective page.</p>
                        <input id="<?= $page->pg_id ?>_pg_title" data-pageid="<?= $page->pg_id ?>" type="text" class="editable" value="<?= $page->pg_title ?>" onchange="swPage_updateTitle($(this),$('#<?= $page->pg_id ?>_pg_title_undo'))" />
                        <span id="<?= $page->pg_id ?>_pg_title_undo" class="undoChange<? if (!isset($updateKey)) echo " ui-state-disabled" ?>" onclick="swPage_undoTitle($('#<?= $page->pg_id ?>_pg_title'),$(this))" data-update-key="<?= $updateKey ?>"></span>
                    </div>    
            	</div>  
                          
      			<div>
					<? $updateKey = $page->getUpdateKeyByType('set_linkname') ?>
                    <h3 class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>"><a href="#">Link Text</a></h3>
                    <div class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>" style="position:relative;padding:5px">
        	            <p>The Link Text is used on the link buttons of your site, this will likely be the same as the Page Title.</p>
    	                <input id="<?= $page->pg_id ?>_pg_linkname" data-pageid="<?= $page->pg_id ?>" type="text" class="editable" value="<?= $page->pg_linkname ?>" onchange="swPage_updateLinkName($(this),$('#<?= $page->pg_id ?>_pg_linkname_undo'))" />
	        	        <span id="<?= $page->pg_id ?>_pg_linkname_undo" class="undoChange<? if (!isset($updateKey)) echo " ui-state-disabled" ?>" onclick="swPage_undoLinkName($('#<?= $page->pg_id ?>_pg_linkname'),$(this))" data-update-key="<?= $updateKey ?>"></span>
                    </div>    
            	</div>
            	
                <!-- Page Description edit to go here -->
                <? /* Not Required for Audley & Audley 			**CODE NEEDS UPDATEING**
            <div class="editSection">
                <p><b>Page description</b></p>
                <p>The description is shown under the title of the page.</p>
                <input id="<?= $page->pg_id ?>_pg_desc" rel="<?= $page->pg_id ?>" type="text" class="editable" value="<?= $page->pg_description ?>" onchange="swPage_updateDescription('<?= $page->pg_id ?>',this.value)" />
                <span class="undoChange" rel="<?= $page->pg_id ?>_pg_desc" rel2="<?= $page->pg_id ?>"></span>
            </div>
			*/ ?>
                
      			<div>
					<? $updateKey = $page->getUpdateKeyByType('set_meta_title') ?>
                    <h3 class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>"><a href="#">Meta Title</a></h3>
                    <div class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>" style="position:relative;padding:5px">
                        <p>The Meta Title is used by search engines as the link to the respective page. It also appears in the title bar/tab of the internet browser and used when people bookmark the page.</p>
                        <input id="<?= $page->pg_id ?>_pg_meta_title" data-pageid="<?= $page->pg_id ?>" type="text" class="editable" value="<?= $page->pg_meta_title ?>" onchange="swPage_setProperty('set_meta_title',$(this),$('#<?= $page->pg_id ?>_pg_meta_title_undo'))" />
                        <span id="<?= $page->pg_id ?>_pg_meta_title_undo" class="undoChange<? if (!isset($updateKey)) echo " ui-state-disabled" ?>" onclick="swPage_undoProperty($('#<?= $page->pg_id ?>_pg_meta_title'),$(this))" data-update-key="<?= $updateKey ?>"></span>
                        <p>It's important to brand your Meta Title as this is often the first thing people see before entering your site. Using a seperator like '-' or ':' before the actual title starts is good practice.</p>
                    </div>    
            	</div>

      			<div>
					<? $updateKey = $page->getUpdateKeyByType('set_meta_description') ?>
                    <h3 class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>"><a href="#">Meta Description</a></h3>
                    <div class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>" style="position:relative;padding:5px">
                        <p>The Meta Description is shown in search engine results under the title of this page. This is the main "driver" for google to find your website.</p>
                        <input id="<?= $page->pg_id ?>_pg_meta_desc" data-pageid="<?= $page->pg_id ?>" type="text" class="editable" value="<?= $page->pg_meta_description ?>" onchange="swPage_setProperty('set_meta_description',$(this),$('#<?= $page->pg_id ?>_pg_meta_desc_undo'))" />
                        <span id="<?= $page->pg_id ?>_pg_meta_desc_undo" class="undoChange<? if (!isset($updateKey)) echo " ui-state-disabled" ?>" onclick="swPage_undoProperty($('#<?= $page->pg_id ?>_pg_meta_desc'),$(this))" data-update-key="<?= $updateKey ?>"></span>
                        <p>Be as descriptive as possible while keeping it to the point. Avoid using the same description for each page.</p>
                    </div>    
            	</div>                

      			<div>
					<? $updateKey = $page->getUpdateKeyByType('set_meta_keywords') ?>
                    <h3 class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>"><a href="#">Meta Keywords</a></h3>
                    <div class="RTEaccordion<? if (isset($updateKey)) echo " ui-state-error" ?>" style="position:relative;padding:5px">
                        <p>The Meta Keywords used by most search engines to find your website (except google).</p>
                        <input id="<?= $page->pg_id ?>_pg_meta_keywords" data-pageid="<?= $page->pg_id ?>" type="text" class="editable" value="<?= $page->pg_meta_keywords ?>" onchange="swPage_setProperty('set_meta_keywords',$(this),$('#<?= $page->pg_id ?>_pg_meta_keywords_undo'))" />
                        <span id="<?= $page->pg_id ?>_pg_meta_keywords_undo" class="undoChange<? if (!isset($updateKey)) echo " ui-state-disabled" ?>" onclick="swPage_undoProperty($('#<?= $page->pg_id ?>_pg_meta_keywords'),$(this))" data-update-key="<?= $updateKey ?>"></span>
                        <p>Keywords need to be seperated by commas (,) and should contain words that are found in the page as well as the "Meta Title" and "Meta Description".</p>
                    </div>    
            	</div>   

			<?
// PAGE CONTENT - SECTIONS ///////////////////////////////////////////////////////////
			if (count($page->pg_sections) > 0) {				
				foreach ($page->pg_sections as $section) {
					echo "<div>";
					include("section.php");
					echo "</div>";
				}
			}
			?>
            </div>
		</div>
		
		
		<?
// FEATURE TABS /////////////////////////////////////////////////////////////////////
		foreach ($page->pg_features as $feature) 
		{
			?>
			<div id="feature_<?= $feature->getUID(); ?>">
			<?
			switch ($feature->getFeatureType()) 
			{
				case swFeature::FEATURE_TYPE_GALLERY:
					$gallery = $feature;
					include("gallery.php");
					break;
				case swFeature::FEATURE_TYPE_PORTFOLIO:
					$portfolio = $feature;
					include("portfolio.php");
					break;
			}
			?>
			</div>
			<?
		}
		?>
	</div>
</div>