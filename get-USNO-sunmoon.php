<?php

if (isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view') {

  // --self downloader --

  $filenameReal = __FILE__;
  $download_size = filesize($filenameReal);
  header('Pragma: public');
  header('Cache-Control: private');
  header('Cache-Control: no-cache, must-revalidate');
  header("Content-type: text/plain,charset=ISO-8859-1");
  header("Accept-Ranges: bytes");
  header("Content-Length: $download_size");
  header('Connection: close');
  readfile($filenameReal);
  exit;
}

function getUSNOsunmoon()
{
  /*
  Function: get-USNO-sunmoon()
  Purpose: fetch and cache the sun/moon data for one day from the US Naval Observatory,
  using a GET request to http://api.usno.navy.mil/rstt/oneday?
  parse the returned JSON, and return data in the an array compatible with the V1.x
  of the script.
  Calling sequence:
  $array = getUSNOsunmoon();
  Returned array contents like:
		$Data['beginciviltwilight'] => 06:52
		$Data['beginciviltwilightdate'] => 01/18/2011
		$Data['sunrise'] => 07:20
		$Data['sunrisedate'] => 01/18/2011
		$Data['suntransit'] => 12:18
		$Data['suntransitdate'] => 01/18/2011
		$Data['sunset'] => 17:17
		$Data['sunsetdate'] => 01/18/2011
		$Data['endciviltwilight'] => 17:46
		$Data['endciviltwilightdate'] => 01/18/2011
		$Data['moonriseprior'] => 15:13
		$Data['moonrisepriordate'] => 01/17/2011
		$Data['moonset'] => 06:16
		$Data['moonsetdate'] => 01/18/2011
		$Data['moonrise'] => 16:21
		$Data['moonrisedate'] => 01/18/2011
		$Data['moontransit'] => 23:45
		$Data['moontransitdate'] => 01/18/2011
		$Data['moonsetnext'] => 07:02
		$Data['moonsetnextdate'] => 01/19/2011
		$Data['moonphase'] => Waxing Gibbous
		$Data['illumination'] => 98%
		$Data['hoursofpossibledaylight'] => 09:57
		$Data['databy'] => USNO|calculated
  Note: the moonriseprior and moonrisenext may not always appear in the data from the USNO
  Author: Ken True - webmaster@saratoga-weather.org
  */

	//  Version 1.00 - 18-Jan-2011 - initial release
	//  Version 1.01 - 23-Mar-2011 - added code for missing moonrise/moonset due to prior/next day times
	//  Version 1.02 - 03-Dec-2011 - fixed moonset date if for following day
	//  Version 2.00 - 29-Jul-2015 - changes to use new JSON API call for aa.usno.navy.mil
	//  Version 2.01 - 29-Jul-2015 - added conditionals for moon-prior/moon-next vars (may not be in JSON)
	//  Version 2.02 - 31-Jul-2015 - fixed issue with missing JSON fracillum/curphase data
	//  Version 2.03 - 01-Aug-2015 - added illum. value for missing JSON fracillum data
	//  Version 2.04 - 11-Nov-2015 - use specific local date request instead of relying on date=today
	//  Version 3.00 - 11-Oct-2017 - use cURL for fetch, provide bypass/calculations for api.usno.navy.mil outages
	//  Version 3.01 - 20-Oct-2017 - fix date format for calculated date functions

  $Version = 'get-USNO-sunmoon.php - Version 3.01 - 20-Oct-2017';

  // -----------local settings-------------------

  $ourTZ = "America/Los_Angeles"; //NOTE: this *MUST* be set correctly to

  // translate UTC times to your LOCAL time for the displays.
  //  set to station latitude/longitude (decimal degrees)

  $myLat = 37.27153397; //North=positive, South=negative decimal degrees
  $myLong = - 122.02274323; //East=positive, West=negative decimal degrees

  // The above settings are for saratoga-weather.org location

  $myCity = 'Saratoga'; // my city name
  $useMDY = true; // true=use mm/dd/yyyy for dates, false=use dd/mm/yyyy for dates
  $cacheFileDir = './'; // default cache file directory
  $cacheName = "USNO-moondata.txt"; // used to store the file so we don't have to fetch from USNO website
  $refetchSeconds = 3600; // refetch every nnnn seconds 3600=1 hour
  $useUSNO = true; // =true; use USNO data, =false; use computed values only (not as accurate)

  // -----------end local settings --------------
  // overrides from Settings.php if available

  global $SITE;
  if (isset($SITE['latitude']))     { $myLat = $SITE['latitude']; }
  if (isset($SITE['longitude']))    { $myLong = $SITE['longitude']; }
  if (isset($SITE['tz']))           { $ourTZ = $SITE['tz']; }
  if (isset($SITE['location']))     { $myCity = $SITE['location']; }
  if (isset($SITE['WDdateMDY']))    { $useMDY = $SITE['WDdateMDY']; }
  if (isset($SITE['cacheFileDir'])) { $cacheFileDir = $SITE['cacheFileDir']; }
  if (isset($SITE['useUSNO']))      { $useUSNO = $SITE['useUSNO']; }
  // end of overrides from Settings.php

  global $Debug, $doDebug;
  $Debug = "<!-- $Version -->\n";
  $Data = array();

  // Set timezone in PHP5/PHP4 manner

  if (!function_exists('date_default_timezone_set')) {
    putenv("TZ=" . $ourTZ);
  }
  else {
    date_default_timezone_set("$ourTZ");
  }

  if (isset($_REQUEST['force']) or isset($_REQUEST['cache'])) {
    $refetchSeconds = 1;
  }

  if (file_exists($cacheName) and date("Ymd", filemtime($cacheName)) !== date("Ymd")) {
    $Debug.= "<!-- local date changed v.s. cache date .. reloading from USNO -->\n";
    $refetchSeconds = 1;
  }

  $Debug.= "<!-- refetch seconds=$refetchSeconds -->\n";
  $doDebug = false;
  if (isset($_REQUEST['debug'])) {
    $doDebug = true;
  }

  if (isset($_REQUEST['calc'])) {
    $useUSNO = ($_REQUEST['calc'] == 'y') ? false : true;
  }

  $myTZOffset = date('Z') / 3600; // difference of our time from UTC in hours
  $lclToday = date("m/d/Y");
  $USNOUrl = "http://api.usno.navy.mil/rstt/oneday?date=$lclToday&coords=$myLat,$myLong&tz=$myTZOffset";
  $cacheName = $cacheFileDir . $cacheName;
  if ($useUSNO) { // do the USNO fetch/process

    // ---------------- start of decode USNO JSON output ----------------
    // either load the cached html page or fetch and cache a new html page

    if (file_exists($cacheName) and filemtime($cacheName) + $refetchSeconds > time()) {
      $Debug.= "<!-- using Cached version of $cacheName -->\n";
      $html = implode('', file($cacheName));
    }
    else {
      $Debug.= "<!-- loading $cacheName from $USNOUrl -->\n";
      $html = get_sunmoon_fetchUrlWithoutHanging($USNOUrl);
      if (preg_match('|200 OK|', $html)) {
        $fp = fopen($cacheName, "w");
        if ($fp) {
          $write = fputs($fp, $html);
          fclose($fp);
        }
        else {
          $Debug.= "<!-- unable to write cache file $cacheName -->\n";
        }
      }
      else {
        $Debug.= "<!-- Error loading from USNO API, cache not saved -->\n";
      }

      $Debug.= "<!-- loading finished. -->\n";
    }

    $i = strpos($html, "\r\n\r\n");
    $headers = substr($html, 0, $i - 1);
    $content = substr($html, $i + 4);
    $Debug.= "<!-- processing JSON entries for Moon data -->\n";
    $rawJSON = $content; // kludge.. our unchunking removed this
    if ($doDebug) {
      $Debug.= "<!-- rawJSON size is " . strlen($rawJSON) . " bytes -->\n";
      $Debug.= "<!-- rawJSON is '" . $rawJSON . "' -->\n";
    }

    //  $rawJSON = WU_prepareJSON($rawJSON);

    $MoonJSON = json_decode($rawJSON, true); // get as associative array
    $Debug.= get_sunmoon_decode_JSON_error();
    if ($doDebug) {
      $Debug.= "<!-- JSON\n" . print_r($MoonJSON, true) . " -->\n";
    }

    /*
    USNO returns info like:
    {
    "error":false,
    "apiversion":"1.0",
    "year":2015,
    "month":7,
    "day":28,"datechanged":false,
    "lon":-122.02,
    "lat":37.67,
    "tz":-7,
    "sundata":[
    {"phen":"BC", "time":"05:39"},
    {"phen":"R", "time":"06:08"},
    {"phen":"U", "time":"13:15"},
    {"phen":"S", "time":"20:20"},
    {"phen":"EC", "time":"20:50"}

    ],
    "moondata":[
    {"phen":"S", "time":"03:25"},
    {"phen":"R", "time":"17:57"},
    {"phen":"U", "time":"23:09"} ]
    , "nextmoondata":[{"phen":"S","time":"04:22"}]
    , "closestphase":{
    "phase":"Full Moon",
    "date":"July 31, 2015",
    "time":"03:43"
    }

    , "fracillum":"91%"
    , "curphase":"Waxing Gibbous" }

    Which we decode as:
    <!-- JSON
    Array
    (
			[error] =>
			[apiversion] => 1.0
			[year] => 2015
			[month] => 7
			[day] => 28
			[datechanged] =>
			[lon] => 0
			[lat] => 0
			[tz] => 0
			[sundata] => Array
			(
			[0] => Array
			(
			[phen] => BC
			[time] => 05:41
			)
			[1] => Array
			(
			[phen] => R
			[time] => 06:03
			)
			[2] => Array
			(
			[phen] => U
			[time] => 12:07
			)
			[3] => Array
			(
			[phen] => S
			[time] => 18:10
			)
			[4] => Array
			(
			[phen] => EC
			[time] => 18:32
			)
			)
			[moondata] => Array
			(
			[0] => Array
			(
			[phen] => S
			[time] => 03:00
			)
			[1] => Array
			(
			[phen] => R
			[time] => 15:29
			)
			[2] => Array
			(
			[phen] => U
			[time] => 21:42
			)
			)
			[nextmoondata] => Array
			(
			[0] => Array
			(
			[phen] => S
			[time] => 03:55
			)
			)
			[closestphase] => Array
			(
			[phase] => Full Moon
			[date] => July 31, 2015
			[time] => 10:43
			)
			[fracillum] => 89%
			[curphase] => Waxing Gibbous
    )
    -->
    */

    // now slice the page for the main times for the sun and moon

    $phen_name = array(
      'BC' => 'Begin civil twilight',
      'R' => 'Rise',
      'U' => 'Upper Transit',
      'S' => 'Set',
      'EC' => 'End civil twilight',
      'L' => 'Lower Transit (above the horizon)',
      '**' => 'object continuously above the horizon',
      '--' => 'object continuously below the horizon',
      '^^' => 'object continuously above the twilight limit',
      '~~' => 'object continuously below the twilight limit',
    );
    $phen_lookup = array(
      'BC' => 'beginciviltwilight',
      'R' => 'rise',
      'U' => 'transit',
      'S' => 'set',
      'EC' => 'endciviltwilight',
      'L' => 'Lower Transit (above the horizon)',
      '**' => 'object continuously above the horizon',
      '--' => 'object continuously below the horizon',
      '^^' => 'object continuously above the twilight limit',
      '~~' => 'object continuously below the twilight limit',
    );
    /*  Targeted output is:
    USNOdata returns
    Array
    (
			[beginciviltwilight] => 05:40
			[beginciviltwilightdate] => 07/28/2015
			[sunrise] => 06:09
			[sunrisedate] => 07/28/2015
			[suntransit] => 13:15
			[suntransitdate] => 07/28/2015
			[sunset] => 20:19
			[sunsetdate] => 07/28/2015
			[endciviltwilight] => 20:48
			[endciviltwilightdate] => 07/28/2015
			[moonriseprior] => 17:00
			[moonrisepriordate] => 07/27/2015
			[moonset] => 03:26
			[moonsetdate] => 07/28/2015
			[moonrise] => 17:56
			[moonrisedate] => 07/28/2015
			[moontransit] => 23:09
			[moontransitdate] => 07/28/2015
			[moonsetnext] => 04:23
			[moonsetnextdate] => 07/29/2015
			[moonphase] => Waxing Gibbous
			[illumination] => 91%
			[hoursofpossibledaylight] => 14:10
    )
    */
    $Data = array();
    $useDateFormat = $useMDY ? "m/d/Y" : "d/m/Y";
    $dateprior = date($useDateFormat, strtotime("-1 day"));
    $datenow = date($useDateFormat);
    $datenext = date($useDateFormat, strtotime("+1 day"));
    $mtype = 'sun';
    foreach($MoonJSON['sundata'] as $n => $d) {
      $mt = $mtype . $phen_lookup[$d['phen']];
      if (preg_match('|civil|i', $mt)) {
        $mt = $phen_lookup[$d['phen']]; // no 'sun' in civil entries
      }

      $Data[$mt] = $d['time'];
      $Data[$mt . 'date'] = $datenow;
    }

    $mtype = 'moon';
    foreach($MoonJSON['moondata'] as $n => $d) {
      $mt = $mtype . $phen_lookup[$d['phen']];
      $Data[$mt] = $d['time'];
      $Data[$mt . 'date'] = $datenow;
    }

    $mtype = 'moon';
    if (isset($MoonJSON['nextmoondata'])) {
      foreach($MoonJSON['nextmoondata'] as $n => $d) {
        $mt = $mtype . $phen_lookup[$d['phen']] . 'next';
        $Data[$mt] = $d['time'];
        $Data[$mt . 'date'] = $datenext;
      }
    }

    $mtype = 'moon';
    if (isset($MoonJSON['prevmoondata'])) {
      foreach($MoonJSON['prevmoondata'] as $n => $d) {
        $mt = $mtype . $phen_lookup[$d['phen']] . 'prior';
        $Data[$mt] = $d['time'];
        $Data[$mt . 'date'] = $dateprior;
      }
    }

    if (isset($MoonJSON['curphase'])) {
      $Data['moonphase'] = $MoonJSON['curphase'];
    }

    if (!isset($Data['moonphase']) and isset($MoonJSON['closestphase']['phase'])) {
      $Data['moonphase'] = $MoonJSON['closestphase']['phase'];
      $Debug.= "<!-- note: 'curphase' not in JSON.  Used 'closestphase' instead. -->\n";
    }

    if (isset($MoonJSON['fracillum'])) {
      $Data['illumination'] = $MoonJSON['fracillum'];
    }
    elseif (isset($Data['moonphase'])) {
      if (preg_match('|full|i', $Data['moonphase'])) {
        $Data['illumination'] = '100%';
      }

      if (preg_match('|quarter|i', $Data['moonphase'])) {
        $Data['illumination'] = '50%';
      }

      if (preg_match('|new|i', $Data['moonphase'])) {
        $Data['illumination'] = '0%';
      }

      if (isset($Data['illumination'])) {
        $Debug.= "<!-- note: 'fracillum' not in JSON, 'illumination' set to '" . 
				$Data['illumination'] . "' based on moon phase of '" . 
				$Data['moonphase'] . "'. -->\n";
      }
    }
    else {
      $Debug.= "<!-- note: 'fracillum' not in JSON, no 'illumination' is available. -->\n";
    }

    if (isset($Data['sunrise']) and isset($Data['sunset'])) {
      $diff = strtotime($Data['sunset']) - strtotime($Data['sunrise']);
      $diffh = intval($diff / 3600); // hours
      $diffm = intval(($diff / 60) % 60);
      $Data['hoursofpossibledaylight'] = sprintf("%02d:%02d", $diffh, $diffm);
    }

    if (!isset($Data['moonrise']) and isset($Data['moonriseprior'])) {
      $Debug.= "<!-- moonrise missing.. using moonriseprior -->\n";
      $Data['moonrise'] = $Data['moonriseprior'];
      $Data['moonrisedate'] = $Data['moonrisepriordate'];
    }

    if (!isset($Data['moonset']) and isset($Data['moonsetnext'])) {
      $Debug.= "<!-- moonset missing.. using moonsetnext -->\n";
      $Data['moonset'] = $Data['moonsetnext'];
      $Data['moonsetdate'] = $Data['moonsetnextdate'];
    }

    $Data['databy'] = 'USNO';
    $Debug.= "<!-- USNOdata\n" . print_r($Data, true) . " -->\n";
    print $Debug;
    return ($Data);

    // ---------------- end of decode USNO JSON output ----------------

  }
  else { // do the compute lookup

    // ---------------- start of compute moon times instead of using USNO data ----------------

    $Debug.= "<!-- useUSNO=false .. doing computation for values instead of USNO fetch -->\n";

    // create an instance of the class, and use the current time

    $timeFormat = $useMDY ?"H:i m/d/Y":"H:i d/m/Y";
    $moon = new calcMoonPhase();
    $age = round($moon->age() , 1);
    $stage = $moon->phase() < 0.5 ? 'waxing' : 'waning';
    $Data['illumination'] = round($moon->illumination() * 100.0) . '%';
    $new_moon = gmdate($timeFormat, $moon->new_moon());
    $next_new_moon = gmdate($timeFormat, $moon->next_new_moon());
    $first_quarter = gmdate($timeFormat, $moon->first_quarter());
    $next_first_quarter = gmdate($timeFormat, $moon->next_first_quarter());
    $full_moon = gmdate($timeFormat, $moon->full_moon());
    $next_full_moon = gmdate($timeFormat, $moon->next_full_moon());
    $last_quarter = gmdate($timeFormat, $moon->last_quarter());
    $next_last_quarter = gmdate($timeFormat, $moon->next_last_quarter());
    $Data['moonphase'] = $moon->phase_name();
    list($Y, $M, $D) = explode(' ', date("Y n j"));
    $moonrs = calcMoon::calculateMoonTimes($M, $D, $Y, $myLat, $myLong);
    list($Data['moonrise'], $Data['moonrisedate']) = explode(" ", date($timeFormat, $moonrs->moonrise));
    list($Data['moonset'], $Data['moonsetdate']) = explode(" ", date($timeFormat, $moonrs->moonset));
    $SINFO = date_sun_info(time() , $myLat, $myLong);

    // print_r($SINFO);

    /*
    Array
    (
			[sunrise] => 1480345321
			[sunset] => 1480380672
			[transit] => 1480362996
			[civil_twilight_begin] => 1480343611
			[civil_twilight_end] => 1480382381
			[nautical_twilight_begin] => 1480341680
			[nautical_twilight_end] => 1480384313
			[astronomical_twilight_begin] => 1480339797
			[astronomical_twilight_end] => 1480386196
    )
    */
    list($Data['beginciviltwilight'], $Data['beginciviltwilightdate']) = 
		  explode(" ", date($timeFormat, $SINFO['civil_twilight_begin']));
    list($Data['sunrise'], $Data['sunrisedate']) = 
		  explode(" ", date($timeFormat, $SINFO['sunrise']));
    list($Data['suntransit'], $Data['suntransitdate']) = 
		  explode(" ", date($timeFormat, $SINFO['transit']));
    list($Data['sunset'], $Data['sunsetdate']) = 
		  explode(" ", date($timeFormat, $SINFO['sunset']));
    list($Data['endciviltwilight'], $Data['endciviltwilightdate']) = 
		  explode(" ", date($timeFormat, $SINFO['civil_twilight_end']));
    $Data['hoursofpossibledaylight'] = gmdate('H:i', $SINFO['sunset'] - $SINFO['sunrise']);
    $moonTimes = new calcMoonRiSet($myLat, $myLong, $ourTZ);
    $moonTimes->setDate(date("Y") , date("m") , date("d"));
    $moonTransit = $moonTimes->transit["timestamp"];
    $Debug.= "<!-- moonTransit " . print_r($moonTransit, true) . " -->\n";
    if ($moonTransit > 99999) {
      list($Data['moontransit'], $Data['moontransitdate']) = 
			  explode(" ", date($timeFormat, $moonTransit));
    }

    $Data['databy'] = 'calculated';
    $Debug.= "<!-- USNOdata\n" . print_r($Data, true) . " -->\n";
    print $Debug;
    return ($Data);

    // ---------------- end of compute moon times instead of using USNO data ----------------

  }
} // end of getUSNOsunmoon function

