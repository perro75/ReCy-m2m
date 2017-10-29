<?php

//This class creates alarms for use by external devices
class alarm
{
	private $uid;
 	private $hx;
 	private $ui;

 	public function __construct($uid)
 	{
 		$this->uid = $uid;
 		//log IP of client
 		$file = "$uid/alarm.txt";
 		file_put_contents($file, 'ALM-TEST');
 	}
 
}
?>