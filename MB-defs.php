<?php
/*
 File: MB-defs.php

 Purpose: provide a bridge to naming of weather variables from the native Meteobridge software package to
          Weather-Display names used in common scripts like ajax-dashboard.php and ajax-gizmo.php

 
 Author: Ken True - webmaster@saratoga-weather.org

 (created by gen-defs.php - V1.09 - 02-Mar-2013)
 Generated on 2013-03-02 11:03:58 PST

//Version MB-defs.php - V1.00 - 08-Mar-2013
//Version MB-defs.php - V1.01 - 09-Mar-2013 - fixes for stations w/o solar and/or UV sensors
//Version MB-defs.php - V1.02 - 17-Mar-2013 - added processing for new Meteobridge system variables
//Version MB-defs.php - V1.03 - 19-Aug-2013 - added Davis forecast text support Meteobridge 1.8(2198)+
//Version MB-defs.php - V1.04 - 08-Feb-2014 - corrected $yearrn to use rain0rain-yearsum variable

*/
// --------------------------------------------------------------------------

// allow viewing of generated source

if (isset($_REQUEST["sce"]) and strtolower($_REQUEST["sce"]) == "view" ) {
//--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header("Pragma: public");
   header("Cache-Control: private");
   header("Cache-Control: no-cache, must-revalidate");
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header("Connection: close");
   
   readfile($filenameReal);
   exit;
}
$WXsoftware = 'MB';  
// this has WD $varnames = $WX['MB-varnames']; equivalents
 
$uomtemp = $WX['uomTemp'];
$uombaro = $WX['uomBaro'];
$uomwind = $WX['uomWind'];
$uomrain = $WX['uomRain'];
$time = $WX['time'];
$date = $WX['date'];
$temperature = $WX['th0temp-act'];
$tempnodp  = round($temperature,0); // calculated value
$humidity = $WX['th0hum-act'];
$dewpt = $WX['th0dew-act'];
$maxtemp = $WX['th0temp-dmax'];
$mintemp = $WX['th0temp-dmin'];
$windch = $WX['wind0chill-act'];
$windchnodp  = round($windch,0); // calculated value
$maxtempyest = $WX['th0temp-ydmax'];
$mintempyest = $WX['th0temp-ydmin'];
$avgspd = $WX['wind0avgwind-act'];
$gstspd = $WX['wind0wind-hmax'];
$maxgst = $WX['wind0wind-dmax'];
$maxgsthr = $WX['wind0wind-hmax'];
$dirdeg = $WX['wind0dir-act'];
$dirlabel  = MB_deg2dir($dirdeg); // calculated value
$baro = $WX['thb0seapress-act'];
$dayrn = $WX['rain0total-daysum'];
$monthrn = $WX['rain0total-monthsum'];
$yearrn = $WX['rain0total-yearsum'];
$currentrainratehr = $WX['rain0rate-act'];
$maxrainrate = $WX['rain0rate-dmax'];
$maxrainratehr = $WX['rain0rate-hmax'];
$yesterdayrain = $WX['rain0total-ydaysum'];
$mrecordwindgust = $WX['wind0wind-mmax'];
$highbaro = $WX['thb0seapress-dmax'];
$hourrn = $WX['rain0total-sum60'];
$minchillyest = $WX['wind0chill-ydmin'];
$minwindch = $WX['wind0chill-dmin'];

// end of generation script

// manual adds

if(MB_isData('mbsystem-swversion')) {
  $wdversiononly = $WX['mbsystem-swversion'];
  $wdbuild = (MB_isData('mbsystem-buildnum'))?$WX['mbsystem-buildnum']:'n/a';
  $wdplatform = (MB_isData('mbsystem-platform'))?$WX['mbsystem-platform']:'';
  $wdversion = $WX['mbsystem-swversion'] . ' build '.$wdbuild. ' '. $wdplatform;	
}

$heatidx = MB_calcHeatIndex($temperature,$humidity,$uomtemp);

if(MB_isData('sol0rad-act')) { 
 $VPsolar = $WX['sol0rad-act'];
 $highsolaryest = $WX['sol0rad-ydmax'];
 $highsolar = $WX['sol0rad-dmax']; // fix this one! max instead of min
}
if(MB_isData('sol0rad-dmaxtime')) {$highsolartime = MB_timeOnly($WX['sol0rad-dmaxtime']);} 
if(MB_isData('sol0rad-ydmaxtime')) {$highsolaryesttime = MB_timeOnly($WX['sol0rad-ydmaxtime']);}

