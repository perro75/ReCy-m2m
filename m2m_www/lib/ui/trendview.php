<?php
$layer = $_GET['layer'];
$number = $_GET['number'];
$hours = $_GET['hours'];

$sensor = $analogSensorSet->getSensorInLayer($layer, $number);

$history = $sensor->getLastHourHistory($hours, true);

$trend = $history->getChange(true);

?>
<h3><?php echo $sensor->getName(); ?></h3>

<div class="row">
	<div class="col-xs-6 col-md-2"><?php echo number_format((float)$trend, 1, '.', '') ."C /$hours h"; ?></div>
	<div class="col-xs-6 col-md-2"><?php echo number_format((float)$trend / $hours, 1, '.', '') ."C /1 h"; ?></div>
	<div class="col-xs-6 col-md-2"> <?php echo number_format((float)$trend / $hours * 24, 1, '.', '') ."C /24h"; ?></div>
	<div class="col-xs-6 col-md-2"> <?php echo "Min: ".number_format((float)$history->getMin(), 1, '.', '')."C"; ?></div>
	<div class="col-xs-6 col-md-2"> <?php echo "Max: ".number_format((float)$history->getMax(), 1, '.', '')."C"; ?></div>
	<div class="col-xs-6 col-md-2"> <?php echo "Avg: ".number_format((float)$history->getAvg(), 1, '.', '')."C"; ?></div>
</div>
<hr/>

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

<div class="row">
	<div class="col-md-2"><?php echo "<a href=\"?display=trend&layer=$layer&number=$number&hours=6\">6h</a>"; ?></div>
	<div class="col-md-2"><?php echo "<a href=\"?display=trend&layer=$layer&number=$number&hours=24\">24h</a>"; ?></div>
	<div class="col-md-2"><?php echo "<a href=\"?display=trend&layer=$layer&number=$number&hours=73\">3vrk</a>"; ?></div>
	<div class="col-md-2"><?php echo "<a href=\"?display=trend&layer=$layer&number=$number&hours=168\">1vko</a>"; ?></div>
	<div class="col-md-2"><?php echo "<a href=\"?display=trend&layer=$layer&number=$number&hours=720\">1kk</a>"; ?></div>
	<div class="col-md-2"><?php echo "<a href=\"?display=trend&layer=$layer&number=$number&hours=4320\">6kk</a>"; ?></div>
	
</div>