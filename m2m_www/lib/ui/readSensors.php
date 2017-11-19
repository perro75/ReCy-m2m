<?php
/*
* Read sensor data and definitions into AnalogSensor. objects, based on settings.
// Settings_sensors must be loaded
// AnalogSensor class must be loaded
// Type functions must be set in common-php

SAMPLE setting;
$ainData[1][4]['group'] = "A_UTE|1";
$ainData[1][4]['name'] = "UTOMHUS";
$ainData[1][4]['type'] = "18B20";
$ainData[1][4]['high'] = 10.0;
$ainData[1][4]['low'] = -10.0;
*/

//array of analog sensors
$analogSensors = array();

$laynum = 0;
$ainum = 0;


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ADD DEFINITIONS
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//sort settings array
ksort($ainData);
//create AnalogSensor- objects
foreach($ainData as $laynum=>$layer)
{
	//sort layer
	ksort($layer);
	foreach($layer as $ainum=>$data)
    {
		$sensor = new AnalogSensor($laynum, $ainum);
		
		$groupData = explode('|', $data['group']);
		$group = $groupData[0];
		$weight = $groupData[1];
		$name = $data['name'];
		$type = $data['type'];
		$high = $data['high'];
		$low = $data['low'];
		
		//add defined data from settings
		$sensor->setDefinedData($group, $weight, $name, $type, $high, $low);
		$analogSensors[] = $sensor;
    }
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ADD DATA
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     $sql = getAranaLatestSecAvg($span); //span in settings file
     
     //read rows. each layer has one row
     foreach( $db->query($sql) as $row)
     {
         //get age and query for row
         $age  = $row['age'];
         $layer = (int)$row['layer'];
         
         //each row has 6 datacolumns, get each value by key
         $keylist = array('A1','A2','A3','A4','A5','A6');
         for ($ain=1; $ain <= 6; $ain++)
         {
			 //Find type of AnalogSensor
			 for ($i=0; $i < count($analogSensors); $i++)
			 {
				 if ($analogSensors[$i]->isMatch($layer, $ain))
				 {
					 $type = $analogSensors[$i]->sensorType();
					 $key = $keylist[$ain-1];
					 $val = $row[$key];//toUnitVal($row[$key], $type);
					 $unit = toUnit($type);
					 
					 $analogSensors[$i]->setValue($val, $unit);
					 $analogSensors[$i]->setDataAge($age);
					 
				 }
			 }
         }
	 }

//Create sensor SET
$analogSensorSet = new AnalogSensorSet($analogSensors);

?>