if(MB_isData('uv0index-act')) {
  $VPuv = $WX['uv0index-act'];
  $highuv = $WX['uv0index-dmax'];
  $highuvyest = $WX['uv0index-ydmax'];
}
if(MB_isData('uv0index-dmaxtime')) {$highuvtime = MB_timeOnly($WX['uv0index-dmaxtime']);}
if(MB_isData('uv0index-ydmaxtime')) {$highuvyesttime = MB_timeOnly($WX['uv0index-ydmaxtime']);}

if(MB_isData('th0temp-dmaxtime')) {$maxtempt = MB_timeOnly($WX['th0temp-dmaxtime']);}
if(MB_isData('th0temp-dmintime')) {$mintempt = MB_timeOnly($WX['th0temp-dmintime']);}
if(MB_isData('th0temp-ydmaxtime')) {$maxtempyestt = MB_timeOnly($WX['th0temp-ydmaxtime']);}
if(MB_isData('th0temp-ydmintime')) {$mintempyestt = MB_timeOnly($WX['th0temp-ydmintime']);}
if(MB_isData('wind0chill-dmintime')) {$minwindcht = MB_timeOnly($WX['wind0chill-dmintime']);}

if(MB_isData('wind0wind-mmaxtime')) {$mrecordwindgustt = MB_timeOnly($WX['wind0wind-mmaxtime']);}
if(MB_isData('thb0seapress-dmaxtime')) {$highbarot = MB_timeOnly($WX['thb0seapress-dmaxtime']);}

// change last hour

if(MB_isData('th0temp-val60')) {
	$tempchangehour = $WX['th0temp-act']-$WX['th0temp-val60'];
}

if(MB_isData('th0hum-val60')) {
	$humchangelasthour = $WX['th0hum-act']-$WX['th0hum-val60'];
}

if(MB_isData('th0dew-val60')) {
	$dewchangelasthour = $WX['th0dew-act']-$WX['th0dew-val60'];
}

if(MB_isData('thb0seapress-val60')) {
	$trend = $WX['thb0seapress-act']-$WX['thb0seapress-val60'];
	$pressuretrendname = MB_get_barotrend_text($trend,$uombaro);
} else {
	$pressuretrendname = '--';
}

$lowbaro = $WX['thb0seapress-dmin'];
$lowbarot = MB_timeOnly($WX['thb0seapress-dmintime']);
$maxgstdirectionletter = ' '; // not collected data
$maxgstt = MB_timeOnly($WX['wind0wind-dmaxtime']);

// trends info
$temp0minuteago = $WX['th0temp-act'];
$wind0minuteago = $WX['wind0avgwind-act'];
$dir0minuteago =  MB_deg2dir($WX['wind0dir-act']);
$hum0minuteago =  $WX['th0hum-act'];
$dew0minuteago =  $WX['th0dew-act'];
$baro0minuteago = $WX['thb0seapress-act'];
$rain0minuteago = $WX['rain0total-daysum'];
$rainDP = ( preg_match('|mm|i',$uomrain) )?'%01.1F':'%01.2F';

if ( MB_isdata('th0temp-val5') ) { // only set if the variable has valid data
  $temp5minuteago = $WX['th0temp-val5'];
  $wind5minuteago = $WX['wind0avgwind-val5'];
  $dir5minuteago =  MB_deg2dir($WX['wind0dir-val5']);
  $hum5minuteago =  $WX['th0hum-val5'];
  $dew5minuteago =  $WX['th0dew-val5'];
  $baro5minuteago = $WX['thb0seapress-val5'];
  $rain5minuteago = sprintf($rainDP,$WX['rain0total-val5']-$WX['rain0total-ydmin']); // should be dmax!
}

if ( MB_isdata('th0temp-val10') ) { // only set if the variable has valid data
  $temp10minuteago = $WX['th0temp-val10'];
  $wind10minuteago = $WX['wind0avgwind-val10'];
  $dir10minuteago =  MB_deg2dir($WX['wind0dir-val10']);
  $hum10minuteago =  $WX['th0hum-val10'];
  $dew10minuteago =  $WX['th0dew-val10'];
  $baro10minuteago = $WX['thb0seapress-val10'];
  $rain10minuteago = sprintf($rainDP,$WX['rain0total-val10']-$WX['rain0total-ydmin']); // should be dmax!
}

if ( MB_isdata('th0temp-val15') ) { // only set if the variable has valid data
  $temp15minuteago = $WX['th0temp-val15'];
  $wind15minuteago = $WX['wind0avgwind-val15'];
  $dir15minuteago =  MB_deg2dir($WX['wind0dir-val15']);
  $hum15minuteago =  $WX['th0hum-val15'];
  $dew15minuteago =  $WX['th0dew-val15'];
  $baro15minuteago = $WX['thb0seapress-val15'];
  $rain15minuteago = sprintf($rainDP,$WX['rain0total-val15']-$WX['rain0total-ydmin']); // should be dmax!
}

