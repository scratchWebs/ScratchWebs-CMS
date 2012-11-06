<?php 
require_once("header.php"); 

$user = $sessionObject->user;
$pages = $sessionObject->pages;
$images = $sessionObject->images;
$sections = $sessionObject->sections;
$features = $sessionObject->features;

?>
	<link rel="stylesheet" type="text/css" href="jquery.ui/css/uitheme/jquery-ui-1.8.16.custom.css" />
	<link rel="stylesheet" type="text/css" href="css/imgareaselect-animated.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.contextMenu.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="rte/jquery.rte.css"/>
    <script type="text/javascript" src="scripts/jquery.ui.accordion.custom.js"></script>
    <script type="text/javascript" src="scripts/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="scripts/ckeditor/adapters/jquery.js"></script>
	<script type="text/javascript" src="scripts/dialog-patch.js"></script>
    <script type="text/javascript" src="scripts/jquery.ajaxmanager.js"></script>
    <script type="text/javascript" src="scripts/ahpi.imgload.js"></script>
    <script type="text/javascript" src="scripts/passwordStrength.js"></script>
    <script type="text/javascript" src="scripts/jquery.imgareaselect.pack.js"></script>
    <script type="text/javascript" src="scripts/ajax-upload.js"></script>
    <script type="text/javascript" src="scripts/common.js"></script>
    <script type="text/javascript" src="scripts/swImage.js"></script>
    <script type="text/javascript" src="scripts/swGallery.js"></script>
    <script type="text/javascript" src="scripts/swPortfolio.js"></script>
    <script type="text/javascript" src="scripts/swPage.js"></script>
    <script type="text/javascript" src="scripts/swSection.js"></script>
    <script type="text/javascript" src="scripts/swWebLog.js"></script>
    <script type="text/javascript" src="scripts/cms.js"></script>
    <script type="text/javascript" src="rte/jquery.rte.js"></script>
	<script type="text/javascript" src="rte/jquery.rte.tb.js"></script>
	
	<div id="divMainEditor" title="Text Editor" class="hidden">
		<textarea id="txtMainEditor" style="margin:auto"></textarea>
	</div>
	
    <div id="div_main_loading" class="cloud" style="margin:auto; width:300px; display:block; text-align:center; margin-top:200px; padding:50px 100px; font-family:Arial, Helvetica, sans-serif; font-size:10pt"><h2>Welcome...</h2><img src="images/ajax-loader2.gif" alt="Initializing..." style="margin-right:15px" />Initializing ScratchWebs CMS.</div>
    
    <div id="div_update_loading" class="cloud"><img src="images/ajax-loader2.gif" />&nbsp; &nbsp; Updating session...</div>
    
    <div id="div_main_content" style="display:none">
        <div id="status_bar" class="ui-corner-all">
            <div id="status_commit" class="ui-widget-header ui-corner-left">
                <span class="ui-icon ui-icon-disk"></span>
                <span class="commit_text">No Changes to Commit</span>
            </div>
            <div id="status_msg" class="ui-widget-header">
            	<p id="swTitle">- ScratchWebs CMS -</p>
                <p id="swStatus"></p>
            </div>
            <div id="status_logout" class="ui-widget-header ui-corner-right">
				<span class="ui-icon ui-icon-triangle-1-s"></span>
				<span style="float:left">Account: <?= $user->user_full_name; ?></span>
            </div>
        </div>
        
        <ul id="userOptions" class="menu popupMenu">
          <li><a onclick='pwdFn();'>Change password</a></li>
           <li><a onclick='logoutFn();'>Log out</a></li> 
        </ul>
        
        <div id="main" class="tabs">
            <ul>
                <li><a href="#tabSetup"><span class="ui-icon ui-icon-wrench"></span>Website Setup</a></li>
                 <?php 
	                 if ($user->user_type == swUser::USER_TYPE_ADMIN)
	                 {
	                 	?>
	                 	<li><a href="#tabStats"><span class="ui-icon ui-icon-clipboard"></span>Website Stats</a></li>
	              		<li><a href="#tabHistory"><span class="ui-icon ui-icon-clock"></span>History</a></li>
	                    <li><a href="#tabControlPanel"><span class="ui-icon ui-icon-person"></span>Control Panel</a></li>
	                    <li><a href="#tabAdmin"><span class="ui-icon ui-icon-gear"></span>CMS Admin</a></li>
	                    <li><a href="#tabLogs"><span class="ui-icon ui-icon-clock"></span>Logs</a></li>
	                    <?
	                 }
                 ?>   
			</ul>
		
            <div id="tabSetup">
	        	<? include("tabs/tabSetup.php"); ?>
            </div>
            
            <?php 
	            if ($user->user_type == swUser::USER_TYPE_ADMIN)
	            {
	             	?>
		            <div id="tabStats">
		                <? include("tabs/tabStats.php"); ?>
		            </div>
	                <div id="tabHistory">
	                    <? include("tabs/tabHistory.php"); ?>
	                </div>
	                <div id="tabControlPanel">
	                    <? include("tabs/tabControlPanel.php"); ?>
	                </div>
	                <div id="tabAdmin">
	                    <? include("tabs/tabAdmin.php"); ?>
	                </div>
	                <div id="tabLogs">
	                    <? include("tabs/tabLogs.php"); ?>
	                </div>
	                <?
	            }
            ?>
        </div>
        
        <div id="noChangeDialog" title="Commit Changes" style="font-size:12px" class="ui-dialog-content ui-widget-content"><p>It appears that you have not made any changes to your website.</p></div>        
        <div id="changeDialog" title="Commit Changes" style="font-size:12px" class="ui-dialog-content ui-widget-content">
            <p>Please check the your ammendments before comitting them to the live site<!--, you may preview the changes beforehand if you wish.--></p>
            <span id="commitSelectAll" class="selectAll">Select All</span><span id="commitDeselectAll" class="deselectAll">Clear All</span>
            <div id="sessionChanges" style="max-height:350px; overflow-y:scroll; border:#CCCCCC solid 1px; margin:10px 0"></div>
            <!--<span class="previewChanges">Preview Changes</span>-->
            <span id="commitChanges" class="commitChanges">Commit selected changes</span>
            <!--<span id="undoChanges" class="commitChanges">Undo selected changes</span> -->
        </div>
        
        <div id="passwordDialog" title="Change Password" style="font-size:12px" class="ui-dialog-content ui-widget-content">
        	<div id="pw1">
	        	<table>
	                <tr><td>Old password:</td><td><input id="oldPW" type="password" /></td></tr>
	                <tr><td>New password:</td><td><input id="newPW" type="password" /></td></tr>
	                <tr><td>Confirm new:</td><td><input id="newPW2" type="password" /></td></tr>
	            </table>
	            <div style="text-align:center"><span class="passwordChange" onclick="changePasswordAjax();">Change Password</span> &nbsp;<span class="passwordCancel" onclick='$( "#passwordDialog" ).dialog("close");'>Cancel</span></div>
	            <div id="pwMsg" style="display:none; text-align:center; color:#ff0000; position:relative; top:5px"></div>
	        </div>
	        <div id="pw2" style="display:none">
	        	<p>Your password was changed successfullly</p>
	        	<div style="text-align:center"><span class="passwordCancel" onclick='pwSuccess()'>Close</span></div>
	        </div>
        </div>
        
        <div id="resetDialog" title="About to log off . . ." style="font-size:12px" class="ui-dialog-content ui-widget-content">
			<p>You will be logged out after 30 minutes of inactivity.</p>
	    	<div style="text-align:center"><span class="resetTimer" onclick='logoutFn();'></span> &nbsp;<span class="resetCancel" onclick='resetTimer()'>Cancel</span></div>
        </div>

        
    </div>
    
    
<? 
require_once("footer.php"); 
?>