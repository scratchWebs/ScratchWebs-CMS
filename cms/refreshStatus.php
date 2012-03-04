<?php
require_once("cms.php");

$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

$commit_form = '<form action="commit.php" method="post">' . 
			   '<input type="hidden" name="commit_form" value="true" />';

foreach($sessionObject->sessionUpdates as $key => $sessionUpdate)
{
	$commit_form .= '<p style="margin:3px 0"><input type="checkbox" id="'.$key.'" name="'.$key.'" value="'.$key.'" checked="checked" />' .
					'<label for="'.$key.'">' . $sessionUpdate->getDesciption() . '</label></p>';
}

$commit_form .= '</form>';

// output this as xml so we can pass the numberOfChanges to the page. jQuery will extract the HTML.
echo '<?xml version="1.0"?>' .
	 '<sessionUpdate>' .
		 '<noOfChanges>' . count($sessionObject->sessionUpdates) . '</noOfChanges>' .
		 '<sessionChanges><![CDATA[' . $commit_form . ']]></sessionChanges>' .
	 '</sessionUpdate>';

header("content-type:application/xml;charset=utf-8 .xml");
?>