if ( MB_isdata('th0temp-val30') ) { // only set if the variable has valid data
  $temp30minuteago = $WX['th0temp-val30'];
  $wind30minuteago = $WX['wind0avgwind-val30'];
  $dir30minuteago =  MB_deg2dir($WX['wind0dir-val30']);
  $hum30minuteago =  $WX['th0hum-val30'];
  $dew30minuteago =  $WX['th0dew-val30'];
  $baro30minuteago = $WX['thb0seapress-val30'];
  $rain30minuteago = sprintf($rainDP,$WX['rain0total-val30']-$WX['rain0total-ydmin']); // should be dmax!
}

if ( MB_isdata('th0temp-val60') ) { // only set if the variable has valid data
  $temp60minuteago = $WX['th0temp-val60'];
  $wind60minuteago = $WX['wind0avgwind-val60'];
  $dir60minuteago =  MB_deg2dir($WX['wind0dir-val60']);
  $hum60minuteago =  $WX['th0hum-val60'];
  $dew60minuteago =  $WX['th0dew-val60'];
  $baro60minuteago = $WX['thb0seapress-val60'];
  $rain60minuteago = sprintf($rainDP,$WX['rain0total-val60']-$WX['rain0total-ydmin']); // should be dmax!
}

$mrecordhighbaro = $WX['thb0seapress-mmax'];
$mrecordhighbaroday = substr($WX['thb0seapress-mmaxtime'],6,2);
$mrecordhighbaromonth = substr($WX['thb0seapress-mmaxtime'],4,2);
$mrecordhighbaroyear = substr($WX['thb0seapress-mmaxtime'],0,4);

$mrecordlowbaro = $WX['thb0seapress-mmin'];
$mrecordlowbaroday = substr($WX['thb0seapress-mmintime'],6,2);
$mrecordlowbaromonth = substr($WX['thb0seapress-mmintime'],4,2);
$mrecordlowbaroyear = substr($WX['thb0seapress-mmintime'],0,4);

$mrecordhightemp = $WX['th0temp-mmax'];
$mrecordhightempday = substr($WX['th0temp-mmaxtime'],6,2);
$mrecordhightempmonth = substr($WX['th0temp-mmaxtime'],4,2);
$mrecordhightempyear = substr($WX['th0temp-mmaxtime'],0,4);

$mrecordlowtemp = $WX['th0temp-mmin'];
$mrecordlowtempday = substr($WX['th0temp-mmintime'],6,2);
$mrecordlowtempmonth = substr($WX['th0temp-mmintime'],4,2);
$mrecordlowtempyear = substr($WX['th0temp-mmintime'],0,4);

$mrecordlowchill = $WX['wind0chill-mmin'];
$mrecordlowchillday = substr($WX['wind0chill-mmintime'],6,2);
$mrecordlowchillmonth = substr($WX['wind0chill-mmintime'],4,2);
$mrecordlowchillyear = substr($WX['wind0chill-mmintime'],0,4);

$mrecordhighgust = $WX['wind0wind-mmax'];
$mrecordhighgustday = substr($WX['wind0wind-mmaxtime'],6,2);
$mrecordhighgustmonth = substr($WX['wind0wind-mmaxtime'],4,2);
$mrecordhighgustyear = substr($WX['wind0wind-mmaxtime'],0,4);


$yrecordhighbaro = $WX['thb0seapress-ymax'];
$yrecordhighbaroday = substr($WX['thb0seapress-ymaxtime'],6,2);
$yrecordhighbaromonth = substr($WX['thb0seapress-ymaxtime'],4,2);
$yrecordhighbaroyear = substr($WX['thb0seapress-ymaxtime'],0,4);

$yrecordlowbaro = $WX['thb0seapress-ymin'];
$yrecordlowbaroday = substr($WX['thb0seapress-ymintime'],6,2);
$yrecordlowbaromonth = substr($WX['thb0seapress-ymintime'],4,2);
$yrecordlowbaroyear = substr($WX['thb0seapress-ymintime'],0,4);

$yrecordhightemp = $WX['th0temp-ymax'];
$yrecordhightempday = substr($WX['th0temp-ymaxtime'],6,2);
$yrecordhightempmonth = substr($WX['th0temp-ymaxtime'],4,2);
$yrecordhightempyear = substr($WX['th0temp-ymaxtime'],0,4);

