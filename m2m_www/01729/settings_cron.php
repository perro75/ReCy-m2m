<?php

//List all hours as decimals, when to trigger cron
// All times in decimal UTC, ex. 05.25 == 05:15 / 08.5 == 08:30 

$cronrun[] = 00.00; //03:00
$cronrun[] = 03.00; //06:00

$cronrun[] = 05.00; //08:00

$cronrun[] = 06.00; //09:00
$cronrun[] = 09.00; //12:00

$cronrun[] = 12.25; //15:15
$cronrun[] = 15.00; //18:00

$cronrun[] = 17.00; //20:00

$cronrun[] = 18.00; //21:00
$cronrun[] = 21.00; //00:00

//List all actions as local php-srcipts to run as include upon trigger
//scripts assumed to be in this UID-folder
$croninclude[] = "add/cleancam1.php";
$croninclude[] = "add/cleancam1.php";

$croninclude[] = "add/mailstefan.php";

$croninclude[] = "add/cleancam1.php";
$croninclude[] = "add/cleancam1.php";

$croninclude[] = "add/cleancam1.php";
$croninclude[] = "add/cleancam1.php";

$croninclude[] = "add/mailstefan.php";

$croninclude[] = "add/cleancam1.php";
$croninclude[] = "add/cleancam1.php";

?>