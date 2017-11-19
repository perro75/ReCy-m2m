<?php
$layer = $_GET['layer'];
$number = $_GET['number'];
$hours = $_GET['hours'];

$sensor = $analogSensorSet->getSensorInLayer($layer, $number);

if ($hours > 3)
{	
	$history = $sensor->getLastHourHistory($hours, true);
}
else
{
	$history = $sensor->getLastHourHistory($hours);
}

$trend = $history->getChange(true);

?>
<h3><?php echo $sensor->getName(). " ($trend/$hours h)"; ?></h3>
<div class="ct-chart ct-perfect-fourth"></div>
	
<script>
/*http://gionkunz.github.io/chartist-js/api-documentation.html*/
var data = {
	  // Data labels
	  //labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
	  
	  //data series
	  series: [
		{
			name: 'Serie 1',
			data: [<?php echo $history->chartistXy()?>]
		},
		{
			//name: 'Serie 2',
			//data: [{x:3, y:17}, {x:4, y:17},{x:5, y:17},{x:6, y:17},{x:78, y:17}]
		}]};

var options = {
  //width: 600,
  //height: 800,
  axisX: {
  //TO SHOW LABELS, USE THE DEFAULT STEPAXIS, AND NO x- VALUES
	type: Chartist.AutoScaleAxis,
	scaleMinSpace: 60
  },
  axisY: {
    //ticks: [0, 50, 75, 87.5, 100],
    low: <?php echo floor($history->getMin())?>, 
	high: <?php echo floor($history->getMax()+1)?>,
	scaleMinSpace: 1,
	onlyInteger: true
  },
  series:
	{
		'Serie 1': {showPoint: false, showArea: true,  lineSmooth: Chartist.Interpolation.simple() /*step, simple, none*/}
	}
	
};

new Chartist.Line('.ct-chart', data, options);
</script>
