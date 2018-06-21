<?php
#-------------------------------------------------------------------------------------
# Program: conds.php
# Purpose: receive GET mode conditions from Meteobridge HTTP Push Services
#    and save for MBrealtime.txt
#    to use in ajaxMBwx.js script for AJAX Conditions updates.
# Author: Ken True - webmaster@saratoga-weather.org
#-------------------------------------------------------------------------------------
// Version 1.00 - 08-Mar-2013 - initial release
// Version 1.01 - 09-Mar-2013 - fixed handling for missing solar and UV sensors
// Version 1.02 - 11-Mar-2013 - additional mbsystem- sensor values processed
// Version 1.03 - 17-Mar-2013 - added ability to make SteelSeries MBrealtimegauges.txt file
// Version 1.04 - 21-Mar-2013 - fixed rain unit conversion bug
// Version 1.05 - 04-Jun-2013 - added tag for Steel Series gauges support
$Version = "conds.php Version 1.05 - 04-Jun-2013";

// settings ------------------------------
$gaugeFile = "./MBrealtimegauges.txt"; // relative path/name of realtimegauges file
$uomTemp = '&deg;C';   // ='&deg;C', ='&deg;F'
$uomBaro = ' hPa';    // =' hPa', =' mb', =' inHg'
$uomWind = ' m/s';     // =' km/h', =' kts', =' m/s', =' mph'
$uomRain = ' mm';      // =' mm', =' in'
$WDdateMDY = true;     // =true  dates are 'month/day/year'
//                     // =false dates are 'day/month/year'
$ourTZ = "America/Los_Angeles";  //NOTE: this *MUST* be set correctly to
// translate UTC times to your LOCAL time for the displays.
$timeOnlyFormat = 'H:i';          // Euro format hh:mm  (hh=00..23);

// ---------------------------------------
// end of settings -- no further changes to the source are needed below
$doDebug = false;
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

$cacheFileDir = './';                     // default cache file directory
$cacheName = "MBconditions.txt";           // locally cached conditions string from Meteobridge

if(file_exists('Settings.php')) { include_once('Settings.php'); }

// overrides from Settings.php if available
global $SITE;
if(isset($SITE['cacheFileDir']))     { $cacheFileDir = $SITE['cacheFileDir']; }
$outFileName = $cacheFileDir.$cacheName;
if(isset($SITE['realtimefile']))     { $outFileName = $SITE['realtimefile']; }

$Data = array();

