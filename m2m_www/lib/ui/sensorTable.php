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

	//FOR: PRINT ROWS OF GROUPHEADERS
		//FOR: PRINT ROWS OF SENSORS
		
	foreach($analogSensorSet->getGroups() as $groupName)
	{
		echo '<th class="bg-dark text-white text-center p-0" colspan=5><span>'.$groupName.'</span></th></tr>'."\r\n";
		foreach($analogSensorSet->getSensorsInGroup($groupName) as $sensor)
		{
			$history = $sensor->getLastHourHistory(6);
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
			
			//link to trend-display
			//$trendLink = "$uiBaseUrl/aindata.php?layer=$laynum&ain=$ainum&type=short";
			$trendLink1 = "$uiBaseUrl?display=trend&layer=$laynum&number=$ainum&hours=1";
			$trendLink2 = "$uiBaseUrl?display=trend&layer=$laynum&number=$ainum&hours=12";
			$trendLink3 = "$uiBaseUrl?display=trend&layer=$laynum&number=$ainum&hours=24";
			
			
			echo '<tr class="'.$trClass.'">';
			echo "<td>$age</td><td>$name</td>";
			echo "<td class=$valClass>$val</td>";
			echo "<td class=$trendClass>$trend/6h</td>";
			echo "<td><a href=\"$trendLink1\">1</a>|<a href=\"$trendLink2\">12</a>|<a href=\"$trendLink3\">24h</a></td>";
            echo '</tr>',"\r\n";
		}
	
	}
	
	?>
  </tbody>
</table>