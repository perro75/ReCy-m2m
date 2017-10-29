<?php

$to = 'stefan@mickels.fi';
$subject = 'Mickels - m2m';

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: m2m@mickels.fi' . "\r\n" .
			'Reply-To: stefan@mickels.fi' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
	
// Put your HTML here
$message = file_get_contents('http://mickels.fi/m2m/01729/ui.php?type=m');
$message = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $message);

// Mail it
mail($to, $subject, $message, $headers);
?>