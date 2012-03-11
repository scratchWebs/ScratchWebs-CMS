<?php

class swImage extends dbObject
{
	const UID = "img_";
	
	const IMAGE_SIZE_THUMB = 1;
	const IMAGE_SIZE_PREVIEW = 2;
	const IMAGE_SIZE_LARGE = 3;
	const IMAGE_SIZE_ORIGINAL = 4;
	
	const IMAGE_DEFAULT_QUALITY = 80;
	
	public $img_id;
	public $img_code_ref = "";
	public $img_name;
	public $img_desc_short;
	public $img_desc_long;
	public $img_width;
	public $img_height;
	public $img_type;
	public $img_data_thumb;
	public $img_data_preview;
	public $img_data_large;
	public $img_data_original;
	public $img_featured = 0;
	public $img_order = 0;
	public $img_internal_count = 0;
	public $img_external_count = 0;
	public $img_fk_gallery_id;
	public $img_fk_section_id;
	public $img_fk_pg_id;
	
	public function getUID() {
		return self::UID . $this->img_id;
	}
	public function getObjectID()
	{
		return $this->img_id;
	}
	public function getObjectType()
	{
		return dbObject::OBJECT_TYPE_IMAGE;
	}
	public function getImageSrc($size = self::IMAGE_SIZE_THUMB,$relativePathToCMS = "/cms/")
	{
		$param = "";
		// This switch statement is to load the image from the session (if it exists)
		switch ($size) {
			case self::IMAGE_SIZE_THUMB:
				if ($this->img_data_thumb != NULL) $param = "&fromSession=true";
				break;
			case self::IMAGE_SIZE_PREVIEW:
				if ($this->img_data_preview != NULL) $param = "&fromSession=true";
				break;
			case self::IMAGE_SIZE_LARGE:
				if ($this->img_data_large != NULL) $param = "&fromSession=true";
				break;
			case self::IMAGE_SIZE_ORIGINAL:
				if ($this->img_data_original != NULL) $param = "&fromSession=true";
				break;
		}
		return $relativePathToCMS . "getImage.php?id=" . $this->img_id . "&size=" . $size . $param;
	}
	public static function getSrc($img_id,$size = self::IMAGE_SIZE_THUMB,$relativePathToCMS = "/cms/")
	{
		return $relativePathToCMS . "getImage.php?id=" . $img_id . "&size=" . $size;
	}
	public static function getOriginalSrc($image_id,$relativePathToCMS = "/cms/")
	{
		return $relativePathToCMS . "getImage.php?id=" . $image_id . "&size=" . swImage::IMAGE_SIZE_ORIGINAL;
	}
	public function isFirstImage()
	{
		return ($this->img_order == 0) ? true : false;
	}
	public function getImageNameWithoutExtension()
	{	
		$info = pathinfo($this->img_name);
		return $info['filename'];
	}
	public function createFromUploadedFile($tmpFilePath,$name)
	{
		// check to see if an image has been uploaded
		if (is_uploaded_file($tmpFilePath))
		{
			$imgSize = getimagesize($tmpFilePath);
			
			$this->img_name = $name;
			$this->img_width = $imgSize[0];			// original width & height in html format
			$this->img_height = $imgSize[1];		// original width & height in html format
			$this->img_type = $imgSize['mime'];		// mime type (for use in response header)
			
			$this->img_data_original = file_get_contents($tmpFilePath);
			$this->img_data_thumb = self::resizeImageFromFile($tmpFilePath,100,170);
			$this->img_data_preview = self::resizeImageFromFile($tmpFilePath,280,350);
			$this->img_data_large = self::resizeImageFromFile($tmpFilePath,670,350);
			
			$this->img_id = uniqid();			// create a unique reference so the image can be loaded from the session
		}
	}
	
