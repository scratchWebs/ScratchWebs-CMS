<?php
require_once("../cms.php");

// Ensure the user is logged in
$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

if ($_POST['resize'] == 'true')
{
	set_time_limit(0);
	
	echo "start - mem usage: " . memory_get_usage() / 1000000 . "MB <p />";
		
	$images = swImage::getAllImages(true);
	
	echo "fetched " . count($images) . " images - " . memory_get_usage() / 1000000 . "MB <p />";
	
	foreach ($images as $imgKey => $img)
	{
		$originalImage = $img->img_data_original;
		
		//$img->img_data_thumb = swImage::resizeImageFromData($originalImage,$img->img_type,170,100);
		//$img->img_data_preview = swImage::resizeImageFromData($originalImage,$img->img_type,280,350);
		$img->img_data_large = swImage::resizeImageFromData($originalImage,$img->img_type,610,350);

		//$img->update();
		
		// get the new width/height
		$img->img_data_large = imagecreatefromstring($img->img_data_large);
		$w = imagesx($img->img_data_large);
		$h = imagesy($img->img_data_large);
		
		$img->img_data_original = '';
		$img->img_data_large = '';
		$img->img_data_preview = '';
		$img->img_data_thumb = '';
		
		if ($w > 610 || $h > 350) echo '<b style="color:#f00">';
		echo "w:" . $w . " h:" . $h . " - ";
		echo $imgKey . ": " . $img->img_name;
		
		//echo "Mem usage: " . memory_get_usage()  / 1000000 . "MB";
		//echo "<img src=\"data:" . $img->img_type . ";base64," . base64_encode($img->img_data_original) . "\" />";
		
		if ($w > 670 || $h > 350) echo "</b>";
		echo '<br />';
		
		unset($images[$imgKey]);
	}
	
	echo "<p /> finnished - " . memory_get_usage()  / 1000000 . "MB";
} else {
?>
<html>
<head>
<title></title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
</head>
<body>
<script type="text/javascript">
function resize()
{
	$('#divStatus').html('loading... please wait');
	
	$.ajax({
		url: "refreshImageSizes.php",
		data:{
			resize:'true'
		},
		type:'post',
		success: function(response){
			$('#divStatus').html('response:' + response);
		},
		error: function(error){
			$('#divStatus').html('error: ' + error.statusText + '<div>' + error.responseText + '</div>');
		}
	});
}
</script>
<h3>Refresh Images Sizes</h3>
<a href="#" onclick="resize();return false">Resize</a>
<div id="divStatus"></div>
</body>
</html>
<? } ?>