// --------- end of mainline function --------------
// get contents from one URL and return as string
// ---------------------------------------------------------------------------

function get_sunmoon_fetchUrlWithoutHanging($url, $useFopen = false)
{

  // get contents from one URL and return as string

  global $Debug, $needCookie;
  $overall_start = time();
  if (!$useFopen) {

    // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed

    $numberOfSeconds = 30;

    // Thanks to Curly from ricksturf.com for the cURL fetch functions

    $data = '';
    $domain = parse_url($url, PHP_URL_HOST);
    $theURL = str_replace('nocache', '?' . $overall_start, $url); // add cache-buster to URL if needed
    $Debug.= "<!-- curl fetching '$theURL' -->\n";
    $ch = curl_init(); // initialize a cURL session
    curl_setopt($ch, CURLOPT_URL, $theURL); // connect to provided URL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // don't verify peer certificate
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (get-USNO-sunmoon.php - saratoga-weather.org)');
    curl_setopt($ch, CURLOPT_HTTPHEADER, // request LD-JSON format
    array(
      "Accept: text/html,text/plain"
    ));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $numberOfSeconds); //  connection timeout
    curl_setopt($ch, CURLOPT_TIMEOUT, $numberOfSeconds); //  data timeout
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the data transfer
    curl_setopt($ch, CURLOPT_NOBODY, false); // set nobody
    curl_setopt($ch, CURLOPT_HEADER, true); // include header information

    //  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);              // follow Location: redirect
    //  curl_setopt($ch, CURLOPT_MAXREDIRS, 1);                      //   but only one time

    if (isset($needCookie[$domain])) {
      curl_setopt($ch, $needCookie[$domain]); // set the cookie for this request
      curl_setopt($ch, CURLOPT_COOKIESESSION, true); // and ignore prior cookies
      $Debug.= "<!-- cookie used '" . $needCookie[$domain] . "' for GET to $domain -->\n";
    }

    $data = curl_exec($ch); // execute session
    if (curl_error($ch) <> '') { // IF there is an error
      $Debug.= "<!-- curl Error: " . curl_error($ch) . " -->\n"; //  display error notice
    }

    $cinfo = curl_getinfo($ch); // get info on curl exec.
    /*
    curl info sample
    Array
    (
			[url] => http://saratoga-weather.net/clientraw.txt
			[content_type] => text/plain
			[http_code] => 200
			[header_size] => 266
			[request_size] => 141
			[filetime] => -1
			[ssl_verify_result] => 0
			[redirect_count] => 0
			[total_time] => 0.125
			[namelookup_time] => 0.016
			[connect_time] => 0.063
			[pretransfer_time] => 0.063
			[size_upload] => 0
			[size_download] => 758
			[speed_download] => 6064
			[speed_upload] => 0
			[download_content_length] => 758
			[upload_content_length] => -1
			[starttransfer_time] => 0.125
			[redirect_time] => 0
			[redirect_url] =>
			[primary_ip] => 74.208.149.102
			[certinfo] => Array
			(
			)
			[primary_port] => 80
			[local_ip] => 192.168.1.104
			[local_port] => 54156
    )
    */
    $Debug.= "<!-- HTTP stats: " . " RC=" . $cinfo['http_code'];
		if (isset($cinfo['primary_ip'])) {
			$Debug .= " dest=" . $cinfo['primary_ip'];
		}
		
    if (isset($cinfo['primary_port'])) {
      $Debug.= " port=" . $cinfo['primary_port'];
    }

    if (isset($cinfo['local_ip'])) {
      $Debug.= " (from sce=" . $cinfo['local_ip'] . ")";
    }

    $Debug.= "\n      Times:" . 
		  " dns=" . sprintf("%01.3f", round($cinfo['namelookup_time'], 3)) . 
		  " conn=" . sprintf("%01.3f", round($cinfo['connect_time'], 3)) . 
		  " pxfer=" . sprintf("%01.3f", round($cinfo['pretransfer_time'], 3));
    if ($cinfo['total_time'] - $cinfo['pretransfer_time'] > 0.0000) {
      $Debug.= " get=" . sprintf("%01.3f", round($cinfo['total_time'] - $cinfo['pretransfer_time'], 3));
    }

    $Debug.= " total=" . sprintf("%01.3f", round($cinfo['total_time'], 3)) . " secs -->\n";

    // $Debug .= "<!-- curl info\n".print_r($cinfo,true)." -->\n";

    curl_close($ch); // close the cURL session

    // $Debug .= "<!-- raw data\n".$data."\n -->\n";

    $i = strpos($data, "\r\n\r\n");
    $headers = substr($data, 0, $i);
    $content = substr($data, $i + 4);
    if ($cinfo['http_code'] <> '200') {
      $Debug.= "<!-- headers returned:\n" . $headers . "\n -->\n";
    }

    return $data; // return headers+contents
  }
  else {

    //   print "<!-- using file_get_contents function -->\n";

    $STRopts = array(
      'http' => array(
        'method' => "GET",
        'protocol_version' => 1.1,
        'header' => "Cache-Control: no-cache, must-revalidate\r\n" . 
					"Cache-control: max-age=0\r\n" . 
					"Connection: close\r\n" . 
					"User-agent: Mozilla/5.0 (get-USNO-sunmoon.php - saratoga-weather.org)\r\n" . 
					"Accept: text/html,text/plain\r\n"
      ) ,
      'https' => array(
        'method' => "GET",
        'protocol_version' => 1.1,
        'header' => "Cache-Control: no-cache, must-revalidate\r\n" .
					"Cache-control: max-age=0\r\n" .
					"Connection: close\r\n" .
					"User-agent: Mozilla/5.0 (get-USNO-sunmoon.php - saratoga-weather.org)\r\n" .
					"Accept: text/html,text/plain\r\n"
      )
    );
    $STRcontext = stream_context_create($STRopts);
    $T_start = get_sunmoon_fetch_microtime();
    $xml = file_get_contents($url, false, $STRcontext);
    $T_close = get_sunmoon_fetch_microtime();
    $headerarray = get_headers($url, 0);
    $theaders = join("\r\n", $headerarray);
    $xml = $theaders . "\r\n\r\n" . $xml;
    $ms_total = sprintf("%01.3f", round($T_close - $T_start, 3));
    $Debug.= "<!-- file_get_contents() stats: total=$ms_total secs -->\n";
    $Debug.= "<-- get_headers returns\n" . $theaders . "\n -->\n";

    //   print " file() stats: total=$ms_total secs.\n";

    $overall_end = time();
    $overall_elapsed = $overall_end - $overall_start;
    $Debug.= "<!-- fetch function elapsed= $overall_elapsed secs. -->\n";

    //   print "fetch function elapsed= $overall_elapsed secs.\n";

    return ($xml);
  }
} // end get_sunmoon_fetchUrlWithoutHanging

