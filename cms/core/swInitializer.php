<?php

class swInitializer
{
	public $db_server;
	public $db_username;
	public $db_password;
	public $db_name;
	
	public function testConnection() {
		$this->_testConnection(false);
	}
	
	private function _testConnection($isInitializing) {
		
		if (!mysql_connect($this->db_server, $this->db_username, $this->db_password)) {
			echo "error: connecting to server";
			return false;
			
		} else if (!mysql_select_db($this->db_name)) {
			echo "error: check database name";
			return false;
			
		} else {
			if (!$isInitializing) {
				echo "Success!";
			}
			return true;
		}	
	}
	
	public function initialize() {
		
		// first test the connection
		if ($this->_testConnection(true)) {
	
			$settings = new dbSettings();
			
			$settings->db_server = $this->db_server;
			$settings->db_username = $this->db_username;
			$settings->db_password = $this->db_password;
			$settings->db_name = $this->db_name;
			
			if ($settings->saveSettings() && 
				$this->_createTables()) {
				
				return true;
				
			} else {
				
				$settings->deleteSettings();
				
				return false;
			}
		} else {
			return false;
		}
		
	}
	
	private function _createTables() {
		
		try 
		{
			// Tables are in a specific order to ensure 
			// the foregin key's are correctly created
			$modules = array(new swPage(),
							 new swPortfolio(),
							 new swSection(),
							 new swGallery(),
							 new swImage(),
							 new swUser(),
							 new swLog(),
							 new swPageStat());
							 
			foreach ($modules as $dbObject) {
				echo "<br />creating table " . $dbObject->getTableName();
				$dbObject->createTable();
			}
			
			return true;
		} 
		catch(Exception $e) 
		{
			echo "<br />Error creating table";
			return false;
		}
		
	}
}

?>