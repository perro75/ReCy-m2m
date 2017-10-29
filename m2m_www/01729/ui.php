<?php

//A.****************Process NMEA COMMAND send to the controller AND RELOAD *******************

// if nmea command issued as GET['nmea'] pass this to m2m.php for processing
// normally this shall be an $ARSTA sentence.
// After delay of 3 seconds, ui.php is reloaded
if (isset($_GET['nmea']))
{
  $currdir = getcwd();
  chdir('..');
  include 'm2m.php';
  sleep(3); //needed for database data to update
  header( 'Location: http://www.mickels.fi/m2m/'.$uid.'/ui.php' ) ; //XXX create path somewhere
}

//B. **************** DISPLAY UI ***************************

// Common functions and settings for this ui.
// Additional includes are made through the settings-php
include 'settings.php';

//html header & css from file
include 'lib/header.php';

//print the data with parameters set in settings.php

$span = 20;
if (isset($_GET['span']))
{
	$span = $_GET['span'];
}

if ($_GET['type'] == 'short')
{
	echo printAiList($db, $ainData, $timeout, $span);
}

elseif ($_GET['type'] == 'm')
{
		echo printAiMobile($db, $ainData, $timeout, $span);
		//echo printDO($db, $bitName, $uid);
}
else
{
	echo printAiTable($db, $ainData, $timeout, $span);
	echo printDO($db, $bitName, $uid);
	echo printDI($db);
}

//print the footer
include 'lib/footer.php';

	
function printDI($db)
{
	$sql = getLastArdio();
	$res ="\n\n<div><h2>Digital inputs (12&13), latest data</h2>\n";
	$res .="<table class=\"digital\">\n";
	$res .=@"
	<tr> <th>AGE</th>  <th>COUNT</th> <th>LAYER</th>  
	<th>D12</th>  <th>D13</th> </tr>\n"; 
	
	foreach( $db->query($sql) as $row) 
	{	
	 $res .="<tr>";
	  $res .= "<td>".$row['age']."</td>";
	  $res .= "<td>".$row['count']."</td>";
	  $res .= "<td>".$row['layer']."</td>";
	  $res .= "<td>".$row['D12']."</td>";
	  $res .= "<td>".$row['D13']."</td>";
     $res .="</tr>\n";
	}
	$res .="</table></div>\n";
	return $res;
}

function printAiList($db, $ainData, $timeout, $span)
{
	$sql = getAranaLatestSecAvg($span);
	$no = 1;
	$res ="\n\n<div><h2>Analog sensors</h2>\n";
	
	$res .="<table class=\"analog list\">\n";
	$res .=@"
	<tr> <th>No</th> <th>AGE</th>  <th>COUNT</th> <th>L/AIN</th> <th>TYPE</th> <th>NAME</th> 
	<th>".getTimeSpanLabel($span)." AVG</th> </tr>\n"; 
	
	$tr = array();
	
	foreach( $db->query($sql) as $row) 
	{	
	  $tout = ($row['age'] > $timeout) ? "class=\"TIMEOUT\"" : ""; 
	  $ageTd  = "<td>".$row['age']."</td>";
	  $countTd = "<td>".$row['count']."</td>";
	  
	  $layer = (int)$row['layer'];
	  $keylist = array('A1','A2','A3','A4','A5','A6');
	  //keep the rows in array for sorting
	  //$tr = array();
	  
	  for ($ain=1; $ain <= 6; $ain++)
	  {
		  //get name and type is set in settings.php
		  $name = isset($ainData[$layer][$ain]['name']) ? $ainData[$layer][$ain]['name'] : "NA";
		  $type = isset($ainData[$layer][$ain]['type']) ? $ainData[$layer][$ain]['type'] : "NA";
		  //get group from settings.php
		  $group = isset($ainData[$layer][$ain]['group']) ? $ainData[$layer][$ain]['group'] : "NA";
		  
		  //only write data that is set
		  if ($name == "NA") continue;
		  
		  //Name is link to sensor datapage
		  $link = ($name == "NA") ? "NA" : "<a href=\"aindata.php?layer=$layer&ain=$ain&type=short\">$name</a>";
		  
		  //Create val using functions in common.php
		  $key = $keylist[$ain-1];
		  $val = toUnitVal($row[$key], $type);
		  $unit = toUnit($type);

		  $layerTd = "<td>$layer/$ain</td>";
		  $typeTd = "<td>$type</td>";
			
		  //Set high / low limits
		  $high = ( isset($ainData[$layer][$ain]['high']) &&  $ainData[$layer][$ain]['high'] <= $val ) ? "high" : "";
		  $low =  ( isset($ainData[$layer][$ain]['low']) &&  $ainData[$layer][$ain]['low'] >= $val ) ? "low" : "";
		  
		  //set classes and data in <td> 
		  $nameTd = "<td class =\"$type $high $low\">$link</td>";
		  $valueTd = "<td class =\"$type $high $low\">".$val." ".$unit."</td>";
		  $noTd = "<td>".$no++."</td>";
		  
		  $groups[$no-2] = $group;
		  $tr[$no-2] = "<tr $tout> $noTd $ageTd $countTd $layerTd $typeTd $nameTd $valueTd </tr>";
	  }
	 }
	 asort($groups);
	 $currentGroup = null;
	 
	 foreach($groups as $key=>$val)
	 {
		$gName = explode("|", $val);
		$gName = $gName[0];
		
		if ($gName != $currentGroup)
		{
			$res .= "\n<tr><th class=\"groupheader\" colspan=7>$gName</th></tr>\n";
			$currentGroup = $gName;
		}
		
		$res .= "\n".$tr[$key]."\n";
	 }
	 
	$res .="</table>\n";
	return $res;
}