// ------------------------------------------------------------------

function get_sunmoon_fetch_microtime()
{
  list($usec, $sec) = explode(" ", microtime());
  return ((float)$usec + (float)$sec);
}

// ------------------------------------------------------------------

function get_sunmoon_decode_JSON_error()
{
  $Status = '';
  $Status.= "<!-- json_decode returns ";
  switch (json_last_error()) {
  case JSON_ERROR_NONE:
    $Status.= ' - No errors';
    break;

  case JSON_ERROR_DEPTH:
    $Status.= ' - Maximum stack depth exceeded';
    break;

  case JSON_ERROR_STATE_MISMATCH:
    $Status.= ' - Underflow or the modes mismatch';
    break;

  case JSON_ERROR_CTRL_CHAR:
    $Status.= ' - Unexpected control character found';
    break;

  case JSON_ERROR_SYNTAX:
    $Status.= ' - Syntax error, malformed JSON';
    break;

  case JSON_ERROR_UTF8:
    $Status.= ' - Malformed UTF-8 characters, possibly incorrectly encoded';
    break;

  default:
    $Status.= ' - Unknown error, json_last_error() returns \'' . json_last_error() . "'";
    break;
  }

  $Status.= " -->\n";
  return ($Status);
}

// ------------------------------------------------------------------
// Classes for moon calculations
// ------------------------------------------------------------------