if( isset($_GET['d']) ) {
	
  if($doDebug) {
	$fp = fopen('./MBrealtime-args.txt', "w");
	if ($fp) {
	  $write = fputs($fp, 'd='.trim($_GET['d']));
	  fclose($fp);
	}
  }
  
  $Data = explode(',', trim($_GET['d']) );

  /*
Field	Cumulus	MB Realtime
0	date format=dd/mm/yy	[DD]/[MM]/[YYYY]
1	 timehhmmss	[hh]:[mm]:[ss]
2	 temp	[th0temp-act]
3	 hum	[th0hum-act]
4	 dew	[th0dew-act]
5	 wspeed	[wind0avgwind-act]
6	 wlatest	[wind0wind-act]
7	 bearing	[wind0dir-act]
8	 rrate	[rain0rate-act]
9	 rfall	[rain0total-daysum]
10	 press	[thb0seapress-act]
11	 currentwdir	[wind0dir-act]
12	 beaufortnumber	[wind0wind-act=bft.0]
13	 windunit	m/s
14	 tempunitnodeg	C
15	 pressunit	hPa
16	 rainunit	mm
17	 windrun	--
18	 presstrendval	[thb0seapress-val60:--]
19	 rmonth	[rain0total-monthsum]
20	 ryear	[rain0total-yearsum]
21	 rfallY	[rain0total-ydaysum]
22	 intemp	[thb0temp-act]
23	 inhum	[thb0hum-act]
24	 wchill	[wind0chill-act]
25	 temptrendval	[th0temp-val60:--]
26	 tempTH	[th0temp-dmax]
27	 TtempTH	[th0temp-dmaxtime]
28	 tempTL	[th0temp-dmin]
29	 TtempTL	[th0temp-dmintime]
30	 windTM	[wind0avgwind-dmax]
31	 TwindTM	[wind0avgwind-dmaxtime]
32	 wgustTM	[wind0wind-dmax]
33	 TwgustTM	[wind0wind-dmaxtime]
34	 pressTH	[thb0seapress-dmax]
35	 TpressTH	[thb0seapress-dmaxtime]
36	 pressTL	[thb0seapress-dmin]
37	 TpressTL	[thb0seapress-dmintime]
38	 version	[mbsystem-swversion:--]
39	 build	[mbsystem-buildnum:--]
40	 wgust	[wind0wind-max10]
41	 heatindex	--
42	 humidex	--
43	 UV	[uv0index-act:--]
44	 ET	--
45	 SolarRad	[sol0rad-act:--]
46	 avgbearing	[wind0dir-avg10:--]
47	 rhour	[rain0total-sum60]
48	 forecastnumber	--
49	 isdaylight	[mbsystem-daynightflag:--]
50	 SensorContactLost	--
51	 wdir	[wind0dir-avg10:--]
52	 cloudbasevalue	--
53	 cloudbaseunit	m
54	 apptemp	--
55	 SunshineHours	[mbsystem-daylength:--]
56	 CurrentSolarMax	--
57	 IsSunny	--
--- added
58   UVTH [uv0index-dmax:--]
  
  */ 
  
// Fill in missing data were possible

# Humidex
  $Data[42] = conds_calcHumidex ($Data[2],$Data[3],'C','C');

# Heat Index
  $Data[41] = conds_calcHeatIndex ($Data[2],$Data[3],'C','C');
  
# Times from timestamps
  $Data[27] = conds_timeOnly($Data[27]);  
  $Data[29] = conds_timeOnly($Data[29]);  
  $Data[31] = conds_timeOnly($Data[31]);  
  $Data[33] = conds_timeOnly($Data[33]);  
  $Data[35] = conds_timeOnly($Data[35]);  
  $Data[37] = conds_timeOnly($Data[37]);
  
# 1 hour changes if available
  # Barotrend in hPa
  if($Data[18] !== '--') { $Data[18] = sprintf("%01.1F",$Data[10]-$Data[18]); }  
  # Temperature trend in C
  if($Data[25] !== '--') { $Data[25] = sprintf("%01.1F",$Data[2]-$Data[25]); }  
  
# wind direction
  $Data[11] = conds_deg2dir ($Data[11]);
  $Data[51] = conds_deg2dir ($Data[51]);
  
# day/night flag
  $td = trim($Data[49]);
  if($td <> '--') {
	 $Data[49] = ($Data[49] == 'D')?'1':'0'; 
  }
  
# version fixer
 if(strlen($Data[38]) < 1) { $Data[38] = '--'; }
  
# now clean it up and repack to save the realtime file
  
  foreach ($Data as $i => $val) {
	  // replace missing values with '--' for results.
	  if(strlen($val) < 1 ) { $Data[$i] = '--'; }
	  $Data[$i] = preg_replace('| |','_',$Data[$i]); // fix embedded blanks if any
  }
  
  $out = join(' ',$Data); 
  $MBrealtimeStatus = '';
  
  $fp = fopen($outFileName, "w");
  if ($fp) {
	$write = fputs($fp, $out);
	fclose($fp);
  } else {
	$perms = fileperms($outFileName);
	$permsdecoded = conds_decode_permissions($perms);
	$permsoctal = substr(sprintf('%o', $perms), -4);
	$MBrealtimeStatus =  "<p>Failure: unable to write $outFileName permissions=$permsdecoded [$permsoctal] </p>\n";
  }

} else {
	print "<p>Failure: No weather conditions provided.</p>\n";
	return;
}

#-------------------------------------------------------------------------------------
# now create the SteelSeries JSON file using realtime+MBtags.php data
#-------------------------------------------------------------------------------------


if(isset($SITE['WXtags']) and file_exists($SITE['WXtags'])) {
	include_once($SITE['WXtags']);
} else {
	return;
}

if(isset($SITE['realtimegauges'])) {$gaugeFile = $SITE['realtimegauges'];}

// re-get the raw-data array in units of C,m/s,hPa,mm for updates.
// the MBtags will already be in station-prefered units so use directly.

$Data = explode(',', trim($_GET['d']) );