function printAiMobile($db, $ainData, $timeout, $span)
{
	$sql = getAranaLatestSecAvg($span);
	$no = 1;
	
	$res ="<table class=\"analog list\">\n";
	$res .=@"
	<tr> <th>AGE</th> <th>NAME</th> 
	<th>".getTimeSpanLabel($span)." AVG</th> </tr>\n"; 
	
	$tr = array();
	
	foreach( $db->query($sql) as $row) 
	{	
	  $tout = ($row['age'] > $timeout) ? "class=\"TIMEOUT\"" : ""; 
	  $ageTd  = "<td>".$row['age']."</td>";
	  $countTd = "<td>".$row['count']."</td>";
	  
	  $layer = (int)$row['layer'];
	  $keylist = array('A1','A2','A3','A4','A5','A6');
	  //keep the rows in array for sorting
	  //$tr = array();
	  
	  for ($ain=1; $ain <= 6; $ain++)
	  {
		  //get name and type is set in settings.php
		  $name = isset($ainData[$layer][$ain]['name']) ? $ainData[$layer][$ain]['name'] : "NA";
		  $type = isset($ainData[$layer][$ain]['type']) ? $ainData[$layer][$ain]['type'] : "NA";
		  //get group from settings.php
		  $group = isset($ainData[$layer][$ain]['group']) ? $ainData[$layer][$ain]['group'] : "NA";
		  
		  //only write data that is set
		  if ($name == "NA") continue;
		  
		  //Name is link to sensor datapage
		  $link = ($name == "NA") ? "NA" : "<a href=\"aindata.php?layer=$layer&ain=$ain&type=short\">$name</a>";
		  
		  //Create val using functions in common.php
		  $key = $keylist[$ain-1];
		  $val = toUnitVal($row[$key], $type);
		  $unit = toUnit($type);

		  $layerTd = "<td>$layer/$ain</td>";
		  $typeTd = "<td>$type</td>";
			
		  //Set high / low limits
		  $high = ( isset($ainData[$layer][$ain]['high']) &&  $ainData[$layer][$ain]['high'] <= $val ) ? "high" : "";
		  $low =  ( isset($ainData[$layer][$ain]['low']) &&  $ainData[$layer][$ain]['low'] >= $val ) ? "low" : "";
		  
		  //set classes and data in <td> 
		  $nameTd = "<td class =\"$type $high $low\">$link</td>";
		  $valueTd = "<td class =\"$type $high $low\">".$val." ".$unit."</td>";
		  $noTd = "<td>".$no++."</td>";
		  
		  $groups[$no-2] = $group;
		  $tr[$no-2] = "<tr $tout> $ageTd $nameTd $valueTd </tr>";
	  }
	 }
	 asort($groups);
	 $currentGroup = null;
	 
	 foreach($groups as $key=>$val)
	 {
		$gName = explode("|", $val);
		$gName = $gName[0];
		
		if ($gName != $currentGroup)
		{
			$res .= "\n<tr><th class=\"groupheader\" colspan=7>$gName</th></tr>\n";
			$currentGroup = $gName;
		}
		
		$res .= "\n".$tr[$key]."\n";
	 }
	 
	$res .="</table>\n";
	return $res;
}

