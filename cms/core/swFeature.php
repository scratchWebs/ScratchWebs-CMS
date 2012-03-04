<?php

class swFeature extends dbObject 
{
	const FEATURE_TYPE_CONTACT = 0;
	const FEATURE_TYPE_GALLERY = 1;
	const FEATURE_TYPE_PORTFOLIO = 2;
	
	public $feature_id;
	public $feature_type;
	public $feature_code_ref;
	public $feature_fk_pg_id;
	
	public $feature_object;
	
	public function getUID() {
		return (isset($this->feature_object)) ? $this->feature_object->getUID() : NULL;
	}
	public function getObjectType()
	{
		return $this->feature_object->getObjectType();
	}
	public static function getFeatureObjects($features)
	{
		$feature_objects = array();
		
		foreach ($features as $feature)
			array_push($feature_objects,$feature->feature_object);
		
		return $feature_objects;
	}
	public function saveAsNew()
	{
		$feature_object->saveAsNew();
	}
	public function update()
	{
		$feature_object->update();
	}
	public function createFromId($id)
	{
		// not possible has we need id and type
	}
	public function getTableName()
	{
		return 'tblFeatures';
	}
	public function createTable()
	{
		// CREATE TABLE 
		$sql = "CREATE TABLE  `audley_1`.`tblFeatures` (
					`feature_id` INT NOT NULL,
					`feature_type` TINYINT NOT NULL,
					`feature_code_ref` varchar(30) NOT NULL,
					`feature_fk_pg_id` INT NOT NULL,
				  PRIMARY KEY  (`feature_id`,`feature_type`,`feature_fk_pg_id`)
				) ENGINE = MYISAM ;";
					
		if (mysql_query($sql))
			return true;
		else
			return false;
	}
	public function createFeatureFromSQLData($data,$images = NULL)
	{
		$this->feature_id = $data["feature_id"];
		$this->feature_type = $data["feature_type"];
		$this->feature_code_ref = $data["feature_code_ref"];
		$this->feature_fk_pg_id = $data["feature_fk_pg_id"];
		
		$feature_object = NULL;
		
		switch ($this->feature_type) {
			case self::FEATURE_TYPE_CONTACT:
				
				break;
			case self::FEATURE_TYPE_GALLERY:
				$feature_object = new swGallery();
				$feature_object->createFromId($this->feature_id);
				$feature_object->linkImages($images);
				break;
			case self::FEATURE_TYPE_PORTFOLIO:
				$feature_object = new swPortfolio();
				$feature_object->createFromId($this->feature_id);
				$feature_object->linkImages($images);
				break;
		}
		
		$this->feature_object = $feature_object;
	}
	static function getFeaturesForPage($pg_id)
	{
		$features = array();
		
		$sql = "SELECT * 
				FROM tblFeatures
				WHERE feature_fk_pg_id = $pg_id;";
				
		$result = mysql_query($sql);
		
		while ($data = mysql_fetch_array($result)) {
			$feature = new swFeature();
			$feature->createFeatureFromSQLData($data);
			
			$features[$feature->feature_code_ref] = $feature;
		}
		
		return $features;
	}
	static function getAllFeatures($pages = NULL,$images = NULL)
	{
		$features = array();
		
		$sql = "SELECT * 
				FROM tblFeatures;";
				
		$result = mysql_query($sql);
		
		while ($data = mysql_fetch_array($result))
		{
			$feature = new swFeature();
			$feature->createFeatureFromSQLData($data,$images);
			
			$features[$feature->feature_code_ref] = $feature;
			
			// link this feature to the appropriate page
			if ($pages != NULL)
			{
				foreach ($pages as $page)
				{
					if ($page->pg_id == $feature->feature_fk_pg_id) {
						$page->pg_features[$feature->feature_code_ref] = $feature;
						break;
					}
				}
			}
		}
		
		return $features;
	}
	
}

?>