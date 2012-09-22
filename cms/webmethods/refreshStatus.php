<?php
require_once("../cms.php");

$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

$commit_form = '<form action="webmethods/commit.php" method="post">' . 
			   '<input type="hidden" name="commit_form" value="true" />';

foreach($sessionObject->sessionUpdates as $key => $sessionUpdate)
{
	$commit_form .= '<p style="margin:3px 0"><input type="checkbox" id="'.$key.'" name="'.$key.'" value="'.$key.'" checked="checked" />' .
					'<label for="'.$key.'">' . $sessionUpdate->getDesciption() . '</label>';
					
	//$commit_form = '<a href="#" style="float:right" onclick="' .
	
			// some php required to generate undo function
			
			// This is difficult to do/maintain as the code will be different for each object
			// Suggest that undo code gets standardised on the client first
			// IE: something like jsObject.undo(updateType); where jsObject 
			// is a client side object representing the page/section or whatever

			// Have disabled until this is done
	
	//				. '">undo</a></p>';
}

$commit_form .= '</form>';

// output this as xml so we can pass the numberOfChanges to the page. jQuery will extract the HTML.
header("content-type:application/xml;charset=utf-8 .xml");
echo '<?xml version="1.0"?>' .
	 '<sessionUpdate>' .
		 '<noOfChanges>' . count($sessionObject->sessionUpdates) . '</noOfChanges>' .
		 '<sessionChanges><![CDATA[' . $commit_form . ']]></sessionChanges>' .
	 '</sessionUpdate>';

?>