	public static function resizeImageFromData($imageData,$mimeType,$maxWidth,$maxHeight,$quality = self::IMAGE_DEFAULT_QUALITY)
	{
		if (gettype($imageData) == string) $imageData = imagecreatefromstring($imageData); // ensure the data from the database has been converted to a php resource
		
		// get the current width/height
		$originalWidth = imagesx($imageData);
		$originalHeight = imagesy($imageData);
		
		$newWidth = $originalWidth;
		$newHeight = $originalHeight;
		
		// calculate the new resized width/height
		if ($originalWidth > $maxWidth) {
			$newHeight = $newHeight * ($maxWidth/$newWidth);
			$newWidth = $maxWidth;
		}
		
		if ($newHeight > $maxHeight) {
			$newWidth = $newWidth * ($maxHeight/$newHeight);
			$newHeight = $maxHeight;
		}
		
		return self::_createImageFromData($imageData,$mimeType,$newWidth,$newHeight,
										  0,0,$originalWidth,$originalHeight,$quality);
	}
	public static function resizeImageFromFile($pathToFile,$maxWidth,$maxHeight,$quality = self::IMAGE_DEFAULT_QUALITY)
	{	
		// get the current width/height
		$originalImgSize = getimagesize($pathToFile);
		list($originalWidth, $originalHeight) = $originalImgSize;
		
		$newWidth = $originalWidth;
		$newHeight = $originalHeight;
		
		// calculate the new resized width/height
		if ($originalWidth > $maxWidth) {
			$newHeight = $newHeight * ($maxWidth/$newWidth);
			$newWidth = $maxWidth;
		}
		
		if ($newHeight > $maxHeight) {
			$newWidth = $newWidth * ($maxHeight/$newHeight);
			$newHeight = $maxHeight;
		}
		
		return self::_createImageFromFile($pathToFile,$newWidth,$newHeight,
										  0,0,$originalWidth,$originalHeight,$quality);
	}
	
	public static function cropImageFromFile($pathToFile,$cropW,$cropH,
									 		 $selectionX,$selectionY,$selectionW,$selectionH,$quality = self::IMAGE_DEFAULT_QUALITY)
	{
		return self::_createImageFromFile($pathToFile,$cropW,$cropH,
										  $selectionX,$selectionY,$selectionW,$selectionH,$quality);
	}
	public static function cropImageFromData($imageData,$mimeType,$cropW,$cropH,
										 	 $selectionX,$selectionY,$selectionW,$selectionH,$quality = self::IMAGE_DEFAULT_QUALITY)
	{
		if (gettype($imageData) == string) $imageData = imagecreatefromstring($imageData); // ensure the data from the database has been converted to a php resource
		
		return self::_createImageFromData($imageData,$mimeType,$cropW,$cropH,
										  $selectionX,$selectionY,$selectionW,$selectionH,$quality);
	}
	
