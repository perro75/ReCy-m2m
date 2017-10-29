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
//include 'lib/header.php';

//print the data with parameters set in settings.php

$span = 20;
if (isset($_GET['span']))
{
	$span = $_GET['span'];
}

echo printAndroid($db, $ainData, $timeout, $span);

//print the footer
//include 'lib/footer.php';


function printAndroid($db, $ainData, $timeout, $span)
{
	$sql = getAranaLatestSecAvg($span);
	$no = 1;
	
	$res ="";
	$tr = array();
	
	foreach( $db->query($sql) as $row) 
	{	
	  $resTd = ($row['age'] > $timeout) ? "TIMEOUT|" : "OK|"; 
	  $ageTd  = $row['age']."|";
	  
	  $layer = (int)$row['layer'];
	  $keylist = array('A1','A2','A3','A4','A5','A6');
	  
	  //keep the rows in array for sorting
	  $tr = array();
	  
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
		  //$link = ($name == "NA") ? "NA" : "<a href=\"aindata.php?layer=$layer&ain=$ain&type=short\">$name</a>";
		  
		  //Create val using functions in common.php
		  $key = $keylist[$ain-1];
		  $val = toUnitVal($row[$key], $type);
		  $unit = toUnit($type);

		  $layerTd = "$layer/$ain|";
		  $typeTd = "$type|";
			
		  //Set high / low limits
		  $high = ( isset($ainData[$layer][$ain]['high']) &&  $ainData[$layer][$ain]['high'] <= $val ) ? "HIGH" : "";
		  $low =  ( isset($ainData[$layer][$ain]['low']) &&  $ainData[$layer][$ain]['low'] >= $val ) ? "LOW" : "";
		  
		  $warning = "NOSET|";
		  if (isset($ainData[$layer][$ain]['alarm']) && $ainData[$layer][$ain]['alarm'] == true)
		  {
			$warning = $high.$low;
			$warning = ($warning == "") ? "NORM|" : $warning."|";
		  }
		  $nameTd = $name."|";
		  $valueTd = $val." ".$unit."|";
		  
		  $no++;
		  
		  $groups[$no-2] = $group;
		  echo "$resTd $warning $tout $nameTd $valueTd\n";
	  }
	 }
}

?>
