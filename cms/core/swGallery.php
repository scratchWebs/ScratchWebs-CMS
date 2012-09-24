<?php

class swGallery extends swFeature
{
	const UID = "g_";
	
	public $gallery_id;
	public $gallery_name = "";
	public $gallery_code_ref = "";
	public $gallery_desc_short = "";
	public $gallery_desc_long = "";
	public $gallery_order = 0;
	public $gallery_featured = true;
	public $gallery_internal_count = 0;
	public $gallery_external_count = 0;
	public $gallery_fk_pg_id;
	public $gallery_fk_portfolio_id;
	
	public $gallery_images = array();
	public $gallery_type = "Gallery";
	
	public function event_sessionUpdated(swSessionUpdate $sessionUpdate,swSessionObject $sessionObject)
	{
		if ($sessionUpdate->is_delete)
		{
			// we are deleting the gallery. Undo any session updates to the images inside this gallery
			foreach ($sessionObject->sessionUpdates as $update)
				if (get_class($update->update_object) == 'swImage')
				{
					$image = $this->getImageFromId($update->update_object->img_id);
					if (isset($image)) $update->undo($sessionObject);
				}
		}
	}
	public function isFirstGallery()
	{
		return ($this->gallery_order == 0) ? true : false;
	}
	public function noUpdates()
	{
		$noUpdates = count($this->sessionUpdates);
		
		foreach ($this->gallery_images as $img)
			$noUpdates += $img->noUpdates();
		
		return $noUpdates;
	}
	public function getObjectType()
	{
		return dbObject::OBJECT_TYPE_GALLERY;
	}
	public function getObjectID()
	{
		return $this->gallery_id;
	}
	public function getUID()
	{
		return self::UID . $this->gallery_id;
	}
	public function getFeatureType()
	{
		return swFeature::FEATURE_TYPE_GALLERY;
	}
	public function createFromId($id,$getImages = false)
	{
		$sql = "SELECT * 
				FROM tblGalleries
				WHERE gallery_id = $id;";
				
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createGalleryFromSQLData($data,$getImages);
			
			return true;
		} else {
			return false;
		}
	}
	static function getGalleriesForPage($pg_id)
	{
		$galleries = array();
		
		$sql = "SELECT * 
				FROM tblGalleries
				WHERE gallery_fk_pg_id = $pg_id
					AND delete_flag = 0
					AND enabled = 1
				ORDER BY gallery_order ASC";
		
		$result = mysql_query($sql);
		
		while (($data = mysql_fetch_array($result)) == true) {
			
			$gallery = new swGallery();
			$gallery->createGalleryFromSQLData($data,false);
			
			$galleries[$gallery->gallery_code_ref] = $gallery;
		}
		
		return $galleries;
		
	}
	static function getGalleriesForPortfolio($portfolio_id,$featuredOnly = false,$getImages = false)
	{
		$galleries = array();
		
		// the following sql only get's featured galleries if they have images
		if ($featuredOnly) {
			$sql = "SELECT tblGalleries.*, noOfImages.imageCount
					FROM tblGalleries
						JOIN (SELECT img_fk_gallery_id, count(*) as imageCount
								FROM tblImages
								WHERE img_featured = 1
								Group By img_fk_gallery_id) as noOfImages
							ON noOfImages.img_fk_gallery_id = tblGalleries.gallery_id
					WHERE gallery_fk_portfolio_id = $portfolio_id
						AND gallery_featured = 1
						AND imageCount > 0
						AND delete_flag = 0
						AND enabled = 1
					ORDER BY gallery_order ASC;";
		} else {
			$sql = "SELECT tblGalleries.*
					FROM tblGalleries
					WHERE gallery_fk_portfolio_id = $portfolio_id
						AND delete_flag = 0
					ORDER BY gallery_order ASC;";
		}
		
		$result = mysql_query($sql);
		
		while (($data = mysql_fetch_array($result)) == true) {
			$gallery = new swGallery();
			$gallery->createGalleryFromSQLData($data,$getImages,$featuredOnly);
			
			$galleries[$gallery->gallery_id] = $gallery;
		}
		
		return $galleries;
	}
	public function createGalleryFromSQLData($data,$getImages = true,$getFeatured = false)
	{
		$this->delete_flag = (bool) $data["delete_flag"];
		$this->enabled = (bool) $data["enabled"];
		$this->gallery_id = $data["gallery_id"];
		$this->gallery_name = $data["gallery_name"];
		$this->gallery_code_ref = $data["gallery_code_ref"];
		$this->gallery_desc_short = $data["gallery_desc_short"];
		$this->gallery_desc_long = $data["gallery_desc_long"];
		$this->gallery_order = $data["gallery_order"];
		$this->gallery_featured = $data["gallery_featured"];
		$this->gallery_internal_count = $data["gallery_internal_count"];
		$this->gallery_external_count = $data["gallery_external_count"];
		$this->gallery_fk_pg_id = $data["gallery_fk_pg_id"];
		$this->gallery_fk_portfolio_id = $data["gallery_fk_portfolio_id"];
		
		if ($getImages)
			$this->gallery_images = swImage::getImagesForGallery($this->gallery_id,$getFeatured);
		
	}
	public function addImage($image)
	{	
		$image->img_fk_gallery_id = $this->gallery_id;		// link the image to the gallery
		$image->img_order = count($this->gallery_images);	// add image to end of gallery
		$this->gallery_images[$image->img_id] = $image;		// add to image collectino
	}
	public function linkImages($images)
	{
		if (!isset($images))
			$this->gallery_images = swImage::getImagesForGallery($this->gallery_id);
		else
			foreach ($images as $img)
				if ($img->img_fk_gallery_id == $this->gallery_id)
					$this->gallery_images[$img->img_id] = $img;
	}
	public function getImageFromId($imageID)
	{
		foreach ($this->gallery_images as $img) {
			if ($img->img_id == $imageID) return $img;
		}
		return NULL;
	}
	public function removeImageById($imageID)
	{
		unset($this->gallery_images[$imageID]);
	}
	public function getFeaturedImages()
	{
		$featured_images = array();
		
		foreach ($this->gallery_images as $img) {
			if ($img->img_featured) array_push($featured_images,$img);
		}
		
		return $featured_images;
	}
	public function getFeaturedImage()
	{
		foreach ($this->gallery_images as $img) {
			if ($img->img_featured) return $img;
		}
		// there is no featured image specified... use the first image in the gallery
		if (count($this->gallery_images) > 0) return $this->gallery_images[0];
	}
	public function setFeaturedImage($imageID)
	{
		foreach ($this->gallery_images as $img)
		{
			if ($img->img_id == $imageID)
				$img->img_featured = true;
			elseif ($img->img_featured) {
				$img->img_featured = false;
			}
		}
	}
	public function generateMetaDescription()
	{
		$meta = $this->gallery_name;
		if ($this->gallery_desc_short != "") $meta .= " - " . $this->gallery_desc_short;
		if ($this->gallery_desc_long != "") $meta .= " - " . $this->gallery_desc_long;
		return $meta;
	}
	public function generateMetaKeywords()
	{
		$meta = $this->gallery_name;
		if ($this->gallery_desc_short != "") $meta .= " - " . $this->gallery_desc_short;
		if ($this->gallery_desc_long != "") $meta .= " - " . $this->gallery_desc_long;
		return str_replace(",,",",",str_replace(" ",",",$meta));
	}
	public function sortImages()
	{
		function cmp( $a, $b )
		{ 
		  if(  $a->img_order ==  $b->img_order ){ return 0 ; } 
		  return ($a->img_order < $b->img_order) ? -1 : 1;
		} 
		
		uasort($this->gallery_images,'cmp');
	}
	public function saveAsNew()
	{
		$fields = "";
		if (isset($this->gallery_fk_pg_id)) $fields = ",gallery_fk_pg_id";
		if (isset($this->gallery_fk_portfolio_id)) $fields .= ",gallery_fk_portfolio_id";
		
		$values = "";
		if (isset($this->gallery_fk_pg_id)) $values .= "," . $this->gallery_fk_pg_id;
		if (isset($this->gallery_fk_portfolio_id)) $values .= "," . $this->gallery_fk_portfolio_id;
		
		$sql = "INSERT INTO tblGalleries 
						(delete_flag,
						 enabled,
						 gallery_name,
						 gallery_code_ref,
						 gallery_desc_short,
						 gallery_desc_long,
						 gallery_order,
						 gallery_featured,
						 gallery_internal_count,
						 gallery_external_count
						 $fields) 
					VALUES 
						(" . (int) $this->delete_flag . ",
						 " . (int) $this->enabled . ",
						 '" . mysql_real_escape_string(substr($this->gallery_name,0,100)) . "',
						 '" . mysql_real_escape_string(substr($this->gallery_code_ref,0,30)) . "',
						 '" . mysql_real_escape_string(substr($this->gallery_desc_short,0,500)) . "',
						 '" . mysql_real_escape_string(substr($this->gallery_desc_long,0,1000)) . "',
						 " . $this->gallery_order . ",
						 " . (int) $this->gallery_featured . ",
						 " . $this->gallery_internal_count . ",
						 " . $this->gallery_internal_count . "
						 $values);";
						 
		mysql_query($sql) or die(mysql_error());
		
		// store the new ID
		$this->gallery_id = mysql_insert_id();
		
		// update images with new id
		foreach ($this->gallery_images as $image)
			$image->img_fk_gallery_id = $this->gallery_id;
	}
	public function update()
	{
		if ($this->gallery_id !== NULL)
		{
			$param = "";
			if (isset($this->gallery_fk_pg_id)) $param = ",gallery_fk_pg_id = " . $this->gallery_fk_pg_id;
			if (isset($this->gallery_fk_portfolio_id)) $param .= ",gallery_fk_portfolio_id = " . $this->gallery_fk_portfolio_id;
			
			$sql = "UPDATE tblGalleries 
						SET delete_flag = '" . (int) $this->delete_flag . "',
							enabled = '" . (int) $this->enabled . "',
							gallery_name = '" . mysql_real_escape_string(substr($this->gallery_name,0,100)) . "',
							gallery_code_ref = '" . mysql_real_escape_string(substr($this->gallery_code_ref,0,30)) . "',
							gallery_desc_short = '" . mysql_real_escape_string(substr($this->gallery_desc_short,0,500)) . "',
							gallery_desc_long = '" . mysql_real_escape_string(substr($this->gallery_desc_long,0,1000)) . "',
							gallery_order = " . $this->gallery_order . ",
							gallery_featured = " . (int) $this->gallery_featured . ",
							gallery_internal_count = " . $this->gallery_internal_count . ",
							gallery_internal_count = " . $this->gallery_internal_count . "
							$param
					WHERE gallery_id = " . $this->gallery_id . ";"; 
							 
			mysql_query($sql) or die(mysql_error());
		}
	}
	public function getTableName()
	{
		return 'tblGalleries';
	}
	public function createTable()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `tblGalleries` (
					  `delete_flag` tinyint(1) NOT NULL default '0',
					  `enabled` tinyint(1) NOT NULL default '1',
					  `gallery_id` int(11) NOT NULL auto_increment,
					  `gallery_code_ref` varchar(30) NOT NULL,
					  `gallery_name` varchar(100) NOT NULL,
					  `gallery_desc_short` varchar(500) NOT NULL,
					  `gallery_desc_long` varchar(1000) NOT NULL,
					  `gallery_order` int(11) NOT NULL,
					  `gallery_featured` tinyint(1) NOT NULL,
					  `gallery_internal_count` int(11) NOT NULL,
					  `gallery_external_count` int(11) NOT NULL,
					  `gallery_fk_pg_id` int(11) NULL,
					  `gallery_fk_portfolio_id` int(11) NULL,
					  PRIMARY KEY  (`gallery_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
				
		if (mysql_query($sql))
			return true;
		else
			return false;
	}
}

?>