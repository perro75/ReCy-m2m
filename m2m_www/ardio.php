<?php

$db->exec(@"
CREATE TABLE IF NOT EXISTS ARDIO (
ts integer,
layer integer,
uid integer, 
idx integer, 
d0 integer, 
d1 integer, 
d2 integer, 
d3 integer, 
d4 integer, 
d5 integer, 
d6 integer, 
d7 integer, 
d8 integer, 
d9 integer, 
d10 integer, 
d11 integer, 
d12 integer, 
d13 integer, 
PRIMARY KEY(ts, layer, uid, idx)
);
"); 

$db->exec(@"
CREATE TABLE IF NOT EXISTS ARDIO_BY_HOUR(
  ts integer,
  layer integer,
  hourNo integer,
  dayNo integer,
  weekNo integer,
  weekDayNo integer,
  yearNo integer,
  monthNo integer,
  monthDayNo integer,
  count integer,
  idxs integer,
  min0 integer,
  max0 integer,
  avg0 real,
  min1 integer,
  max1 integer,
  avg1 real,
  min2 integer,
  max2 integer,
  avg2 real,
  min3 integer,
  max3 integer,
  avg3 real,
  min4 integer,
  max4 integer,
  avg4 real,
  min5 integer,
  max5 integer,
  avg5 real,
  min6 integer,
  max6 integer,
  avg6 real,
  min7 integer,
  max7 integer,
  avg7 real,
  min8 integer,
  max8 integer,
  avg8 real,
  min9 integer,
  max9 integer,
  avg9 real,
  min10 integer,
  max10 integer,
  avg10 real,
  min11 integer,
  max11 integer,
  avg11 real,
  min12 integer,
  max12 integer,
  avg12 real,
  min13 integer,
  max13 integer,
  avg13 real,
  PRIMARY KEY(ts, layer)
);
");

$i=0; //first datafield is layer, [0]
while ( ($field = $nmea->getField($i)) != null)
{
	$p[$i++] = $field;
}

$db->exec(utf8_encode("INSERT OR REPLACE INTO ARDIO (ts, layer, uid, idx, d0, d1, d2, d3, d4, d5, d6, d7, d8, d9, d10, d11, d12, d13) VALUES ($ts, ".$p[0].", $uid, $idx,".$p[1].",".$p[2].",".$p[3].",".$p[4].",".$p[5].",".$p[6].",".$p[7].",".$p[8].",".$p[9].",".$p[10].",".$p[11].",".$p[12].",".$p[13].",".$p[14].")" ) );

//update hourly statistics every minute
if (($idx % 60) == 0)
{
	$sql = @"
	
	INSERT OR REPLACE INTO ARDIO_BY_HOUR

	select ts/3600 * 3600 as ts,
	layer, 
	strftime('%H', datetime(ts, 'unixepoch')) as hourNo, 
	strftime('%j', datetime(ts, 'unixepoch')) as dayNo, 
	strftime('%W', datetime(ts, 'unixepoch')) as weekNo,
	strftime('%w', datetime(ts, 'unixepoch')) as weekDayNo,
	strftime('%Y', datetime(ts, 'unixepoch')) as yearNo,
	strftime('%m', datetime(ts, 'unixepoch')) as monthNo,
	strftime('%d', datetime(ts, 'unixepoch')) as monthDayNo,
	 
	count(ts) as count,
	(max(idx) - min(idx)) + 1 as idxs,
	
	min(d0) as min0, 
	max(d0) as max0, 
	round(avg(d0),2) as avg0, 

	min(d1) as min1, 
	max(d1) as max1, 
	round(avg(d1),2) as avg1, 

	min(d2) as min2, 
	max(d2) as max2, 
	round(avg(d2),2) as avg2, 

	min(d3) as min3, 
	max(d3) as max3, 
	round(avg(d3),2) as avg3, 

	min(d4) as min4, 
	max(d4) as max4, 
	round(avg(d4),2) as avg4, 

	min(d5) as min5, 
	max(d5) as max5, 
	round(avg(d5),2) as avg5, 

	min(d6) as min6, 
	max(d6) as max6, 
	round(avg(d6),2) as avg6, 
	
	min(d7) as min7, 
	max(d7) as max7, 
	round(avg(d7),2) as avg7, 

	min(d8) as min8, 
	max(d8) as max8, 
	round(avg(d8),2) as avg8, 

	min(d9) as min9, 
	max(d9) as max9, 
	round(avg(d9),2) as avg9, 

	min(d10) as min10, 
	max(d10) as max10, 
	round(avg(d10),2) as avg10, 

	min(d11) as min11, 
	max(d11) as max11, 
	round(avg(d11),2) as avg11, 

	min(d12) as min12, 
	max(d12) as max12, 
	round(avg(d12),2) as avg12, 

	min(d13) as min13, 
	max(d13) as max13, 
	round(avg(d13),2) as avg13 

	from ardio group by ts/3600, layer
	order by ts desc;
	";
	
	$db->exec($sql);
	
	$clean = @"
	DELETE FROM ARDIO WHERE ts / 3600 + $saveHours < (SELECT MAX(ts)/3600 FROM ARDIO_BY_HOUR);
	"; 
	$db->exec($clean);
}
?>