<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
 <?php
   //refresh only if variable set
   $mob = ($_GET['type']=='m'); 
   $refresh = $mob ? "15" : "5";
   if(isset($_GET['refresh'])) {echo '<meta http-equiv="refresh" content="'.$refresh.'">'; } 
 ?>
  <link rel="stylesheet" type="text/css" href="lib/ui.css">
  <link rel="stylesheet" type="text/css" href="lib/aindata.css">

  <script type="text/javascript" src="http://www.mickels.fi/lib/jquery/jquery-1.10.2.js"></script>
  <title><?php echo $uiTitle?></title>
 </head>
 
 <body>  
 
  <div class="header">
   <h1> <?php if (!$mob) echo $uiTitle ?> </h1>
   <?php echo date("d.m.y H:i:s",time()); if(isset($_GET['refresh'])) echo " - REFRESH"?>
  </div>
  
  <div class="toplinks">
    <?php //echo $toplink_html; ?>
  </div>
 
  <div class="ui content">
  <!-- HEADER STOP -->