$yrecordlowtemp = $WX['th0temp-ymin'];
$yrecordlowtempday = substr($WX['th0temp-ymintime'],6,2);
$yrecordlowtempmonth = substr($WX['th0temp-ymintime'],4,2);
$yrecordlowtempyear = substr($WX['th0temp-ymintime'],0,4);

$yrecordlowchill = $WX['wind0chill-ymin'];
$yrecordlowchillday = substr($WX['wind0chill-ymintime'],6,2);
$yrecordlowchillmonth = substr($WX['wind0chill-ymintime'],4,2);
$yrecordlowchillyear = substr($WX['wind0chill-ymintime'],0,4);

$yrecordwindgust = $WX['wind0wind-ymax'];
$yrecordhighgustday = substr($WX['wind0wind-ymaxtime'],6,2);
$yrecordhighgustmonth = substr($WX['wind0wind-ymaxtime'],4,2);
$yrecordhighgustyear = substr($WX['wind0wind-ymaxtime'],0,4);

if(isset($WX['forecast-text']) and $WX['forecast-text'] <> '') {
	$vpforecasttext = $WX['forecast-text'];
}


# MB unique functions included from MB-functions-inc.txt 
#-------------------------------------------------------------------------------------
# function processed WD variables
#-------------------------------------------------------------------------------------

global $SITE;

$SITE['commaDecimal'] = strpos($temperature,',') !==false?true:false; // using comma for decimal point?
if(!isset($SITE['WDdateMDY'])) {$WDdateMDY = false;} else {$WDdateMDY = $SITE['WDdateMDY'];}
if(isset($SITE['conditionsMETAR'])) { // override with METAR conditions for text and icon if requested.
	include_once("get-metar-conditions-inc.php");
	list($sunrise,$sunset) = MB_getSuntimes("$date $time",$SITE['latitude'],$SITE['longitude']);
	list($Currentsolardescription,$iconnumber) = mtr_conditions($SITE['conditionsMETAR'], $time, $sunrise, $sunset);
}
# generate the separate date/time variables by dissection of input date/time and format
list($date_year,$date_month,$date_day,$time_hour,$time_minute,$monthname,$dayname)
  = MB_setDateTimes($date,$time,$WDdateMDY);

$beaufortnum =  MB_beaufortNumber($avgspd,$uomwind);
$bftspeedtext = MB_beaufortText($beaufortnum);

list($chandler,$chandlertxt,$chandlerimg) = MB_CBI($temperature,$uomtemp,$humidity);

if(!isset($wdversion) and isset($SITE['WXsoftwareVersion'])) {$wdversion = $SITE['WXsoftwareVersion']; }

$humidex = MB_calcHumidex($temperature,$humidity,$uomtemp); // WD Sample= '61.6°F'

list($feelslike,$heatcolourword) = MB_setFeelslike ($temperature,$windch,$humidex,$uomtemp);

$date = MB_dateOnly($date_year.$date_month.$date_day,$WDdateMDY); // convert YYYYMMDD to DD/MM/YYYY or MM/DD/YYYY

#-------------------------------------------------------------------------------------
# MB support function - MB_getSuntimes($time,$stationlatitude,$stationlongitude);
#-------------------------------------------------------------------------------------
function MB_getSuntimes($stationtime,$stationlatitude,$stationlongitude) {
	
	$tstamp = strtotime($stationtime);
	if(function_exists('date_sun_info')) {
	  $info = date_sun_info($tstamp,$stationlatitude,$stationlongitude);
	  $t = $info['sunrise'] . ' ' . $info['sunset'];
	} else {
	  $t = 'n/a n/a';
	}
	
	return(explode(' ',$t));
	
} // end MB_getSuntimes

#-------------------------------------------------------------------------------------
# MB support function - MB_isdata
#-------------------------------------------------------------------------------------

function MB_isdata( $variable ) {
  global $WX;
  
// see if $WX array has valid data contents
  if(isset($WX[$variable]) and $WX[$variable] !== '--') { return (true); } else { return (false); }	
}


#-------------------------------------------------------------------------------------
# MB support function - MB_calcHumidex
#-------------------------------------------------------------------------------------

