<?php
// Author: Ken True - webmaster-weather.org
// gen-MBtags.php 
// Purpose: read the MBtags-template.txt file from meteobridge and generate the template file for use with
//          meteobridge to substitute weather values for MB tags in MBtags.php
//
// Version 1.00 - 08-Mar-2013 - Initial release
// Version 1.01 - 09-Mar-2013 - added handling for stations with no solar and/or no UV sensors
// Version 1.02 - 17-Mar-2013 - added additional variables from updated Meteobridge software
// Version 1.03 - 19-Aug-2013 - added Davis forecast info  for Meteobridge 1.8(2198)+
// --------------------------------------------------------------------------
// allow viewing of generated source
$Version = 'gen-MBtags.php - V1.03 - 18-Aug-2013';
$WXsoftware = 'MB'; // do NOT change this
$defsFile = $WXsoftware . '-defs.php'; // do NOT change this .. name of definitions file

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

$inFile = 'MBtags-template.txt';

if(!file_exists($inFile)) {
	print "<h3>Program: $Version</h3>\n";
	print "<h1>Error: file '$inFile' not found.</h1>\n";
	print "<p>Make sure '$inFile' is in this same directory.</p>";
	return;
}
   $uomTemp = ' &deg;C';
   $uomWind = ' m/s';
   $uomBaro = ' hPa';
   $uomRain = ' mm';

if(file_exists("Settings.php")) { 
  include_once("Settings.php");
  $uomTemp = $SITE['uomTemp'];
  $uomWind = $SITE['uomWind'];
  $uomBaro = $SITE['uomBaro'];
  $uomRain = $SITE['uomRain'];
}

$showComments = true;
if(isset($_REQUEST['comments'])) {
	$showComments = (strtolower($_REQUEST['comments']) == 'yes')?true:false;
}

$Sensors = array(
  'th0temp|temp|outdoor temperature',
  'th0hum|.0|outdoor humidity',
  'th0dew|temp|outdoor dewpoint',
  'thb0temp|temp|indoor temperature',
  'thb0hum|.0|indoor humidity',
  'thb0dew|temp|indoor dewpoint',
  'thb0press|baro|station pressure',
  'thb0seapress|baro|sealevel pressure',
  'wind0wind|wind|windspeed',
  'wind0avgwind|wind|average windspeed',
  'wind0dir|.0|wind direction',
  'wind0chill|temp|outdoor wind chill temperature',
  'rain0rate|rain|rain rate',
  'rain0total|rain|rain',
  'uv0index|.1|uv index',
  'sol0rad|.0|solar rad',
);
$UOM = array();
/*
$SITE['uomTemp'] = '&deg;F';  // ='&deg;C', ='&deg;F'
$SITE['uomBaro'] = ' inHg';    // =' hPa', =' mb', =' inHg'
$SITE['uomWind'] = ' mph';   // =' km/h', =' kts', =' m/s', =' mph'
$SITE['uomRain'] = ' in';     // =' mm', =' in'
*/
$UOM['temp'] = (preg_match('|F|i',$uomTemp))?'=F.1':'.1';
$UOM['baro'] = (preg_match('|inHg|i',$uomBaro))?'=inHg.2':'.1'; 
if(preg_match('|km|i',$uomWind)) {
	$UOM['wind'] = '=kmh.1';
} elseif (preg_match('|kts|i',$uomWind)) {
	$UOM['wind'] = '=kn.1';
} elseif (preg_match('|mph|i',$uomWind)) {
	$UOM['wind'] = '=mph.1';
} else {
	$UOM['wind'] = '.1'; // default for m/s
}

$UOM['rain'] = (preg_match('|in|i',$uomRain))?'=in.2':'.1'; 

$SensorUOM = array();

foreach ($Sensors as $i => $s) {
	list($varname,$type,$desc) = explode('|',$s);
	if(isset($UOM[$type])) { 
	  $SensorUOM[$varname] = $UOM[$type]; 
	} else {
	  $SensorUOM[$varname] = $type;
	}
}

