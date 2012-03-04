<?php
require_once("cms.php");

//$custom_footer = PATH_HTML . "footer.php";

//if (file_exists($custom_footer)) {
//	$CMSMode = true;
//	require_once($custom_footer);
//} else {

?>
</body>
</html>
<?
//}
// ensure we save any changes to the session object
$sessionObject->save();
?>