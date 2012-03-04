<?php

$dbSettings = new dbSettings();

if ($dbSettings->hasData) {
	// connect to mysql using the stored settings
	mysql_connect($dbSettings->db_server,
				  $dbSettings->db_username,
				  $dbSettings->db_password) 
			or die(mysql_error());
			
	mysql_select_db($dbSettings->db_name) or die(mysql_error());
}

unset($dbSettings);

?>