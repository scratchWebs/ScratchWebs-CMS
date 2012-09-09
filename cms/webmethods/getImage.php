<?php
require_once("../cms.php");

if (isset($_GET['id'])) $img_id = $_GET['id'];
if (isset($_GET['size'])) $img_size = $_GET['size'];

$img_data = null;
$img_type = null;

// make sure the image id is available and numeric then get from the database/session
if(isset($img_id) && is_numeric($img_id)) {
	if (isset($_GET["fromSession"])) $loadFromSession = $_GET["fromSession"];
	
	if (isset($loadFromSession) && $loadFromSession == "true") {
		$sessionObject = new swSessionObject();

		foreach ($sessionObject->images as $img)		// get the image from the session (this will be because the image has been cropped but not yet added to the database)
		{
			if ($img->img_id == $img_id)
			{
				$img_type = $img->img_type;
				
				switch ($img_size) {
					case swImage::IMAGE_SIZE_THUMB:
						$img_data = $img->img_data_thumb;
						break 2;
					case swImage::IMAGE_SIZE_PREVIEW:
						$img_data = $img->img_data_preview;
						break 2;
					case swImage::IMAGE_SIZE_LARGE:
						$img_data = $img->img_data_large;
						break 2;
					case swImage::IMAGE_SIZE_ORIGINAL:
						$img_data = $img->img_data_original;
						break 2;
					default:
						$img_data = $img->img_data_preview;
						break 2;
				}
			}
		}
	} else {		// get the image from the db
		switch ($img_size) {
			case swImage::IMAGE_SIZE_THUMB:
				$ImgSizeParam = "img_data_thumb";
				break;
			case swImage::IMAGE_SIZE_PREVIEW:
				$ImgSizeParam = "img_data_preview";
				break;
			case swImage::IMAGE_SIZE_LARGE:
				$ImgSizeParam = "img_data_large";
				break;
			case swImage::IMAGE_SIZE_ORIGINAL:
				$ImgSizeParam = "img_data_original";
				break;
			default:
				$ImgSizeParam = "img_data_preview";
				break;
		}
		
		$sql = "SELECT " . $ImgSizeParam . ", img_type 
				FROM tblImages 
				WHERE img_id = " . $img_id . ";";
	
		// the result of the query
		$result = mysql_query("$sql");
		
		$img_data = mysql_result($result,0,0);
		$img_type = mysql_result($result,0,1);
	}
	
	
	
	// otherwise get the uploaded image from the session
} else if (isset($img_id))
{
	$sessionObject = new swSessionObject();

	foreach ($sessionObject->images as $img)
	{
		if ($img->img_id == $img_id)
		{
			$img_type = $img->img_type;
			
			switch ($img_size) {
				case swImage::IMAGE_SIZE_THUMB:
					$img_data = $img->img_data_thumb;
					break 2;
				case swImage::IMAGE_SIZE_PREVIEW:
					$img_data = $img->img_data_preview;
					break 2;
				case swImage::IMAGE_SIZE_LARGE:
					$img_data = $img->img_data_large;
					break 2;
				case swImage::IMAGE_SIZE_ORIGINAL:
					$img_data = $img->img_data_original;
					break 2;
				default:
					$img_data = $img->img_data_preview;
					break 2;
			}
		}
	}
}

if (isset($img_data) && isset($img_type)) {
	// set the header for the image
	header("Content-type: $img_type");
	echo $img_data;
}
?>