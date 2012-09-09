<?php

class swWebLogEntry extends dbObject
{
	const UID = "wle";
	
	public $wlentry_id;
	public $wlentry_text = "";
	public $wlentry_author = "";
	public $wlentry_date;
	public $wlentry_order;
	public $wlentry_fk_weblog_id;
	
	public $weblog;
	
	public function getUID() {
		return self::UID . $this->wlentry_id;
	}
	public function getObjectID()
	{
		return $this->wlentry_id;
	}
	public function getObjectType()
	{
		return dbObject::OBJECT_TYPE_WEBLOGENTRY;
	}
	public function noUpdates()
	{
		return count($this->sessionUpdates);
	}
	public function createFromId($id)
	{
		$sql = "SELECT * 
				FROM tblweblogentries
				WHERE wlentry_id = $id
					AND delete_flag = 0;";
				
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createWebLogEntryFromSQLData($data);
			
			return true;
		} else {
			return false;
		}
	}
	public static function getEntriesForWebLog(swWebLog $weblog)
	{
		$entries = array();
		
		$sql = 'SELECT * ' .
				'FROM tblweblogentries ' .
				'WHERE wlentry_fk_weblog_id = ' . $weblog->weblog_id . ' ' .
					'AND delete_flag = 0;';
		
		$result = mysql_query($sql) or die(mysql_error());
		
		while (($data = mysql_fetch_array($result)) == true)
		{
			$wlentry = new swWebLogEntry();
			
			$wlentry->createWebLogEntryFromSQLData($data);
			
			$wlentry->weblog = $weblog;
			
			$entries[$wlentry->wlentry_id] = $wlentry;
		}
		
		return $entries;
	}
	public function createWebLogEntryFromSQLData($data)
	{
		$this->delete_flag = $data["delete_flag"];
		$this->enabled = $data["enabled"];
		$this->wlentry_id = $data["wlentry_id"];
		$this->wlentry_text = $data["wlentry_text"];
		$this->wlentry_author = $data["wlentry_author"];
		$this->wlentry_date = $data["wlentry_date"];
		$this->wlentry_order = $data["wlentry_order"];
		$this->wlentry_fk_weblog_id = $data["wlentry_fk_weblog_id"];
	}
	public function saveAsNew()
	{
		$success = false;
		
		$sql = "INSERT INTO tblweblogentries 
						(delete_flag,
						 enabled,
						 wlentry_text,
						 wlentry_author,
						 wlentry_date,
						 wlentry_order,
						 wlentry_fk_weblog_id) 
					VALUES 
						(" . (int) $this->delete_flag . ",
						 " . (int) $this->enabled . ",
						 '" . mysql_real_escape_string(substr($this->wlentry_text,0,5000)) . "',
						 '" . mysql_real_escape_string(substr($this->wlentry_author,0,100)) . "',
						 '" . $this->wlentry_date . "',
						 " . (int) $this->wlentry_order . ",
						 " . (int) $this->wlentry_fk_weblog_id . ");";
		
		if (mysql_query($sql) or die(mysql_error()))
		{
			$newid = mysql_insert_id();
			
			if (isset($this->weblog)) {
				$this->weblog->weblog_entries[$newid] = $this;
				unset($this->weblog->weblog_entries[$this->wlentry_id]);
			}
			
			$this->wlentry_id = $newid;
			
			$success =  true;
		}
		
		return $success;
	}
	public function update()
	{
		$success = false;
		
		if ($this->wlentry_id !== NULL)
		{			
			$sql = "UPDATE tblweblogentries 
						SET delete_flag = " . (int) $this->delete_flag . ",
							enabled = " . (int) $this->enabled . ",
							wlentry_text = '" . mysql_real_escape_string(substr($this->wlentry_text,0,5000)) . "',
							wlentry_author = '" . mysql_real_escape_string(substr($this->wlentry_author,0,100)) . "',
							wlentry_date = '" . $this->wlentry_date . "',
							wlentry_order = " . (int) $this->wlentry_order . ",
							wlentry_fk_weblog_id = " . (int) $this->wlentry_fk_weblog_id . "
					WHERE wlentry_id = " . $this->wlentry_id . ";"; 
							 
			if (mysql_query($sql) or die(mysql_error())) $success =  true;
		}
		
		return $success;
		
	}
	public function getTableName() {
		return 'tblweblogentries';
	}
	public function createTable()
	{
		$sql = "CREATE TABLE `tblweblogentries` (
					  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
					  `enabled` tinyint(1) NOT NULL DEFAULT '1',
					  `wlentry_id` int(11) NOT NULL AUTO_INCREMENT,
					  `wlentry_text` varchar(5000) NOT NULL,
					  `wlentry_author` varchar(100) DEFAULT NULL,
					  `wlentry_date` datetime NOT NULL,
					  `wlentry_order` int(11) NOT NULL,
					  `wlentry_fk_weblog_id` int(11) NOT NULL,
				  PRIMARY KEY (`wlentry_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1$$";
				
		return (mysql_query($sql)) ? true : false;
	}
}

?>