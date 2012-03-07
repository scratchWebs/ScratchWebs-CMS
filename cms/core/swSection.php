<?php

class swSection extends dbObject
{
	const UID = "sect";
	
	public $section_id;
	public $section_code_ref = "";
	public $section_name = "";
	public $section_html = "";
	public $section_max_size = 500;
	public $section_order;
	public $section_fk_pg_id;
	
	public $images = array();
	
	public function getUID() {
		return self::UID . $this->section_id;
	}
	public function getObjectType()
	{
		return dbObject::OBJECT_TYPE_SECTION;
	}
	public function __construct($code_ref = NULL)
	{
		if ($code_ref !== NULL)
			$this->createFromCodeRef($code_ref);
	}
	public function createFromCodeRef($code_ref)
	{
		$sql = "SELECT * 
				FROM tblSections
				WHERE section_code_ref = '$code_ref';";
				
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) !== 0) {
			$data = mysql_fetch_array($result);
			
			$this->createPageFromSQLData($data);
			
			return true;
		} else
			return false;
	}
	static function getSectionFromArray($sections,$id)
	{
		$returnSection = null;
		
		foreach ($sections as $section)
			if ($section->section_id == $id) {
				$returnSection = $section;
				break;
			}
		
		return $returnSection;
	}
	static function getAllSections($pages = NULL,$images = NULL)
	{
		$sections = array();
		
		$sql = "SELECT * 
				FROM tblSections
					ORDER BY section_order ASC;";
				
		$result = mysql_query($sql);
		
		while (($data = mysql_fetch_array($result)) == true) {
			
			$section = new swSection();
			
			$section->createSectionFromSQLData($data);
			
			foreach ($images as $img)
			{
				if ($img->img_fk_section_id == $section->section_id)
					$section->images[$img->code_ref] = $img;		// add any images associated with this section
			}
			
			$sections[$section->section_code_ref] = $section;
			
			// link this section to the appropriate page
			if ($pages !== NULL) {
				foreach ($pages as $page) {
					
					if ($page->pg_id == $section->section_fk_pg_id) {
						$page->pg_sections[$section->section_code_ref] = $section;
						break;
					}
				}
			}
		}
		
		return $sections;
		
	}
	static function getSectionsForPage($pg_id)
	{
		$sections = array();
		
		$sql = "SELECT * 
				FROM tblSections
				WHERE section_fk_pg_id = $pg_id
					AND delete_flag = 0
					AND enabled = 1
				ORDER BY section_order ASC;";
		
		$result = mysql_query($sql);
		
		while (($data = mysql_fetch_array($result)) == true)
		{
			$section = new swSection();
			$section->createSectionFromSQLData($data);
			$sections[$section->section_code_ref] = $section;
		}
		
		return $sections;
	}
	public function saveAsNew() {
		
		$success = false;
		
		if ($this->section_fk_pg_id !== NULL) {
				
			$sql = "INSERT INTO tblSections 
							(delete_flag,
							 enabled,
							 section_name,
							 section_code_ref,
							 section_html,
							 section_max_size,
							 section_order,
							 section_fk_pg_id) 
						VALUES 
							(" . (int) $this->delete_flag . ",
							 " . (int) $this->enabled . ",
							 '" . mysql_real_escape_string(substr($this->section_name,0,100)) . "',
							 '" . mysql_real_escape_string(substr($this->section_code_ref,0,30)) . "',
							 '" . mysql_real_escape_string(substr($this->section_html,0,5000)) . "',
							 " . $this->section_max_size . ",
							 " . $this->section_order . ",
							 " . $this->section_fk_pg_id . ");";
							 
			if (mysql_query($sql))
				$success = true;
				
		}
	
		return $success;
	
	}
	public function update() {
		
		$success = false;
		
		if ($this->section_id !== NULL) {
			
			$sql = "UPDATE tblSections 
						SET delete_flag = " . (int) $this->delete_flag . ",
							enabled = " . (int) $this->enabled . ",
							section_name = '" . mysql_real_escape_string(substr($this->section_name,0,100)) . "',
							section_code_ref = '" . mysql_real_escape_string(substr($this->section_code_ref,0,30)) . "',
							section_html = '" . mysql_real_escape_string(substr($this->section_html,0,5000)) . "',
							section_max_size = " . $this->section_max_size . ",
							section_order = " . $this->section_order . ",
							section_fk_pg_id = " . $this->section_fk_pg_id . " 
					WHERE section_id = " . $this->section_id . ";";
							 
			if (mysql_query($sql))
				$success = true;
				
		}
		
		return $success;
	
	}
	public function createFromId($id) {
		
		$sql = "SELECT * 
				FROM tblSections
				WHERE section_id = $id
					ORDER BY section_order ASC;";
				
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createSectionFromSQLData($data);
			
			return true;
		} else {
			return false;
		}
		
	}
	public function createSectionFromSQLData($data)
	{
		$this->delete_flag = $data["delete_flag"];
		$this->enabled = $data["enabled"];
		$this->section_id = $data["section_id"];
		$this->section_code_ref = $data["section_code_ref"];
		$this->section_name = $data["section_name"];
		$this->section_html = $data["section_html"];
		$this->section_max_size = $data["section_max_size"];
		$this->section_order = $data["section_order"];
		$this->section_fk_pg_id = $data["section_fk_pg_id"];
	}
	public function getTableName() {
		return 'tblSections';
	}
	public function createTable() {
		
		$sql = "CREATE TABLE IF NOT EXISTS `tblSections` (
					  `delete_flag` tinyint(1) NOT NULL default '0',
					  `enabled` tinyint(1) NOT NULL default '1',
					  `section_id` int(11) NOT NULL auto_increment,
					  `section_code_ref` varchar(30) NOT NULL,
					  `section_name` varchar(100) NOT NULL,
					  `section_html` varchar(5000) NOT NULL,
					  `section_max_size` int(11) NOT NULL,
					  `section_order` int(11) NOT NULL,
					  `section_fk_pg_id` int(11) NOT NULL,
					  PRIMARY KEY  (`section_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
				
		if (mysql_query($sql))
			return true;
		else
			return false;

	}
}

?>