<?php
require_once("cms.php");

// variable to ut login error's into
$ErrorMessage = "";

$settings = new dbSettings();

if (! $settings->hasData) {
	// If DB settings have not already been created 
	// then redirect to the init page
	header("location: initialize/");
	exit;
}

if (isset($_POST["hidFormSubmitted"])) {
	if ($_POST["hidFormSubmitted"] == "true") {
		
		$user_name = $_POST["usr"];
		$user_pass = $_POST["pwd"];
		
		$user = new swUser();
		
		$loginSuccessfull = $user->login($user_name,$user_pass);
		
		if ($loginSuccessfull) {
			$sessionObject = new swSessionObject();
			
			$sessionObject->isLoggedIn = true;
			$sessionObject->user = $user;
			$sessionObject->loadAllCMSContent();
			
			$log = new swLog();
			$log->log_object_type = dbObject::OBJECT_TYPE_USER;
			$log->log_object_id = $user->user_id;
			$log->log_type = swLog::LOG_TYPE_USER_LOGIN;
			$log->log_fk_user_id = $user->user_id;
			$log->saveAsNew();
		} else {
			$log = new swLog();
			$log->log_object_type = dbObject::OBJECT_TYPE_USER;
			$log->log_type = swLog::LOG_TYPE_USER_LOGIN_FAILED;
			$log->log_message = 'username: "' . $user_name . '" password: "'  . $user_pass . '"';
			$log->saveAsNew();
			
			$ErrorMessage = "<span style='color:#C00'>Login Failed</span>";
		}
	}
}

// if the user is logged in then redirect to the admin page
$sessionObject = $_SESSION["swSessionObject"];
if (isset($sessionObject) || $sessionObject->isLoggedIn) {
	// ensure the session data get's written before we redirect
	header("location: " . DOCUMENT_ROOT . "cms");
	exit;
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ScratchWebs CMS Login</title>
    <link rel="stylesheet" type="text/css" href="jquery.ui/css/uitheme/jquery-ui-1.8.16.custom.css">
    <style>
		body{background:#EAEAEA}
        /* Site wide styles */
        label {width: 90px;font-weight:bold;display:block;float:left;text-align:left;height:15px;}
        input[type=text], input[type=password], textarea {width:175px;margin-left:5px;height:15px;}
        /* Login Page Styles */
		#login #content {width:305px;margin:0 auto;}
        #login{margin-top:100px}
		.ui-widget {font-size:13px;width:305px;position:relative;z-index:1003;}
        .ui-widget p {padding-bottom:5px;}
        #login p.ui-state-error {padding:5px;display:none;}
        #login .ui-widget-header, #login .ui-widget div.ui-widget-content {padding:12px;}
		.ui-widget-header{border-bottom:none}
        #login .ui-widget form div {padding:3px 0px;}
        #login form {text-align:right;padding:0;margin:0}
        #login .ui-widget-overlay {z-index: 1002;}
		#btnLogin{width:100%;}
		#btnHelp{float:right;width:28px;height:16px;}
		.emailButton{width:28px;height:20px;}
		#helpDialog, #loginDialog{display:none;}
		
    </style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
    <script type="text/javascript">
		$(document).ready(function() {
			$("#btnHelp").button({icons: {primary: "ui-icon-help", text: false}});
			$("#btnHelp").click(function(){openHelp();});
			$("#dialogContainer").html($("#loginDialog").html());
			$("#btnLogin").button();
		});
		
		var curr_usr = '';
		var curr_pwd = '';
		var helpOpen = 0;
		function openHelp () {
			if (helpOpen == 0) {
				curr_usr = $("#user_name").val();
				curr_pwd = $("#user_pass").val();
				$("#boxTitle").html("ScratchWebs CMS Help");
				$("#dialogContainer").html($("#helpDialog").html());
				$(".emailButton").button({icons: {primary: "ui-icon-mail-closed", text: false}});
				$("#btnHelp").button({icons: {primary: "ui-icon-key", text: false}});
				$("#btnHelp").attr("title", "Back to Login");
				helpOpen = 1;
			}
			else {
				$("#boxTitle").html("ScratchWebs CMS Login");
				$("#dialogContainer").html($("#loginDialog").html());
				$("#user_name").val(curr_usr);
				$("#user_pass").val(curr_pwd);
				$("#btnLogin").button();
				$("#btnHelp").attr("title", "Help");
				$("#btnHelp").button({icons: {primary: "ui-icon-help", text: false}});
				helpOpen = 0;
			}
		}
		
		function submitForm () {
			type();
			document.getElementById('loginForm').submit();
		}
		
		var text="Please wait . . .";
		var delay=200;
		var currentChar=1;
		function type()
		{
			var dest = $("#loginError");
			if (dest)
			{
			  dest.html(text.substr(0, currentChar));
			  currentChar++
			  if (currentChar>text.length)
			  {
				currentChar=1;
				setTimeout("type()", 5000);
			  }
			  else
			  {
				setTimeout("type()", delay);
			  }
			}

		}
			
		function handleEnter(inField, e) {
			var charCode;
			if(e && e.which){charCode = e.which;}
			else if(window.event){
				e = window.event;
				charCode = e.keyCode;}
			if(charCode == 13) {
				$("#btnLogin").focus();
				$("#user_name").blur();
				$("#user_pass").blur();
				submitForm();
			}
		}
    </script>
</head>

<body>

    <div id="login">
        <div id="content">
            <div class="ui-widget ui-corner-all login">
                <div class="ui-widget-header ui-corner-top"><a id="btnHelp" title="Help"></a><span id="boxTitle">ScratchWebs CMS Login</span></div>
                <div id="dialogContainer" class="ui-widget-content ui-corner-bottom" style="padding-bottom:6px">

                </div>
            </div>
        </div>
        
    </div>
    						<div id="loginDialog">
                                <div style="margin-bottom:10px">Please login to the CMS...</div>
                                <form method="post" action="" id="loginForm"><input type="hidden" name="hidFormSubmitted" value="true" />
                                    <div><label for="user_name">Username:</label><input type="text" id="user_name" name="usr" value="<? if (isset($_POST["usr"])) echo $_POST["usr"]; ?>" /></div>
                                    <div><label for="user_pass">Password:</label><input type="password" id="user_pass" name="pwd" onkeypress="handleEnter(this, event)" value="" /></div>
                                    <div><a id="btnLogin" onClick="javascript:submitForm();">Login</a></div>
                                    <div id='loginError' style='text-align:center;color:#777'>
										<? if (isset($ErrorMessage)) echo $ErrorMessage; ?>
                                    </div>
                                </form>
                            </div>

                            <div id="helpDialog">
                            	<div style="margin-bottom:10px">Scratchwebs CMS version:	&nbsp;&nbsp;&nbsp;1.00</div>
                                <div style="margin-bottom:10px; text-align:justify">If you need any help or assistance with <b>ScratchWebs CMS</b>, or have any queries or suggestions, please do not hesitate to contact us:</div>
                                <table style="padding:0px; border:0px">
                                	<tr height="28"><td width="35"><a class="emailButton" href="mailto:tomhrvy@gmail.com"></a></td><td width="120">Thomas Harvey</td><td>07816 270 860</td></tr>
                                    <tr height="28"><td><a class="emailButton" href="mailto:looshus@gmail.com"></a></td><td>Luke Davies</td><td>07776 228 173</td></tr>
                                </table>
                            </div>
	</body>
</html>