/**
 * Moon phase calculation class
 * Adapted for PHP from Moontool for Windows (http://www.fourmilab.ch/moontoolw/)
 * by Samir Shah (http://rayofsolaris.net)
 * License: MIT
 * Adapted by K. True - Saratoga-weather.org 15-Aug-2017
 *
 */
class calcMoonPhase

{
  private $timestamp;
  private $phase;
  private $illum;
  private $age;
  private $dist;
  private $angdia;
  private $sundist;
  private $sunangdia;
  private $synmonth;
  private $quarters = null;
  function __construct($pdate = null)
  {
    if (is_null($pdate)) $pdate = time();
    /*  Astronomical constants  */
    $epoch = 2444238.5; // 1980 January 0.0
    /*  Constants defining the Sun's apparent orbit  */
    $elonge = 278.833540; // Ecliptic longitude of the Sun at epoch 1980.0
    $elongp = 282.596403; // Ecliptic longitude of the Sun at perigee
    $eccent = 0.016718; // Eccentricity of Earth's orbit
    $sunsmax = 1.495985e8; // Semi-major axis of Earth's orbit, km
    $sunangsiz = 0.533128; // Sun's angular size, degrees, at semi-major axis distance
    /*  Elements of the Moon's orbit, epoch 1980.0  */
    $mmlong = 64.975464; // Moon's mean longitude at the epoch
    $mmlongp = 349.383063; // Mean longitude of the perigee at the epoch
    $mlnode = 151.950429; // Mean longitude of the node at the epoch
    $minc = 5.145396; // Inclination of the Moon's orbit
    $mecc = 0.054900; // Eccentricity of the Moon's orbit
    $mangsiz = 0.5181; // Moon's angular size at distance a from Earth
    $msmax = 384401; // Semi-major axis of Moon's orbit in km
    $mparallax = 0.9507; // Parallax at distance a from Earth
    $synmonth = 29.53058868; // Synodic month (new Moon to new Moon)
    $this->synmonth = $synmonth;
    $lunatbase = 2423436.0; // Base date for E. W. Brown's numbered series of lunations (1923 January 16)
    /*  Properties of the Earth  */

    // $earthrad = 6378.16;				// Radius of Earth in kilometres
    // $PI = 3.14159265358979323846;	// Assume not near black hole

    $this->timestamp = $pdate;

    // pdate is coming in as a UNIX timstamp, so convert it to Julian

    $pdate = $pdate / 86400 + 2440587.5;
    /* Calculation of the Sun's position */
    $Day = $pdate - $epoch; // Date within epoch
    $N = $this->fixangle((360 / 365.2422) * $Day); // Mean anomaly of the Sun
    $M = $this->fixangle($N + $elonge - $elongp); // Convert from perigee co-ordinates to epoch 1980.0
    $Ec = $this->kepler($M, $eccent); // Solve equation of Kepler
    $Ec = sqrt((1 + $eccent) / (1 - $eccent)) * tan($Ec / 2);
    $Ec = 2 * rad2deg(atan($Ec)); // True anomaly
    $Lambdasun = $this->fixangle($Ec + $elongp); // Sun's geocentric ecliptic longitude
    $F = ((1 + $eccent * cos(deg2rad($Ec))) / (1 - $eccent * $eccent)); // Orbital distance factor
    $SunDist = $sunsmax / $F; // Distance to Sun in km
    $SunAng = $F * $sunangsiz; // Sun's angular size in degrees
    /* Calculation of the Moon's position */
    $ml = $this->fixangle(13.1763966 * $Day + $mmlong); // Moon's mean longitude
    $MM = $this->fixangle($ml - 0.1114041 * $Day - $mmlongp); // Moon's mean anomaly
    $MN = $this->fixangle($mlnode - 0.0529539 * $Day); // Moon's ascending node mean longitude
    $Ev = 1.2739 * sin(deg2rad(2 * ($ml - $Lambdasun) - $MM)); // Evection
    $Ae = 0.1858 * sin(deg2rad($M)); // Annual equation
    $A3 = 0.37 * sin(deg2rad($M)); // Correction term
    $MmP = $MM + $Ev - $Ae - $A3; // Corrected anomaly
    $mEc = 6.2886 * sin(deg2rad($MmP)); // Correction for the equation of the centre
    $A4 = 0.214 * sin(deg2rad(2 * $MmP)); // Another correction term
    $lP = $ml + $Ev + $mEc - $Ae + $A4; // Corrected longitude
    $V = 0.6583 * sin(deg2rad(2 * ($lP - $Lambdasun))); // Variation
    $lPP = $lP + $V; // True longitude
    $NP = $MN - 0.16 * sin(deg2rad($M)); // Corrected longitude of the node
    $y = sin(deg2rad($lPP - $NP)) * cos(deg2rad($minc)); // Y inclination coordinate
    $x = cos(deg2rad($lPP - $NP)); // X inclination coordinate
    $Lambdamoon = rad2deg(atan2($y, $x)) + $NP; // Ecliptic longitude
    $BetaM = rad2deg(asin(sin(deg2rad($lPP - $NP)) * sin(deg2rad($minc)))); // Ecliptic latitude
    /* Calculation of the phase of the Moon */
    $MoonAge = $lPP - $Lambdasun; // Age of the Moon in degrees
    $MoonPhase = (1 - cos(deg2rad($MoonAge))) / 2; // Phase of the Moon

    // Distance of moon from the centre of the Earth

    $MoonDist = ($msmax * (1 - $mecc * $mecc)) / (1 + $mecc * cos(deg2rad($MmP + $mEc)));
    $MoonDFrac = $MoonDist / $msmax;
    $MoonAng = $mangsiz / $MoonDFrac; // Moon's angular diameter

    // $MoonPar = $mparallax / $MoonDFrac;							// Moon's parallax
    // store results

    $this->phase = $this->fixangle($MoonAge) / 360; // Phase (0 to 1)
    $this->illum = $MoonPhase; // Illuminated fraction (0 to 1)
    $this->age = $synmonth * $this->phase; // Age of moon (days)
    $this->dist = $MoonDist; // Distance (kilometres)
    $this->angdia = $MoonAng; // Angular diameter (degrees)
    $this->sundist = $SunDist; // Distance to Sun (kilometres)
    $this->sunangdia = $SunAng; // Sun's angular diameter (degrees)
  }