$rawrecs = file($inFile);
header("Content-type: text/plain");
print_start();
#
# generate tags for some builtin variables that don't match tag names
#
$Builtins = array(
  'date' => '[YYYY]-[MM]-[DD]|local date',
  'time' => '[hh]:[mm]:[ss]|local time',
  'dateUTC' => '[UYYYY]-[UMM]-[UDD]|UTC date',
  'timeUTC' => '[Uhh]:[Umm]:[Uss]|UTCtime',
  'uomTemp' => "$uomTemp|UOM temperature",
  'uomWind' => "$uomWind|UOM wind",
  'uomBaro' => "$uomBaro|UOM barometer",
  'uomRain' => "$uomRain|UOM rain",
  'mbsystem-swversion' => '[mbsystem-swversion:--]|Meteobridge version string (example: "1.1")',
  'mbsystem-buildnum' => '[mbsystem-buildnum:--]|build number as integer (example: 1673)',
  'mbsystem-platform' => '[mbsystem-platform:--]|string that specifies hw platform (example: "TL-MR3020")',
  'mbsystem-language' => '[mbsystem-language:--]|language used on Meteobridge web interface (example: "English")',
  'mbsystem-timezone' => '[mbsystem-timezone:--]|defined timezone (example: "Europe/Berlin")',
  'mbsystem-latitude' => '[mbsystem-latitude:--]|latitude as float (example: 53.875120)',
  'mbsystem-longitude' => '[mbsystem-longitude:--]|longitude as float (example: 9.885357)',
  'mbsystem-lunarage' => '[mbsystem-lunarage:--]|days passes since new moon as integer (example: 28)',
  'mbsystem-lunarpercent' => '[mbsystem-lunarpercent:--]|lunarphase given as percentage from 0% (new moon) to 100% (full moon)',
  'mbsystem-lunarsegment' => '[mbsystem-lunarsegment:--]|lunarphase segment as integer (0 = new moon, 1-3 = growing moon: quarter, half, three quarters, 4 = full moon, 5-7 = shrinking moon: three quarter, half, quarter)',
  'mbsystem-daylength' => '[mbsystem-daylength:--]|length of day (example: "11:28")',
  'mbsystem-civildaylength' => '[mbsystem-civildaylength:--]|alternative method for daylength computation (example: "12:38")',
  'mbsystem-nauticaldaylength' => '[mbsystem-nauticaldaylength:--]|alternative method for daylength computation (example: "14:00")',
  'mbsystem-sunrise' => '[mbsystem-sunrise:--]|time of sunrise in local time. Can be converted to UTC by applying "=utc" to the variable (example: "06:47", resp. "05:47")',
  'mbsystem-sunset' => '[mbsystem-sunset:--]|time of sunset in local time. Can be converted to UTC by applying "=utc" to the variable (example: "18:15", resp. "17:15")',
  'mbsystem-civilsunrise' => '[mbsystem-civilsunrise:--]|alternative computation for sunrise.',
  'mbsystem-civilsunset' => '[mbsystem-civilsunset:--]|alternative computation for sunset.',
  'mbsystem-nauticalsunrise' => '[mbsystem-nauticalsunrise:--]|alternative computation for sunrise.',
  'mbsystem-nauticalsunset' => '[mbsystem-nauticalsunset:--]|alternative alternative computation for sunset..',
  'mbsystem-daynightflag' => '[mbsystem-daynightflag:--]|returns "D" when there is daylight, otherwise "N".',
  'mbsystem-moonrise' => '[mbsystem-moonrise:--]|time of moonrise in local time. Please notice that not every day has a moonrise time, therefore, variable can be non-existent on certain days (example: "05:46", resp. "04:46")',
  'mbsystem-moonset' => '[mbsystem-moonset:--]|time of moonset in local time. Please notice that not every day has a moonset time, therefore, variable can be non-existent on certain days.',
  'forecast-rule' => '[forecast-rule:--]|Davis forecast rule number',
  'forecast-text' => '[forecast-text:]|Davis forecast reports in English',
);

