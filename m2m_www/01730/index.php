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
	
  <title><?php echo $uiTitle;?></title>

  </head>

<body>

<?php 


 //add 
 $ainData = readAinData($db, $ainData, $timeout, $span);

 //Function reads data from database, and combines it to $ainData- structure
 function readAinData($db, $ainData, $timeout, $span)
 {
     
     $sql = getAranaLatestSecAvg($span);
     
     //read rows
     foreach( $db->query($sql) as $row)
     {
         //get age and query for row
         $age  = $row['age'];
         $layer = (int)$row['layer'];
         
         //each row has 6 datacolumns, get each value 
         $keylist = array('A1','A2','A3','A4','A5','A6');
         for ($ain=1; $ain <= 6; $ain++)
         {
             //Create val using functions in common.php
			 $type = @$ainData[$layer][$ain]['type']; //ADD: Ignore errors: Only configured layers/ains are initialized
             $key = $keylist[$ain-1];
             $val = toUnitVal($row[$key], $type);
             $unit = toUnit($type);
             
             //set data into array
             $ainData[$layer][$ain]['age'] = $age;
             $ainData[$layer][$ain]['val'] = $val;
             $ainData[$layer][$ain]['unit'] = $unit;
         }
     }
     
     return $ainData;
 }
 
 function trAinData($ainData)
 {
	 global $timeout;
	 global $uiBaseUrl;
	 
     $tr = array();
     
     $laynum = -1;
     $ainunm = 0;
     
     foreach($ainData as $laynum=>$layer)
     {
         
         foreach($layer as $ainum=>$ain)
         {   
             //only print those with names
             if (!isset($ain['name']))
                 continue;
             
             $parts = explode('|', $ain['group']);
             $gName = $parts[0];
             $gNum = $parts[1];
             
             $classAged = "text-secondary";
             $classLow =  "text-danger";
             $classHigh = "text-danger";
             
             $trClass = ($ain['age'] > $timeout) ? $classAged : "none";
             $tdClass = "none";
             
               $res = '<tr>';
                 $res .= '<th class="bg-dark text-white text-center p-0" colspan=4><span>'.$gName.'</span></th></tr>';
                 $res .= '<tr class="'.$trClass.'">';
                 $res .= '<td>'.$ain['age'].'</td>';
                 $res .= '<td>'.$ain['name'].'</td>';
                 $res .= '<td class="'.$tdClass.'">'.$ain['val'].'</td>';
                 $res .= "<td><a href=\"$uiBaseUrl/aindata.php?layer=$laynum&ain=$ainum&type=short\">".'<img src="lib/arrow.png" width=25></a></td>';
                 $res .= '</tr>';
                 
             $idx = $ain['group'];
             $tr[$idx] = $res;
        }
     }
        ksort($tr);
        $prevGroup = "";
        
        foreach ($tr as &$r)
        {
            $start = strpos($r,"<span>");
            $end = strpos($r,"</span>");
            $group = substr($r, $start, ($end-$start));
            if ($group == $prevGroup)
			{
                $toRemove = substr($r,0, strpos($r,"</tr>"));
                //remove group <tr>....</tr>
                $r = str_replace($toRemove, "",$r);
            }
			
			$prevGroup = $group;
        }
        
        return $tr;
 }

 ?>
 
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
	
<table class="table table-striped">
  <thead>
    <tr>
      <th>Age</th>
      <th>Name</th>
	  <th>Value</th>
	  <th>Trend</th>
	  <th>Stats</th>
    </tr>
  </thead>
  <tbody >
	<?php 

	foreach($analogSensorSet->getGroups() as $groupName)
	{
		echo '<th class="bg-dark text-white text-center p-0" colspan=5><span>'.$groupName.'</span></th></tr>';
		foreach($analogSensorSet->getSensorsInGroup($groupName) as $sensor)
		{
			$history = $sensor->getLastHourHistory();
			$trend = $history->getChange(true);
			$val = $sensor->getValue();
			$name = $sensor->getName();
			$age = $sensor->getDataAge();
			
			$highLimit = $sensor->getHighLimit();
			$lowLimit = $sensor->getLowLimit();
			
			$classAged = "text-secondary";
			
            $valLow =  "text-primary";
            $valHigh = "text-danger";
            
			$trendUp =  "text-success";
            $trendDown = "text-primary";
            
            $trClass = ($age > $timeout) ? $classAged : "none";
			
            $valClass = $val > $highLimit ? $valHigh : "none";
			$valClass = $val < $lowLimit ? $valLow : $valClass;
			
			$trendClass = $trend > 1 ? $trendUp : "none";
			$trendClass = $trend < -1 ? $trendDown : $trendClass;
			
			$laynum = $sensor->getLayer();
			$ainum = $sensor->getNumber();
			
			$trendLink = "$uiBaseUrl/aindata.php?layer=$laynum&ain=$ainum&type=short";
			
			echo '<tr class="'.$trClass.'">';
			echo "<td>$age</td><td>$name</td><td class=$valClass>$val</td>";
			echo "<td class=$trendClass>$trend/h</td>";
			echo "<td><a href=\"$trendLink\">".'<img src="lib/arrow.png" width=25></a></td>';
            echo '</tr>';
		}
	
	}
	
	?>
  </tbody>
</table>
</div>
<div class="p-3 bg-dark text-white">M2M system using recycled hardware by Stefan Engström (c)2013 </div>
</body>

</html> 
