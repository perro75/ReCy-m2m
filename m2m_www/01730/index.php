<?php 

//refresh every 5 seconds if parameter set
if(isset($_GET['refresh'])) 
{
    echo '<meta http-equiv="refresh" content="5">';
} 

 // Additional includes are made through the settings-php
 include 'settings.php';
 
?>

<!DOCTYPE html>
<html>

<head>

	 <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!--Bootstrap, JQuery, Popper -->
 	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" 
		integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" 
		crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" 
		integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" 
		crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" 
		integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" 
		crossorigin="anonymous"></script>
	
	<!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" 
		integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" 
		crossorigin="anonymous">
		
	<!-- CHARTIST -->
	<link rel="stylesheet" href="http://cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
    <script src="http://cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
	<style>
		
		//styling of axis in CSS
		.ct-series-a .ct-line {
		  stroke: red;
		  stroke-width: 3px;
		  stroke-dasharray: 10px 10px;
		}
		
		.ct-series-b .ct-line {
		  stroke: green;
		  stroke-width: 3px;
		}
		
		.ct-series-b .ct-point {
		  stroke: black;
		  stroke-width: 10px;
		}
		
		.ct-chart
		{
			width: 100%;
		}
		
		
	</style>
	
  <title><?php echo $uiTitle;?></title>

  </head>

<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
      <a class="navbar-brand" href="#"><?php echo $uiTitle;?></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
		
		<!--LINKS-->
          <li class="nav-item <?php if (!isset($_GET['refresh'])) echo 'active';?>">
            <a class="nav-link" href="">Static</a>
          </li>
           <li class="nav-item <?php if (isset($_GET['refresh'])) echo 'active';?>">
            <a class="nav-link" href="?refresh">Refresh</a>
          </li>

         </ul>
      </div>
    </nav>

<div class="container p-3">

<?php
	
	if (isset($_GET['display']) && $_GET['display'] == "trend")
		include $uiLibPath."/trendview.php";
	else
		include $uiLibPath."/sensorTable.php"; 
?>

</div>
<div class="p-3 bg-dark text-white">M2M system using recycled hardware by Stefan Engström (c)2013 </div>
</body>

</html> 