foreach ($Builtins as $name => $v) {
	list($var,$desc) = explode('|',$v.'|');
	print "$name|$var|// $desc:|:\n";
}

foreach ($rawrecs as $rec) {
/*
input:
Parameters (Description)
wview Tag	Variable
wvar	$STNNAME$	Station name
wvar	$CURDATE_MDY$	Date (ex. January 1, 2004) Date

output:
STNNAME|$STNNAME$|// Station name:|:
CURDATE_MDY|$CURDATE_MDY$|// Date (ex. January 1, 2004) Date:|:

*/
  preg_match('|^wvar\t(\S+)\t(.*)$|i',$rec,$matches);
  if(!isset($matches[1])) {continue;}
  $ourname = $matches[1];
  $descr = '';
  if(isset($matches[2])) {$descr = trim($matches[2]);}
  list($sensor,$selector) = explode('-',$ourname);
  if(isset($SensorUOM[$sensor])) { 
    $converter = $SensorUOM[$sensor];
  } else {
	$converter = '';
  }
  if(preg_match('|time|i',$selector)) { $converter = ''; }
  $varname = '['.$ourname.$converter.':--]';
  $comment = '';
  if($showComments) {
	  $comment = '|// '.$descr;
  }
  print "$ourname|$varname$comment:|:\n";
	
	
}
print_end();

// end of mainline
function print_start() {
	global $Version,$WXsoftware,$defsFile,$inFile;
print '<?php
/*
 File: MBtags.php

 Purpose: load Meteobridge variables into a $WX[] array for use with the Canada/World/USA template sets
 NOTE: this file must be processed by Meteobridge as a template file and uploaded to the website
   as MBtags.php using the Meteobridge extended Push Services configuration.

 Author: Ken True - webmaster@saratoga-weather.org

';
print " (created by $Version)\n";
print "\n These tags generated on ".gmdate('Y-m-d H:m:s T',time())."\n";
print "   From $inFile updated ".gmdate('Y-m-d H:m:s T',filemtime($inFile))."\n\n";

print '*/
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
' . 
"\$WXsoftware = '$WXsoftware';  
\$defsFile = '$defsFile';  // filename with \$varnames = \$WX['$WXsoftware-varnames']; equivalents\n " .
'
$rawdatalines = \'
';
}

function print_end() {
global $WXsoftware, $defsFile;	
print '\'; // END_OF_RAW_DATA_LINES;

// end of generation script

// put data in  array
//
$WX = array();
global $WX;
$WXComment = array();
$data = explode(":|:",$rawdatalines);
$nscanned = 0;
foreach ($data as $v => $line) {
  list($vname,$vval,$vcomment) = explode("|",trim($line).\'|||\');
  if ($vname <> "" and strpos($vval,\'$\') === false) {
    $WX[$vname] = trim($vval);
    if($vcomment <> "") { $WXComment[$vname] = trim($vcomment); }
  }
  $nscanned++;
}
if(isset($_REQUEST[\'debug\'])) {
  print "<!-- loaded $nscanned $WXsoftware \$WX[] entries -->\n";
}

if (isset($_REQUEST["sce"]) and strtolower($_REQUEST["sce"]) == "dump" ) {

  print "<pre>\n";
  print "// \$WX[] array size = $nscanned entries.\n";
  foreach ($WX as $key => $val) {
	  $t =  "\$WX[\'$key\'] = \'$val\';";
	  if(isset($WXComment[$key])) {$t .=  " $WXComment[$key]"; }
	  print "$t\n";
  }
  print "</pre>\n";

}
if(file_exists("'.$defsFile.'")) { include_once("'.$defsFile.'"); }
?>';
}