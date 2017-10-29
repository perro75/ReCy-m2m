<?php

$db->exec(@"
CREATE TABLE IF NOT EXISTS ARSTA (
ts integer,
uid integer,
idx integer, 
uival integer,
sentence,
hexval,
PRIMARY KEY(ts, uid, idx, sentence)
);
");

$i=0; //first datafield is ui, second is HEX

switch($header)
{
 case '$ARSTA':
  $ui  = $nmea->getField(0);
  $hex = $nmea->getField(1);
  
  // get the last configuration command
  $sql = "SELECT hexval FROM ARSTA WHERE sentence LIKE '\$ARSET' ORDER BY ts DESC LIMIT 1";
  foreach( $db->query($sql) as $row) 
  {
	$lastCommand =  $row['hexval'];
  }
  if (strcmp($lastCommand, $hex) != 0 && $lastCommand != null)
  {
	$command = "\$ARSET,$lastCommand*ff"; //send ARSET to controller
  }
	
 break;
 
 case '$ARSET':
  $ui  = -1;
  $hex = $nmea->getField(0);
  echo $hex;
 break;
}

$db->exec(utf8_encode("INSERT OR IGNORE INTO ARSTA (ts, uid, idx, uival, sentence, hexval) VALUES ($ts, $uid, $idx, $ui, '$header', '$hex')"));


//clean up every minute
if (($idx % 60) == 0)
{
	$clean = @"
	DELETE FROM ARSTA WHERE ts / 3600 + $saveHours < (SELECT MAX(ts)/3600 FROM ARSTA);
	";
	
	$db->exec($clean);
}
?>