  private function fixangle($a)
  {
    return ($a - 360 * floor($a / 360));
  }

  //  KEPLER  --   Solve the equation of Kepler.

  private function kepler($m, $ecc)
  {
    $epsilon = 0.000001; // 1E-6
    $e = $m = deg2rad($m);
    do {
      $delta = $e - $ecc * sin($e) - $m;
      $e-= $delta / (1 - $ecc * cos($e));
    }

    while (abs($delta) > $epsilon);
    return $e;
  }

  /*  Calculates  time  of  the mean new Moon for a given
  base date.  This argument K to this function is the
  precomputed synodic month index, given by:
  K = (year - 1900) * 12.3685
  where year is expressed as a year and fractional year.
  */
  private function meanphase($sdate, $k)
  {

    // Time in Julian centuries from 1900 January 0.5

    $t = ($sdate - 2415020.0) / 36525;
    $t2 = $t * $t;
    $t3 = $t2 * $t;
    $nt1 = 2415020.75933 
		  + $this->synmonth * $k 
			+ 0.0001178 * $t2 - 0.000000155 * $t3 
			+ 0.00033 * sin(deg2rad(166.56 + 132.87 * $t - 0.009173 * $t2));
    return $nt1;
  }

  /*  Given a K value used to determine the mean phase of
  the new moon, and a phase selector (0.0, 0.25, 0.5,
  0.75), obtain the true, corrected phase time.
  */
  private function truephase($k, $phase)
  {
    $apcor = false;
    $k+= $phase; // Add phase to new moon time
    $t = $k / 1236.85; // Time in Julian centuries from 1900 January 0.5
    $t2 = $t * $t; // Square for frequent use
    $t3 = $t2 * $t; // Cube for frequent use
    $pt = 2415020.75933 // Mean time of phase
     + $this->synmonth * $k 
		 + 0.0001178 * $t2 
		 - 0.000000155 * $t3 
		 + 0.00033 * sin(deg2rad(166.56 + 132.87 * $t - 0.009173 * $t2));
    $m = 359.2242 + 29.10535608 * $k - 0.0000333 * $t2 - 0.00000347 * $t3; // Sun's mean anomaly
    $mprime = 306.0253 + 385.81691806 * $k + 0.0107306 * $t2 + 0.00001236 * $t3; // Moon's mean anomaly
    $f = 21.2964 + 390.67050646 * $k - 0.0016528 * $t2 - 0.00000239 * $t3; // Moon's argument of latitude
    if ($phase < 0.01 || abs($phase - 0.5) < 0.01) {

      // Corrections for New and Full Moon

      $pt+= (0.1734 - 0.000393 * $t) * sin(deg2rad($m)) 
			  + 0.0021 * sin(deg2rad(2 * $m)) 
				- 0.4068 * sin(deg2rad($mprime)) 
				+ 0.0161 * sin(deg2rad(2 * $mprime)) 
				- 0.0004 * sin(deg2rad(3 * $mprime)) 
				+ 0.0104 * sin(deg2rad(2 * $f)) 
				- 0.0051 * sin(deg2rad($m + $mprime)) 
				- 0.0074 * sin(deg2rad($m - $mprime)) 
				+ 0.0004 * sin(deg2rad(2 * $f + $m)) 
				- 0.0004 * sin(deg2rad(2 * $f - $m)) 
				- 0.0006 * sin(deg2rad(2 * $f + $mprime)) 
				+ 0.0010 * sin(deg2rad(2 * $f - $mprime)) 
				+ 0.0005 * sin(deg2rad($m + 2 * $mprime));
      $apcor = true;
    }
    else
    if (abs($phase - 0.25) < 0.01 || abs($phase - 0.75) < 0.01) {
      $pt+= (0.1721 - 0.0004 * $t) * sin(deg2rad($m)) + 0.0021 * sin(deg2rad(2 * $m)) 
			  - 0.6280 * sin(deg2rad($mprime)) 
				+ 0.0089 * sin(deg2rad(2 * $mprime)) 
				- 0.0004 * sin(deg2rad(3 * $mprime)) 
				+ 0.0079 * sin(deg2rad(2 * $f)) 
				- 0.0119 * sin(deg2rad($m + $mprime)) 
				- 0.0047 * sin(deg2rad($m - $mprime)) 
				+ 0.0003 * sin(deg2rad(2 * $f + $m)) 
				- 0.0004 * sin(deg2rad(2 * $f - $m)) 
				- 0.0006 * sin(deg2rad(2 * $f + $mprime)) 
				+ 0.0021 * sin(deg2rad(2 * $f - $mprime)) 
				+ 0.0003 * sin(deg2rad($m + 2 * $mprime)) 
				+ 0.0004 * sin(deg2rad($m - 2 * $mprime)) 
				- 0.0003 * sin(deg2rad(2 * $m + $mprime));
      if ($phase < 0.5) // First quarter correction
      $pt+= 0.0028 - 0.0004 * cos(deg2rad($m)) + 0.0003 * cos(deg2rad($mprime));
      else

      // Last quarter correction

      $pt+= - 0.0028 + 0.0004 * cos(deg2rad($m)) - 0.0003 * cos(deg2rad($mprime));
      $apcor = true;
    }

    if (!$apcor) { // function was called with an invalid phase selector
      return false;
		}
    return $pt;
  }

  /* 	Find time of phases of the moon which surround the current date.
  Five phases are found, starting and
  ending with the new moons which bound the  current lunation.
  */
  private function phasehunt()
  {
    $sdate = $this->utctojulian($this->timestamp);
    $adate = $sdate - 45;
    $ats = $this->timestamp - 86400 * 45;
    $yy = (int)gmdate('Y', $ats);
    $mm = (int)gmdate('n', $ats);
    $k1 = floor(($yy + (($mm - 1) * (1 / 12)) - 1900) * 12.3685);
    $adate = $nt1 = $this->meanphase($adate, $k1);
    while (true) {
      $adate+= $this->synmonth;
      $k2 = $k1 + 1;
      $nt2 = $this->meanphase($adate, $k2);

      // if nt2 is close to sdate, then mean phase isn't good enough, we have to be more accurate

      if (abs($nt2 - $sdate) < 0.75) $nt2 = $this->truephase($k2, 0.0);
      if ($nt1 <= $sdate && $nt2 > $sdate) break;

      $nt1 = $nt2;
      $k1 = $k2;
    }

    // results in Julian dates

    $data = array(
      $this->truephase($k1, 0.0) ,
      $this->truephase($k1, 0.25) ,
      $this->truephase($k1, 0.5) ,
      $this->truephase($k1, 0.75) ,
      $this->truephase($k2, 0.0) ,
      $this->truephase($k2, 0.25) ,
      $this->truephase($k2, 0.5) ,
      $this->truephase($k2, 0.75)
    );
    $this->quarters = array();
    foreach($data as $v) $this->quarters[] = ($v - 2440587.5) * 86400; // convert to UNIX time
  }

  /*  Convert UNIX timestamp to astronomical Julian time (i.e. Julian date plus day fraction).  */
  private function utctojulian($ts)
  {
    return $ts / 86400 + 2440587.5;
  }

  private function get_phase($n)
  {
    if (is_null($this->quarters)) $this->phasehunt();
    return $this->quarters[$n];
  }

  /* Public functions for accessing results */
  function phase()
  {
    return $this->phase;
  }

  function illumination()
  {
    return $this->illum;
  }

  function age()
  {
    return $this->age;
  }

  function distance()
  {
    return $this->dist;
  }

  function diameter()
  {
    return $this->angdia;
  }

  function sundistance()
  {
    return $this->sundist;
  }

  function sundiameter()
  {
    return $this->sunangdia;
  }

  function new_moon()
  {
    return $this->get_phase(0);
  }

