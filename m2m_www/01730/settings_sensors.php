<?php
//SENSOR DATA SETTINGS
//+++++++++++++++++++++++++++++++++++++++++++++++++++++
$ainData[1][4]['group'] = "A_UTE|1";
$ainData[1][4]['name'] = "UTOMHUS";
$ainData[1][4]['type'] = "18B20";
$ainData[1][4]['high'] = 10.0;
$ainData[1][4]['low'] = -10.0;

$ainData[1][3]['group'] = "B_INNE|1";
$ainData[1][3]['name'] = "VÅNING 1";
$ainData[1][3]['type'] = "18B20";
$ainData[1][3]['high'] = 20.0;
$ainData[1][3]['low'] = 12.0;

$ainData[0][1]['group'] = "B_INNE|2";
$ainData[0][1]['name'] = "VÅNING 2";
$ainData[0][1]['type'] = "18B20";
$ainData[0][1]['high'] = 20.0;
$ainData[0][1]['low'] = 12.0;

$ainData[0][2]['group'] = "C_VÄRME|1";
$ainData[0][2]['name'] = "PANNA IN";
$ainData[0][2]['type'] = "18B20";
$ainData[0][2]['high'] = 70.0;
$ainData[0][2]['low'] = 10.0;

$ainData[0][3]['group'] = "C_VÄRME|3";
$ainData[0][3]['name'] = "VÄRME IN";
$ainData[0][3]['type'] = "18B20";
$ainData[0][3]['high'] = 75.0;
$ainData[0][3]['low'] = 60.0;

$ainData[0][4]['group'] = "C_VÄRME|2";
$ainData[0][4]['name'] = "PANNA UT";
$ainData[0][4]['type'] = "18B20";
$ainData[0][4]['high'] = 75.0;
$ainData[0][4]['low'] = 60.0;

$ainData[0][5]['group'] = "C_VÄRME|4";
$ainData[0][5]['name'] = "VÄRME UT";
$ainData[0][5]['type'] = "18B20";
$ainData[0][5]['high'] = 50.0;
$ainData[0][5]['low'] = 38.0;

$ainData[0][6]['group'] = "D_TEST|1";
$ainData[0][6]['name'] = "TEST_1";
$ainData[0][6]['type'] = "18B20";
$ainData[0][6]['high'] = 25.0;
$ainData[0][6]['low'] = 10.0;

$ainData[1][1]['group'] = "D_TEST|2";
$ainData[1][1]['name'] = "TEST_2";
$ainData[1][1]['type'] = "18B20";
$ainData[1][1]['high'] = 25.0;
$ainData[1][1]['low'] = 10.0;

$ainData[1][2]['group'] = "D_TEST|3";
$ainData[1][2]['name'] = "TEST_3";
$ainData[1][2]['type'] = "18B20";
$ainData[1][2]['high'] = 25.0;
$ainData[1][2]['low'] = 10.0;

$ainData[1][5]['group'] = "C_VÄRME|5";
$ainData[1][5]['name'] = "RETUR";
$ainData[1][5]['type'] = "18B20";
$ainData[1][5]['high'] = 35.0;
$ainData[1][5]['low'] = 12.0;

$uiBit[1]['normal'] = true;
$uiBit[1]['alarm'] = true;
$uiBit[1]['namenormal'] = 'AC 220V OK';
$uiBit[1]['namealarm'] = 'Strömavbrott';

//+++++++++++++++++++++++++++++++++++++++++++++++++++++


/*
//**** NAMES FOR bits 
//Outputs 8..11 bits 0..3
//$bitName[0] = "RED LED";
//$bitName[1] = "GREEN LED";
//$bitName[2] = "YELLOW LED";
//$bitName[7] = "TEST";
*/
?>