function MB_calcHumidex ($temp,$humidity,$useunit) {
// Calculate Humidex from temperature and humidity
// Source of calculation: http://www.physlink.com/reference/weather.cfm	
  global $Debug,$WX;
  if(preg_match('|F|i',$WX['uomTemp'])) {
    $T= MB_convertTemp($temp,'C');
  } else {
	$T = $temp;
  }
  $H = $humidity;
  
  $t=7.5*$T/(237.7+$T);
  $et=pow(10,$t);
  $e=6.112*$et*($H/100);
  $humidex=$T+(5/9)*($e-10);
  $Debug .= "<!-- calcHumidex T=$T C, H=$H calc=$humidex ";
  if ($humidex < $T) {
	 $humidex=$T;
     $Debug .= " set to T, ";
  }
  if(preg_match('|F|i',$useunit)) {
     # convert to F
     $humidex = sprintf("%01.1f",round((1.8 * $humidex) + 32.0,1));	  
  }
  $humidex = round($humidex,1);
  $Debug .= " humidex=$humidex $useunit -->\n"; 
  return($humidex);	
}

#-------------------------------------------------------------------------------------
# MB support function - MB_calcHeatIndex
#-------------------------------------------------------------------------------------

function MB_calcHeatIndex ($temp,$humidity,$useunit) {
// Calculate Heat Index from temperature and humidity
// Source of calculation: http://woody.cowpi.com/phpscripts/getwx.php.txt	
  global $Debug;
  if(preg_match('|C|i',$useunit)) {
    $tempF = round(1.8 * $temp + 32,1);
  } else {
	$tempF = round($temp,1);
  }
  $rh = $humidity;
  
  
  // Calculate Heat Index based on temperature in F and relative humidity (65 = 65%)
  if ($tempF > 79 && $rh > 39) {
	  $hiF = -42.379 + 2.04901523 * $tempF + 10.14333127 * $rh - 0.22475541 * $tempF * $rh;
	  $hiF += -0.00683783 * pow($tempF, 2) - 0.05481717 * pow($rh, 2);
	  $hiF += 0.00122874 * pow($tempF, 2) * $rh + 0.00085282 * $tempF * pow($rh, 2);
	  $hiF += -0.00000199 * pow($tempF, 2) * pow($rh, 2);
	  $hiF = round($hiF,1);
	  $hiC = round(($hiF - 32) / 1.8,1);
  } else {
	  $hiF = $tempF;
	  $hiC = round(($hiF - 32) / 1.8,1);
  }
  $Debug .= "<!-- MB_calcHeatIndex temp=$temp ($tempF F) C, rh=$rh calc=$hiF F, $hiC C ";
  if(preg_match('|F|i',$useunit)) {
     $heatIndex = $hiF;	  
  } else {
	 $heatIndex = $hiC;
  }
  $Debug .= " heatIndex=$heatIndex $useunit -->\n"; 
  return($heatIndex);	
}

#-------------------------------------------------------------------------------------
# MB support function - MB_convertTemp
#-------------------------------------------------------------------------------------

function MB_convertTemp ($rawtemp,$useunit) {
	 $dpTemp = 1;
	 if(!is_numeric($rawtemp)) { return($rawtemp); } // no conversions for missing values
	 if (preg_match('|C|i',$useunit))  { // convert F to C
		return( sprintf("%01.${dpTemp}f",round(($rawtemp-32.0) / 1.8,$dpTemp)));
	 } else {  // leave as F
		return (sprintf("%01.${dpTemp}f", round($rawtemp*1.0,$dpTemp)));
	 }
}

#-------------------------------------------------------------------------------------
# MB support function - MB_timeOnly
#-------------------------------------------------------------------------------------

function MB_timeOnly ($indatetime) {
// Return HH:MM (24hr time format)
// expecting
// 0....+....1....+
// 20110622061003
// yyyymmddhhMMss
  return(substr($indatetime,8,2).':'.substr($indatetime,10,2));
}

#-------------------------------------------------------------------------------------
# MB support function - MB_dateOnly
#-------------------------------------------------------------------------------------

function MB_dateOnly ($indatetime,$MDY=true) {
// Return dd/mm/yyyy or mm/dd/yyyy
// expecting
// 0....+....1....+
// 20110622061003
// yyyymmddhhMMss
  if($MDY) {
    return(substr($indatetime,4,2).'/'.substr($indatetime,6,2).'/'.substr($indatetime,0,4));
  } else {
    return(substr($indatetime,6,2).'/'.substr($indatetime,4,2).'/'.substr($indatetime,0,4));
  }
}

#-------------------------------------------------------------------------------------
# MB support function - MB_WDrecordDate
#-------------------------------------------------------------------------------------

function MB_WDrecordDate ($indatetime) {
// extract Y, M, D and return as array
// expecting
// 0....+....1....+
// 20110622061003
// yyyymmddhhMMss
  return(array(substr($indatetime,0,4),substr($indatetime,4,2),substr($indatetime,6,2)));
}