	private static function _createImageFromFile($pathToFile,$dest_W,$dest_H,
									 			 $source_X,$source_Y,$source_W,$source_H,$quality = self::IMAGE_DEFAULT_QUALITY)
	{
		// first ensure we don't run out of memory
		ini_set('memory_limit', '200M');
		
		$originalImgSize = getimagesize($pathToFile);
		
		// Set up the appropriate image handling settings 
		// based on the original image's mime type
		switch ($originalImgSize['mime'])
		{
			case 'image/gif':
				$creationFunction	= 'ImageCreateFromGif';
				$outputFunction		= 'ImagePng';
				$mime				= 'image/png'; // We need to convert GIFs to PNGs to avoid transparency issues
				$doSharpen			= FALSE;
				$quality			= round(10 - ($quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
			break;
			
			case 'image/x-png':
			case 'image/png':
				$creationFunction	= 'ImageCreateFromPng';
				$outputFunction		= 'ImagePng';
				$doSharpen			= FALSE;
				$quality			= round(10 - ($quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
			break;
			
			default:
				$creationFunction	= 'ImageCreateFromJpeg';
				$outputFunction	 	= 'ImageJpeg';
				$doSharpen			= TRUE;
			break;
		}
		
		// Set up a blank canvas for the resized image
		$canvas = imagecreatetruecolor($dest_W, $dest_H);
				
		// If this is a GIF or a PNG, we need to set up transparency
		if (in_array($originalImgSize['mime'], array('image/gif', 'image/png'))) {
			imagealphablending($canvas, false);
			imagesavealpha($canvas, true);
		}

		// load the file into memory
        $originalImgData = $creationFunction($pathToFile);
		
		// Resample the original image into the resized canvas
		imagecopyresampled($canvas, $originalImgData, 0, 0, $source_X, $source_Y, $dest_W, $dest_H, $source_W, $source_H);
		
		// Sharpen the image based on two things:
		if ($doSharpen) {
			$sharpness	= self::_findSharp($source_W, $dest_W);
			
			$sharpenMatrix = array(
				array(-1, -2, -1),
				array(-2, $sharpness + 12, -2),
				array(-1, -2, -1)
			);
			$divisor = $sharpness;
			imageconvolution($canvas, $sharpenMatrix, $divisor, 0);
		}
		
		// Write the resized image to the cache
		ob_start();
		$outputFunction($canvas, NULL, $quality);
		$resizedImage = ob_get_contents();
		ob_end_clean();
		
		// clean up the memory
		imagedestroy($canvas);
		
		return $resizedImage;
	}
	
	private static function _createImageFromData($imageData,$mimeType,$dest_W,$dest_H,
												 $source_X,$source_Y,$source_W,$source_H,$quality = self::IMAGE_DEFAULT_QUALITY)
	{
		// first ensure we don't run out of memory
		ini_set('memory_limit', '200M');
		
		// Set up the appropriate image handling settings 
		// based on the original image's mime type
		switch ($mimeType)
		{
			case 'image/gif':
				$outputFunction		= 'ImagePng';
				$mime				= 'image/png'; // We need to convert GIFs to PNGs to avoid transparency issues
				$doSharpen			= FALSE;
				$quality			= round(10 - ($quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
			break;
			
			case 'image/x-png':
			case 'image/png':
				$outputFunction		= 'ImagePng';
				$doSharpen			= FALSE;
				$quality			= round(10 - ($quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
			break;
			
			default:
				$outputFunction	 	= 'ImageJpeg';
				$doSharpen			= TRUE;
			break;
		}
		
		// Set up a blank canvas for the resized image
		$canvas = imagecreatetruecolor($dest_W, $dest_H);
				
		// If this is a GIF or a PNG, we need to set up transparency
		if (in_array($mimeType, array('image/gif', 'image/png'))) {
			imagealphablending($canvas, false);
			imagesavealpha($canvas, true);
		}

		// Resample the original image into the resized canvas
		imagecopyresampled($canvas, $imageData, 0, 0, $source_X, $source_Y, $dest_W, $dest_H, $source_W, $source_H);
		
		// Sharpen the image based on two things:
		if ($doSharpen) {
			$sharpness	= self::_findSharp($source_W, $dest_W);
			
			$sharpenMatrix = array(
				array(-1, -2, -1),
				array(-2, $sharpness + 12, -2),
				array(-1, -2, -1)
			);
			$divisor = $sharpness;
			imageconvolution($canvas, $sharpenMatrix, $divisor, 0);
		}
		
		// Write the resized image to the cache
		ob_start();
		$outputFunction($canvas, NULL, $quality);
		$resizedImage = ob_get_contents();
		ob_end_clean();
		
		// clean up the memory
		imagedestroy($canvas);
		
		return $resizedImage;
	}
	
	private static function _findSharp($orig, $final)
	{
		$final	= $final * (750.0 / $orig);
		$a		= 52;
		$b		= -0.27810650887573124;
		$c		= .00047337278106508946;
		
		$result = $a + $b * $final + $c * $final * $final;
		
		return max(round($result), 0);
	}
	
	public function saveAsNew()
	{
		$fields = "";
		if (isset($this->img_fk_gallery_id)) $fields = ",img_fk_gallery_id";
		if (isset($this->img_fk_section_id)) $fields .= ",img_fk_section_id";
		if (isset($this->img_fk_pg_id)) $fields .= ",img_fk_pg_id";
		
		$values = "";
		if (isset($this->img_fk_gallery_id)) $values .= "," . $this->img_fk_gallery_id;
		if (isset($this->img_fk_section_id)) $values .= "," . $this->img_fk_section_id;
		if (isset($this->img_fk_pg_id)) $values .= "," . $this->img_fk_pg_id;
		
		$sql = "INSERT INTO tblImages
                	(delete_flag,
					 enabled,
					 img_name,
					 img_code_ref,
					 img_desc_long,
					 img_desc_short,
					 img_width, 
					 img_height, 
					 img_type, 
					 img_data_thumb, 
					 img_data_preview, 
					 img_data_large, 
					 img_data_original, 
					 img_featured, 
					 img_order, 
					 img_internal_count, 
					 img_external_count 
					 $fields)
                VALUES
                	(" . (int) $this->delete_flag . ",
					 " . (int) $this->enabled . ",
					 '" . mysql_real_escape_string(substr($this->img_name,0,100)) ."',
					 '" . mysql_real_escape_string(substr($this->img_code_ref,0,30)) ."',
					 '" . mysql_real_escape_string(substr($this->img_desc_long,0,500)) ."',
					 '" . mysql_real_escape_string(substr($this->img_desc_short,0,1000)) ."',
					 " . $this->img_width .",
					 " . $this->img_height .",
					 '" . mysql_real_escape_string($this->img_type) ."',
					 '" . addslashes($this->img_data_thumb) ."',
					 '" . addslashes($this->img_data_preview) ."',
					 '" . addslashes($this->img_data_large) ."',
					 '" . addslashes($this->img_data_original) ."',
					 " . (int) $this->img_featured . ",
					 " . $this->img_order . ",
					 " . $this->img_internal_count . ",
					 " . $this->img_external_count . "
					 $values);";
					
		mysql_query($sql) or die(mysql_error());
		
		$this->img_id = mysql_insert_id();
		$this->is_new = false;
		$this->has_changed = false;
	}
	public function update()
	{
		$success = false;
		
		if ($this->img_id !== NULL)
		{
			$params = "";
			if ($this->img_fk_gallery_id != "") $params = ",img_fk_gallery_id = " . $this->img_fk_gallery_id;
			if ($this->img_fk_section_id != "") $params .= ",img_fk_section_id = " . $this->img_fk_section_id;
			if ($this->img_fk_pg_id != "") $params .= ",img_fk_pg_id = " . $this->img_fk_pg_id;
			
			$sql = "UPDATE tblImages 
						SET delete_flag = " . (int) $this->delete_flag . ",
							enabled = " . (int) $this->enabled . ",
							img_name = '" . mysql_real_escape_string(substr($this->img_name,0,100)) . "',
							img_code_ref = '" . mysql_real_escape_string(substr($this->img_code_ref,0,30)) . "',
							img_desc_long = '" . mysql_real_escape_string(substr($this->img_desc_long,0,500)) . "',
							img_desc_short = '" . mysql_real_escape_string(substr($this->img_desc_short,0,1000)) . "',
							img_width = " . $this->img_width . ",
							img_height = " . $this->img_height . ",
							img_type = '" . mysql_real_escape_string($this->img_type) . "',
							" . self::_getUpdateImageParam($this->img_data_thumb, self::IMAGE_SIZE_THUMB) . "
							" . self::_getUpdateImageParam($this->img_data_preview, self::IMAGE_SIZE_PREVIEW) . "
							" . self::_getUpdateImageParam($this->img_data_large, self::IMAGE_SIZE_LARGE) . "
							" . self::_getUpdateImageParam($this->img_data_original, self::IMAGE_SIZE_ORIGINAL) . "
							img_featured = " . (int) $this->img_featured . ",
							img_order = " . $this->img_order . ",
							img_internal_count = " . $this->img_internal_count . ",
							img_external_count = " . $this->img_external_count . "
							$params
					WHERE img_id = " . $this->img_id . ";"; 
			
			mysql_query($sql) or die(mysql_error());
				$success =  true;
		}
		return $success;
	}
	private function _getUpdateImageParam($imageData,$imageSize)
	{
		$param = "";
		
		if (isset($imageData) && $imageData != "") {
			switch ($imageSize) {
				case self::IMAGE_SIZE_THUMB:
					$param = "img_data_thumb = '" . addslashes($imageData) . "',";
					break;
				case self::IMAGE_SIZE_PREVIEW:
					$param = "img_data_preview = '" . addslashes($imageData) . "',";
					break;
				case self::IMAGE_SIZE_LARGE:
					$param = "img_data_large = '" . addslashes($imageData) . "',";
					break;
				case self::IMAGE_SIZE_ORIGINAL:
					$param = "img_data_original = '" . addslashes($imageData) . "',";
					break;
			}
		}
		return $param;
	}
	private static function getfieldsWithoutData()
	{
		$fields = "delete_flag,
					enabled,
					img_id,
					img_name,
					img_code_ref,
					img_desc_long,
					img_desc_short,
					img_width, 
					img_height, 
					img_type, 
					img_featured, 
					img_order, 
					img_internal_count, 
					img_external_count,
					img_fk_gallery_id,
					img_fk_section_id,
					img_fk_pg_id";
					 
		return $fields;
	}
	public function createFromId($id)
	{
		$sql = "SELECT " . self::getfieldsWithoutData() . "
				FROM tblImages
				WHERE img_id = $id;";
				
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createImageFromSQLData($data);
			
			return true;
		} else {
			return false;
		}
	}
	static function getImagesForGallery($gallery_id,$featuredOnly = false,$enabledOnly = true)
	{
		$images = array();
		
		if ($featuredOnly) $featuredParam = "AND img_featured = 1";
		if ($enabledOnly) $enabledParam = "AND enabled = 1";
		
		$sql = "SELECT " . self::getfieldsWithoutData() . " 
				FROM tblImages
				WHERE img_fk_gallery_id = $gallery_id
					  $featuredParam
					  $enabledParam
					AND delete_flag = 0
				ORDER BY img_order ASC;";
		
		$result = mysql_query($sql);
		
		while (($data = mysql_fetch_array($result)) == true)
		{
			$image = new swImage();
			$image->createImageFromSQLData($data);
			
			array_push($images,$image);
		}
		
		return $images;
	}
	static function getAllImages($getOriginalData = false,$enabled = true,$deleted = false)
	{
		$images = array();
		
		if ($getOriginalData) {
			ini_set('memory_limit', '200M');
			$paramiter = ",img_data_original ";
		} else $paramiter = "";
		
		$sql = "SELECT " . self::getfieldsWithoutData() . $paramiter . " 
				FROM tblImages
				WHERE enabled = " . (int) $enabled . "
					AND delete_flag = " . (int) $deleted . "
				ORDER BY img_order ASC;";
		
		$result = mysql_query($sql);
		
		while (($data = mysql_fetch_array($result)) == true)
		{
			$image = new swImage();
			$image->createImageFromSQLData($data);
			$images[$image->img_id] = $image;
		}
		
		return $images;
	}
	public function createImageFromSQLData($data)
	{
		$this->delete_flag = $data["delete_flag"];
		$this->enabled = $data["enabled"];
		$this->img_id = $data["img_id"];
		$this->img_code_ref = $data["img_code_ref"];
		$this->img_name = $data["img_name"];
		$this->img_desc_short = $data["img_desc_short"];
		$this->img_desc_long = $data["img_desc_long"];
		$this->img_width = $data["img_width"];
		$this->img_height = $data["img_height"];
		$this->img_type = $data["img_type"];
		$this->img_featured = $data["img_featured"];
		$this->img_order = $data["img_order"];
		$this->img_internal_count = $data["img_internal_count"];
		$this->img_external_count = $data["img_external_count"];
		$this->img_fk_gallery_id = $data["img_fk_gallery_id"];
		$this->img_fk_section_id = $data["img_fk_section_id"];
		$this->img_fk_pg_id = $data["img_fk_pg_id"];
		
		if (isset($data["img_data_thumb"])) $this->img_data_thumb = $data["img_data_thumb"];
		if (isset($data["img_data_preview"])) $this->img_data_preview = $data["img_data_preview"];
		if (isset($data["img_data_large"])) $this->img_data_large = $data["img_data_large"];
		if (isset($data["img_data_original"])) $this->img_data_original = $data["img_data_original"];
	}
	public function getTableName()
	{
		return 'tblImages';
	}
	public function createTable()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `tblImages` (
					  `delete_flag` tinyint(1) NOT NULL default '0',
					  `enabled` tinyint(1) NOT NULL default '1',
					  `img_id` int(11) NOT NULL auto_increment,
					  `img_code_ref` varchar(30) NOT NULL,
					  `img_name` varchar(100) NOT NULL,
					  `img_desc_short` varchar(500) NOT NULL,
					  `img_desc_long` varchar(1000) NOT NULL,
					  `img_width` int(5) NOT NULL,
					  `img_height` int(5) NOT NULL,
					  `img_type` varchar(25) NOT NULL,
					  `img_data_thumb` longblob NOT NULL,
					  `img_data_preview` longblob NOT NULL,
					  `img_data_large` longblob NOT NULL,
					  `img_data_original` longblob NOT NULL,
					  `img_featured` tinyint(1) NOT NULL,
					  `img_order` int(11) NOT NULL,
					  `img_internal_count` int(11) NOT NULL default '0',
					  `img_external_count` int(11) NOT NULL default '0',
					  `img_fk_gallery_id` int(11) NULL,
					  `img_fk_section_id` int(11) NULL,
					  `img_fk_pg_id` int(11) NULL,
					  PRIMARY KEY  (`img_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
				
		if (mysql_query($sql))
			return true;
		else
			return false;
	}
	
}	