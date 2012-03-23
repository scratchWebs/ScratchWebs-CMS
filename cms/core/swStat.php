<?php

class swStat extends dbObject
{
	const UID = "log";
	
	public $stat_id;
	public $stat_date;
	public $stat_object_type;
	public $stat_object_id;
	public $stat_ip_address;
	public $stat_referer;
	public $stat_user_agent;
	
	public function getObjectDescription()
	{
		if ($this->stat_object_type == dbObject::OBJECT_TYPE_PAGE)
		{
			$sql = "SELECT pg_title FROM tblPages WHERE pg_id = " . $this->stat_object_id . ";";
			$result = mysql_query($sql);
			return "Page: " . mysql_result($result,0,0);
		} else {
			return "description not implemented for this type";
		}
	}
	
	public function getUID() {
		return self::UID . $this->stat_id;
	}
	public function getObjectID()
	{
		return $this->stat_id;
	}
	public function getObjectType()
	{
		return dbObject::OBJECT_TYPE_STAT;
	}
	public function noUpdates()
	{
		throw new Exception("This object doesn't support Updates");
	}
	public static function getStats($ammount = 50)
	{
		$stats = array();
		
		$sql = "SELECT *
				FROM tblStats
				ORDER BY stat_date DESC
				LIMIT 0," . $ammount . "";
		
		$result = mysql_query($sql) or die(mysql_error());
		
		while (($data = mysql_fetch_array($result)) == true)
		{
			$stat = new swStat();
			$stat->createStatFromSQLData($data);
			
			$stats[$stat->stat_id] = $stat;
		}
		
		return $stats;
	}
	public function createStatFromSQLData($data)
	{
		$this->stat_id = $data["stat_id"];
		$this->stat_date = $data["stat_date"];
		$this->stat_object_type = $data["stat_object_type"];
		$this->stat_object_id = $data["stat_object_id"];
		$this->stat_ip_address = $data["stat_ip_address"];
		$this->stat_referer = $data["stat_referer"];
		$this->stat_user_agent = $data["stat_user_agent"];
	}
	public function createFromId($id)
	{
		throw new Exception("not able to create a pagestat from id yet");
	}
	public function saveAsNew()
	{
		$sql = "INSERT INTO tblStats 
						(stat_date,
						 stat_object_type,
						 stat_object_id,
						 stat_ip_address,
						 stat_referer,
						 stat_user_agent) 
					VALUES 
						(NOW(),
						 " . $this->stat_object_type . ",
						 " . $this->stat_object_id . ",
						 '" . $_SERVER['REMOTE_ADDR'] . "',
						 '" . substr($_SERVER['HTTP_REFERER'],0,256) . "',
						 '" . substr($_SERVER['HTTP_USER_AGENT'],0,256) . "');";
		
		mysql_query($sql) or die(mysql_error());
	}
	public function update()
	{
		throw new Exception("Not allowed to update a pagestat");
	}
	
	public function getTableName()
	{
		return 'tblStats';
	}
	public function createTable()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `tblStats` (
					  `stat_id` int(11) NOT NULL auto_increment,
					  `stat_date` datetime NOT NULL,
					  `stat_object_type` int(11) NOT NULL,
					  `stat_object_id` tinyint(4) NOT NULL,
					  `stat_ip_address` varchar(20) NOT NULL,
					  `stat_referer`  varchar(256) NOT NULL,
					  `stat_user_agent` varchar(256) NOT NULL,
					  PRIMARY KEY  (`stat_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
				
		mysql_query($sql) or die(mysql_error());
	}
}

?>