#-------------------------------------------------------------------------------------
# MB support function - MB_fixupTime
#-------------------------------------------------------------------------------------

function MB_fixupTime ($intime) {
  global $Debug;
  $tfixed = preg_replace('/^(\S+)\s+(\S+)$/is',"$2",$intime);
  $t = explode(':',$tfixed);
  if (preg_match('/p/i',$tfixed)) { $t[0] = $t[0] + 12; }
  if ($t[0] > 23) {$t[0] = 12; }
  if (preg_match('/^12.*a/i',$tfixed)) { $t[0] = 0; }
  if ($t[0] < '10') {$t[0] = sprintf("%02d",$t[0]); } // leading zero on hour.
  $t2 = join(':',$t); // put time back to gether;
  $t2 = preg_replace('/[^\d\:]/is','',$t2); // strip out the am/pm if any
  $Debug .= "<!-- MB_fixupTime in='$intime' tfixed='$tfixed' out='$t2' -->\n";
  return($t2);
  	
} // end MB_fixupTime

#-------------------------------------------------------------------------------------
# MB support function - MB_setDateTimes
#-------------------------------------------------------------------------------------

function MB_setDateTimes ($indate,$intime,$MDYformat=true) {
// returns: $date_year,$date_month,$date_day,$time_hour,$time_minute,$date_month,$monthname,$dayname
  global $Debug;
  $Debug .= "<!-- MB_setDateTimes date='$indate' time=$intime' MDY=$MDYformat -->\n";
  
  $MBtime = strtotime("$indate $intime");
   
  $MBtime = date('Y m d H i F l',$MBtime);
  $Debug .= "<!-- MB_setDateTimes MBtime='$MBtime' values set -->\n";
  if(isset($_REQUEST['debug'])) {echo $Debug; } 
  return(explode(' ',$MBtime)); // results returned in array for list() assignment
  	
} // end MB_setDateTimes

#-------------------------------------------------------------------------------------
# MB support function - MB_deg2dir - Convert wind direction degrees to cardinal name
#-------------------------------------------------------------------------------------

function MB_deg2dir ($degrees) {
   // figure out a text value for compass direction
// Given the wind direction, return the text label
// for that value.  16 point compass
   $winddir = $degrees;
   if ($winddir == "--") { return($winddir); }

  if (!isset($winddir)) {
    return "---";
  }
  if (!is_numeric($winddir)) {
	return($winddir);
  }
  $windlabel = array ("N","NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S",
	 "SSW","SW", "WSW", "W", "WNW", "NW", "NNW");
  $dir = $windlabel[ fmod((($winddir + 11) / 22.5),16) ];
  return($dir);

} // end function MB_deg2dir


#-------------------------------------------------------------------------------------
# MB support function - MB_beaufortNumber
#-------------------------------------------------------------------------------------

function MB_beaufortNumber ($inWind,$usedunit) {
   global $Debug;
   
   $rawwind = $inWind;
// first convert all winds to knots
   if(strpos($inWind,',') !== false) {
	   $rawwind = preg_replace('|,|','.',$inWind);
   }
   $WINDkts = 0.0;
   if       (preg_match('/kts|knot/i',$usedunit)) {
	   $WINDkts = $rawwind * 1.0;
   } elseif (preg_match('/mph/i',$usedunit)) {
	   $WINDkts = $rawwind * 0.8689762;
   } elseif (preg_match('/mps|m\/s/i',$usedunit)) {
	   $WINDkts = $rawwind * 1.94384449;
   } elseif  (preg_match('/kmh|km\/h/i',$usedunit)) {
	   $WINDkts = $rawwind * 0.539956803;
   } else {
	   $Debug .= "<!-- MB_beaufortNumber .. unknown input unit '$usedunit' for wind=$rawwind -->\n";
	   $WINDkts = $rawwind * 1.0;
   }

// return a number for the beaufort scale based on wind in knots
  if ($WINDkts < 1 ) {return(0); }
  if ($WINDkts < 4 ) {return(1); }
  if ($WINDkts < 7 ) {return(2); }
  if ($WINDkts < 11 ) {return(3); }
  if ($WINDkts < 17 ) {return(4); }
  if ($WINDkts < 22 ) {return(5); }
  if ($WINDkts < 28 ) {return(6); }
  if ($WINDkts < 34 ) {return(7); }
  if ($WINDkts < 41 ) {return(8); }
  if ($WINDkts < 48 ) {return(9); }
  if ($WINDkts < 56 ) {return(10); }
  if ($WINDkts < 64 ) {return(11); }
  if ($WINDkts >= 64 ) {return(12); }
  return("0");
} // end MB_beaufortNumber