$Status = "// $Version \n";
/* needed output (with Cumulus tags cited below) is:

{"date":"<#date format=hh:nn>",
"temp":"<#temp>",
"tempTL":"<#tempTL>",
"tempTH":"<#tempTH>",
"intemp":"<#intemp>",
"dew":"<#dew>",
"dewpointTL":"<#dewpointTL>",
"dewpointTH":"<#dewpointTH>",
"apptemp":"<#apptemp>",
"apptempTL":"<#apptempTL>",
"apptempTH":"<#apptempTH>",
"wchill":"<#wchill>",
"wchillTL":"<#wchillTL>",
"heatindex":"<#heatindex>",
"heatindexTH":"<#heatindexTH>",
"humidex":"<#humidex>",
"wlatest":"<#wlatest>",
"wspeed":"<#wspeed>",
"wgust":"<#wgust>",
"wgustTM":"<#wgustTM>",
"bearing":"<#bearing>",
"avgbearing":"<#avgbearing>",
"press":"<#press>",
"pressTL":"<#pressTL>",
"pressTH":"<#pressTH>",
"pressL":"<#pressL>",
"pressH":"<#pressH>",
"rfall":"<#rfall>",
"rrate":"<#rrate>",
"rrateTM":"<#rrateTM>",
"hum":"<#hum>",
"humTL":"<#humTL>",
"humTH":"<#humTH>",
"inhum":"<#inhum>",
"SensorContactLost":"<#SensorContactLost>",
"forecast":"<#forecastenc>",
"tempunit":"<#tempunitnodeg>",
"windunit":"<#windunit>",
"pressunit":"<#pressunit>",
"rainunit":"<#rainunit>",
"temptrend":"<#temptrend>",
"TtempTL":"<#TtempTL>",
"TtempTH":"<#TtempTH>",
"TdewpointTL":"<#TdewpointTL>",
"TdewpointTH":"<#TdewpointTH>",
"TapptempTL":"<#TapptempTL>",
"TapptempTH":"<#TapptempTH>",
"TwchillTL":"<#TwchillTL>",
"TheatindexTH":"<#TheatindexTH>",
"TrrateTM":"<#TrrateTM>",
"ThourlyrainTH":"<#ThourlyrainTH>",
"LastRainTipISO":"<#LastRainTipISO>",
"hourlyrainTH":"<#hourlyrainTH>",
"ThumTL":"<#ThumTL>",
"ThumTH":"<#ThumTH>",
"TpressTL":"<#TpressTL>",
"TpressTH":"<#TpressTH>",
"presstrendval":"<#presstrendval>",
"Tbeaufort":"<#Tbeaufort>",
"TwgustTM":"<#TwgustTM>",
"windTM":"<#windTM>",
"bearingTM":"<#bearingTM>",
"timeUTC":"<#timeUTC format=yyyy,m,d,h,m,s>",
"BearingRangeFrom10":"<#BearingRangeFrom10>",
"BearingRangeTo10":"<#BearingRangeTo10>",
"UV":"<#UV>",
"UVTH":<#UVTH>",
"SolarRad":"<#SolarRad>",
"CurrentSolarMax":"<#CurrentSolarMax>",
"domwinddir":"<#domwinddir>",
"WindRoseData":[<#WindRoseData>],
"version":"<#version>",
"build":"<#build>",
"ver":"9"}
*/

# Set timezone in PHP5/PHP4 manner
if (!function_exists('date_default_timezone_set')) {
  putenv("TZ=" . $ourTZ);
  } else {
  date_default_timezone_set("$ourTZ");
}

$sTempUOM = $SITE['uomTemp'];
$sWindUOM = $SITE['uomWind'];
$sBaroUOM = $SITE['uomBaro'];
$sRainUOM = $SITE['uomRain'];

// Note: $Data[] has info in C,m/s,hPa,mm irrespective of site selections.

# Humidex
  $Data[42] = conds_calcHumidex ($Data[2],$Data[3],$uomTemp,$sTempUOM);

# Heat Index
  $Data[41] = conds_calcHeatIndex ($Data[2],$Data[3],$uomTemp,$sTempUOM);
  
# Times from timestamps
  $Data[27] = conds_timeOnly($Data[27]);  
  $Data[29] = conds_timeOnly($Data[29]);  
  $Data[31] = conds_timeOnly($Data[31]);  
  $Data[33] = conds_timeOnly($Data[33]);  
  $Data[35] = conds_timeOnly($Data[35]);  
  $Data[37] = conds_timeOnly($Data[37]);
  
# 1 hour changes if available
  # input Barotrend in hPa
  if($Data[18] !== '--') { 
    $Data[18] = conds_convertBaro($Data[10]-$Data[18],$uomBaro,$sBaroUOM); 
  }  
  # Temperature trend in C
  if($Data[25] !== '--') { 
    $Data[25] = conds_convertTempRate($Data[2]-$Data[25],$uomTemp,$sTempUOM);
  }  
  
