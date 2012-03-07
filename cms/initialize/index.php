<?php
require_once("../cms.php");

$settings = new dbSettings();

if ($settings->hasData) {
	// If settings have already been created 
	// then don't allow them to be changed
	header("location: ../");
	
} else {
	
	if (!$_POST["init"] == "true") {
		_showForm();
	} else {
		_init();
	}
}

unset($settings);

function _init() {
	
	require_once(PATH_CORE . "swInitializer.php");
	
	$init = new swInitializer;
	
	$init->db_server = $_POST["db_server"];
	$init->db_username = $_POST["db_username"];
	$init->db_password = $_POST["db_password"];
	$init->db_name = $_POST["db_name"];
	
	if ($_POST["testConnection"] == "true") {
		$init->testConnection();
	} else {
		if ($init->initialize()) {
			echo "<br /><br />Successfully initialized<br />
				  <br />default username: " . swUser::DEFFAULT_USER_NAME . "
				  <br />default password: " . swUser::DEFFAULT_USER_PASSWORD . "
				  <br /><br /><a href=\"../\">Click here to login...</a>";
		} else {
			echo "<br />Error while initializing";
		}
	}
	
}


function _showForm() {
	
	?>
    
    <!DOCTYPE html>
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$title?></title>
    <script src="/cms/scripts/jquery-1.6.4.min.js"></script>
	<script type="text/javascript">
	function _testConnection() {
		_init("&testConnection=true");
	}
	function _init(param) {
		
		$('#divSuccess').hide();
		$('#divError').hide();
		
		var server = document.getElementById('txtServer').value;
		var username = document.getElementById('txtUsername').value;
		var password = document.getElementById('txtPassword').value;
		var db_name = document.getElementById('txtDBName').value;
		
		$.ajax({
			type: "POST",
			url: "?",
			data: "init=true" +
				  "&db_server=" + server +
				  "&db_username=" + username +
				  "&db_password=" + password +
				  "&db_name=" + db_name +
				  param,
			success: function(msg){	
				$('#divSuccess').html(msg).show();
			},
			error: function(e) {
				$('#divError').html(e.statusText).show();
				document.getElementById('btnTest').disabled=false;
				document.getElementById('btnInit').disabled=false;
			}
		});
		
	}
	</script>
    </head>
    <body>
	
	<h1>Initialize ScratchWebs CMS</h1>
    
        <form method="post" action="return false">
            <table>
                <tr>
                    <td>Database server:</td>
                    <td><input id="txtServer" type="text" name="db_server" value="db1" /></td>
                </tr>
                <tr>
                    <td>Database username:</td>
                    <td><input id="txtUsername" type="text" name="db_username" value="audley_1" /></td>
                </tr>
                <tr>
                    <td>Database password:</td>
                    <td><input id="txtPassword" type="password" name="db_password" value="ArchieP4ss" /></td>
                </tr>
                <tr>
                    <td>Database name:</td>
                    <td><input id="txtDBName" type="text" name="db_name" value="audley_1" /></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input id="btnTest" type="button" value="Test Connection" onClick="_testConnection()" />
                        <input id="btnInit" type="button" value="Initialize" onClick="_init('');
                                                        document.getElementById('btnTest').disabled=true;
                                                        this.disabled=true" />
                    </td>
                </tr>
            </table>
            <div id="divSuccess" style="display:none"></div>
            <div id="divError" style="display:none"></div>
        </form>
    </body>
    </html>
    
	<?
	
}
?>