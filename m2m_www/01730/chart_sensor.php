<?php

/*pChart 2.x linechart of temperatures*/

$outfile ="chart_sensor.png";

// Standard inclusions
include("../pChart/class/pData.class.php");
include("../pChart/class/pDraw.class.php");
include("../pChart/class/pImage.class.php");

//set path to fonts
$fontPath = "../pChart/fonts/";

 /* Create your dataset object */ 
 $myData = new pData(); 
 $myData->setAxisName(0,$dataUnit);

//linecolors
$color[0] = array("R"=>0,"G"=>0,"B"=>0);
$color[1] = array("R"=>255,"G"=>51,"B"=>51);
$color[2] = array("R"=>51,"G"=>51,"B"=>255);

 //Add data as set in array $serie[][] by calling page
 $i=1; 
 foreach($serie as $s)
 {
	$myData->addpoints($s,"Serie".$i);
	$myData->setPalette("Serie".$i,$color[$i-1]);
	$i++;
 }

 $myData->setSerieWeight("Serie1",2);
 
 $j=1;
 foreach($name as $n)
 {
	$myData->setSerieDescription("Serie".$j++, $n);
 }
 
/* Bind a data serie to the X axis */

//$myData->addPoints(array(-1,-2,-3,-4,-5,-6,-7,-8,-9,-10,-11,-12,-13,-14,-15,-16,-17,-18,-19,-20,-21,-22,-23,-24),"Labels");
//$myData->setSerieDescription("Labels","Age");
//$myData->setAbscissa("Labels");
 
 /* Create a pChart object and associate your dataset */ 
 $myPicture = new pImage(1200,800,$myData);

 $myPicture->setFontProperties(array("FontName"=>"$fontPath/verdana.ttf","FontSize"=>25));
 $myPicture->drawText(200,50,$gTitle,array("R"=>0,"G"=>0,"B"=>0));

 /* Choose a nice font */
 $myPicture->setFontProperties(array("FontName"=>"$fontPath/verdana.ttf","FontSize"=>20));
 
 /* Write a legend box */
$myPicture->drawLegend(1000,50,array("BoxSize"=>8,"R"=>222,"G"=>222,"B"=>222,"Surrounding"=>40,"Family"=>LEGEND_FAMILY_CIRCLE));
 
 /* Define the boundaries of the graph area */
 $myPicture->setGraphArea(100,20,1100,720);

 //set axis scale
 $AxisBoundaries = array(0=>array("Min"=>$yMin,"Max"=>$yMax));
 //skip labels if more than 24 hours
 $labelSkip  = (count($serie[0]) > 24) ? count($serie[0])/24*2-1: 1;
 
 $scaleSettings  = array("LabelSkip"=>$labelSkip, "Factors"=>array($yScale), "GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE,"Mode"=>SCALE_MODE_MANUAL, "ManualScale"=>$AxisBoundaries);
 $myPicture->drawScale($scaleSettings);
 
 /*Draw the thresholds*/
$myPicture->drawThreshold($minLimit,array("WriteCaption"=>TRUE,"Caption"=>"LOW LIMIT"));
$myPicture->drawThreshold($maxLimit,array("WriteCaption"=>TRUE,"Caption"=>"HIGH LIMIT"));

 /* Draw the scale, keep everything automatic */ 
 //$myPicture->drawSplineChart();
 $myPicture->drawLineChart();

 /* Render the picture to file */
 $myPicture->render($outfile);

//echo time as param to image to force reload for different image
?>

<a href="chart_sensor.png"><img width="800" src="chart_sensor.png?<?PHP echo time();?>"/></a>