#-------------------------------------------------------------------------------------
# MB support function - MB_beaufortText
#-------------------------------------------------------------------------------------

function MB_beaufortText ($beaufortnumber) {

  $B = array( /* Beaufort 0 to 12 in English */
   "Calm", "Light air", "Light breeze", "Gentle breeze", "Moderate breeze", "Fresh breeze",
   "Strong breeze", "Near gale", "Gale", "Strong gale", "Storm",
   "Violent storm", "Hurricane"
  );

  if(isset($B[$beaufortnumber])) {
	return $B[$beaufortnumber];
  } else {
    return "Unknown $beaufortnumber Bft";
  }
	
	
} // end MB_beaufortText

#-------------------------------------------------------------------------------------
# MB support function - MB_setFeelslike
#-------------------------------------------------------------------------------------


function MB_setFeelslike ($temp,$windchill,$heatindex,$tempUOM) {
global $Debug;
// establish the feelslike temperature and return a word describing how it feels

$HeatWords = array(
 'Unknown', 'Extreme Heat Danger', 'Heat Danger', 'Extreme Heat Caution', 'Extremely Hot', 'Uncomfortably Hot',
 'Hot', 'Warm', 'Comfortable', 'Cool', 'Cold', 'Uncomfortably Cold', 'Very Cold', 'Extreme Cold' );

// first convert all temperatures to Centigrade if need be
  $TC = $temp;
  $WC = $windchill;
  $HC = $heatindex;
  
  if(strpos($TC,',') !== false) {
	$TC = preg_replace('|,|','.',$temp);
	$WC = preg_replace('|,|','.',$windchill);
	$HC = preg_replace('|,|','.',$heatindex);
  }
  
  if (preg_match('|F|i',$tempUOM))  { // convert F to C if need be
	 $TC = sprintf("%01.1f",round(($TC-32.0) / 1.8,1));
	 $WC = sprintf("%01.1f",round(($WC-32.0) / 1.8,1));
	 $HC = sprintf("%01.1f",round(($HC-32.0) / 1.8,1));
  }
 
 // Feelslike
 
  if ($TC <= 16.0 ) {
	$feelslike = $WC; //use WindChill
  } elseif ($TC >=27.0) {
	$feelslike = $HC; //use HeatIndex
  } else {
	$feelslike = $TC;   // use temperature
  }

  if (preg_match('|F|i',$tempUOM))  { // convert C back to F if need be
	$feelslike = (1.8 * $feelslike) + 32.0;
  }
  $feelslike = round($feelslike,0);

// determine the 'heat color word' to use  
 $hcWord = $HeatWords[0];
 $hcFound = false;
 if ($TC > 32 and $HC > 29) {
	if ($HC > 54 and ! $hcFound) { $hcWord = $HeatWords[1]; $hcFound = true;}
	if ($HC > 45 and ! $hcFound) { $hcWord = $HeatWords[2]; $hcFound = true; }
	if ($HC > 39 and ! $hcFound) { $hcWord = $HeatWords[4]; $hcFound = true; }
	if ($HC > 29 and ! $hcFound) { $hcWord = $HeatWords[6]; $hcFound = true; }
 } elseif ($WC < 16 ) {
	if ($WC < -18 and ! $hcFound) { $hcWord = $HeatWords[13]; $hcFound = true; }
	if ($WC < -9 and ! $hcFound)  { $hcWord = $HeatWords[12]; $hcFound = true; }
	if ($WC < -1 and ! $hcFound)  { $hcWord = $HeatWords[11]; $hcFound = true; }
	if ($WC < 8 and ! $hcFound)   { $hcWord = $HeatWords[10]; $hcFound = true; }
	if ($WC < 16 and ! $hcFound)  { $hcWord = $HeatWords[9]; $hcFound = true; }
 } elseif ($WC >= 16 and $TC <= 32) {
	if ($TC <= 26 and ! $hcFound) { $hcWord = $HeatWords[8]; $hcFound = true; }
	if ($TC <= 32 and ! $hcFound) { $hcWord = $HeatWords[7]; $hcFound = true; }
 }

 if(isset($_REQUEST['debug'])) {
  echo "<!-- MB_setFeelslike input T,WC,HI,U='$temp,$windchill,$heatindex,$tempUOM' cnvt T,WC,HI='$TC,$WC,$HC' feelslike=$feelslike hcWord=$hcWord -->\n";
 }

 return(array($feelslike,$hcWord));
	
} // end of MB_setFeelslike

#-------------------------------------------------------------------------------------
# MB support function - MB_CBI - Chandler Burning Index
#-------------------------------------------------------------------------------------

