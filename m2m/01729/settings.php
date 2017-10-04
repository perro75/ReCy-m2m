<?php
/*
* Data in the settings-file is project specific and usually changed for each project.
* 
*/

//common functions usually not changed between projects
include 'lib/common.php';

//database query-functions
include 'lib/sql.php';

//class used to encode hex
include '../HexCode.php';

//Title and name of page
$uiTitle = "ReCy - Mosavägen 317 A";

//html to display at the top of ui-page
$toplink_html = @"
<a href=\"ui.php\">Etusivu</a>
|
<a href=\"ui3.php\">Pda</a>
|
<a href=\"ui3.php?refresh\">Refresh</a>
|
<a href=\"http://www.mickels.fi/cams/index.php\">Kamerat</a>";

//the data-timeout in seconds (indication of timeout on screen)
$timeout = 30;

include 'settings_data.php';

?>