  function first_quarter()
  {
    return $this->get_phase(1);
  }

  function full_moon()
  {
    return $this->get_phase(2);
  }

  function last_quarter()
  {
    return $this->get_phase(3);
  }

  function next_new_moon()
  {
    return $this->get_phase(4);
  }

  function next_first_quarter()
  {
    return $this->get_phase(5);
  }

  function next_full_moon()
  {
    return $this->get_phase(6);
  }

  function next_last_quarter()
  {
    return $this->get_phase(7);
  }

  function phase_name()
  { // Ken's take on Phase Name calculation
    $ph = round($this->phase * 100);
    if ($ph <= 1 || $ph >= 98) {
      return ('New Moon');
    }
    elseif ($ph > 1 && $ph < 24) {
      return ('Waxing Crescent');
    }
    elseif ($ph >= 24 && $ph <= 27) {
      return ('First Quarter');
    }
    elseif ($ph > 27 && $ph < 49) {
      return ('Waxing Gibbous');
    }
    elseif ($ph >= 49 && $ph <= 52) {
      return ('Full Moon');
    }
    elseif ($ph > 52 && $ph < 74) {
      return ('Waning Gibbous');
    }
    elseif ($ph >= 74 && $ph <= 77) {
      return ('Last Quarter');
    }
    elseif ($ph > 77 && $ph < 98) {
      return ('Waning Crescent');
    }
  }
} // end class MoonPhase
/******************************************************************************
* The following is a PHP implementation of the JavaScript code found at:      *
* http://bodmas.org/astronomy/riset.html                                      *
*                                                                             *
* Original maths and code written by Keith Burnett <bodmas.org>               *
* PHP port written by Matt "dxprog" Hackmann <dxprog.com>                     *
*                                                                             *
* This program is free software: you can redistribute it and/or modify        *
* it under the terms of the GNU General Public License as published by        *
* the Free Software Foundation, either version 3 of the License, or           *
* (at your option) any later version.                                         *
*                                                                             *
* This program is distributed in the hope that it will be useful,             *
* but WITHOUT ANY WARRANTY; without even the implied warranty of              *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               *
* GNU General Public License for more details.                                *
*                                                                             *
* You should have received a copy of the GNU General Public License           *
* along with this program.  If not, see <http://www.gnu.org/licenses/>.       *
*                                                                             *
* Adapted by K. True - Saratoga-weather.org 15-Aug-2017                       *
******************************************************************************/
class calcMoon

{
  /**
   * Calculates the moon rise/set for a given location and day of year
   */
  public static

  function calculateMoonTimes($month, $day, $year, $lat, $lon)
  {
    $utrise = $utset = 0;

    //		$timezone = (int)($lon / 15);

    $date = self::modifiedJulianDate($month, $day, $year);

    //		$date -= $timezone / 24;

    $timezone = date('Z') / 3600; // KT
    $date-= $timezone / 24;
    $latRad = deg2rad($lat);
    $sinho = 0.0023271056;
    $sglat = sin($latRad);
    $cglat = cos($latRad);
    $rise = false;
    $set = false;
    $above = false;
    $hour = 1;
    $ym = self::sinAlt($date, $hour - 1, $lon, $cglat, $sglat) - $sinho;
    $above = $ym > 0;
    while ($hour < 25 && (false == $set || false == $rise)) {
      $yz = self::sinAlt($date, $hour, $lon, $cglat, $sglat) - $sinho;
      $yp = self::sinAlt($date, $hour + 1, $lon, $cglat, $sglat) - $sinho;
      $quadout = self::quad($ym, $yz, $yp);
      $nz = $quadout[0];
      $z1 = $quadout[1];
      $z2 = $quadout[2];
      $xe = $quadout[3];
      $ye = $quadout[4];
      if ($nz == 1) {
        if ($ym < 0) {
          $utrise = $hour + $z1;
          $rise = true;
        }
        else {
          $utset = $hour + $z1;
          $set = true;
        }
      }

      if ($nz == 2) {
        if ($ye < 0) {
          $utrise = $hour + $z2;
          $utset = $hour + $z1;
        }
        else {
          $utrise = $hour + $z1;
          $utset = $hour + $z2;
        }
      }

      $ym = $yp;
      $hour+= 2.0;
    }

    // Convert to unix timestamps and return as an object

    $retVal = new stdClass();
    $utrise = self::convertTime($utrise);
    $utset = self::convertTime($utset);
    $retVal->moonrise = $rise ? mktime($utrise['hrs'], $utrise['min'], 0, $month, $day, $year) : mktime(0, 0, 0, $month, $day + 1, $year);
    $retVal->moonset = $set ? mktime($utset['hrs'], $utset['min'], 0, $month, $day, $year) : mktime(0, 0, 0, $month, $day + 1, $year);
    return $retVal;
  }

  /**
   *	finds the parabola throuh the three points (-1,ym), (0,yz), (1, yp)
   *  and returns the coordinates of the max/min (if any) xe, ye
   *  the values of x where the parabola crosses zero (roots of the self::quadratic)
   *  and the number of roots (0, 1 or 2) within the interval [-1, 1]
   *
   *	well, this routine is producing sensible answers
   *
   *  results passed as array [nz, z1, z2, xe, ye]
   */
  private static function quad($ym, $yz, $yp)
  {
    $nz = $z1 = $z2 = 0;
    $a = 0.5 * ($ym + $yp) - $yz;
    $b = 0.5 * ($yp - $ym);
    $c = $yz;
    $xe = - $b / (2 * $a);
    $ye = ($a * $xe + $b) * $xe + $c;
    $dis = $b * $b - 4 * $a * $c;
    if ($dis > 0) {
      $dx = 0.5 * sqrt($dis) / abs($a);
      $z1 = $xe - $dx;
      $z2 = $xe + $dx;
      $nz = abs($z1) < 1 ? $nz + 1 : $nz;
      $nz = abs($z2) < 1 ? $nz + 1 : $nz;
      $z1 = $z1 < - 1 ? $z2 : $z1;
    }

    return array(
      $nz,
      $z1,
      $z2,
      $xe,
      $ye
    );
  }

  /**
   *	this rather mickey mouse function takes a lot of
   *  arguments and then returns the sine of the altitude of the moon
   */
  private static function sinAlt($mjd, $hour, $glon, $cglat, $sglat)
  {
    $mjd+= $hour / 24;
    $t = ($mjd - 51544.5) / 36525;
    $objpos = self::minimoon($t);
    $ra = $objpos[1];
    $dec = $objpos[0];
    $decRad = deg2rad($dec);
    $tau = 15 * (self::lmst($mjd, $glon) - $ra);
    return $sglat * sin($decRad) + $cglat * cos($decRad) * cos(deg2rad($tau));
  }

  /**
   *	returns an angle in degrees in the range 0 to 360
   */
  private static function degRange($x)
  {
    $b = $x / 360;
    $a = 360 * ($b - (int)$b);
    $retVal = $a < 0 ? $a + 360 : $a;
    return $retVal;
  }

  private static function lmst($mjd, $glon)
  {
    $d = $mjd - 51544.5;
    $t = $d / 36525;
    $lst = self::degRange(280.46061839 
		  + 360.98564736629 * $d 
		  + 0.000387933 * $t * $t 
		  - $t * $t * $t / 38710000);
    return $lst / 15 + $glon / 15;
  }

