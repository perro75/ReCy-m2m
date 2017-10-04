<?php
/*
* Data in the settings-file is project specific and usually changed for each project.
* 
*/

//**** NAMES FOR bits 
//Outputs 8..11 bits 0..3
//$bitName[0] = "RED LED";
//$bitName[1] = "GREEN LED";
//$bitName[2] = "YELLOW LED";
//$bitName[7] = "TEST";

//Names for AIN
//layer 0...7 -> ain 1..6

$ainData[0][1]['group'] = "INOMHUS|1";
$ainData[0][1]['name'] = "VÅNING 2";
$ainData[0][1]['type'] = "18B20";
$ainData[0][1]['high'] = 20.0;
$ainData[0][1]['low'] =5.0;

$ainData[0][2]['group'] = "PANNRUM|1";
$ainData[0][2]['name'] = "PANNA IN";
$ainData[0][2]['type'] = "18B20";
$ainData[0][2]['high'] = 70.0;
$ainData[0][2]['low'] = 10.0;

$ainData[0][3]['group'] = "PANNRUM|3";
$ainData[0][3]['name'] = "VÄRME IN";
$ainData[0][3]['type'] = "18B20";
$ainData[0][3]['high'] = 75.0;
$ainData[0][3]['low'] = 60.0;

$ainData[0][4]['group'] = "PANNRUM|2";
$ainData[0][4]['name'] = "PANNA UT";
$ainData[0][4]['type'] = "18B20";
$ainData[0][4]['high'] = 75.0;
$ainData[0][4]['low'] = 60.0;

$ainData[0][5]['group'] = "PANNRUM|4";
$ainData[0][5]['name'] = "VÄRME UT";
$ainData[0][5]['type'] = "18B20";
$ainData[0][5]['high'] = 50.0;
$ainData[0][5]['low'] = 38.0;
/*
$ainData[1][1]['group'] = "ADXL|1";
$ainData[1][1]['name'] = "ROLL";
$ainData[1][1]['type'] = "ADXL";
$ainData[1][1]['high'] = 0.0;
$ainData[1][1]['low'] = 90.0;
*/

$ainData[1][2]['group'] = "PANNRUM|6";
$ainData[1][2]['name'] = "DRAGLUCKA";
$ainData[1][2]['type'] = "ADXL";
$ainData[1][2]['high'] = 0.0;
$ainData[1][2]['low'] = 90.0;

/*
$ainData[0][4]['group'] = "B_Våning 2|4";
$ainData[0][4]['name'] = "Vinden";
$ainData[0][4]['type'] = "LDR1";
$ainData[0][4]['high'] = 2;
$ainData[0][4]['low'] =  0.1;

$ainData[0][5]['group'] = "B_Våning 2|1";
$ainData[0][5]['name'] = "Ute(Uppe)";
$ainData[0][5]['type'] = "TMP36";
$ainData[0][5]['high'] = 10.0;
$ainData[0][5]['low'] = 0.0;

$ainData[0][6]['group'] = "B_Våning 2|3";
$ainData[0][6]['name'] = "Element(Vinden)";
$ainData[0][6]['type'] = "TMP36";
$ainData[0][6]['high'] = 20.0;
$ainData[0][6]['low'] = 15.0;

$ainData[5][3]['group'] = "A_Pannrum|2";
$ainData[5][3]['name'] = "Boiler (Nere)";
$ainData[5][3]['type'] = "TMP36";
$ainData[5][3]['high'] = 95.0;
$ainData[5][3]['low'] = 60.0;
//$ainData[5][3]['alarm'] = true;

$ainData[7][3]['group'] = "A_Pannrum|1";
$ainData[7][3]['name'] = "Boiler (Uppe)";
$ainData[7][3]['type'] = "TMP36";
$ainData[7][3]['high'] = 96.0;
$ainData[7][3]['low'] = 70.0;
$ainData[7][3]['alarm'] = true;

$ainData[2][3]['group'] = "A_Pannrum|3";
$ainData[2][3]['name'] = "Panna Ut";
$ainData[2][3]['type'] = "TMP36";
$ainData[2][3]['high'] = 90.0;
$ainData[2][3]['low'] = 65.0;
$ainData[2][3]['alarm'] = true;

$ainData[0][3]['group'] = "C_Källare|1";
$ainData[0][3]['name'] = "Källare (Mobil1)";
$ainData[0][3]['type'] = "TMP36";
$ainData[0][3]['high'] = 20;
$ainData[0][3]['low'] = 4;

$ainData[3][3]['group'] = "C_Källare|2";
$ainData[3][3]['name'] = "Källare (Mobil2)";
$ainData[3][3]['type'] = "TMP36";
$ainData[3][3]['high'] = 20;
$ainData[3][3]['low'] = 4;
*/

$uiBit[1]['normal'] = true;
$uiBit[1]['alarm'] = true;
$uiBit[1]['namenormal'] = 'AC 220V OK';
$uiBit[1]['namealarm'] = 'Strömavbrott';

?>