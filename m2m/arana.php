<?php

$db->exec(@"
CREATE TABLE IF NOT EXISTS ARANA (
ts integer,
layer integer,
uid integer, 
idx integer, 
a1 integer, 
a2 integer, 
a3 integer, 
a4 integer, 
a5 integer, 
a6 integer, 
PRIMARY KEY(ts, layer, uid, idx)
);
"); 

$db->exec(@"
CREATE TABLE IF NOT EXISTS ARANA_BY_HOUR(
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
  PRIMARY KEY(ts, layer)
);
");

$db->exec(@"
CREATE VIEW IF NOT EXISTS ARANA_BY_MIN AS

	select 
	ts/60 * 60 as ts,
	layer, 
	strftime('%M', datetime(ts, 'unixepoch')) as minuteNo,
	strftime('%H', datetime(ts, 'unixepoch')) as hourNo, 
	strftime('%j', datetime(ts, 'unixepoch')) as dayNo, 
	strftime('%W', datetime(ts, 'unixepoch')) as weekNo,
	strftime('%w', datetime(ts, 'unixepoch')) as weekDayNo,
	strftime('%Y', datetime(ts, 'unixepoch')) as yearNo,
	strftime('%m', datetime(ts, 'unixepoch')) as monthNo,
	strftime('%d', datetime(ts, 'unixepoch')) as monthDayNo,
	 
	count(ts) as count,
	(max(idx) - min(idx)) + 1 as idxs,
	
	min(a1) as min1, 
	max(a1) as max1, 
	round(avg(a1),2) as avg1, 

	min(a2) as min2, 
	max(a2) as max2, 
	round(avg(a2),2) as avg2, 

	min(a3) as min3, 
	max(a3) as max3, 
	round(avg(a3),2) as avg3, 

	min(a4) as min4, 
	max(a4) as max4, 
	round(avg(a4),2) as avg4, 

	min(a5) as min5, 
	max(a5) as max5, 
	round(avg(a5),2) as avg5, 

	min(a6) as min6, 
	max(a6) as max6, 
	round(avg(a6),2) as avg6 

	from arana group by ts/60, layer
	order by ts desc;
");

$i=0; //first datafield is layer, [0]
while ( ($field = $nmea->getField($i)) != null)
{
	$p[$i++] = $field;
}

$db->exec(utf8_encode("INSERT OR REPLACE INTO ARANA (ts, layer, uid, idx, a1, a2, a3, a4, a5, a6) VALUES ($ts, ".$p[0].", $uid, $idx,".$p[1].",".$p[2].",".$p[3].",".$p[4].",".$p[5].",".$p[6].")") );

//update hourly statistics every minute
if (($idx % 60) == 0)
{
	$sql = @"
	
	INSERT OR REPLACE INTO ARANA_BY_HOUR

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
	
	min(a1) as min1, 
	max(a1) as max1, 
	round(avg(a1),2) as avg1, 

	min(a2) as min2, 
	max(a2) as max2, 
	round(avg(a2),2) as avg2, 

	min(a3) as min3, 
	max(a3) as max3, 
	round(avg(a3),2) as avg3, 

	min(a4) as min4, 
	max(a4) as max4, 
	round(avg(a4),2) as avg4, 

	min(a5) as min5, 
	max(a5) as max5, 
	round(avg(a5),2) as avg5, 

	min(a6) as min6, 
	max(a6) as max6, 
	round(avg(a6),2) as avg6 

	from arana group by ts/3600, layer
	order by ts desc;
	";
	
	$db->exec($sql);
	
	$clean = @"
	DELETE FROM ARANA WHERE ts / 3600 + $saveHours < (SELECT MAX(ts)/3600 FROM ARANA_BY_HOUR);
	"; 
	$db->exec($clean);
}
?>