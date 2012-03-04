<?php
require_once("cms.php");

$sessionObject = new swSessionObject();

$log = new swLog();
$log->log_object_type = dbObject::OBJECT_TYPE_USER;
$log->log_object_id = $sessionObject->user->user_id;
$log->log_type = swLog::LOG_TYPE_USER_LOGOUT;
$log->log_message = '';
$log->log_fk_user_id = $sessionObject->user->user_id;
$log->saveAsNew();

session_unset();
session_destroy();

header("location: login.php");

?>