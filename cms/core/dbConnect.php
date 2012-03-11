<?php

$dbSettings = new dbSettings();

if ($dbSettings->hasData) {
	// connect to mysql using the stored settings
	mysql_connect($dbSettings->db_server,
				  $dbSettings->db_username,
				  $dbSettings->db_password) 
			or die(mysql_error());
			
	mysql_select_db($dbSettings->db_name) or die(mysql_error());
	
	// ensure usign correct version
	$versionSQL = "SELECT id_dbVersion FROM _dbVersion;"; 
	$version = mysql_result(mysql_query($versionSQL),0,0);
	
	//if ($version != DB_VERSION) throw new Exception("CMS Database is not the correct version");
	if ($version != DB_VERSION) {echo "CMS Database version incorrect: currently at v" . $version . " and needs to be v" . DB_VERSION; exit;}
}

unset($dbSettings);

?>