  /**
   * takes t and returns the geocentric ra and dec in an array mooneq
   * claimed good to 5' (angle) in ra and 1' in dec
   * tallies with another approximate method and with ICE for a couple of dates
   */
  private static function minimoon($t)
  {
    $p2 = 6.283185307;
    $arc = 206264.8062;
    $coseps = 0.91748;
    $sineps = 0.39778;
    $lo = self::frac(0.606433 + 1336.855225 * $t);
    $l = $p2 * self::frac(0.374897 + 1325.552410 * $t);
    $l2 = $l * 2;
    $ls = $p2 * self::frac(0.993133 + 99.997361 * $t);
    $d = $p2 * self::frac(0.827361 + 1236.853086 * $t);
    $d2 = $d * 2;
    $f = $p2 * self::frac(0.259086 + 1342.227825 * $t);
    $f2 = $f * 2;
    $sinls = sin($ls);
    $sinf2 = sin($f2);
    $dl = 22640 * sin($l);
    $dl+= - 4586 * sin($l - $d2);
    $dl+= 2370 * sin($d2);
    $dl+= 769 * sin($l2);
    $dl+= - 668 * $sinls;
    $dl+= - 412 * $sinf2;
    $dl+= - 212 * sin($l2 - $d2);
    $dl+= - 206 * sin($l + $ls - $d2);
    $dl+= 192 * sin($l + $d2);
    $dl+= - 165 * sin($ls - $d2);
    $dl+= - 125 * sin($d);
    $dl+= - 110 * sin($l + $ls);
    $dl+= 148 * sin($l - $ls);
    $dl+= - 55 * sin($f2 - $d2);
    $s = $f + ($dl + 412 * $sinf2 + 541 * $sinls) / $arc;
    $h = $f - $d2;
    $n = - 526 * sin($h);
    $n+= 44 * sin($l + $h);
    $n+= - 31 * sin(-$l + $h);
    $n+= - 23 * sin($ls + $h);
    $n+= 11 * sin(-$ls + $h);
    $n+= - 25 * sin(-$l2 + $f);
    $n+= 21 * sin(-$l + $f);
    $L_moon = $p2 * self::frac($lo + $dl / 1296000);
    $B_moon = (18520.0 * sin($s) + $n) / $arc;
    $cb = cos($B_moon);
    $x = $cb * cos($L_moon);
    $v = $cb * sin($L_moon);
    $w = sin($B_moon);
    $y = $coseps * $v - $sineps * $w;
    $z = $sineps * $v + $coseps * $w;
    $rho = sqrt(1 - $z * $z);
    $dec = (360 / $p2) * atan($z / $rho);
    $ra = (48 / $p2) * atan($y / ($x + $rho));
    $ra = $ra < 0 ? $ra + 24 : $ra;
    return array(
      $dec,
      $ra
    );
  }

  /**
   *	returns the self::fractional part of x as used in self::minimoon and minisun
   */
  private static function frac($x)
  {
    $x-= (int)$x;
    return $x < 0 ? $x + 1 : $x;
  }

  /**
   * Takes the day, month, year and hours in the day and returns the
   * modified julian day number defined as mjd = jd - 2400000.5
   * checked OK for Greg era dates - 26th Dec 02
   */
  private static function modifiedJulianDate($month, $day, $year)
  {
    if ($month <= 2) {
      $month+= 12;
      $year--;
    }

    $a = 10000 * $year + 100 * $month + $day;
    $b = 0;
    if ($a <= 15821004.1) {
      $b = - 2 * (int)(($year + 4716) / 4) - 1179;
    }
    else {
      $b = (int)($year / 400) - (int)($year / 100) + (int)($year / 4);
    }

    $a = 365 * $year - 679004;
    return $a + $b + (int)(30.6001 * ($month + 1)) + $day;
  }

  /**
   * Converts an hours decimal to hours and minutes
   */
  private static function convertTime($hours)
  {
    $hrs = (int)($hours * 60 + 0.5) / 60.0;
    $h = (int)($hrs);
    $m = (int)(60 * ($hrs - $h) + 0.5);
    return array(
      'hrs' => $h,
      'min' => $m
    );
  }
} // end class Moon
/******************************************************************************
* The following is courtesy of Jachym https://meteotemplate.com               *
* Note: the moon rise/set is NOT quite as accurate as that                    *
* provided by the prior calcMoon class above, so is only used for moon        *
* transit time                                                                *
*                                                                             *
*******************************************************************************
*/
/* ############ MOON FUNCTIONS #################### */

// Moon rise/set

class calcMoonRiSet

{
  const RADEG = 57.29577951308232;
  const DEGRAD = 0.01745329251994;
  const ARC = 206264.8;
  const SIN_EPS = 0.39768; // sin+cos obliquity ecliptic (23d26m)
  const COS_EPS = 0.91752;
  const PREC = 18; // precision
  private $_sinEarthLatitude, $_cosEarthLatitude;
  private $_data = array();
  public

  function __construct($earthLatitude = false, $earthLongitude = false, $earthTimezone = false)
  {
    if ($earthLatitude === false) $this->earthLatitude = ini_get('date.default_latitude');
    else $this->earthLatitude = $earthLatitude;
    if ($earthLongitude === false) $this->earthLongitude = ini_get('date.default_longitude');
    else $this->earthLongitude = $earthLongitude;
    if ($earthTimezone === false) $this->earthTimezone = ini_get('date.timezone');
    else $this->earthTimezone = $earthTimezone;

    // set current day

    $this->setDate(date("Y", time()) , date("n", time()) , date("j", time()));
  }

  // set day

  public function setDate($year, $month, $day)
  {
    if ($year < 1583 or $year > 2500) return (false);
    $old_timezone = date_default_timezone_get();
    date_default_timezone_set($this->earthTimezone);

    // calculation day's table, begin+end time

    $t = $tb = mktime(0, 0, 0, $month, $day, $year);
    $te = mktime(24, 0, 0, $month, $day, $year);
    $this->tdiff = ($te - $tb) / self::PREC;
    $this->_sinEarthLatitude = $this->dsin($this->earthLatitude);
    $this->_cosEarthLatitude = $this->dcos($this->earthLatitude);
    $i = 0;
    while ($i <= self::PREC) {
      $this->_data[$i]["timestamp"] = $t;
      $jd = $this->getJulianDate($t);
      $LST = $this->getLST($jd); // Local Sidereal Time
      $this->_data[$i]["LST"] = $LST;
      list($RA, $de) = $this->miniMoon(($jd - 2451545.0) / 36525.0);
      $this->_data[$i]["RA"] = $RA;
      $HA = $LST - $RA; // hour angle
      if ($HA > 12) $HA-= 24;
      $this->_data[$i]["HA"] = $HA;
      $this->_data[$i]["sAlt"] = 
			  $this->_sinEarthLatitude * $this->dsin($de) 
				+ $this->_cosEarthLatitude * $this->dcos($de) * $this->dcos(15 * $this->_data[$i]["HA"]); // sinus Altitude
      $t+= $this->tdiff;
      $i++;
    }

    // Moon transit

    list($this->transit["timestamp"], $this->transit["hhmm"], $this->transit["hh:mm"]) = $this->getTransit("HA");

    // Moon's rise and set

    list($this->rise["timestamp"], 
		  $this->rise["hhmm"], 
		  $this->rise["hh:mm"], 
		  $this->set["timestamp"], 
			$this->set["hhmm"], 
			$this->set["hh:mm"], 
			$this->rise2["timestamp"], 
			$this->rise2["hhmm"], 
			$this->rise2["hh:mm"], 
			$this->set2["timestamp"], 
			$this->set2["hhmm"], 
			$this->set2["hh:mm"]) = $this->getRiSet("sAlt", $this->dsin(0.125));
    date_default_timezone_set($old_timezone);
    return (true);
  }

  // PRIVATE //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  private function getTransit($o)
  {
    $fT = false;
    for ($i = 1; $i < self::PREC && !$fT; $i+= 2) {
      if (($this->_data[$i - 1][$o] < 0 && $this->_data[$i][$o] >= 0) || 
			    ($this->_data[$i][$o] <= 0 && $this->_data[$i + 1][$o] > 0)) {
        list($nz, $z1, $z2, $xe, $ye) = 
				  $this->quad($this->_data[$i - 1][$o], $this->_data[$i][$o], $this->_data[$i + 1][$o]);
        $fT = true;
        $oTran = $this->_data[$i]["timestamp"] + $this->tdiff * $z1;
      }
    }

    if ($fT) list($transit["timestamp"], $transit["hhmm"], $transit["hh:mm"]) = $this->formatTime($oTran);
    else {
      $transit["timestamp"] = false;
      $transit["hhmm"] = "    ";
      $transit["hh:mm"] = "     ";
    }

    return array(
      $transit["timestamp"],
      $transit["hhmm"],
      $transit["hh:mm"]
    );
  }