# wind direction
//  $Data[11] = conds_deg2dir ($Data[11]);
//  $Data[51] = conds_deg2dir ($Data[51]);


// Assemble the JSON data array for output
$JSONdata = array();

$JSONdata["date"] 	= $Data[1]; // (this is really 'time') WD Sample= '3:39 PM'
$JSONdata["dateFormat"] = ($WDdateMDY)?'m/d/y':'d/m/y'; // WD Sample= 'm/d/y'
$JSONdata["temp"] 	= conds_convertTemp($Data[2],$uomTemp,$sTempUOM); // WD Sample= '64.7°F'
$JSONdata["tempTL"] = conds_convertTemp($Data[28],$uomTemp,$sTempUOM); // WD Sample= '34.4°F'
$JSONdata["tempTH"] = conds_convertTemp($Data[26],$uomTemp,$sTempUOM); // WD Sample= '64.7°F'
$JSONdata["intemp"] = conds_convertTemp($Data[22],$uomTemp,$sTempUOM); // WD Sample= '73.2'
$JSONdata["dew"] 	= conds_convertTemp($Data[4],$uomTemp,$sTempUOM); // WD Sample= '34.9°F'
$JSONdata["dewpointTL"] = $WX['th0dew-dmin']; // WD Sample= '30.1 °F'
$JSONdata["dewpointTH"] = $WX['th0dew-dmax']; // WD Sample= '40.8 °F'
// Note: Meteobridge does not provide apparent temperature .. we substitute Humidex
$JSONdata["apptemp"] =  $Data[42]; // WD Sample= '63.4'
$JSONdata["apptempTL"] = $Data[42]; // WD Sample= '32.2'
$JSONdata["apptempTH"] = $Data[42]; // WD Sample= '72.3'
$JSONdata["wchill"] = conds_convertTemp($Data[24],$uomTemp,$sTempUOM); // WD Sample= '64.7°F'
$JSONdata["wchillTL"] = $WX['wind0chill-dmin']; // WD Sample= '34.4 °F'
$JSONdata["heatindex"] = $Data[41]; // WD Sample= '64.7°F'
$JSONdata["heatindexTH"] = $Data[41]; // WD Sample= '64.7 °F'
$JSONdata["humidex"] = $Data[42]; // WD Sample= '61.6°F'
$JSONdata["wlatest"] = conds_convertWind($Data[6],$uomWind,$sWindUOM); // WD Sample= '0.0 mph'
$JSONdata["wspeed"] = conds_convertWind($Data[5],$uomWind,$sWindUOM); // WD Sample= '0.4 mph'
$JSONdata["wgust"] = conds_convertWind($Data[40],$uomWind,$sWindUOM); // WD Sample= '7.0 mph'
$JSONdata["wgustTM"] = conds_convertWind($Data[32],$uomWind,$sWindUOM); // WD Sample= '11.0 mph'
$JSONdata["bearing"] = round($Data[11]); // WD Sample= '292 °'
$JSONdata["avgbearing"] = round($Data[46]); // WD Sample= '311°'
$JSONdata["press"] = conds_convertBaro($Data[10],$uomBaro,$sBaroUOM); // WD Sample= '30.138 in.'
$JSONdata["pressTL"] = conds_convertBaro($Data[36],$uomBaro,$sBaroUOM); // WD Sample= '30.124 in.'
$JSONdata["pressTH"] = conds_convertBaro($Data[34],$uomBaro,$sBaroUOM); // WD Sample= '30.229 in.'
$JSONdata["pressL"] = $WX['thb0seapress-amin']; // WD Sample= '26.001'
$JSONdata["pressH"] = $WX['thb0seapress-amax']; // WD Sample= '30.569'
$JSONdata["rfall"] = conds_convertRain($Data[9],$uomRain,$sRainUOM); // WD Sample= '0.00 in.'
$JSONdata["rrate"] = conds_convertRain($Data[8],$uomRain,$sRainUOM); // WD Sample= '0.00'
$JSONdata["rrateTM"] = $WX['rain0rate-dmax']; // WD Sample= '0.000'
$JSONdata["hum"] = $Data[3]; // WD Sample= '33'
$JSONdata["humTL"] = $WX['th0hum-dmin']; // WD Sample= '31'
$JSONdata["humTH"] = $WX['th0hum-dmax']; // WD Sample= '86'
$JSONdata["inhum"] = $Data[23]; // WD Sample= '32'
$JSONdata["SensorContactLost"] = '0'; // WD Sample= '0'
$JSONdata["forecast"] = 'Conditions updated: '.$Data[1]; // WD Sample= 'increasing clouds and warmer. precipitation possible within 12 to 24 hrs. windy.'
$JSONdata["tempunit"] = preg_match('|C|i',$sTempUOM)?'C':'F'; // WD Sample= 'F'
$JSONdata["windunit"] = trim($sWindUOM); // WD Sample= 'mph'
$JSONdata["pressunit"] = trim($sBaroUOM); // WD Sample= 'inHg'
$JSONdata["rainunit"] = trim($sRainUOM); // WD Sample= 'in'
$JSONdata["temptrend"] = conds_convertTempRate($Data[25],$uomTemp,$sTempUOM); // WD Sample= '+1.0 °F/last hr'
$JSONdata["TtempTL"] = $Data[29]; // WD Sample= '7:40 AM'
$JSONdata["TtempTH"] = $Data[27]; // WD Sample= '3:19 PM'
$JSONdata["TdewpointTL"] = conds_timeOnly($WX['th0dew-dmintime']); // WD Sample= '7:40 AM'
$JSONdata["TdewpointTH"] = conds_timeOnly($WX['th0dew-dmaxtime']); // WD Sample= '9:16 AM'
$JSONdata["TapptempTL"] = '00:00'; // WD Sample= '7:13 AM'
$JSONdata["TapptempTH"] = '00:00'; // WD Sample= '1:14 PM'
$JSONdata["TwchillTL"] = conds_timeOnly($WX['wind0chill-dmintime']); // WD Sample= '3:19 PM'
$JSONdata["TheatindexTH"] = 'n/a'; // WD Sample= '3:19 PM'
$JSONdata["TrrateTM"] = '00:00'; // WD Sample= '00:00 AM'
$JSONdata["ThourlyrainTH"] = '00:00'; // conds_convertRain($Data[47],$uomRain,$sRainUOM); // WD Sample= ''
$JSONdata["LastRainTipISO"] = 'n/a'; // WD Sample= '1/12/2013 4:12 AM'
$JSONdata["hourlyrainTH"] = '0.0'; // WD Sample= '0.000'
$JSONdata["ThumTL"] = conds_timeOnly($WX['th0hum-dmintime']); // WD Sample= '3:22 PM'
$JSONdata["ThumTH"] = conds_timeOnly($WX['th0hum-dmaxtime']);; // WD Sample= '8:05 AM'
$JSONdata["TpressTL"] = $Data[37]; // WD Sample= '2:18 PM'
$JSONdata["TpressTH"] = $Data[35]; // WD Sample= '10:09 AM'
$JSONdata["presstrendval"] = $Data[18]; // WD Sample= '-0.019 in. '
$JSONdata["Tbeaufort"] = 'F'.round($Data[12]); // WD Sample= '3'
$JSONdata["TwgustTM"] = $Data[33]; // WD Sample= '2:19 PM'
$JSONdata["windTM"] = conds_convertWind($Data[32],$uomWind,$sWindUOM); // WD Sample= '6.2 mph'
$JSONdata["bearingTM"] = round($Data[46]); // WD Sample= '315'
$fixedTimestamp = strtotime(conds_fixupDate($Data[0],false).' '.$JSONdata["date"]);
$JSONdata["timeUTC"] = gmdate('Y,m,d,H,i,s',$fixedTimestamp); // WD Sample= '2013,01,20,23,39,59'
$JSONdata["BearingRangeFrom10"] = '359'; // WD Sample= '289°'
$JSONdata["BearingRangeTo10"] = '0'; // WD Sample= '6°'
$JSONdata["UV"] = $Data[43]; // WD Sample= '0.7'
$JSONdata["UVTH"] = $Data[58]; // WD Sample= '7.7'
$JSONdata["SolarRad"] = $Data[45]; // WD Sample= '267'
$JSONdata["CurrentSolarMax"] = $Data[45]; // WD Sample= '238'
$JSONdata["SolarTM"] = $WX['sol0rad-dmax']; // WD Sample= '560'
$JSONdata["domwinddir"] = conds_deg2dir($Data[46]); // WD Sample= 'Northwesterly'
$JSONdata["WindRoseData"] = '[0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0]';
// Note: Meteobridge does not collect/publish this windrose data 
// a WD Sample='[22.0,23.0,7.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,3.0,233.0,139.0]'
// $uomWindRun = (preg_match('|C|',$uomTemp))?'km/h':'mph';
$JSONdata["windrun"] = '0.0'; // new in ver=9 -- Meteobridge does not collect windrun
$JSONdata["version"] = $Data[38]; // WD Sample= '10.37R'
$JSONdata["build"] = $Data[39]; // WD Sample= '45'
$JSONdata["ver"] = "10"; // constant);

