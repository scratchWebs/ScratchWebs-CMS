<?php
require_once("cms.php");
?>
<div id="div_stats_update">
<h3>Last 50 Site Visit Statistics</h3>
<p><a href="" id="a_stats_refresh" onclick="tabStats_refresh();return false" style="float:right">Refresh</a>
Last Refreshed at: <?= date('d/m/y h:m:s', time()) ?></p>
<table class="logTable" style="padding:0px; border:0px">
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

	<?  if ($_GET['refresh'] != 'true') {  ?>
        <script type="text/javascript">
			var timer_refreshStats;
        	$(document).ready(function(e) {
                tabStats_refresh();
            });
			function tabStats_refresh() {
				// refresh page via ajax on a timer
				if (typeof timer_refreshStats != 'undefined') clearTimeout(timer_refreshStats);
				timer_refreshStats = setTimeout("tabLog_refresh()",10000);
				
				// only when the log tab is open
				if ($('#a_stats_refresh').is(':visible')) {
					$('#a_stats_refresh').text('Refreshing...');
					$.swAjax('tabStats.php',{refresh:true}, function(response){
						$('#div_stats_update').replaceWith(response)
					},'html')
				}
			}
        </script>
    <?  }  ?>