function MB_CBI($inTemp,$inTempUOM,$inHumidity) {
	// thanks to Chris from sloweather.com for the CBI calculation script
	// modified by Ken True for template usage
	
	preg_match('/([\d\.\,\+\-]+)/',$inTemp,$t); // strip non-numeric from inTemp if any
	$ctemp = $t[1];
	if(strpos($ctemp,',') !== false) {
		$ctemp = preg_replace('|,|','.',$ctemp);
	}
	if(!preg_match('|C|i',$inTempUOM)) {
	  $ctemp = ($ctemp-32.0) / 1.8; // convert from Fahrenheit	
	}
	preg_match('/([\d\.\,\+\-]+)/',$inHumidity,$t); // strip non-numeric from inHumidity if any
	$rh = $t[1];
	if(strpos($rh,',') !== false) {
		$rh = preg_replace('|,|','.',$rh);
	}

	// Start Index Calcs
	
	// Chandler Index
	$cbi = (((110 - 1.373 * $rh) - 0.54 * (10.20 - $ctemp)) * (124 * pow(10,-0.0142 * $rh) ))/60;
	// CBI = (((110 - 1.373*RH) - 0.54 * (10.20 - T)) * (124 * 10**(-0.0142*RH)))/60
	
	//Sort out the Chandler Index
	$cbi = round($cbi,1);
	if ($cbi > "97.5") {
		$cbitxt = "EXTREME";
		$cbiimg= "fdl_extreme.gif";
	
	} elseif ($cbi >="90") {
		$cbitxt = "VERY HIGH";
		$cbiimg= "fdl_vhigh.gif";
	
	} elseif ($cbi >= "75") {
		$cbitxt = "HIGH";
		$cbiimg= "fdl_high.gif";
	
	} elseif ($cbi >= "50") {
		$cbitxt = "MODERATE";
		$cbiimg= "fdl_moderate.gif";
	
	} else {
		$cbitxt="LOW";
		$cbiimg= "fdl_low.gif";
	}
	 $data = array($cbi,$cbitxt,$cbiimg);
	 return $data;
	 
} // end MB_CBI

#-------------------------------------------------------------------------------------
# MB support function - MB_get_barotrend_text
#-------------------------------------------------------------------------------------

function MB_get_barotrend_text($rawpress,$usedunit='hPa') {
  global $Debug;
// routine from Anole's wxsticker PHP (adapted)  
//   Barometric Trend(3 hour)

// Change Rates
// Rapidly: =.06" inHg; 1.5 mm Hg; 2 hPa; 2 mb
// Slowly: =.02" inHg; 0.5 mm Hg; 0.7 hPa; 0.7 mb

// 5 Arrow Positions:
// Rising Rapidly
// Rising Slowly
// Steady
// Falling Slowly
// Falling Rapidly

// Page 52 of the PDF Manual
// http://www.davisnet.com/product_documents/weather/manuals/07395.234-VP2_Manual.pdf

// first convert to hPa for comparisons
	 if (preg_match('/hPa|mb/i',$usedunit)) {
		$btrend = sprintf("%02.1f",round($rawpress * 1.0,1)); // leave in hPa
	 } elseif (preg_match('/mm/i',$usedunit)) {
	   $btrend = sprintf("%02.1f",round($rawpress * 1.333224,1)); 
	 } else { // convert from inHg
		$btrend = sprintf("%02.1f",round($rawpress  * 33.86388158,1));
	 }

   // figure out a text value for barometric pressure trend
   (float)$baromtrend = $btrend;
//   settype($baromtrend, "float");
   switch (TRUE) {
      case (($baromtrend >= -0.6) and ($baromtrend <= 0.6)):
        $baromtrendwords = "Steady";
      break;
      case (($baromtrend > 0.6) and ($baromtrend < 2.0)):
        $baromtrendwords = "Rising Slowly";
      break;
      case ($baromtrend >= 2.0):
        $baromtrendwords = "Rising Rapidly";
      break;
      case (($baromtrend < -0.6) and ($baromtrend > -2.0)):
        $baromtrendwords = "Falling Slowly";
      break;
      case ($baromtrend <= -2.0):
        $baromtrendwords = "Falling Rapidly";
      break;
   } // end switch
   $Debug .= "<!-- MB_get_barotrend_text in=$rawpress $usedunit change out=$btrend hPa change [$baromtrend] ($baromtrendwords) -->\n";
  return($baromtrendwords);
}

#-------------------------------------------------------------------------------------
# end of MB support functions
#-------------------------------------------------------------------------------------

?>