<?php

abstract class swFeature extends dbObject {
	
	const FEATURE_TYPE_CONTACT = 0;
	const FEATURE_TYPE_GALLERY = 1;
	const FEATURE_TYPE_PORTFOLIO = 2;
	
	public abstract function getFeatureType();
	
	public static function getAllFeatures($pages,$images)
	{
		$features = array();
		
		$sql = "SELECT feature_id,feature_type,feature_code_ref,pg_code_ref 
				FROM tblFeatures
				LEFT JOIN tblPages ON tblPages.pg_id = tblFeatures.feature_fk_pg_id;";
		
		$result = mysql_query($sql);
		
		while (($data = mysql_fetch_array($result)) == true)
		{
			$pageCodeRef = $data["pg_code_ref"];
			
			// get the page that this feature belongs too
			$page = (isset($pages[$pageCodeRef])) ? $pages[$pageCodeRef] : null;
			if (isset($page)) {
				$featureID = $data["feature_id"];
				$featureType = $data["feature_type"];
				$featureCodeRef = $data["feature_code_ref"];
				
				$feature = self::_getFeature($featureID,$featureType,$images);	// create the feature
				$features[$featureCodeRef] = $feature;							// add the feature to the array to return
				
				$page->pg_features[$featureCodeRef] = $feature;				// link this feature to the appropriate page
			}
		}
		
		return $features;
	}
	
	private static function _getFeature($featureID,$featureType,$images)
	{
		$feature = null;
		
		switch ($featureType){
			case self::FEATURE_TYPE_GALLERY:
				$feature = new swGallery();
				$feature->createFromId($featureID);
				$feature->linkImages($images);
				break;
			case self::FEATURE_TYPE_PORTFOLIO:
				$feature = new swPortfolio();
				$feature->createFromId($featureID);
				$feature->linkImages($images);
				break;
			default:
				throw new Exception("Unknown feature");
		}
		
		return $feature;
	}
	
}

?>