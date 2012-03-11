<?php

class swPortfolio extends dbObject
{
	const UID = "pf";
	
	public $portfolio_id;
	public $portfolio_name = "";
	public $portfolio_gallery_rename = "";
	public $portfolio_order = 0;
	public $portfolio_featured = true;
	public $portfolio_internal_count = 0;
	public $portfolio_external_count = 0;
	
	public $galleries;
	
	public function getUID() {
		return self::UID . $this->portfolio_id;
	}
	public function getObjectID()
	{
		return $this->portfolio_id;
	}
	public function getObjectType()
	{
		return dbObject::OBJECT_TYPE_PORTFOLIO;
	}
	public function __construct($code_ref = NULL)
	{
		if ($code_ref !== NULL) {
			$this->createFromCodeRef($code_ref);
		}
	}
	public function createFromCodeRef($code_ref)
	{	
		$sql = "SELECT * 
				FROM tblPortfolios
				JOIN tblFeatures
					ON tblFeatures.feature_id = tblPortfolios.portfolio_id
					AND tblFeatures.feature_type = " . swFeature::FEATURE_TYPE_PORTFOLIO . "
				WHERE tblFeatures.feature_code_ref = '$code_ref';";

		$result = mysql_query($sql);

		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createPortfolioFromSQLData($data,true,true);
			
			return true;
		} else {
			return false;
		}
	}
	public function createFromId($id) {
		
		$sql = "SELECT * 
				FROM tblPortfolios
				WHERE portfolio_id = $id
					AND delete_flag = 0;";
				
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createPortfolioFromSQLData($data);
			
			return true;
		} else {
			return false;
		}
	}
	public function createPortfolioFromSQLData($data,$getFeatured = false,$getImages = false)
	{
		$this->portfolio_id = $data["portfolio_id"];
		$this->portfolio_name = $data["portfolio_name"];
		$this->portfolio_gallery_rename = $data["portfolio_gallery_rename"];
		$this->portfolio_order = $data["portfolio_order"];
		$this->portfolio_featured = $data["portfolio_featured"];
		$this->portfolio_internal_count = $data["portfolio_internal_count"];
		$this->portfolio_external_count = $data["portfolio_external_count"];
		
		$this->galleries = swGallery::getGalleriesForPortfolio($this->portfolio_id,$getFeatured,$getImages);
		
		foreach ($this->galleries as $gallery)
			$gallery->gallery_type = $this->portfolio_gallery_rename;
	}
	public function getGalleryById($id)
	{
		foreach ($this->galleries as $gallery)
			if ($gallery->gallery_id == $id) return $gallery;
		
		return NULL;	// else return nothing
	}
	public function linkImages($images)
	{
		foreach ($this->galleries as $gallery) {
			$gallery->linkImages($images);
		}
	}
	public function addGallery(&$gallery)
	{
		$gallery->gallery_fk_portfolio_id = $this->portfolio_id;
		$gallery->gallery_order = count($this->galleries);
		$gallery->gallery_id = uniqid();
		$gallery->is_new = true;
		$this->galleries[$gallery->gallery_id] = $gallery;
	}
	public function removeGallery(&$gallery)
	{
		unset($gallery->gallery_fk_portfolio_id);
		unset($this->galleries[$gallery->gallery_id]);
	}
	public function sortGalleries()
	{
		function cmp( $a, $b )
		{ 
		  if(  $a->gallery_order ==  $b->gallery_order ){ return 0 ; } 
		  return ($a->gallery_order < $b->gallery_order) ? -1 : 1;
		} 
		
		usort($this->galleries,'cmp');
	}
	public function saveAsNew() {
		
		$sql = "INSERT INTO tblPortfolios 
						(delete_flag,
						 enabled,
						 portfolio_name,
						 portfolio_gallery_rename,
						 portfolio_order,
						 portfolio_featured,
						 portfolio_internal_count,
						 portfolio_external_count) 
					VALUES 
						(" . (int) $this->delete_flag . ",
						 " . (int) $this->enabled . ",
						 '" . mysql_real_escape_string(substr($this->portfolio_name,0,100)) . "',
						 '" . mysql_real_escape_string(substr($this->portfolio_gallery_rename,0,100)) . "',
						 " . $this->portfolio_order . ",
						 " . (int) $this->portfolio_featured . ",
						 " . $this->portfolio_internal_count . ",
						 " . $this->portfolio_external_count . ");";
						 
		if (mysql_query($sql))
			return true;
		else
			return false;
		
	}
	public function update() {
		
		$success = false;
		
		if ($this->portfolio_id !== NULL) {
			
			$sql = "UPDATE tblPortfolios 
						SET delete_flag = '" . (int) $this->delete_flag . "',
							enabled = '" . (int) $this->enabled . "',
							portfolio_name = '" . mysql_real_escape_string(substr($this->portfolio_name,0,100)) . "',
							portfolio_gallery_rename = '" . mysql_real_escape_string(substr($this->portfolio_gallery_rename,0,100)) . "',
							portfolio_order = " . $this->portfolio_order . ",
							portfolio_featured = " . (int) $this->portfolio_featured . ",
							portfolio_internal_count = " . $this->portfolio_internal_count . ",
							portfolio_external_count = " . $this->portfolio_external_count . "
					WHERE portfolio_id = " . $this->portfolio_id . ";"; 
							 
			if (mysql_query($sql))
				$success =  true;
				
		}
		
		return $success;
		
	}
	public function getTableName() {
		return 'tblPortfolios';
	}
	public function createTable() {
		
		$sql = "CREATE TABLE IF NOT EXISTS `tblPortfolios` (
					  `delete_flag` tinyint(1) NOT NULL default '0',
					  `enabled` tinyint(1) NOT NULL default '1',
					  `portfolio_id` int(11) NOT NULL auto_increment,
					  `portfolio_name` varchar(100) NOT NULL,
					  `portfolio_gallery_rename` varchar(100) NOT NULL,
					  `portfolio_order` int(11) NOT NULL,
					  `portfolio_featured` tinyint(1) NOT NULL,
					  `portfolio_internal_count` int(11) NOT NULL,
					  `portfolio_external_count` int(11) NOT NULL,
					  PRIMARY KEY  (`portfolio_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
				
		if (mysql_query($sql))
			return true;
		else
			return false;

	}
}

?>