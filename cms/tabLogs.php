<?php
require_once("cms.php");
?>
<div id="div_log_update">
<h3>Showing last 50 events</h3>
<p><a href="" id="a_log_refresh" onclick="tabLog_refresh();return false" style="float:right">Refresh</a>
Last Refreshed at: <?= date('d/m/y h:m:s', time()) ?></p>
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

	<?  if ($_GET['refresh'] != 'true') {  ?>
        <script type="text/javascript">
			var timer_refreshLog;
        	$(document).ready(function(e) {
                tabLog_refresh();
            });
			function tabLog_refresh() {
				// refresh page via ajax on a timer
				if (typeof timer_refreshLog != 'undefined') clearTimeout(timer_refreshLog);
				timer_refreshLog = setTimeout("tabLog_refresh()",10000);
				
				// only when the log tab is open
				if ($('#a_log_refresh').is(':visible')) {
					$('#a_log_refresh').text('Refreshing...');
					$.swAjax('tabLogs.php',{refresh:true}, function(response){
						$('#div_log_update').replaceWith(response)
					},'html')
				}
			}
        </script>
    <?  }  ?>