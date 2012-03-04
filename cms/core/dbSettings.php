<?php

class dbSettings
{
	const filename = "settings.ini";
	
	public $hasData = false;
	
	public $db_server;
	public $db_username;
	public $db_password;
	public $db_name;
	
	public function __construct() {
		
		if (file_exists(PATH_LOCAL . self::filename)) {
			$constants = parse_ini_file(PATH_LOCAL . self::filename);
			
			$this->db_server = $constants["db_server"];
			$this->db_username = $constants["db_username"];
			$this->db_password = $constants["db_password"];
			$this->db_name = $constants["db_name"];
			
			$this->hasData = true;
		}
	}
	
	public function saveSettings() {
		
		$constants = array(
						'db_server' => $this->db_server,
						'db_username' => $this->db_username,
						'db_password' => $this->db_password,
						'db_name' => $this->db_name
					 );
		
		if ($this->write_ini_file($constants, PATH_LOCAL . self::filename)) {
			return true;
		} else {
			return false;
		}
		
	}
	
	public function deleteSettings() {
		if (file_exists(PATH_LOCAL . self::filename)) {
			unlink(PATH_LOCAL . self::filename);
		}
	}
	
	public function write_ini_file($assoc_arr, $path) { 
	
		$content = ""; 
		
		foreach ($assoc_arr as $key=>$elem) { 
			if(is_array($elem)) 
			{ 
				for($i=0;$i<count($elem);$i++) 
				{ 
					$content .= $key."[] = \"".$elem[$i]."\"\n"; 
				} 
			} 
			else if($elem=="") $content .= $key." = \n"; 
			else $content .= $key." = \"".$elem."\"\n"; 
		} 
	
		if (!$handle = fopen($path, 'w')) { 
			return false; 
		} 
		if (!fwrite($handle, $content)) { 
			return false; 
		} 
		fclose($handle); 
		return true; 
	}
}

?>