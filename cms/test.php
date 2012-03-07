<?php
require_once("cms.php");
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<style>
body{text-size:8pt}
</style>
</head>
<body>
<div id="div_log_update">
<h3>Showing last 50 events</h3>
<table class="logTable">
<tr>
    <th>Date</th>
    <th>User</th>
    <th>Type</th>
    <th>Log Message</th>
    <th>User Agent</th>
    <th>IP Address</th>
</tr>
<?
$logs = swLog::getLogs(50);
foreach ($logs as $log)
{
	echo '<tr>';
	
	echo '<td>' . $log->log_date . '</td>';
	echo '<td>' . $log->user_full_name . '</td>';
	echo '<td>' . $log->getTypeDescription() . '</td>';
	echo '<td>' . $log->log_message . '</td>';
	echo '<td>' . $log->log_user_agent . '</td>';
	echo '<td><a href="http://whois.domaintools.com/' . $log->ip_address . '">' . $log->ip_address . '</a></td>';
	
	echo '</tr>';
}
?>
</table>
</div>

<br /><br /><br /><br />

<div id="div_stats_update">
<h3>Last 50 Site Visit Statistics</h3>
<table class="logTable">
<tr>
    <th>Date</th>
    <th>Viewed Item</th>
    <th>Referer</th>
    <th>User Agent</th>
    <th>IP Address</th>
</tr>
<?
$stats = swPageStat::getStats(50);
foreach ($stats as $stat)
{
	echo '<tr>';
	
	echo '<td>' . $stat->stat_date . '</td>';
	echo '<td>' . $stat->getObjectDescription() . '</td>';
	echo '<td>' . $stat->stat_referer . '</td>';
	echo '<td>' . $stat->stat_user_agent . '</td>';
	echo '<td><a href="http://whois.domaintools.com/' . $stat->stat_ip_address . '">' . $stat->stat_ip_address . '</a></td>';
	
	echo '</tr>';
}
?>
</table>
</div>
</body>
</html>