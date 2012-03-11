<?php

$dbSettings = new dbSettings();

if ($dbSettings->hasData) {
	// connect to mysql using the stored settings
	mysql_connect($dbSettings->db_server,
				  $dbSettings->db_username,
				  $dbSettings->db_password) 
			or die(mysql_error());
			
	mysql_select_db($dbSettings->db_name) or die(mysql_error());
	
	// ensure using correct db version
	$dbVersionSQL = "SELECT id_dbVersion FROM _dbVersion;"; 
	$dbVersion = mysql_result(mysql_query($dbVersionSQL),0,0);
	
	if ($dbVersion != DB_VERSION) 
		throw new Exception("CMS Database version mismatch: 
							 website is at v" . DB_VERSION . ". and database is at v" . $dbVersion);
}

unset($dbSettings);	// close the settings so as to not lock the file

?>