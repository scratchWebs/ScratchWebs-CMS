<?php

class swWebLog extends swFeature
{
	const UID = "wl";
	
	public $weblog_id;
	public $weblog_name = "";
	public $weblog_desc = "";
	public $weblog_entry_name = "";
	
	public $weblog_entries = array(); 
	
	public function getUID() {
		return self::UID . $this->weblog_id;
	}
	public function getObjectID()
	{
		return $this->weblog_id;
	}
	public function getObjectType()
	{
		return dbObject::OBJECT_TYPE_WEBLOG;
	}
	public function getFeatureType()
	{
		return swFeature::FEATURE_TYPE_WEBLOG;
	}
	public function noUpdates()
	{
		return count($this->sessionUpdates);
	}
	public function __construct($code_ref = NULL)
	{
		if ($code_ref !== NULL) {
			$this->createFromCodeRef($code_ref);
		}
	}
	public function createFromCodeRef($code_ref)
	{	
		$sql = "SELECT tblweblogs.* 
				FROM tblweblogs
				JOIN tblFeatures
					ON tblFeatures.feature_id = tblweblogs.weblog_id
					AND tblFeatures.feature_type = " . swFeature::FEATURE_TYPE_WEBLOG . "
				WHERE tblFeatures.feature_code_ref = '$code_ref';";

		$result = mysql_query($sql);

		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createWebLogFromSQLData($data,true);
			
			return true;
		} else {
			return false;
		}
	}
	public function createFromId($id,$loadEntries = true)
	{
		$sql = "SELECT * 
				FROM tblweblogs
				WHERE weblog_id = $id
					AND enabled = 1;";
				
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createWebLogFromSQLData($data,$loadEntries);
			
			return true;
		} else {
			return false;
		}
	}
	public function createWebLogFromSQLData($data,$loadEntries)
	{
		$this->delete_flag = $data["delete_flag"];
		$this->enabled = $data["enabled"];
		$this->weblog_id = $data["weblog_id"];
		$this->weblog_name = $data["weblog_name"];
		$this->weblog_desc = $data["weblog_desc"];
		$this->weblog_entry_name = $data["weblog_entry_name"];
		
		if ($loadEntries) $this->weblog_entries = swWebLogEntry::getEntriesForWebLog($this);
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
						 '" . mysql_real_escape_string(substr($this->weblog_entry_name,50)) . "');";
						 
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
							weblog_entry_name = '" . mysql_real_escape_string(substr($this->weblog_entry_name,0,50)) . "'
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
	
	public function addEntry(swWebLogEntry $wlentry)
	{
		if (!isset($wlentry->wlentry_id)) $wlentry->wlentry_id = uniqid();
	
		$wlentry->wlentry_fk_weblog_id = $this->weblog_id;
		$wlentry->wlentry_order = count($this->weblog_entries);
		$wlentry->weblog = $this;
		
		swCommon::array_unshift_withkey($this->weblog_entries, $wlentry->wlentry_id, $wlentry);
	}
	public function removeEntry($wlentry_id)
	{
		unset($this->weblog_entries[$wlentry_id]);
	}
	public function getWebLogEntryById($id)
	{
		return $this->weblog_entries[$id];
	}
	public function sortEntries()
	{
		function cmp( $a, $b )
		{ 
		  if(  $a->wlentry_order ==  $b->wlentry_order ){ return 0 ; } 
		  return ($a->wlentry_order < $b->wlentry_order) ? 1 : -1;		// reverse the order (the order will be an admin option in the future)
		} 
		
		uasort($this->weblog_entries,'cmp');
	}
}

?>