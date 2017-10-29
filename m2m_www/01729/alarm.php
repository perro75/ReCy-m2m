<?php

include 'settings.php';

//seconds span for analog data
$span = 30;

// analog data
$sql = getAranaLatestSecAvg($span);
foreach( $db->query($sql) as $row)
{
	//current layer
	$layer = (int)$row['layer'];
	
	//age for this layer
	$age = $row['age'];
	
	$ain = 0;

	$keylist = array('A1','A2','A3','A4','A5','A6');
	foreach($keylist as $key)
	{
		$ain++;
		//get name and type is set in settings.php
		$name = isset($ainData[$layer][$ain]['name']) ? $ainData[$layer][$ain]['name'] : "NA";
		$type = isset($ainData[$layer][$ain]['type']) ? $ainData[$layer][$ain]['type'] : "NA";

		//Create val using functions in common.php
		$val = toUnitVal($row[$key], $type);
		$unit = toUnit($type);

		//Set high / low limits boolean
		$high = ( isset($ainData[$layer][$ain]['high']) &&  $ainData[$layer][$ain]['high'] <= $val );
		$low =  ( isset($ainData[$layer][$ain]['low']) &&  $ainData[$layer][$ain]['low'] >= $val );
        
		//is in alarmstate
		$alarm =  ( isset($ainData[$layer][$ain]['alarm'])  &&  ($high || $low));
		
		$almtype = $high ? "HIGH" : "LOW";
		echo ( $alarm ) ? "ALM: $name is $almtype \r" : "";
	}
}

//UIVAL

$sql = "SELECT uival, hexval FROM ARSTA WHERE sentence LIKE '\$ARSTA' ORDER BY ts DESC LIMIT 1";

foreach( $db->query($sql) as $row)
{
	$arsta = $row['hexval'];
	$ui = (int) $row['uival'];

	for ($i=1; $i <= 4; $i = $i*2)
	{
		//boolean value showing if bit is set
		$val = $ui / ($i * 2 ) >= $i;
		
		if ( isset($uiBit[$i]['alarm']) )
		{
			echo "UI $i:";
			echo ($val == $uiBit[$i]['normal']) ? $uiBit[$i]['namenormal'] : $uiBit[$i]['namealarm'];
		}
	}
}

?>