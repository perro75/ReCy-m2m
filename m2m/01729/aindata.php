<?php

include 'settings.php';
include 'lib/header.php';

$ain = $_GET['ain'];
$layer = $_GET['layer'];
$name = $ainData[$layer][$ain]['name'];
$type = $ainData[$layer][$ain]['type'];
$high = $ainData[$layer][$ain]['high'];
$low = $ainData[$layer][$ain]['low'];

echo "<h2>Statistics for $type $name</h2>";

echo @"
<table>
<tr><td>
<a class=\"toplink\" href=\"?ain=$ain&layer=$layer\">24 HOURS, AVG ONLY</a>
</td><td>
<a class=\"toplink\" href=\"?ain=$ain&layer=$layer&DISPLAY=ALL\">24 HOURS, MIN & MAX</a>
</td><td>
<a class=\"toplink\" href=\"?ain=$ain&layer=$layer&GRAPH=LONG\">24 DAYS, AVG ONLY</a>
</td><td>
<a class=\"toplink\" href=\"?ain=$ain&layer=$layer&GRAPH=LONG&DISPLAY=ALL\">24 DAYS, MIN & MAX</a>
</td></tr>

<tr><td>
<a class=\"toplink\" href=\"?ain=$ain&layer=$layer&GRAPH=SHORT&LIMIT=720\">12 HOURS, AVG ONLY</a>
</td><td>
<a class=\"toplink\" href=\"?ain=$ain&layer=$layer&GRAPH=SHORT&DISPLAY=ALL&LIMIT=720\">12 HOURS, MIN & MAX</a>
</td><td>
<a class=\"toplink\" href=\"?ain=$ain&layer=$layer&GRAPH=SHORT&LIMIT=60\">LAST HOUR, AVG ONLY</a>
</td><td>
<a class=\"toplink\" href=\"?ain=$ain&layer=$layer&GRAPH=SHORT&DISPLAY=ALL&LIMIT=60\">LAST HOUR, MIN & MAX</a>
</td></tr>
</table>
";

echo printStats($db, $ainData, $ain, $layer, $_GET);


echo "<table class=\"sensordata\"><tr><td>";

echo "<ul>";
 echo "<li>High limit: $high</li>";
 echo "<li>Low limit: $low</li>";
 echo "<li>Unit: ".toUnit($type)."</li>";
 echo "<li>Value/5V: ".toUnitVal(5000, $type)."</li>";
 echo "<li>Value/4V: ".toUnitVal(4000, $type)."</li>";
 echo "<li>Value/3V: ".toUnitVal(3000, $type)."</li>";
 echo "<li>Value/2V: ".toUnitVal(2000, $type)."</li>";
 echo "<li>Value/1V: ".toUnitVal(1000, $type)."</li>";
 echo "<li>Value/0V: ".toUnitVal(0000, $type)."</li>";
echo "</ul>";


//**** WEEKSTATS ****
$res = $db->query(@"SELECT MAX(max$ain) as max, AVG(avg$ain) as avg, MIN(min$ain) as min, weekNo FROM ARANA_BY_HOUR WHERE layer = $layer GROUP BY weekNo ORDER BY ts DESC LIMIT 10 ");
$res->setFetchMode(PDO::FETCH_NUM);
echo "</td><td>";
	echo "<table class=\"weekstats\"> <tr> <th>WEEK</th> <th>MIN</th> <th>AVG</th> <th>MAX</th> </tr>";

	foreach ($res as $row)
	{	
		$min = sprintf ("%1.1f",toUnitVal($row[2], $type)).' '.toUnit($type);
		$avg = sprintf ("%1.1f",toUnitVal($row[1], $type)).' '.toUnit($type);
		$max = sprintf ("%1.1f",toUnitVal($row[0], $type)).' '.toUnit($type);
		echo "<tr><td>".$row[3]."</td><td>$min</td><td>$avg</td><td>$max</td></tr>";
	}

	echo "</table>";

//**** DAYSTATS ****
$res = $db->query(@"SELECT MAX(max$ain) as max, AVG(avg$ain) as avg, MIN(min$ain) as min, dayNo FROM ARANA_BY_HOUR WHERE layer = $layer GROUP BY dayNo ORDER BY ts DESC LIMIT 10 ");
$res->setFetchMode(PDO::FETCH_NUM);
echo "</td><td>";
	echo "<table class=\"weekstats\"> <tr> <th>DAY</th> <th>MIN</th> <th>AVG</th> <th>MAX</th> </tr>";

	foreach ($res as $row)
	{	
		$min = sprintf ("%1.1f",toUnitVal($row[2], $type)).' '.toUnit($type);
		$avg = sprintf ("%1.1f",toUnitVal($row[1], $type)).' '.toUnit($type);
		$max = sprintf ("%1.1f",toUnitVal($row[0], $type)).' '.toUnit($type);
		echo "<tr><td>".$row[3]."</td><td>$min</td><td>$avg</td><td>$max</td></tr>";
	}

	echo "</table>";

