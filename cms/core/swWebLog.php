<?php

class swWebLog extends swFeature
{
	const UID = "wl";
	
	public $weblog_id;
	public $weblog_name = "";
	public $weblog_desc = "";
	public $weblog_entry_name = "";
	public $weblog_fk_pg_id;
	
	public function getUID() {
		return self::UID . $this->weblog_id;
	}
	public function getObjectID()
	{
		return $this->$weblog_id;
	}
	public function getObjectType()
	{
		return dbObject::OBJECT_TYPE_WEBLOG;
	}
	public function getFeatureType()
	{
		return swFeature::FEATURE_TYPE_WEBLOG;
	}
	public function createFromId($id)
	{
		$sql = "SELECT * 
				FROM tblweblogs
				WHERE weblog_id = $id
					AND enabled = 1;";
				
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createWebLogFromSQLData($data);
			
			return true;
		} else {
			return false;
		}
	}
	public function createWebLogFromSQLData($data)
	{
		$this->delete_flag = $data["delete_flag"];
		$this->enabled = $data["enabled"];
		$this->weblog_id = $data["weblog_id"];
		$this->weblog_name = $data["weblog_name"];
		$this->weblog_desc = $data["weblog_desc"];
		$this->weblog_entry_name = $data["weblog_entry_name"];
		$this->weblog_fk_pg_id = $data["weblog_fk_pg_id"];
	}
	public function saveAsNew()
	{
		$sql = "INSERT INTO tblweblogs 
						(delete_flag,
						 enabled,
						 weblog_name,
						 weblog_desc,
						 weblog_entry_name,
						 weblog_fk_pg_id) 
					VALUES 
						(" . (int) $this->delete_flag . ",
						 " . (int) $this->enabled . ",
						 '" . mysql_real_escape_string(substr($this->weblog_name,0,50)) . "',
						 '" . mysql_real_escape_string(substr($this->weblog_desc,0,500)) . "',
						 '" . mysql_real_escape_string(substr($this->weblog_entry_name,50)) . "',
						 " . (int) $this->weblog_fk_pg_id . ");";
						 
		return (mysql_query($sql)) ? true : false;
	}
	public function update()
	{
		$success = false;
		
		if ($this->weblog_fk_pg_id !== NULL)
		{			
			$sql = "UPDATE tblweblogs 
						SET delete_flag = " . (int) $this->delete_flag . ",
							enabled = " . (int) $this->enabled . ",
							weblog_name = '" . mysql_real_escape_string(substr($this->weblog_name,0,50)) . "',
							weblog_desc = " . mysql_real_escape_string(substr($this->weblog_desc,0,500)) . ",
							weblog_entry_name = '" . mysql_real_escape_string(substr($this->weblog_entry_name,0,50)) . "',
							weblog_fk_pg_id = " . (int) $this->weblog_fk_pg_id . "
					WHERE weblog_id = " . $this->$weblog_id . ";"; 
 			
			if (mysql_query($sql)) $success =  true;
		}
		
		return $success;
	}
	public function getTableName() {
		return 'tblweblogs';
	}
	public function createTable()
	{
		$sql = "CREATE TABLE `tblweblogs` (
					  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
					  `enabled` tinyint(1) NOT NULL DEFAULT '1',
					  `weblog_id` int(11) NOT NULL AUTO_INCREMENT,
					  `weblog_name` varchar(50) NOT NULL,
					  `weblog_desc` varchar(500) DEFAULT NULL,
					  `weblog_entry_name` varchar(50) NOT NULL,
					  PRIMARY KEY (`weblog_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1$$";
				
		return (mysql_query($sql)) ? true : false;
	}
}

?>