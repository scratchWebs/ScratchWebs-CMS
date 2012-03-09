<?php

class swUser extends dbObject
{
	const UID = "usr";
	
	const USER_TYPE_EDITOR = 0;
	const USER_TYPE_ADMIN = 1;
	
	const DEFFAULT_USER_NAME = "swAdmin";
	const DEFFAULT_USER_PASSWORD = "swAdmin";
	const DEFFAULT_USER_FULL_NAME = "ScratchWebs Admin";
	const DEFFAULT_USER_EMAIL = "tomhrvy@gmail.com,looshus@gmail.com";
	
	public $user_id;
	public $user_name = "";
	public $user_pass = "";
	public $user_type = self::USER_TYPE_ADMIN;
	public $user_full_name = "";
	public $user_email = "";
	public $user_is_expired = true;
	
	public function getUID() {
		return self::UID . $this->user_id;
	}
	public function getObjectType()
	{
		return dbObject::OBJECT_TYPE_USER;
	}
	public function login($user,$pass)
	{
		$user_name = mysql_real_escape_string($user);
		$user_pass = mysql_real_escape_string($pass);
	
		$sql = "SELECT * 
				FROM tblUsers 
				WHERE user_name = '$user_name'
					AND user_pass = '$user_pass'
					AND delete_flag = 0
					AND enabled = 1;";
		
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createUserFromSQLData($data);
		
			return true;
		} else 
			return false;
	}
	
	public function createFromId($id) {
		
		$sql = "SELECT * 
				FROM tblUsers 
				WHERE user_id = $id;";
		
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) == 1) {
			$data = mysql_fetch_array($result);
			
			$this->createUserFromSQLData($data);
		
			return true;
		} else {
			return false;
		}
	}
	public function createUserFromSQLData($data)
	{
			$this->delete_flag = $data["delete_flag"];
			$this->enabled = $data["enabled"];
			$this->user_id = $data["user_id"];
			$this->user_name = $data["user_name"];
			$this->user_pass = $data["user_pass"];
			$this->user_type = $data["user_type"];
			$this->user_full_name = $data["user_full_name"];
			$this->user_email = $data["user_email"];
			$this->user_is_expired = $data["user_is_expired"];
	}
	
	public function saveAsNew() {
		
		$sql = "INSERT INTO tblUsers 
						(delete_flag,
						 enabled,
						 user_name,
						 user_pass,
						 user_type,
						 user_full_name,
						 user_email,
						 user_is_expired)
					 VALUES 
						(" . (int) $this->delete_flag . ",
						 " . (int) $this->enabled . ",
						 '" . mysql_real_escape_string(substr($this->user_name,0,20)) . "',
						 '" . mysql_real_escape_string(substr($this->user_pass,0,20)) . "',
						 " . $this->user_type . ",
						 '" . mysql_real_escape_string(substr($this->user_full_name,0,50)) . "',
						 '" . mysql_real_escape_string(substr($this->user_email,0,100)) . "',
						 " . (int) $this->user_is_expired . ");";
		
						 
		if (mysql_query($sql))
			return true;
		else
			return false;
		
	}
	public function update() {
		
		$success = false;
		
		if ($this->user_id !== NULL) {
		
			$sql = "UPDATE tblUsers 
						SET delete_flag = " . (int) $this->delete_flag . ",
							enabled = " . (int) $this->enabled . ",
							user_name = '" . mysql_real_escape_string(substr($this->user_name,0,20)) . "',
							user_pass = '" . mysql_real_escape_string(substr($this->user_pass,0,20)) . "',
							user_type = " . $this->user_type . ",
							user_full_name = '" . mysql_real_escape_string(substr($this->user_full_name,0,50)) . "',
							user_email = '" . mysql_real_escape_string(substr($this->user_email,0,100)) . "',
							user_is_expired = " . (int) $this->user_is_expired . "
					WHERE user_id = " . $this->user_id . ";";
						 
			if (mysql_query($sql))	$success =  true;
		}
		
		return $success;
			
	}
	
	public function getTableName() {
		return 'tblUsers';
	}
	public function createTable() {
		
		// CREATE TABLE 
		// AND INESRT DEFAULT ADMIN USER
		$sql = "CREATE TABLE IF NOT EXISTS `tblUsers` (
					  `delete_flag` tinyint(1) NOT NULL default '0',
					  `enabled` tinyint(1) NOT NULL default '1',
					  `user_id` int(11) NOT NULL auto_increment,
					  `user_name` varchar(20) NOT NULL,
					  `user_pass` varchar(20) NOT NULL,
					  `user_type` tinyint(4) NOT NULL,
					  `user_full_name` varchar(50) NOT NULL,
					  `user_email` varchar(100) NOT NULL,
					  `user_is_expired` tinyint(1) NOT NULL default '1',
					  PRIMARY KEY  (`user_id`)
					) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
					
		if (mysql_query($sql)) {
			
			$this->user_name = self::DEFFAULT_USER_NAME;
			$this->user_pass = self::DEFFAULT_USER_PASSWORD;
			$this->user_type = self::USER_TYPE_ADMIN;
			$this->user_full_name = self::DEFFAULT_USER_FULL_NAME;
			$this->user_email = self::DEFFAULT_USER_EMAIL;
			
			return true;
		} else {
			return false;
		}

	}
}

?>