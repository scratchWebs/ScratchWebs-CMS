<?php

class swLog extends dbObject
{
	const UID = "log";
	
	const LOG_TYPE_USER_LOGIN = 0;
	const LOG_TYPE_USER_LOGOUT = 1;
	const LOG_TYPE_USER_LOGIN_FAILED = 2;
	
	const LOG_TYPE_COMMIT_OBJECT = 10;
	const LOG_TYPE_SESSION_UPDATE = 20;
	const LOG_TYPE_SESSION_UPDATE_UNDO = 30;
	
	public $delete_flag;
	public $log_id;
	public $log_object_type;
	public $log_object_id = -1;
	public $log_type;
	public $log_message = '';
	public $log_date;
	public $ip_address;
	public $log_user_agent;
	public $log_fk_user_id = -1;
	
	public $user_full_name;
	
	public function getTypeDescription()
	{
		switch($this->log_type){
			case swLog::LOG_TYPE_USER_LOGIN:
				return "User Login";
				break;
			case swLog::LOG_TYPE_USER_LOGIN_FAILED:
				return "User Login Failed";
				break;
			case swLog::LOG_TYPE_USER_LOGOUT:
				return "User Logout";
				break;
			case swLog::LOG_TYPE_COMMIT_OBJECT:
				return "Committed Update";
				break;
			case swLog::LOG_TYPE_SESSION_UPDATE:
				return "Session Updated";
				break;
			case swLog::LOG_TYPE_SESSION_UPDATE_UNDO:
				return "Session Update (Undo)";
				break;
			default:
				return "unknown log type";
		}
		
	}
	public function getUID() {
		return self::UID . $this->log_id;
	}
	public function getObjectType()
	{
		return dbObject::OBJECT_TYPE_LOG;
	}
	public static function getLogs($ammount = 50)
	{
		$logs = array();
		
		$sql = "SELECT tblLog.* ,tblUsers.user_full_name
				FROM tblLog 
					JOIN tblUsers ON tblUsers.user_id = tblLog.log_fk_user_id
						OR tblLog.log_fk_user_id = -1
				ORDER BY tblLog.log_date DESC
				LIMIT 0," . $ammount . "";
		
		$result = mysql_query($sql) or die(mysql_error());
		
		while ($data = mysql_fetch_array($result))
		{
			$log = new swLog();
			$log->createLogFromSQLData($data);
			
			$logs[$log->log_id] = $log;
		}
		
		return $logs;
	}
	public function createLogFromSQLData($data)
	{
		$this->delete_flag = $data["delete_flag"];
		$this->log_id = $data["log_id"];
		$this->log_object_type = $data["log_object_type"];
		$this->log_object_id = $data["log_object_id"];
		$this->log_type = $data["log_type"];
		$this->log_message = $data["log_message"];
		$this->log_date = $data["log_date"];
		$this->ip_address = $data["ip_address"];
		$this->log_user_agent = $data["log_user_agent"];
		$this->log_fk_user_id = $data["log_fk_user_id"];
		
		$this->user_full_name = $data["user_full_name"];
	}
	public function createFromId($id)
	{
		throw new Exception("not able to create a log message from id yet");
	}
	public function saveAsNew()
	{
		$sql = "INSERT INTO tblLog 
						(delete_flag,
						 log_object_type,
						 log_object_id,
						 log_type,
						 log_message,
						 log_date,
						 ip_address,
						 log_user_agent,
						 log_fk_user_id) 
					VALUES 
						(" . (int) $this->delete_flag . ",
						 " . $this->log_object_type . ",
						 " . $this->log_object_id . ",
						 " . $this->log_type . ",
						 '" . substr(mysql_real_escape_string($this->log_message),0,200) . "',
						 NOW(),
						 '" . $_SERVER['REMOTE_ADDR'] . "',
						 '" . $_SERVER['HTTP_USER_AGENT'] . "',
						 " . $this->log_fk_user_id . ");";
		
		mysql_query($sql) or die(mysql_error());
	}
	public function update()
	{
		throw new Exception("Not allowed to update a log entry");
	}
	
	public function getTableName()
	{
		return 'tblLog';
	}
	public function createTable()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `tblLog` (
				   	  `delete_flag` tinyint(1) NOT NULL default '0',
					  `log_id` int(11) NOT NULL auto_increment,
					  `log_object_type` tinyint(4) NOT NULL,
					  `log_object_id` int(11) NOT NULL,
					  `log_type` tinyint(4) NOT NULL,
					  `log_message` varchar(200) NOT NULL,
					  `log_date` datetime NOT NULL,
					  `ip_address` varchar(20) NOT NULL,
					  `log_user_agent` varchar(256) NOT NULL,
					  `log_fk_user_id` int(11) NOT NULL,
					  PRIMARY KEY  (`log_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
				
		mysql_query($sql) or die(mysql_error());
	}
}

?>