// JSON assembly done.  Output the JSON file+status
if($doDebug) {
	print "<pre>\n";
  } else {
	header("Content-Type: text/plain; charset=ISO-8859-1");
  }
$out = '';

$out .= '{';
$comma = '';
foreach ($JSONdata as $key => $val) {
	$out .= $comma;
	$out .= "\"$key\":\"$val\"";
	$comma = ",\n";
}
$out .= "}\n";
if($doDebug) { $out .= $Status; }
$MBgaugesStatus = '';
  $fp = fopen($gaugeFile, "w");
  if ($fp) {
	$write = fputs($fp, $out);
	fclose($fp);
  } else {
	$perms = fileperms($gaugeFile);
	$permsdecoded = conds_decode_permissions($perms);
	$permsoctal = substr(sprintf('%o', $perms), -4);
	$MBgaugesStatus =  "<p>Failure: unable to write $gaugeFile permissions=$permsdecoded [$permsoctal] </p>\n";
  }
  
  if(strlen($MBrealtimeStatus) + strlen($MBgaugesStatus) > 0 ) {
	  print $MBrealtimeStatus."\n".$MBrealtimeStatus."\n";
  } else {
	  print "<p>Success.</p>\n";
  }

return;

#-------------------------------------------------------------------------------------
# MB support function - conds_timeOnly
#-------------------------------------------------------------------------------------

