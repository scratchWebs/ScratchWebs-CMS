<?php
require_once("cms.php");
?>
<div id="div_stats_update">
<h3>Website Statistics</h3>
<p><a href="" id="a_stats_refresh" onclick="tabStats_refresh(); return false;" style="float:right">Refresh</a>
Last Refreshed at: <?= date('d/m/y h:m:s', time()) ?></p>

<p style="text-decoration:underline">Website traffic over last month</p>



<script type="text/javascript" src="scripts/statsGraphics.js"></script>
<script type="text/javascript" src="scripts/statsGraphicsLine.js"></script>
<script type="text/javascript" src="scripts/statsGraphicsPie.js"></script>
<script type="text/javascript" src="scripts/statsGraphicsBar.js"></script>


<div id="barCanvas" style="overflow: hidden; position:relative;height:200px;width:868px;"></div>
<script>
	var g = new graph();

<?php 

	$visits = array();							// retrieve the stats
	$browsers = array();
	$ips = array();
	$stats = swStat::getStats(31);
	foreach ($stats as $stat)	{
		$visits[] = $stat->stat_date;
		$browsers[] = $stat->stat_user_agent;
		//$stat->getObjectDescription()
		//$stat->stat_referer
		//$stat->stat_ip_address
	}

	//$dates[][];									// sort visits
	//for($i=0; $i < count($visits); $i++) { 			
	//	  if (in_array(visits[i], $dates)) $dates[$visits[i]]++;
	//	  else $dates[visits[i], 1];
	//	}
	
	$lastmonth = mktime(0, 0, 0, date("m"), date("d")-31,   date("Y"));
	
	echo "g.add('" . date('d\<\b\r\/\>M', $lastmonth) . "'," . rand(0,15) . "); ";
			
	for ($i = 30; $i >= 0; $i--) {
		
		$date = mktime(0, 0, 0, date("m"), date("d")-$i,   date("Y"));
		
		if (date('d', $date) == 1) echo "g.add('" . date('d\<\b\r\/\>M', $date) . "'," . rand(0,15) . "); ";
	    else echo "g.add('" . date('d', $date) . "'," . rand(0,15) . "); ";
	}
	?>
	
	g.render("barCanvas", "", 150);
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