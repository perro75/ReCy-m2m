<?php

function getLastArdio()
{
	// get the last update of digitaldata
	$sql = @"
	SELECT 
	  max(ts) last, 
	  min(ts) first, 
	  max(ts)-min(ts) as span, 
	  strftime('%s', 'now') - max(ts) as age, 
	  
	  COUNT(ts) as count, 
	  layer,
	  
	  round(avg(d12),2) as D12, 
	  round(avg(d13),2) as D13
	  
	  FROM ARDIO ad
	  WHERE ts = (SELECT max(ts) FROM ARDIO ad2 WHERE ad.layer = ad2.layer) 
	  GROUP BY layer 
	  ORDER BY layer;
	";
	
	return $sql;
}

function getAranaLatestSecAvg($sec)
{
	// get analog data as last 20 seconds AVG
	$sql = @"
	SELECT 
	  max(ts) last, 
	  min(ts) first, 
	  max(ts)-min(ts) as span, 
	  strftime('%s', 'now') - max(ts) as age, 
	  
	  COUNT(ts) as count, 
	  layer,
	  
	  round(avg(a1),1) as A1, 
	  round(avg(a2),1) as A2, 
	  round(avg(a3),1) as A3, 
	  round(avg(a4),1) as A4,
	  round(avg(a5),1) as A5, 
	  round(avg(a6),1) as A6 
	  
	  FROM ARANA aa
	  WHERE ts > ( (SELECT max(ts) FROM ARANA aa2 WHERE aa.layer = aa2.layer) - $sec )
	  GROUP BY layer 
	  ORDER BY layer;
	";
	
	return $sql;
}
?>