<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
<?php 
// Global Variables
define('DATAFILE', '/opt/checkup/www/checkup_data.csv.gz');
?>
<script>
window.onload = function () {
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	zoomEnabled: true,
	title:{
		text: "Antenna Ping times (updated every minute)"
	},
	axisX: {
		valueFormatString: "MMM DD hh:mmtt"
	},
	axisY: {
		title: "Ping (in milliseconds)",
		suffix: " ms",
		includeZero: true
	},
	legend:{
		cursor: "pointer",
		fontSize: 16,
		itemclick: toggleDataSeries
	},
	toolTip:{
		shared: true
	},
	data: [
<?php		
 $csv = array_map('str_getcsv', gzfile(DATAFILE));
 array_walk($csv, function(&$a) use ($csv) {
	// Ensure array is always consistent length
	$a = array_pad($a, count($csv[0]), '-0');
	$a = array_combine($csv[0], $a);
 });
 array_shift($csv); // pop off header
 array_multisort(array_column($csv,'ip'), SORT_ASC, $csv);
 $last_ip = "first.in.the.list";
 $totals['Total pings'] = count($csv)-1; $i=0;
 foreach ($csv as $p) {
	if ($p['ip'] !== $last_ip) { // start a new group of ips
		if ($last_ip!=="first.in.the.list") { // finished block 
			echo "\t]\n},\n"; 
			$totals[$p['ip']] = $i; $i=0; // Update totals for IP
		}
		echo "{\n\tname: \"$p[ip]\",\n\ttype: \"spline\",\n\tconnectNullData: true,\n\tshowInLegend: true,\n\tdataPoints: [\n";
		$last_ip = $p['ip']; 
	}
	// Convert to JS date
	$d = explode("-",$p['date']);
	$t = str_split($p['time'],2);
	$jsDate = $d[2].','.($d[0]-1).",$d[1],$t[0],$t[1],$t[2]";
	echo "\t\t{ x: new Date($jsDate), y:".(($p['avg']>0)?$p['avg']:'-0')."},\n";  //each ping datapoint
	$i++;
	$totals[$p['ip']] = $i; // Update totals for IP
}
?>
]}]});
chart.render();
function toggleDataSeries(e){
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	} else {
		e.dataSeries.visible = true;
	}
	chart.render();
}}
// Alternating table rows
$("tr:odd").css({
    "background-color":"#000",
    "color":"#fff"});
</script>
</head>
<body>
<div id="chartContainer" style="height: 300px; width: 100%;"></div>
<script src="canvasjs.min.js"></script>

<style>
table, th, td {
  border: 3px solid black;
  border-radius: 2px;
}
tbody td {
  padding: 2px;
}
tbody tr:nth-child(odd) {
  background-color: #4C8BFF;
  color: #fff;
}
</style>
<div style="float:left">Totals:<hr/>
<table width=400px>
<?php
foreach($totals as $k=>$v) {
    echo "<tr><td align=right>$k</td><td>$v hits</td></tr>";
}
?>
</table></div>
<div style="float:right"><pre>Recent: (here is the latest run)<hr>
<table width=600px>
<?php
$lastrun = explode("\n", shell_exec("zcat ".DATAFILE." | tail -".(count($totals)-1)));
foreach($lastrun as $v) {
    $data = explode(",", $v);	
    echo "<tr>";
    foreach ($data as $v) echo "<td align=right>$v</td>";
    echo "</tr>";
}
?>
</table>
</pre></div>

<span style="position: absolute; left: 0px; top: -20000px; padding: 0px; margin: 0px; border: none; white-space: pre; line-height: normal; font-family: &quot;Trebuchet MS&quot;, Helvetica, sans-serif; font-size: 15px; font-weight: normal; display: none;">Mpgyi</span></body></html>