function conds_timeOnly ($indatetime) {
// Return HH:MM (24hr time format)
// expecting
// 0....+....1....+
// 20110622061003
// yyyymmddhhMMss
  return(substr($indatetime,8,2).':'.substr($indatetime,10,2));
}

#-------------------------------------------------------------------------------------
# MB support function - conds_deg2dir - Convert wind direction degrees to cardinal name
#-------------------------------------------------------------------------------------

function conds_deg2dir ($degrees) {
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

} // end function conds_deg2dir

#-------------------------------------------------------------------------------------
# VWS support function - getBeaufort
#-------------------------------------------------------------------------------------

function getBeaufort ($rawwind) {
   global $Debug;
  
// first convert m/s in MBrealtime to knots

   $WINDkts = round($rawwind * 1.94384);

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
} // end getBeaufortNumber

#-------------------------------------------------------------------------------------
# support function - conds_fixupDate
#-------------------------------------------------------------------------------------

function conds_fixupDate ($indate,$WDdateMDY) {
  // input: mm/dd/yyyy or dd/mm/yyyy format 
  global $Status;
  $d = explode('/',$indate);      // expect ##/##/## form
  if(!isset($d[2])) {$d = explode('-',$indate); } // try ##-##-#### form instead
  if ($d[2] > 70 and $d[2] <= 99) {$d[2] += 1900;} // 2 digit dates 70-99 are 1970-1999
  if ($d[2] < 99) {$d[2] += 2000; } // 2 digit dates (left) are assumed 20xx dates.
  if ($WDdateMDY) {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[0],$d[1]); //  M/D/YYYY -> YYYY-MM-DD
  } else {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[1],$d[0]); // D/M/YYYY -> YYYY-MM-DD
  }
  $Status .= "// fixupDate in='$indate' out='$new' \n";
  return ($new);
  	
} // end conds_fixupDate

#-------------------------------------------------------------------------------------
# utility functions to handle conversions 
# MB support function - conds_convertTemp
#-------------------------------------------------------------------------------------

function conds_convertTemp ($rawtemp,$inunit,$useunit) {
	 $dpTemp = 1;
	 if(!is_numeric($rawtemp)) { return($rawtemp); } // no conversions for missing values
	 if (preg_match('|F|i',$inunit)) {
		$tempC = ($rawtemp-32.0) / 1.8;
	 } else {
		$tempC = $rawtemp * 1.0;
	 }

	 # Temperature now in C
	 
	 if (preg_match('|F|i',$useunit))  { // convert C to F
		return( sprintf("%01.${dpTemp}f",round((1.8 * $tempC) + 32.0,$dpTemp)));
	 } else {  // leave as C
		return (sprintf("%01.${dpTemp}f", round($tempC*1.0,$dpTemp)));
	 }
}
#-------------------------------------------------------------------------------------
# MB support function - conds_convertTempRate
#-------------------------------------------------------------------------------------

