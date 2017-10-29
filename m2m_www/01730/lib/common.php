<?php

//read uid from foldername (included from main folder)
$uid = basename(getcwd());

// open db in current folder
$db = new PDO("sqlite:m2m.db3"); 

//Get the unit for a given type
function toUnit($type)
{
	switch ($type)
	{
		case "18B20";
		return "C";
		
		case "ADXL";
		return "deg";
		
		case "TMP36";
		return "C";
		
		case "LDR1";
		return "V-LDR";
	}
}

//Get the value for the given type
function toUnitVal($mV, $type)
{
	switch ($type)
	{
		
		case "18B20"; 
		return $mV;
		
		case "ADXL"; 
		return abs($mV);
		
		
		case "TMP36"; 
		return round( ($mV - 500) / 10.0, 1);
		
		case "LDR1";
		return round($mV/1000,2);
		
	}
	return (int)$mV."mV";
}

function getTimeSpanLabel($seconds)
{
	$days =  (int)($seconds / 86400);
	$drest = $seconds % 86400;
	
	$hours = (int)($drest / 3600);
	$hrest = $drest % 3600;
	
	$minutes = (int)($hrest / 60);
	$seconds = (int)($hrest % 60);
	
	$d = ($days > 0) ? $days."day" : "";
	$h = ($hours > 0) ? $hours."hour" : "";
	$m = ($minutes > 0) ? $minutes."min" : "";
	$s = ($seconds > 0) ? $seconds."sec" : "";
	
	return $d.$h.$m.$s;
}
?>