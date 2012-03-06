<?php

class swSessionObject
{	
	public $user;
	public $isLoggedIn = false;
	
	public $pages;
	public $images;
	public $sections;
	public $features;
	
	public $sessionUpdates = array();		// these will be instances of swSessionUpdate
	
	public $memory_in_use;
	
	public function saveState()
	{
		$_SESSION["swSessionObject"] = serialize($this);
		//session_write_close();
	}
	public function __construct()
	{
		if (isset($_SESSION["swSessionObject"])) {
			$sessionObject = unserialize($_SESSION["swSessionObject"]);
			
			$this->user = $sessionObject->user;
			$this->isLoggedIn = $sessionObject->isLoggedIn;
			
			$this->images = $sessionObject->images;
			$this->pages = $sessionObject->pages;
			$this->sections = $sessionObject->sections;
			$this->features = $sessionObject->features;
			
			$this->sessionUpdates = $sessionObject->sessionUpdates;
		}
		
		$this->_setMemoryInUse();
	}
	private function _setMemoryInUse()
	{
		$this->memory_in_use = round(memory_get_usage() / 1000000) . "MB";
	}
	public function redirectIfNotLoggedIn()
	{
		if (!$this->isLoggedIn) {
			header("location: " . DOCUMENT_ROOT . "cms/login.php");
			exit;
		}
	}
	public function loadAllCMSContent()
	{
		$this->images = swImage::getAllImages();
		$this->pages = swPage::getAllPagesInOrder($this->images,false);
		$this->sections = swSection::getAllSections($this->pages,$this->images);
		$this->features = swFeature::getAllFeatures($this->pages,$this->images);
		
		$this->_setMemoryInUse();
		$this->saveState();		
	}
	public function findGalleryInSession($gallery_id)
	{
		foreach ($this->features as $feature) {
			if ($feature->feature_type == swFeature::FEATURE_TYPE_GALLERY &&
				$feature->feature_id == $gallery_id)
				return $feature->feature_object;
			
			elseif ($feature->feature_type == swFeature::FEATURE_TYPE_PORTFOLIO) {
				$portfolio = $feature->feature_object;
				
				foreach ($portfolio->galleries as $gallery)
					if ($gallery->gallery_id == $gallery_id)
						return $gallery;
			}
		}
		return NULL;	// return NULL if no gallery was found
	}
	public function findFeatureInSession($feature_id,$feature_type)
	{
		foreach ($this->features as $feature)
			if ($feature->feature_type == $feature_type &&
				$feature->feature_id == $feature_id)
				return $feature->feature_object;
		return NULL;	// return NULL if no feature was found
	}
	public function findImageInSession($image_id)
	{
		foreach ($this->images as $image)
			if ($image->img_id == $image_id)
				return $image;
		return NULL;	// return NULL if no image was found
	}
	public function findSectionInSession($section_id)
	{
		foreach ($this->sections as $section)
			if ($section->section_id == $section_id)
				return $section;
		return NULL;	// return NULL if no section was found
	}
	public function getPageById($page_id)
	{
		foreach ($this->pages as $page)
			if ($page->pg_id == $page_id)
				return $page;
		return NULL;	// return NULL if no page was found
	}
	public function addImage($image)
	{
		$this->images[$image->img_id] = $image;
	}
	private function _removeSessionUpdatesByType($update_type)
	{
		foreach ($this->sessionUpdates as $key => $sessionUpdate)
			if ($sessionUpdate->update_type == $update_type)
				unset($this->sessionUpdates[$key]);
	}
	public function sortPages()
	{
		function cmp( $a, $b )
		{ 
		  if(  $a->pg_order ==  $b->pg_order ){ return 0 ; } 
		  return ($a->pg_order < $b->pg_order) ? -1 : 1;
		} 
		
		usort($this->pages,'cmp');
	}
	
}

?>