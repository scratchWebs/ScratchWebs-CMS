<?php
class swPage extends dbObject
{	
	const UID = "pg_";
	
	public $pg_id;
	public $pg_code_ref = "";
	public $pg_path = "";
	public $pg_linkname = "";
	public $pg_title = "";
	public $pg_description = "";
	public $pg_meta_title = "";
	public $pg_meta_description = "";
	public $pg_meta_keywords = "";
	public $pg_order = 0;
	public $pg_internal_count = 0;
	public $pg_external_count = 0;
	
	public $pg_images = array();
	public $pg_sections = array();
	public $pg_features = array();
	
	public function __construct($code_ref = NULL)
	{
		if ($code_ref !== NULL) {
			$this->createFromCodeRef($code_ref);
			
			$stat = new swStat();
			$stat->stat_object_type = dbObject::OBJECT_TYPE_PAGE;
			$stat->stat_object_id = $this->pg_id;
			$stat->saveAsNew();
		}
	}
	public function isFirstPage()
	{
		return ($this->pg_order == 0);
	}
	public function getUID() {
		return self::UID . $this->pg_id;
	}
	public function getObjectType()
	{
		return dbObject::OBJECT_TYPE_PAGE;
	}
	public function createFromCodeRef($code_ref) {
		
		$sql = "SELECT * 
				FROM tblPages
				WHERE pg_code_ref = '$code_ref';";
				
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) !== 0) {
			$data = mysql_fetch_array($result);
			
			$this->createPageFromSQLData($data,true,false);
			
			return true;
		} else {
			return false;
		}
		
	}
	public function createFromId($id) {
		
		$sql = "SELECT * FROM tblPages
				WHERE pg_id = $id;";
				
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createPageFromSQLData($data);
			
			return true;
		} else {
			return false;
		}
		
	}
	public function createPageFromSQLData($data,$getSections = true,$getFeatures = true)
	{
		$this->delete_flag = $data["delete_flag"];
		$this->enabled = $data["enabled"];
		$this->pg_id = $data["pg_id"];
		$this->pg_code_ref = $data["pg_code_ref"];
		$this->pg_path = $data["pg_path"];
		$this->pg_linkname = $data["pg_linkname"];
		$this->pg_title = $data["pg_title"];
		$this->pg_description = $data["pg_description"];
		$this->pg_meta_title = $data["pg_meta_title"];
		$this->pg_meta_description = $data["pg_meta_description"];
		$this->pg_meta_keywords = $data["pg_meta_keywords"];
		$this->pg_order = $data["pg_order"];
		$this->pg_internal_count = $data["pg_internal_count"];
		$this->pg_external_count = $data["pg_external_count"];
		
		if ($getSections)
			$this->pg_sections = swSection::getSectionsForPage($this->pg_id);
		
		if ($getFeatures)
			$this->pg_features = swFeature::getFeaturesForPage($this->pg_id);
	}
	
	static function getAllPagesInOrder($images = NULL,$enabledOnly = true)
	{
		$pages = array();

		if ($enabledOnly) $enabled_param = "AND enabled = 1";
		
		$sql = "SELECT * 
				FROM tblPages 
				WHERE delete_flag = 0
					$enabled_param
				ORDER BY pg_order ASC;";
		
		$result = mysql_query($sql);
		
		while (($data = mysql_fetch_array($result)) == true)
		{
			$page = new swPage();
			$page->createPageFromSQLData($data, false, false);
			
			foreach ($images as $img)
				if ($img->img_fk_pg_id == $page->pg_id)
					$page->pg_images[$img->code_ref] = $img;	// add any images associated with this page
			
			$pages[$page->pg_code_ref] = $page;
		}
		
		return $pages;
	}
	
	public function saveAsNew()
	{
		$sql = "INSERT INTO tblPages 
						(delete_flag,
						 enabled,
						 pg_path,
						 pg_code_ref,
						 pg_linkname,
						 pg_title,
						 pg_description,
						 pg_meta_title,
						 pg_meta_description,
						 pg_meta_keywords,
						 pg_order,
						 pg_internal_count,
						 pg_external_count) 
					VALUES 
						(" . (int) $this->delete_flag . ",
						 " . (int) $this->enabled . ",
						 '" . mysql_real_escape_string(substr($this->pg_path),0,50) . "',
						 '" . mysql_real_escape_string(substr($this->pg_code_ref,0,30)) . "',
						 '" . mysql_real_escape_string(substr($this->pg_linkname,0,50)) . "',
						 '" . mysql_real_escape_string(substr($this->pg_title,0,100)) . "',
						 '" . mysql_real_escape_string(substr($this->pg_description,0,100)) . "',
						 '" . mysql_real_escape_string(substr($this->pg_meta_title,0,100)) . "',
						 '" . mysql_real_escape_string(substr($this->pg_meta_description,0,1000)) . "',
						 '" . mysql_real_escape_string(substr($this->pg_meta_keywords,0,1000)) . "',
						 " . $this->pg_order . ",
						 " . $this->pg_internal_count . ",
						 " . $this->pg_external_count . ");";
						 
		return mysql_query($sql);
	}
	public function update()
	{
		if ($this->pg_id !== NULL)
		{
			$sql = "UPDATE tblPages 
						SET delete_flag = " . (int) $this->delete_flag . ",
							enabled = " . (int) $this->enabled . ",
							pg_path = '" . mysql_real_escape_string(substr($this->pg_path,0,50)) . "',
							pg_code_ref = '" . mysql_real_escape_string(substr($this->pg_code_ref,0,30)) . "',
							pg_linkname = '" . mysql_real_escape_string(substr($this->pg_linkname,0,50)) . "',
							pg_title = '" . mysql_real_escape_string(substr($this->pg_title,0,100)) . "',
							pg_description = '" . mysql_real_escape_string(substr($this->pg_description,0,100)) . "',
							pg_meta_title = '" . mysql_real_escape_string(substr($this->pg_meta_title,0,100)) . "',
							pg_meta_description = '" . mysql_real_escape_string(substr($this->pg_meta_description,0,1000)) . "',
							pg_meta_keywords = '" . mysql_real_escape_string(substr($this->pg_meta_keywords,0,1000)) . "',
							pg_order = " . $this->pg_order . ",
							pg_internal_count = " . $this->pg_internal_count . ",
							pg_external_count = " . $this->pg_external_count . "
					WHERE pg_id = " . $this->pg_id . ";"; 
							 
			mysql_query($sql) or die(mysql_error());
		}
	}
	
	public function getTableName()
	{
		return 'tblPages';
	}
	public function createTable()
	{
		
		$sql = "CREATE TABLE IF NOT EXISTS `tblPages` (
				    `delete_flag` tinyint(1) NOT NULL default '0',
				    `enabled` tinyint(1) NOT NULL default '1',
					`pg_id` int(11) NOT NULL auto_increment COMMENT 'Auto Incremented ID',
					`pg_code_ref` varchar(30) NOT NULL,
					`pg_path` varchar(50) NOT NULLL default '',
					`pg_linkname` varchar(50) NOT NULLL default '',
					`pg_description` varchar(100) NOT NULLL default '',
					`pg_meta_title` varchar(100) NOT NULL default '',
					`pg_meta_description` varchar(1000) NOT NULL default '',
					`pg_meta_keywords` varchar(1000) NOT NULL default '',
					`pg_title` varchar(100) NOT NULLL default '',
					`pg_order` int(11) NOT NULLL default '0',
					`pg_internal_count` int(11) NOT NULL default '0',
					`pg_external_count` int(11) NOT NULL default '0',
					PRIMARY KEY  (`pg_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
				
		if (mysql_query($sql)) {
			
			$this->pg_enabled = true;
			
			// table has been created
			// now create the default pages
			$this->pg_linkname = "Home";
			$this->pg_code_ref = "home";
			$this->pg_title = "Home Page";
			$this->pg_order = 0;
			$this->save();
			
			$this->pg_linkname = "About";
			$this->pg_code_ref = "about";
			$this->pg_title = "Home Page";
			$this->pg_order = 1;
			$this->save();
			
			$this->pg_linkname = "Contact";
			$this->pg_code_ref = "contact";
			$this->pg_title = "Home Page";
			$this->pg_order = 2;
			$this->save();
			
			return true;
		} else
			return false;
	}
	
}

?>