function printAiTable($db, $ainData, $timeout, $span)
{
	$sql = getAranaLatestSecAvg($span);

	$res ="\n\n<div><h2>Analog inputs ".getTimeSpanLabel($span)." AVG</h2>\n";
	$res .="<table class=\"analog\">\n";
	$res .=@"
	<tr> <th>AGE</th>  <th>COUNT</th> <th>LAYER</th>  
	<th>A1</th>  <th>A2</th>  <th>A3</th>  <th>A4</th>  
	<th>A5</th>  <th>A6</th> </tr>\n"; 
	
	foreach( $db->query($sql) as $row) 
	{	
	  $to = ($row['age'] > $timeout) ? "class=\"TIMEOUT\"" : ""; 
	  $res .="<tr $to>";
	  $res .= "<td>".$row['age']."</td>";
	  $res .= "<td>".$row['count']."</td>";
	  $res .= "<td>".$row['layer']."</td>";
	  $layer = (int)$row['layer'];
	  $ain = 0;

	  $keylist = array('A1','A2','A3','A4','A5','A6');
	  foreach($keylist as $key)
	  {
		  $ain++;
		  //get name and type is set in settings.php
		  $name = isset($ainData[$layer][$ain]['name']) ? $ainData[$layer][$ain]['name'] : "NA";
		  $type = isset($ainData[$layer][$ain]['type']) ? $ainData[$layer][$ain]['type'] : "NA";
		  
		  //Name is link to sensor datapage
		  $link = ($name == "NA") ? "NA" : "<a href=\"aindata.php?layer=$layer&ain=$ain\">$name</a>";
		  
		  //Create val using functions in common.php
		  $val = toUnitVal($row[$key], $type);
		  $unit = toUnit($type);
		  
		  //Set high / low limits
		  $high = ( isset($ainData[$layer][$ain]['high']) &&  $ainData[$layer][$ain]['high'] <= $val ) ? "high" : "";
		  $low =  ( isset($ainData[$layer][$ain]['low']) &&  $ainData[$layer][$ain]['low'] >= $val ) ? "low" : "";
		  
		  //set classes and data in <td> 
		  $res .= "<td class =\"$type $high $low\">$link<br>".$val." ".$unit."</td>";
	  }
	$res .="</tr>\n";
	}
	$res .="</table>\n";
	return $res;
}

//Print states of DO 8..11 with links to toggle state
function printDO($db, $bitName, $uid)
{
	$res ="\n\n<h2>Digital outputs 8..11 and UI-state</h2>\n";
	// get the last configuration response
	$sql = "SELECT uival, hexval FROM ARSTA WHERE sentence LIKE '\$ARSTA' ORDER BY ts 	DESC LIMIT 1";

	foreach( $db->query($sql) as $row) 
	{
		$arsta = $row['hexval'];
		$ui = $row['uival'];
	}
    
	$hx = new HexCode(); //create object<br>
	$hx->begin($arsta);

	$res .="\n<table class=\"hex\"><tr>\n";
	
		for ($i = 0; $i < 16; $i++)
		{

		//get current state and add class ON or OFF
		$class = ($hx->readBit($i) == 1) ? "ON" : "OFF";
		//get copy of HEX-string with given bit toggled
		$hex = $hx->toggleBitCopy($i);
		//print link to togle this bit
		$name = isset($bitName[$i]) ? $bitName[$i] : "NA";
		$res .= "<td><a class=\"$class\" href=\"?uid=".$uid."&idx=0&nmea=\$ARSET,".$hex."*ff\">".$name."</a></td>\n"; 
		if( ($i+1)%4 == 0 ) $res .="</tr><tr>\n"; //linebreak at 4 td
		}
	
	$res .= "</tr></table>\n";
	
	$res .= "<li>HEX:".$hx->readHex()."\n";
	$res .= "<li>UI :".$ui."\n";
	
	return $res;
}


?>