  private function getRiSet($o, $alt)
  {
    $fRise = $fSet = false;
    $fAbove = false;
    $oRise2 = $oSet2 = false;
    if ($this->_data[0][$o] > $alt) $fAbove = true;
    for ($i = 1; $i < self::PREC; $i+= 2) {
      list($nz, $z1, $z2, $xe, $ye) = 
			  $this->quad($this->_data[$i - 1][$o] 
				  - $alt, $this->_data[$i][$o] 
					- $alt, $this->_data[$i + 1][$o] 
					- $alt);
      if ($nz == 1) {
        if ($this->_data[$i - 1][$o] < $alt) {
          if ($fRise === true) $oRise2 = $this->_data[$i]["timestamp"] + $this->tdiff * $z1;
          else {
            $oRise = $this->_data[$i]["timestamp"] + $this->tdiff * $z1;
            $fRise = true;
          }
        }
        else {
          if ($fSet === true) $oSet2 = $this->_data[$i]["timestamp"] + $this->tdiff * $z1;
          else {
            $oSet = $this->_data[$i]["timestamp"] + $this->tdiff * $z1;
            $fSet = true;
          }
        }
      }
      elseif ($nz == 2) {
        if ($ye < 0.0) {
          $oRise = $this->_data[$i]["timestamp"] + $this->tdiff * $z2;
          $oSet = $this->_data[$i]["timestamp"] + $this->tdiff * $z1;
        }
        else {
          $oRise = $this->_data[$i]["timestamp"] + $this->tdiff * $z1;
          $oSet = $this->_data[$i]["timestamp"] + $this->tdiff * $z2;
        }

        $fRise = $fSet = true;
      }
    }

    // output

    if ($fRise === true || $fSet === true) {
      if ($fRise === true) {
        list($rise["timestamp"], $rise["hhmm"], $rise["hh:mm"]) = $this->formatTime($oRise);
        list($rise2["timestamp"], $rise2["hhmm"], $rise2["hh:mm"]) = $this->formatTime($oRise2);
      }
      else {
        $rise["timestamp"] = false;
        $rise["hhmm"] = "    ";
        $rise["hh:mm"] = "     ";
      }

      if ($fSet === true) {
        list($set["timestamp"], $set["hhmm"], $set["hh:mm"]) = $this->formatTime($oSet);
        list($set2["timestamp"], $set2["hhmm"], $set2["hh:mm"]) = $this->formatTime($oSet2);
      }
      else {
        $set["timestamp"] = true;
        $set["hhmm"] = "    ";
        $set["hh:mm"] = "     ";
      }
    }
    else {
      if ($fAbove === true) { // continuously above horizon
        $rise["timestamp"] = $set["timestamp"] = true;
        $rise["hhmm"] = $set["hhmm"] = "****";
        $rise["hh:mm"] = $set["hh:mm"] = "**:**";
      }
      else { // continuously below horizon
        $rise["timestamp"] = $set["timestamp"] = false;
        $rise["hhmm"] = $set["hhmm"] = "----";
        $rise["hh:mm"] = $set["hh:mm"] = "--:--";
      }
    }

    // return

    if ($oRise2 !== false) {
			return array(
				$rise["timestamp"],
				$rise["hhmm"],
				$rise["hh:mm"],
				$set["timestamp"],
				$set["hhmm"],
				$set["hh:mm"],
				$rise2["timestamp"],
				$rise2["hhmm"],
				$rise2["hh:mm"],
				false,
				false,
				false
      );
		}
    elseif ($oSet2 !== false) {
			return array(
				$rise["timestamp"],
				$rise["hhmm"],
				$rise["hh:mm"],
				$set["timestamp"],
				$set["hhmm"],
				$set["hh:mm"],
				false,
				false,
				false,
				$set2["timestamp"],
				$set2["hhmm"],
				$set2["hh:mm"]
			);
		}
    else {
			return array(
				$rise["timestamp"],
				$rise["hhmm"],
				$rise["hh:mm"],
				$set["timestamp"],
				$set["hhmm"],
				$set["hh:mm"],
				false,
				false,
				false,
				false,
				false,
				false
			);
		}
  }

  // Low precision formulae for planetary position, Flandern & Pulkkinen
  // returns ra and dec of Moon to 5 arc min (ra) and 1 arc min (dec)
  // for a few centuries either side of J2000.0
  // Predicts rise and set times to within minutes for about 500 years

  private function miniMoon($T)
  {
    $l0 = $this->frac(0.606433 + 1336.855225 * $T);
    $l = 2 * M_PI * $this->frac(0.374897 + 1325.552410 * $T);
    $ls = 2 * M_PI * $this->frac(0.993133 + 99.997361 * $T);
    $d = 2 * M_PI * $this->frac(0.827361 + 1236.853086 * $T);
    $f = 2 * M_PI * $this->frac(0.259086 + 1342.227825 * $T);

    // perturbation

    $dl = 22640 * sin($l);
    $dl+= - 4586 * sin($l - 2 * $d);
    $dl+= + 2370 * sin(2 * $d);
    $dl+= + 769 * sin(2 * $l);
    $dl+= - 668 * sin($ls);
    $dl+= - 412 * sin(2 * $f);
    $dl+= - 212 * sin(2 * $l - 2 * $d);
    $dl+= - 206 * sin($l + $ls - 2 * $d);
    $dl+= + 192 * sin($l + 2 * $d);
    $dl+= - 165 * sin($ls - 2 * $d);
    $dl+= - 125 * sin($d);
    $dl+= - 110 * sin($l + $ls);
    $dl+= + 148 * sin($l - $ls);
    $dl+= - 55 * sin(2 * $f - 2 * $d);
    $s = $f + ($dl + 412 * sin(2 * $f) + 541 * sin($ls)) / self::ARC;
    $h = $f - 2 * $d;
    $n = - 526 * sin($h);
    $n+= + 44 * sin($l + $h);
    $n+= - 31 * sin(-$l + $h);
    $n+= - 23 * sin($ls + $h);
    $n+= + 11 * sin(-$ls + $h);
    $n+= - 25 * sin(-2 * $l + $f);
    $n+= + 21 * sin(-$l + $f);
    $l_moon = 2 * M_PI * $this->frac($l0 + $dl / 1296000);
    $b_moon = (18520.0 * sin($s) + $n) / self::ARC;

    // convert to equatorial coords using a fixed ecliptic

    $cb = cos($b_moon);
    $x = $cb * cos($l_moon);
    $v = $cb * sin($l_moon);
    $w = sin($b_moon);
    $y = self::COS_EPS * $v - self::SIN_EPS * $w;
    $z = self::SIN_EPS * $v + self::COS_EPS * $w;
    $rho = sqrt(1.0 - $z * $z);
    $de = (180 / M_PI) * atan($z / $rho);
    $RA = (24 / M_PI) * atan($y / ($x + $rho));
    if ($RA < 0) $RA+= 24;
    return (array(
      $RA,
      $de
    ));
  }

  // finds the parabola through the three points (-1,ym), (0,yz), (1, yp) and returns
  // the coordinates of the values of x where the parabola crosses zero (roots of the quadratic)
  // and the number of roots (0, 1 or 2) within the interval [-1, 1]

  private function quad($ym, $yz, $yp)
  {
    $z1 = $z2 = 0;
    $nz = 0;
    $a = 0.5 * ($ym + $yp) - $yz;
    $b = 0.5 * ($yp - $ym);
    $c = $yz;
    $xe = - $b / (2 * $a);
    $ye = ($a * $xe + $b) * $xe + $c;
    $dis = $b * $b - 4.0 * $a * $c;
    if ($dis > 0) {
      $dx = 0.5 * sqrt($dis) / abs($a);
      $z1 = $xe - $dx;
      $z2 = $xe + $dx;
      if (abs($z1) <= 1.0) $nz+= 1;
      if (abs($z2) <= 1.0) $nz+= 1;
      if ($z1 < - 1.0) $z1 = $z2;
    }

    return (array(
      $nz,
      $z1,
      $z2,
      $xe,
      $ye
    ));
  }

  private function getJulianDate($t)
  {

    // return $t/86400 + 2440587.5; // only for 64bit && year > 1582

    $jd = gregoriantojd(gmdate("n", $t) , gmdate("j", $t) , gmdate("Y", $t)) - 0.5;
    $jd+= gmdate("H", $t) / 24 + gmdate("i", $t) / 1440 + gmdate("s", $t) / 86400;
    return ($jd);
  }

  // returns the local sidereal time (degree)

  private function getLST($jd)
  {
    $mjd = $jd - 2451545.0;
    $lst = $this->range(280.46061837 + 360.98564736629 * $mjd);
    return ($lst + $this->earthLongitude) / 15;
  }

  // round time, return array(timestamp, "hhmm", "hh:mm")

  private function formatTime($t)
  {
    $t0 = 60 * (int)($t / 60 + 0.5);
    if (date("j", $t) == date("j", $t0)) $t = $t0;
    return array(
      $t,
      date("Hi", $t) ,
      date("H:i", $t)
    );
  }

  private function frac($x)
  {
    return ($x - floor($x));
  }

  private function range($x)
  {
    return ($x - 360.0 * (Floor($x / 360.0)));
  }

  private function dsin($x)
  {
    return (sin($x * self::DEGRAD));
  }

  private function dcos($x)
  {
    return (cos($x * self::DEGRAD));
  }
} // end class MoonRiSet
// end get-USNO-sunmoon.php
// ------------------------------------------------------------------
