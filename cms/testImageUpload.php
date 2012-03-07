<?php
require("cms.php");

ini_set("memory_limit","100M"); // Max memory this script can use while in execution
ini_set('max_execution_time', 0); // Max execution time 300 seconds = 5 minutes

if ($_POST["form"] == "UploadImage") {
	_uploadImage();
}

$sql = "SELECT max(img_id) 
		FROM tblImages";
$result = mysql_query($sql);
$img_id = mysql_result($result,0,0);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload Document test</title>
<!-- Load Queue widget CSS and jQuery -->
<style type="text/css">@import url(/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css);</style>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>

<!-- Third party script for BrowserPlus runtime (Google Gears included in Gears runtime now) -->
<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>

<!-- Load plupload and all it's runtimes and finally the jQuery UI queue widget -->
<script type="text/javascript" src="/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js"></script>

</head>

<body>

<form method="post" action="" ENCTYPE="multipart/form-data">
<input type="hidden" name="form" value="UploadImage" />
<input type="file" name="myImage" /><br />
<input type="submit" value="upload image" />

</form>
<script type="text/javascript">
// Convert divs to queue widgets when the DOM is ready
$(function() {
	$("#uploader").plupload({
		// General settings
		runtimes : 'gears,flash,silverlight,browserplus,html5',
		url : 'upload.php',
		max_file_size : '10mb',
		chunk_size : '1mb',
		unique_names : true,

		// Resize images on clientside if we can
		resize : {width : 320, height : 240, quality : 90},

		// Specify what files to browse for
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"}
		],

		// Flash settings
		flash_swf_url : '/plupload/js/plupload.flash.swf',

		// Silverlight settings
		silverlight_xap_url : '/plupload/js/plupload.silverlight.xap'
	});

	// Client side form validation
	$('form').submit(function(e) {
        var uploader = $('#uploader').plupload('getUploader');

        // Files in queue upload them first
        if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    $('form')[0].submit();
                }
            });
                
            uploader.start();
        } else
            alert('You must at least upload one file.');

        return false;
    });
});
</script>

...

<form>
	<div id="uploader">
		<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
	</div>
</form>
			
<img src="getImage.php?id=<?= $img_id ?>&size=<?= swImage::IMAGE_SIZE_THUMB ?>" /><br />
<img src="getImage.php?id=<?= $img_id ?>&size=<?= swImage::IMAGE_SIZE_PREVIEW ?>" /><br />
<img src="getImage.php?id=<?= $img_id ?>&size=<?= swImage::IMAGE_SIZE_LARGE ?>" /><br />

</body>
</html>


<?

function _uploadImage() {
	
	$file = $_FILES['myImage'];
	
	$image = new swImage();
	
	$image->createFromUploadedFile($file['tmp_name'],$file['name']);
	
	$image->saveAsNew();
	
}