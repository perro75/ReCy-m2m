<?php
/*
* Data in the settings-file is project specific and partly changed for each project.
*/


//Title and name of page
$uiTitle = "ReCy-m2m - Mickels";

//the data-timeout in seconds (indication of timeout on screen)
$timeout = 20;

//use 20 seconds averaging of data if not overriden elsewhere
$span = 20;


//the BASE URL of the UI ( example http://localhost/01730/ )
$uiBaseUrl = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

//the BASE PATH of the UI ( example E:\Git\repos\perro75\ReCy-m2m\m2m_www\01730 )
$uiBasePath = realPath(null);

//LIB PATH
$uiLibPath = "$uiBasePath/../lib/ui";

//common functions usually not changed between projects
include "$uiLibPath/common.php";

//database query-functions
include "$uiLibPath/sql.php";

//sensor-settings
include "$uiBasePath/settings_sensors.php";

//AnalogSensor class
include "$uiLibPath/AnalogSensor.php";
include "$uiLibPath/AnalogHistory.php";

//Functions for reading sensor-data
include "$uiLibPath/readSensors.php";

//class used to encode hex
include "$uiBasePath/../HexCode.php";





?>