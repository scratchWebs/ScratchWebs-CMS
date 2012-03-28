<?php
require_once("cms.php");
?>
<div id="div_stats_update">
<h3>Last 50 Site Visit Statistics</h3>
<p><a href="" id="a_stats_refresh" onclick="tabStats_refresh(); return false;" style="float:right">Refresh</a>
Last Refreshed at: <?= date('d/m/y h:m:s', time()) ?></p>



<script type="text/javascript" src="scripts/statsGraphics.js"></script>
<script type="text/javascript" src="scripts/statsGraphicsLine.js"></script>
<script type="text/javascript" src="scripts/statsGraphicsPie.js"></script>
<script type="text/javascript" src="scripts/statsGraphicsBar.js"></script>

<div id="lineCanvas" style="overflow: auto; position:relative;height:300px;width:100%;"></div>
<script type="text/javascript">
var g = new line_graph();
g.add('1', 145);
g.add('2', 0);
g.add('3', 175);
g.add('4', 130);
g.add('5', 150);
g.add('6', 175);
g.add('7', 205);
g.add('8', 125);
g.add('9', 125);
g.add('10', 135);
g.add('11', 125);
g.render("lineCanvas", "Line Graph");
</script>


<div id="pieCanvas" style="overflow: auto; position:relative;height:350px;width:380px;"></div>
<script type="text/javascript">
var p = new pie();
p.add("Firefox",100);
p.add("IE",200);
p.add("Chrome",150);
p.add("Safari",120);
p.add("Other",35);
p.render("pieCanvas", "Pie Graph")
</script>

<div id="barCanvas" style="overflow: auto; position:relative;height:300px;width:400px;"></div>
<script>
var g = new graph();
//for small values < 5, use a scale of 10x and for values < 1, use 100x
//g.setScale(10);
g.add('01<br>Jan', 145);
g.add('2', 0);
g.add('3', 50);
g.add('4', 130);
g.add('5', 117);
g.add('6', 175);
g.add('7', 205);
g.add('8', 125);
g.add('9', 125);
g.add('10', 135);
g.add('11', 125);
//If called without a height parameter, defaults to 250
//g.render("myCanvas", "test graph");
g.render("barCanvas", "Bar Graph", 250);
</script>


<table class="logTable" style="padding:0px; border:0px">
<tr>
    <th>Date</th>
    <th>Viewed Item</th>
    <th>Referer</th>
    <th>User Agent</th>
    <th>IP Address</th>
</tr>
<?
$stats = swStat::getStats(50);
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