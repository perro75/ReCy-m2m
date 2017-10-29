<?php

//load current cron-triggers if exists
$cronsettings = "$uid/settings_cron.php";

//execute cron only if settings_cron file exists
if (file_exists($cronsettings))
{
	//load settings form UID-file
	include $cronsettings;

	$lastRunLog = "$uid/cron.log";
	$time = 0; // default timestamp is 1.1.1970

	//update time if file exists
	if (file_exists($lastRunLog)) 
	{
		//read timestamp from file
		$time = file_get_contents($lastRunLog);
	} 

	//time of day part
	$fileTime = $time % 86400;
	//day-part
	$fileDay = ($time - $fileTime);

	$now = time();
	$realTime = $now % 86400;
	$realDay =  ($now - $realTime);

	//whenever the UTC date has changed
	if ($fileDay < $realDay)
	{
		//add correct day at 00:00 to file
		file_put_contents($lastRunLog, $realDay);
	}
	
	//run all tasks due since last time update
	//If system comes alive at noon, all tasks scheduled before noon will be run.
	if ($fileDay == $realDay)
	{
		for ($i=0; $i < count($cronrun); $i++)
		{
			//$cronrun and $croninclude loaded from settings_cron.php
			if ($realTime > $cronrun[$i] * 3600 && $fileTime < $cronrun[$i] * 3600)
			{
				include $uid."/".$croninclude[$i];
				file_put_contents($lastRunLog, time());
			}
		}
	}
}
?>