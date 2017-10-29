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

echo printAiMobile($db, $ainData, $timeout, $span);
//echo printDO($db, $bitName, $uid);

//print the footer
include 'lib/footer.php';

function printAiMobile($db, $ainData, $timeout, $span)
{
	$sql = getAranaLatestSecAvg($span);
	$no = 1;
	
	$res = "<table class=\"analog list\">\n";
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
?>
