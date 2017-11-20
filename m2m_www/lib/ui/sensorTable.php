<table class="table table-striped">
  <thead>
    <tr>
      <th>Age</th>
      <th>Name</th>
	  <th>Value</th>
	  <th>Trend</th>
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
			$history = $sensor->getLastHourHistory(6, true);
			$trend = $history->getChange(true);
				$trend = number_format((float)$trend, 1, '.', ''); //format to always have 1 decimal
			$val = $sensor->getValue();
				$val = number_format((float)$val, 1, '.', ''); //format to always have 1 decimal
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
			$trendLink = "$uiBaseUrl?display=trend&layer=$laynum&number=$ainum&hours=6";
						
			echo '<tr class="'.$trClass.'">';
			echo "<td>$age</td><td>$name</td>";
			echo "<td class=$valClass>$val</td>";
			echo "<td class=$trendClass><a target=\"_blank\" href=\"$trendLink\">$trend/6h</a></td>";
			echo '</tr>',"\r\n";
		}
	}
	
	?>
  </tbody>
</table>