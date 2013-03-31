<?php 
require_once "../cms.php";

// Ensure the user is logged in
$sessionObject = new swSessionObject();
$sessionObject->redirectIfNotLoggedIn();

// first ensure we don't run out of memory
ini_set('memory_limit', '200M');

function _saveImage(swImage $image, $size)
{
	$filename =  dirname(__FILE__) . '/../img_dmp/' . $image->img_id . '_' . $size;
	$imageData = null;
	
	switch ($size)
	{
		case swImage::IMAGE_SIZE_THUMB:
			$imageData = imagecreatefromstring($image->img_data_thumb);
			break;
		case swImage::IMAGE_SIZE_PREVIEW:
			$imageData = imagecreatefromstring($image->img_data_preview);
			break;
		case swImage::IMAGE_SIZE_LARGE:
			$imageData = imagecreatefromstring($image->img_data_large);
			break;
		case swImage::IMAGE_SIZE_ORIGINAL:
			$imageData = imagecreatefromstring($image->img_data_original);
			break;
	}
	
	// Set up the appropriate image handling settings
	// based on the original image's mime type
	switch ($image->img_type)
	{
		case 'image/gif':
			$outputFunction		= 'ImagePng';
			break;

		case 'image/x-png':
		case 'image/png':
			$outputFunction		= 'ImagePng';
			break;

		default:
			$outputFunction	 	= 'ImageJpeg';
			break;
	}


	$outputFunction($imageData, $filename);
}

$images = swImage::getAllImages(true,true,false,true);

foreach ($images as $image)
{
	_saveImage($image, swImage::IMAGE_SIZE_THUMB);
	_saveImage($image, swImage::IMAGE_SIZE_PREVIEW);
	_saveImage($image, swImage::IMAGE_SIZE_LARGE);
	_saveImage($image, swImage::IMAGE_SIZE_ORIGINAL);
}


?>
<html>
<head><title></title></head>
<body>Exported <?php echo count($images); ?> images</body>
</html>