function conds_convertTempRate ($rawtemp,$inunit,$useunit) { // convert temperature RATE of change
	 $dpTemp = 1;
	 if(!is_numeric($rawtemp)) { return($rawtemp); } // no conversions for missing values
	 if (preg_match('|F|i',$inunit))  { // convert F to C rate
		return( sprintf("%01.${dpTemp}f",round($rawtemp / 1.8,$dpTemp)));
	 } else {  // leave as C
		return (sprintf("%01.${dpTemp}f", round($rawtemp*1.0,$dpTemp)));
	 }
}

#-------------------------------------------------------------------------------------
# MB support function - conds_conds_convertWind
#-------------------------------------------------------------------------------------
function conds_convertWind  ( $rawwind,$inunit,$useunit) {
   global $Status;
  
   $using = '';
   $WIND = '';
   $dpWind = 1;
   
// first convert all winds to knots
   if($rawwind == '--') {return ($rawwind); }
   
   $WINDkts = 0.0;
   if       (preg_match('/kts/i',$inunit)) {
	   $WINDkts = $rawwind * 1.0;
   } elseif (preg_match('/mph/i',$inunit)) {
	   $WINDkts = $rawwind * 0.8689762;
   } elseif (preg_match('/mps|m\/s/i',$inunit)) {
	   $WINDkts = $rawwind * 1.94384449;
   } elseif  (preg_match('/kmh|km\/h/i',$inunit)) {
	   $WINDkts = $rawwind * 0.539956803;
   } else {
	   $WINDkts = $rawwind * 1.0;
   }
   
 // now $WINDkts is wind speed in Knots  convert to desired form and decimals
 
   if (preg_match('/kmh|km\/h|km/i',$useunit)) { // output KMH
        $WIND = sprintf($dpWind?"%02.${dpWind}f":"%d",round($WINDkts * 1.85200,$dpWind));
        $using = 'KMH';
   }
   if (preg_match('/mph/i',$useunit)) {
        $WIND = sprintf($dpWind?"%02.${dpWind}f":"%d",round($WINDkts * 1.15077945,$dpWind));
        $using = 'MPH';
   }

   if (preg_match('/mps|m\/s/i',$useunit)) {
        $WIND = sprintf($dpWind?"%02.${dpWind}f":"%d",round($WINDkts * 0.514444444,$dpWind));
        $using = 'M/S';
   }

   if (preg_match('/kts|kn|kt|knots/i',$useunit)) {
        $WIND = sprintf($dpWind?"%02.${dpWind}f":"%d",round($WINDkts * 1.0,$dpWind));
        $using = 'KTS';
   }

 
   $Status .= "// convertWind($rawwind m/s) [$WINDkts kts] to '$WIND' $using \n";
   return($WIND);
}
#-------------------------------------------------------------------------------------
# MB support function - conds_convertBaro
#-------------------------------------------------------------------------------------
function conds_convertBaro ( $rawpress,$inunit,$useunit ) {
     $dpBaro = 1; // for hPa,mb,mm
	 $BARO = 0.0;
	 if(preg_match('|inHg|i',$inunit)) {
		 $BARO = $rawpress  * 33.86388158; // convert to hPa
	 } else {
		 $BARO = $rawpress * 1.0;
	 }
	   
	 if (preg_match('/hPa|mb/i',$useunit)) {
		 
		return (sprintf("%02.${dpBaro}f",round($BARO * 1.0,$dpBaro))); // leave in hPa
		
	 } elseif (preg_match('/mm/i',$useunit)) {
		 
	   return (sprintf("%02.${dpBaro}f",round($BARO * 0.750061561303,$dpBaro)));
	    
	 } else {
		$dpBaro = 2;
		return (sprintf("%02.${dpBaro}f",round($BARO / 33.86388158,$dpBaro))); // inHg
	 }
}
#-------------------------------------------------------------------------------------
# MB support function - conds_calcconvertRain
#-------------------------------------------------------------------------------------
function conds_convertRain ( $rawrain,$inunit,$useunit ) {
   $dpRain = 1; // for mm
   if($rawrain == '--') {return($rawrain); }
   
   if(preg_match('|in|i',$inunit)) { // convert in->mm
     $RAIN = $rawrain * 25.3970886;
   } else {
	 $RAIN = $rawrain * 1.0;
   }

   # rain now in MM
   
   if (preg_match('/in/i',$useunit))  {
	   $dpRain = 2;
	  return (sprintf("%02.${dpRain}f",round($RAIN / 25.3970886,$dpRain)));
   } else {
	  return (sprintf("%02.${dpRain}f",round($RAIN * 1.0,$dpRain))); // leave in mm
   }
}