//**** MINUTESTATS ****
$res = $db->query(@"SELECT MAX(max$ain) as max, AVG(avg$ain) as avg, MIN(min$ain) as min, minuteNo FROM ARANA_BY_MIN WHERE layer = $layer GROUP BY minuteNo ORDER BY ts DESC LIMIT 10 ");
$res->setFetchMode(PDO::FETCH_NUM);
echo "</td><td>";
	echo "<table class=\"weekstats\"> <tr> <th>MINUTE</th> <th>MIN</th> <th>AVG</th> <th>MAX</th> </tr>";

	foreach ($res as $row)
	{	
		$min = sprintf ("%1.1f",toUnitVal($row[2], $type)).' '.toUnit($type);
		$avg = sprintf ("%1.1f",toUnitVal($row[1], $type)).' '.toUnit($type);
		$max = sprintf ("%1.1f",toUnitVal($row[0], $type)).' '.toUnit($type);
		echo "<tr><td>".$row[3]."</td><td>$min</td><td>$avg</td><td>$max</td></tr>";
	}

	echo "</table>";

echo "</td></tr></table>";
include 'lib/footer.php';

function printStats($db, $ainData, $ain, $layer, $_GET)
{

	$name = $ainData[$layer][$ain]['name'];
	$type = $ainData[$layer][$ain]['type'];
	$high = $ainData[$layer][$ain]['high'];
	$low = $ainData[$layer][$ain]['low'];

	if ($_GET['GRAPH'] == 'SHORT')
	{
			$limit = isset($_GET['LIMIT']) ? 'LIMIT '.$_GET['LIMIT'] : ''; 
			$gTitle = "$type $name by MINUTE";
			$res = $db->query(@
			"SELECT 
			AVG(avg$ain) as avg,
			MAX(max$ain) as max,
			MIN(min$ain) as min
			FROM ARANA_BY_MIN  
			WHERE layer = $layer 
			GROUP BY yearNo, dayNo, hourNo, minuteNo  
			ORDER BY ts DESC $limit
			");
	}
	else
	{
			//GRAPH
			if ($_GET['GRAPH'] == 'LONG')
			{
				$gTitle = "$type $name 24 days";
				$lim = "576";
			}
			else
			{
			
				$gTitle = "$type $name 24 hours ";
				$lim = "24";
			}

		$res = $db->query(@
			"SELECT 
			AVG(avg$ain) as avg,
			MAX(max$ain) as max,
			MIN(min$ain) as min
			FROM ARANA_BY_HOUR  
			WHERE layer = $layer 
			GROUP BY yearNo, dayNo, hourNo  
			ORDER BY ts DESC LIMIT $lim
			");
	}
	
$res->setFetchMode(PDO::FETCH_NUM);

//series names
$name = array();
$name[0] = "AVG";
$name[1] = "MAX";
$name[2] = "MIN";

//used by chart_sensor.php
$yMin = 10000;
$yMax = -10000;
$yScale = 1;
$dataUnit = toUnit($type);

foreach ($res as $row)
{	

	if ($_GET['DISPLAY'] == 'ALL')
	{
		$i=0;
		foreach($row as $val)
		{	$uv = toUnitVal($val, $type);
			$serie[$i++][] = $uv;
			//set limits
			$yMin = $yMin > $uv ? $uv : $yMin;
			$yMax = $yMax < $uv ? $uv : $yMax;
		}
	}
	
	else
	
	{
		//AVG only
		$uv = toUnitVal($row[1], $type);
		$serie[0][] = $uv;
		//set limits
		$yMin = $yMin > $uv ? $uv : $yMin;
		$yMax = $yMax < $uv ? $uv : $yMax;
	}	
}

//Scaling (default is 1, set above)
if ($yMax > 110) $yMax = 110;
if ($yMin < -40) $yMin = -40;

//diff minimum  4
if ( ($yMax - $yMin) < 4)
{
	$yMax = $yMin + 4;
}

if ( ($yMax-$yMin) > 10) $yScale = 2;
if ( ($yMax-$yMin) > 20) $yScale = 5;
if ( ($yMax-$yMin) > 50) $yScale = 10;

//limit-lines
$minLimit = $low;
$maxLimit = $high;

include ('chart_sensor.php');
}

?>