#-------------------------------------------------------------------------------------
# MB support function - conds_calcHumidex
#-------------------------------------------------------------------------------------

function conds_calcHumidex ($temp,$humidity,$inunit,$useunit) {
// Calculate Humidex from temperature and humidity
// Source of calculation: http://www.physlink.com/reference/weather.cfm	
  global $Debug;
  if(preg_match('|F|i',$inunit)) {
    $T= conds_convertTemp($temp,'F','C');
  } else {
	$T = $temp;
  }
  $H = $humidity;
  
  $t=7.5*$T/(237.7+$T);
  $et=pow(10,$t);
  $e=6.112*$et*($H/100);
  $humidex=$T+(5/9)*($e-10);
  if ($humidex < $T) {
	 $humidex=$T;
     $Debug .= " set to T, ";
  }
  if(preg_match('|F|i',$useunit)) {
     # convert to F
     $humidex = sprintf("%01.1f",round((1.8 * $humidex) + 32.0,1));	  
  }
  $humidex = round($humidex,1);
  return($humidex);	
}

#-------------------------------------------------------------------------------------
# MB support function - conds_calcHeatIndex
#-------------------------------------------------------------------------------------

function conds_calcHeatIndex ($temp,$humidity,$inunit,$useunit) {
// Calculate Heat Index from temperature and humidity
// Source of calculation: http://woody.cowpi.com/phpscripts/getwx.php.txt	
  global $Debug;
  if(preg_match('|C|i',$inunit)) {
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
	  $hiF = round($tempF,1);
	  $hiC = round(($hiF - 32) / 1.8,1);
  }
  $Debug .= "<!-- conds_calcHeatIndex temp=$temp ($tempF F) C, rh=$rh calc=$hiF F, $hiC C ";
  if(preg_match('|F|i',$useunit)) {
     $heatIndex = $hiF;	  
  } else {
	 $heatIndex = $hiC;
  }
  $Debug .= " heatIndex=$heatIndex $useunit -->\n"; 
  return($heatIndex);	
}

#---------------------------------------------------------  
# decode unix file permissions
#---------------------------------------------------------  

function conds_decode_permissions($perms) {

  if (($perms & 0xC000) == 0xC000) {
	  // Socket
	  $info = 's';
  } elseif (($perms & 0xA000) == 0xA000) {
	  // Symbolic Link
	  $info = 'l';
  } elseif (($perms & 0x8000) == 0x8000) {
	  // Regular
	  $info = '-';
  } elseif (($perms & 0x6000) == 0x6000) {
	  // Block special
	  $info = 'b';
  } elseif (($perms & 0x4000) == 0x4000) {
	  // Directory
	  $info = 'd';
  } elseif (($perms & 0x2000) == 0x2000) {
	  // Character special
	  $info = 'c';
  } elseif (($perms & 0x1000) == 0x1000) {
	  // FIFO pipe
	  $info = 'p';
  } else {
	  // Unknown
	  $info = 'u';
  }
  
  // Owner
  $info .= (($perms & 0x0100) ? 'r' : '-');
  $info .= (($perms & 0x0080) ? 'w' : '-');
  $info .= (($perms & 0x0040) ?
			  (($perms & 0x0800) ? 's' : 'x' ) :
			  (($perms & 0x0800) ? 'S' : '-'));
  
  // Group
  $info .= (($perms & 0x0020) ? 'r' : '-');
  $info .= (($perms & 0x0010) ? 'w' : '-');
  $info .= (($perms & 0x0008) ?
			  (($perms & 0x0400) ? 's' : 'x' ) :
			  (($perms & 0x0400) ? 'S' : '-'));
  
  // World
  $info .= (($perms & 0x0004) ? 'r' : '-');
  $info .= (($perms & 0x0002) ? 'w' : '-');
  $info .= (($perms & 0x0001) ?
			  (($perms & 0x0200) ? 't' : 'x' ) :
			  (($perms & 0x0200) ? 'T' : '-'));
  
  return $info;
}
?>