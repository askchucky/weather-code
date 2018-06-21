<?php
/*
 File: MBtags.php

 Purpose: load Meteobridge variables into a $WX[] array for use with the Canada/World/USA template sets
 NOTE: this file must be processed by Meteobridge as a template file and uploaded to the website
   as MBtags.php using the Meteobridge extended Push Services configuration.

 Author: Ken True - webmaster@saratoga-weather.org

 (created by gen-MBtags.php - V1.02 - 17-Mar-2013)

 These tags generated on 2013-03-17 20:03:44 GMT
   From MBtags-template.txt updated 2013-03-17 20:03:08 GMT

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
$defsFile = 'MB-defs.php';  // filename with $varnames = $WX['MB-varnames']; equivalents
 
$rawdatalines = '
date|2013-03-17|// local date:|:
time|13:17:44|// local time:|:
dateUTC|2013-03-17|// UTC date:|:
timeUTC|20:17:44|// UTCtime:|:
uomTemp|&deg;F|// UOM temperature:|:
uomWind| mph|// UOM wind:|:
uomBaro| inHg|// UOM barometer:|:
uomRain| in|// UOM rain:|:
mbsystem-swversion|1.1|// Meteobridge version string (example: "1.1"):|:
mbsystem-buildnum|1427|// build number as integer (example: 1673):|:
mbsystem-platform|TL-MR3020|// string that specifies hw platform (example: "TL-MR3020"):|:
mbsystem-language|English|// language used on Meteobridge web interface (example: "English"):|:
mbsystem-timezone|America/Los Angeles|// defined timezone (example: "Europe/Berlin"):|:
mbsystem-latitude|37.274710|// latitude as float (example: 53.875120):|:
mbsystem-longitude|-122.022950|// longitude as float (example: 9.885357):|:
mbsystem-lunarage|5|// days passes since new moon as integer (example: 28):|:
mbsystem-lunarpercent|32.5|// lunarphase given as percentage from 0% (new moon) to 100% (full moon):|:
mbsystem-lunarsegment|2|// lunarphase segment as integer (0 = new moon, 1-3 = growing moon: quarter, half, three quarters, 4 = full moon, 5-7 = shrinking moon: three quarter, half, quarter):|:
mbsystem-daylength|12:02|// length of day (example: "11:28"):|:
mbsystem-civildaylength|12:54|// alternative method for daylength computation (example: "12:38"):|:
mbsystem-nauticaldaylength|13:55|// alternative method for daylength computation (example: "14:00"):|:
mbsystem-sunrise|07:15|// time of sunrise in local time. Can be converted to UTC by applying "=utc" to the variable (example: "06:47", resp. "05:47"):|:
mbsystem-sunset|19:17|// time of sunset in local time. Can be converted to UTC by applying "=utc" to the variable (example: "18:15", resp. "17:15"):|:
mbsystem-civilsunrise|06:49|// alternative computation for sunrise.:|:
mbsystem-civilsunset|19:43|// alternative computation for sunset.:|:
mbsystem-nauticalsunrise|06:19|// alternative computation for sunrise.:|:
mbsystem-nauticalsunset|20:14|// alternative alternative computation for sunset..:|:
mbsystem-daynightflag|D|// returns "D" when there is daylight, otherwise "N".:|:
mbsystem-moonrise|10:39|// time of moonrise in local time. Please notice that not every day has a moonrise time, therefore, variable can be non-existent on certain days (example: "05:46", resp. "04:46"):|:
mbsystem-moonset|00:23|// time of moonset in local time. Please notice that not every day has a moonset time, therefore, variable can be non-existent on certain days.:|:
th0temp-act|66.6|// outdoor temperature most recent:|:
th0temp-val5|66.4|// outdoor temperature value 5 minutes ago:|:
th0temp-val10|66.0|// outdoor temperature value 10 minutes ago:|:
th0temp-val15|65.7|// outdoor temperature value 15 minutes ago:|:
th0temp-val30|65.3|// outdoor temperature value 30 minutes ago:|:
th0temp-val60|63.5|// outdoor temperature value 60 minutes ago:|:
th0temp-hmin|65.7|// outdoor temperature min of this hour:|:
th0temp-hmintime|20130317130117|// outdoor temperature timestamp min of this hour:|:
th0temp-hmax|66.6|// outdoor temperature max of this hour:|:
th0temp-hmaxtime|20130317131650|// outdoor temperature timestamp max of this hour:|:
th0temp-dmin|40.6|// outdoor temperature min of today:|:
th0temp-dmintime|20130317072025|// outdoor temperature timestamp min of today:|:
th0temp-dmax|66.6|// outdoor temperature max of today:|:
th0temp-dmaxtime|20130317131650|// outdoor temperature timestamp max of today:|:
th0temp-ydmin|47.1|// outdoor temperature min of yesterday:|:
th0temp-ydmintime|20130316075253|// outdoor temperature timestamp min of yesterday:|:
th0temp-ydmax|73.9|// outdoor temperature max of yesterday:|:
th0temp-ydmaxtime|20130316150944|// outdoor temperature timestamp max of yesterday:|:
th0temp-mmin|37.6|// outdoor temperature min of this month:|:
th0temp-mmintime|20130307052439|// outdoor temperature timestamp min of this month:|:
th0temp-mmax|79.5|// outdoor temperature max of this month:|:
th0temp-mmaxtime|20130313170754|// outdoor temperature timestamp max of this month:|:
th0temp-ymin|37.6|// outdoor temperature min of this year:|:
th0temp-ymintime|20130307052439|// outdoor temperature timestamp min of this year:|:
th0temp-ymax|79.5|// outdoor temperature max of this year:|:
th0temp-ymaxtime|20130313170754|// outdoor temperature timestamp max of this year:|:
th0temp-amin|37.6|// outdoor temperature min of all time:|:
th0temp-amintime|20130307052439|// outdoor temperature timestamp min of all time:|:
th0temp-amax|79.5|// outdoor temperature max of all time:|:
th0temp-amaxtime|20130313170754|// outdoor temperature timestamp max of all time:|:
th0temp-starttime|20130307052439|// outdoor temperature timestamp of first recorded data:|:
th0hum-act|38|// outdoor humidity most recent:|:
th0hum-val5|38|// outdoor humidity value 5 minutes ago:|:
th0hum-val10|39|// outdoor humidity value 10 minutes ago:|:
th0hum-val15|40|// outdoor humidity value 15 minutes ago:|:
th0hum-val30|41|// outdoor humidity value 30 minutes ago:|:
th0hum-val60|42|// outdoor humidity value 60 minutes ago:|:
th0hum-hmin|37|// outdoor humidity min of this hour:|:
th0hum-hmintime|20130317131532|// outdoor humidity timestamp min of this hour:|:
th0hum-hmax|41|// outdoor humidity max of this hour:|:
th0hum-hmaxtime|20130317130045|// outdoor humidity timestamp max of this hour:|:
th0hum-dmin|37|// outdoor humidity min of today:|:
th0hum-dmintime|20130317131532|// outdoor humidity timestamp min of today:|:
th0hum-dmax|93|// outdoor humidity max of today:|:
th0hum-dmaxtime|20130317084034|// outdoor humidity timestamp max of today:|:
th0hum-ydmin|36|// outdoor humidity min of yesterday:|:
th0hum-ydmintime|20130316144519|// outdoor humidity timestamp min of yesterday:|:
th0hum-ydmax|94|// outdoor humidity max of yesterday:|:
th0hum-ydmaxtime|20130316090442|// outdoor humidity timestamp max of yesterday:|:
th0hum-mmin|22|// outdoor humidity min of this month:|:
th0hum-mmintime|20130310155726|// outdoor humidity timestamp min of this month:|:
th0hum-mmax|94|// outdoor humidity max of this month:|:
th0hum-mmaxtime|20130308052618|// outdoor humidity timestamp max of this month:|:
th0hum-ymin|22|// outdoor humidity min of this year:|:
th0hum-ymintime|20130310155726|// outdoor humidity timestamp min of this year:|:
th0hum-ymax|94|// outdoor humidity max of this year:|:
th0hum-ymaxtime|20130308052618|// outdoor humidity timestamp max of this year:|:
th0hum-amin|22|// outdoor humidity min of all time:|:
th0hum-amintime|20130310155726|// outdoor humidity timestamp min of all time:|:
th0hum-amax|94|// outdoor humidity max of all time:|:
th0hum-amaxtime|20130308052618|// outdoor humidity timestamp max of all time:|:
th0hum-starttime|20130308052618|// outdoor humidity timestamp of first recorded data:|:
th0dew-act|40.1|// outdoor dewpoint most recent:|:
th0dew-val5|39.9|// outdoor dewpoint value 5 minutes ago:|:
th0dew-val10|40.3|// outdoor dewpoint value 10 minutes ago:|:
th0dew-val15|40.6|// outdoor dewpoint value 15 minutes ago:|:
th0dew-val30|41.0|// outdoor dewpoint value 30 minutes ago:|:
th0dew-val60|39.9|// outdoor dewpoint value 60 minutes ago:|:
th0dew-hmin|39.4|// outdoor dewpoint min of this hour:|:
th0dew-hmintime|20130317131532|// outdoor dewpoint timestamp min of this hour:|:
th0dew-hmax|41.5|// outdoor dewpoint max of this hour:|:
th0dew-hmaxtime|20130317130045|// outdoor dewpoint timestamp max of this hour:|:
th0dew-dmin|37.9|// outdoor dewpoint min of today:|:
th0dew-dmintime|20130317072025|// outdoor dewpoint timestamp min of today:|:
th0dew-dmax|48.0|// outdoor dewpoint max of today:|:
th0dew-dmaxtime|20130317091446|// outdoor dewpoint timestamp max of today:|:
th0dew-ydmin|43.0|// outdoor dewpoint min of yesterday:|:
th0dew-ydmintime|20130316235705|// outdoor dewpoint timestamp min of yesterday:|:
th0dew-ydmax|54.3|// outdoor dewpoint max of yesterday:|:
th0dew-ydmaxtime|20130316104427|// outdoor dewpoint timestamp max of yesterday:|:
th0dew-mmin|29.7|// outdoor dewpoint min of this month:|:
th0dew-mmintime|20130310155726|// outdoor dewpoint timestamp min of this month:|:
th0dew-mmax|56.7|// outdoor dewpoint max of this month:|:
th0dew-mmaxtime|20130313103743|// outdoor dewpoint timestamp max of this month:|:
th0dew-ymin|29.7|// outdoor dewpoint min of this year:|:
th0dew-ymintime|20130310155726|// outdoor dewpoint timestamp min of this year:|:
th0dew-ymax|56.7|// outdoor dewpoint max of this year:|:
th0dew-ymaxtime|20130313103743|// outdoor dewpoint timestamp max of this year:|:
th0dew-amin|29.7|// outdoor dewpoint min of all time:|:
th0dew-amintime|20130310155726|// outdoor dewpoint timestamp min of all time:|:
th0dew-amax|56.7|// outdoor dewpoint max of all time:|:
th0dew-amaxtime|20130313103743|// outdoor dewpoint timestamp max of all time:|:
th0dew-starttime|20130310155726|// outdoor dewpoint timestamp of first recorded data:|:
thb0temp-act|78.4|// indoor temperature most recent:|:
thb0temp-val5|78.4|// indoor temperature value 5 minutes ago:|:
thb0temp-val10|78.3|// indoor temperature value 10 minutes ago:|:
thb0temp-val15|78.3|// indoor temperature value 15 minutes ago:|:
thb0temp-val30|78.3|// indoor temperature value 30 minutes ago:|:
thb0temp-val60|78.1|// indoor temperature value 60 minutes ago:|:
thb0temp-hmin|78.3|// indoor temperature min of this hour:|:
thb0temp-hmintime|20130317130018|// indoor temperature timestamp min of this hour:|:
thb0temp-hmax|78.4|// indoor temperature max of this hour:|:
thb0temp-hmaxtime|20130317130858|// indoor temperature timestamp max of this hour:|:
thb0temp-dmin|69.6|// indoor temperature min of today:|:
thb0temp-dmintime|20130317070222|// indoor temperature timestamp min of today:|:
thb0temp-dmax|78.4|// indoor temperature max of today:|:
thb0temp-dmaxtime|20130317130858|// indoor temperature timestamp max of today:|:
thb0temp-ydmin|70.5|// indoor temperature min of yesterday:|:
thb0temp-ydmintime|20130316070059|// indoor temperature timestamp min of yesterday:|:
thb0temp-ydmax|78.8|// indoor temperature max of yesterday:|:
thb0temp-ydmaxtime|20130316123859|// indoor temperature timestamp max of yesterday:|:
thb0temp-mmin|67.5|// indoor temperature min of this month:|:
thb0temp-mmintime|20130307065959|// indoor temperature timestamp min of this month:|:
thb0temp-mmax|80.4|// indoor temperature max of this month:|:
thb0temp-mmaxtime|20130308110158|// indoor temperature timestamp max of this month:|:
thb0temp-ymin|67.5|// indoor temperature min of this year:|:
thb0temp-ymintime|20130307065959|// indoor temperature timestamp min of this year:|:
thb0temp-ymax|80.4|// indoor temperature max of this year:|:
thb0temp-ymaxtime|20130308110158|// indoor temperature timestamp max of this year:|:
thb0temp-amin|67.5|// indoor temperature min of all time:|:
thb0temp-amintime|20130307065959|// indoor temperature timestamp min of all time:|:
thb0temp-amax|80.4|// indoor temperature max of all time:|:
thb0temp-amaxtime|20130308110158|// indoor temperature timestamp max of all time:|:
thb0temp-starttime|20130307065959|// indoor temperature timestamp of first recorded data:|:
thb0hum-act|34|// indoor humidity most recent:|:
thb0hum-val5|35|// indoor humidity value 5 minutes ago:|:
thb0hum-val10|35|// indoor humidity value 10 minutes ago:|:
thb0hum-val15|35|// indoor humidity value 15 minutes ago:|:
thb0hum-val30|35|// indoor humidity value 30 minutes ago:|:
thb0hum-val60|34|// indoor humidity value 60 minutes ago:|:
thb0hum-hmin|34|// indoor humidity min of this hour:|:
thb0hum-hmintime|20130317130458|// indoor humidity timestamp min of this hour:|:
thb0hum-hmax|35|// indoor humidity max of this hour:|:
thb0hum-hmaxtime|20130317130018|// indoor humidity timestamp max of this hour:|:
thb0hum-dmin|32|// indoor humidity min of today:|:
thb0hum-dmintime|20130317104623|// indoor humidity timestamp min of today:|:
thb0hum-dmax|40|// indoor humidity max of today:|:
thb0hum-dmaxtime|20130317003858|// indoor humidity timestamp max of today:|:
thb0hum-ydmin|34|// indoor humidity min of yesterday:|:
thb0hum-ydmintime|20130316123221|// indoor humidity timestamp min of yesterday:|:
thb0hum-ydmax|42|// indoor humidity max of yesterday:|:
thb0hum-ydmaxtime|20130316074359|// indoor humidity timestamp max of yesterday:|:
thb0hum-mmin|25|// indoor humidity min of this month:|:
thb0hum-mmintime|20130307101959|// indoor humidity timestamp min of this month:|:
thb0hum-mmax|42|// indoor humidity max of this month:|:
thb0hum-mmaxtime|20130313203208|// indoor humidity timestamp max of this month:|:
thb0hum-ymin|25|// indoor humidity min of this year:|:
thb0hum-ymintime|20130307101959|// indoor humidity timestamp min of this year:|:
thb0hum-ymax|42|// indoor humidity max of this year:|:
thb0hum-ymaxtime|20130313203208|// indoor humidity timestamp max of this year:|:
thb0hum-amin|25|// indoor humidity min of all time:|:
thb0hum-amintime|20130307101959|// indoor humidity timestamp min of all time:|:
thb0hum-amax|42|// indoor humidity max of all time:|:
thb0hum-amaxtime|20130313203208|// indoor humidity timestamp max of all time:|:
thb0hum-starttime|20130307101959|// indoor humidity timestamp of first recorded data:|:
thb0dew-act|47.7|// indoor dewpoint most recent:|:
thb0dew-val5|48.6|// indoor dewpoint value 5 minutes ago:|:
thb0dew-val10|48.4|// indoor dewpoint value 10 minutes ago:|:
thb0dew-val15|48.4|// indoor dewpoint value 15 minutes ago:|:
thb0dew-val30|48.4|// indoor dewpoint value 30 minutes ago:|:
thb0dew-val60|47.5|// indoor dewpoint value 60 minutes ago:|:
thb0dew-hmin|47.7|// indoor dewpoint min of this hour:|:
thb0dew-hmintime|20130317130458|// indoor dewpoint timestamp min of this hour:|:
thb0dew-hmax|48.6|// indoor dewpoint max of this hour:|:
thb0dew-hmaxtime|20130317130858|// indoor dewpoint timestamp max of this hour:|:
thb0dew-dmin|42.1|// indoor dewpoint min of today:|:
thb0dew-dmintime|20130317050558|// indoor dewpoint timestamp min of today:|:
thb0dew-dmax|48.6|// indoor dewpoint max of today:|:
thb0dew-dmaxtime|20130317130858|// indoor dewpoint timestamp max of today:|:
thb0dew-ydmin|44.2|// indoor dewpoint min of yesterday:|:
thb0dew-ydmintime|20130316044002|// indoor dewpoint timestamp min of yesterday:|:
thb0dew-ydmax|51.1|// indoor dewpoint max of yesterday:|:
thb0dew-ydmaxtime|20130316184301|// indoor dewpoint timestamp max of yesterday:|:
thb0dew-mmin|37.0|// indoor dewpoint min of this month:|:
thb0dew-mmintime|20130310045758|// indoor dewpoint timestamp min of this month:|:
thb0dew-mmax|53.2|// indoor dewpoint max of this month:|:
thb0dew-mmaxtime|20130313203208|// indoor dewpoint timestamp max of this month:|:
thb0dew-ymin|37.0|// indoor dewpoint min of this year:|:
thb0dew-ymintime|20130310045758|// indoor dewpoint timestamp min of this year:|:
thb0dew-ymax|53.2|// indoor dewpoint max of this year:|:
thb0dew-ymaxtime|20130313203208|// indoor dewpoint timestamp max of this year:|:
thb0dew-amin|37.0|// indoor dewpoint min of all time:|:
thb0dew-amintime|20130310045758|// indoor dewpoint timestamp min of all time:|:
thb0dew-amax|53.2|// indoor dewpoint max of all time:|:
thb0dew-amaxtime|20130313203208|// indoor dewpoint timestamp max of all time:|:
thb0dew-starttime|20130310045758|// indoor dewpoint timestamp of first recorded data:|:
thb0press-act|29.70|// station pressure most recent:|:
thb0press-val5|29.71|// station pressure value 5 minutes ago:|:
thb0press-val10|29.71|// station pressure value 10 minutes ago:|:
thb0press-val15|29.71|// station pressure value 15 minutes ago:|:
thb0press-val30|29.72|// station pressure value 30 minutes ago:|:
thb0press-val60|29.73|// station pressure value 60 minutes ago:|:
thb0press-hmin|29.70|// station pressure min of this hour:|:
thb0press-hmintime|20130317131650|// station pressure timestamp min of this hour:|:
thb0press-hmax|29.71|// station pressure max of this hour:|:
thb0press-hmaxtime|20130317130018|// station pressure timestamp max of this hour:|:
thb0press-dmin|29.70|// station pressure min of today:|:
thb0press-dmintime|20130317131650|// station pressure timestamp min of today:|:
thb0press-dmax|29.76|// station pressure max of today:|:
thb0press-dmaxtime|20130317090306|// station pressure timestamp max of today:|:
thb0press-ydmin|29.63|// station pressure min of yesterday:|:
thb0press-ydmintime|20130316152646|// station pressure timestamp min of yesterday:|:
thb0press-ydmax|29.73|// station pressure max of yesterday:|:
thb0press-ydmaxtime|20130316235547|// station pressure timestamp max of yesterday:|:
thb0press-mmin|29.43|// station pressure min of this month:|:
thb0press-mmintime|20130308025519|// station pressure timestamp min of this month:|:
thb0press-mmax|29.95|// station pressure max of this month:|:
thb0press-mmaxtime|20130310100145|// station pressure timestamp max of this month:|:
thb0press-ymin|29.43|// station pressure min of this year:|:
thb0press-ymintime|20130308025519|// station pressure timestamp min of this year:|:
thb0press-ymax|29.95|// station pressure max of this year:|:
thb0press-ymaxtime|20130310100145|// station pressure timestamp max of this year:|:
thb0press-amin|29.43|// station pressure min of all time:|:
thb0press-amintime|20130308025519|// station pressure timestamp min of all time:|:
thb0press-amax|29.95|// station pressure max of all time:|:
thb0press-amaxtime|20130310100145|// station pressure timestamp max of all time:|:
thb0press-starttime|20130308025519|// station pressure timestamp of first recorded data:|:
thb0seapress-act|30.10|// sealevel pressure most recent:|:
thb0seapress-val5|30.11|// sealevel pressure value 5 minutes ago:|:
thb0seapress-val10|30.11|// sealevel pressure value 10 minutes ago:|:
thb0seapress-val15|30.11|// sealevel pressure value 15 minutes ago:|:
thb0seapress-val30|30.12|// sealevel pressure value 30 minutes ago:|:
thb0seapress-val60|30.12|// sealevel pressure value 60 minutes ago:|:
thb0seapress-hmin|30.10|// sealevel pressure min of this hour:|:
thb0seapress-hmintime|20130317131650|// sealevel pressure timestamp min of this hour:|:
thb0seapress-hmax|30.11|// sealevel pressure max of this hour:|:
thb0seapress-hmaxtime|20130317130018|// sealevel pressure timestamp max of this hour:|:
thb0seapress-dmin|30.10|// sealevel pressure min of today:|:
thb0seapress-dmintime|20130317131650|// sealevel pressure timestamp min of today:|:
thb0seapress-dmax|30.16|// sealevel pressure max of today:|:
thb0seapress-dmaxtime|20130317090306|// sealevel pressure timestamp max of today:|:
thb0seapress-ydmin|30.03|// sealevel pressure min of yesterday:|:
thb0seapress-ydmintime|20130316152646|// sealevel pressure timestamp min of yesterday:|:
thb0seapress-ydmax|30.12|// sealevel pressure max of yesterday:|:
thb0seapress-ydmaxtime|20130316235547|// sealevel pressure timestamp max of yesterday:|:
thb0seapress-mmin|29.83|// sealevel pressure min of this month:|:
thb0seapress-mmintime|20130308025519|// sealevel pressure timestamp min of this month:|:
thb0seapress-mmax|30.35|// sealevel pressure max of this month:|:
thb0seapress-mmaxtime|20130310100145|// sealevel pressure timestamp max of this month:|:
thb0seapress-ymin|29.83|// sealevel pressure min of this year:|:
thb0seapress-ymintime|20130308025519|// sealevel pressure timestamp min of this year:|:
thb0seapress-ymax|30.35|// sealevel pressure max of this year:|:
thb0seapress-ymaxtime|20130310100145|// sealevel pressure timestamp max of this year:|:
thb0seapress-amin|29.83|// sealevel pressure min of all time:|:
thb0seapress-amintime|20130308025519|// sealevel pressure timestamp min of all time:|:
thb0seapress-amax|30.35|// sealevel pressure max of all time:|:
thb0seapress-amaxtime|20130310100145|// sealevel pressure timestamp max of all time:|:
thb0seapress-starttime|20130308025519|// sealevel pressure timestamp of first recorded data:|:
wind0wind-act|0.9|// windspeed most recent:|:
wind0wind-val5|2.9|// windspeed value 5 minutes ago:|:
wind0wind-val10|4.9|// windspeed value 10 minutes ago:|:
wind0wind-val15|0.0|// windspeed value 15 minutes ago:|:
wind0wind-val30|4.0|// windspeed value 30 minutes ago:|:
wind0wind-val60|4.0|// windspeed value 60 minutes ago:|:
wind0wind-hmin|0.0|// windspeed min of this hour:|:
wind0wind-hmintime|20130317130223|// windspeed timestamp min of this hour:|:
wind0wind-hmax|6.0|// windspeed max of this hour:|:
wind0wind-hmaxtime|20130317130033|// windspeed timestamp max of this hour:|:
wind0wind-dmin|0.0|// windspeed min of today:|:
wind0wind-dmintime|20130317000026|// windspeed timestamp min of today:|:
wind0wind-dmax|11.0|// windspeed max of today:|:
wind0wind-dmaxtime|20130317105442|// windspeed timestamp max of today:|:
wind0wind-ydmin|0.0|// windspeed min of yesterday:|:
wind0wind-ydmintime|20130316000015|// windspeed timestamp min of yesterday:|:
wind0wind-ydmax|12.1|// windspeed max of yesterday:|:
wind0wind-ydmaxtime|20130316153359|// windspeed timestamp max of yesterday:|:
wind0wind-mmin|0.0|// windspeed min of this month:|:
wind0wind-mmintime|20130303162845|// windspeed timestamp min of this month:|:
wind0wind-mmax|32.0|// windspeed max of this month:|:
wind0wind-mmaxtime|20130305212907|// windspeed timestamp max of this month:|:
wind0wind-ymin|0.0|// windspeed min of this year:|:
wind0wind-ymintime|20130303162845|// windspeed timestamp min of this year:|:
wind0wind-ymax|32.0|// windspeed max of this year:|:
wind0wind-ymaxtime|20130305212907|// windspeed timestamp max of this year:|:
wind0wind-amin|0.0|// windspeed min of all time:|:
wind0wind-amintime|20130303162845|// windspeed timestamp min of all time:|:
wind0wind-amax|32.0|// windspeed max of all time:|:
wind0wind-amaxtime|20130305212907|// windspeed timestamp max of all time:|:
wind0wind-starttime|20130303162845|// windspeed timestamp of first recorded data:|:
wind0avgwind-act|0.9|// average windspeed most recent:|:
wind0avgwind-val5|0.9|// average windspeed value 5 minutes ago:|:
wind0avgwind-val10|0.9|// average windspeed value 10 minutes ago:|:
wind0avgwind-val15|2.0|// average windspeed value 15 minutes ago:|:
wind0avgwind-val30|2.0|// average windspeed value 30 minutes ago:|:
wind0avgwind-val60|0.9|// average windspeed value 60 minutes ago:|:
wind0avgwind-hmin|0.9|// average windspeed min of this hour:|:
wind0avgwind-hmintime|20130317130402|// average windspeed timestamp min of this hour:|:
wind0avgwind-hmax|2.0|// average windspeed max of this hour:|:
wind0avgwind-hmaxtime|20130317130000|// average windspeed timestamp max of this hour:|:
wind0avgwind-dmin|0.0|// average windspeed min of today:|:
wind0avgwind-dmintime|20130317000026|// average windspeed timestamp min of today:|:
wind0avgwind-dmax|4.0|// average windspeed max of today:|:
wind0avgwind-dmaxtime|20130317105658|// average windspeed timestamp max of today:|:
wind0avgwind-ydmin|0.0|// average windspeed min of yesterday:|:
wind0avgwind-ydmintime|20130316000015|// average windspeed timestamp min of yesterday:|:
wind0avgwind-ydmax|4.0|// average windspeed max of yesterday:|:
wind0avgwind-ydmaxtime|20130316120101|// average windspeed timestamp max of yesterday:|:
wind0avgwind-mmin|0.0|// average windspeed min of this month:|:
wind0avgwind-mmintime|20130303184700|// average windspeed timestamp min of this month:|:
wind0avgwind-mmax|8.9|// average windspeed max of this month:|:
wind0avgwind-mmaxtime|20130305205026|// average windspeed timestamp max of this month:|:
wind0avgwind-ymin|0.0|// average windspeed min of this year:|:
wind0avgwind-ymintime|20130303184700|// average windspeed timestamp min of this year:|:
wind0avgwind-ymax|8.9|// average windspeed max of this year:|:
wind0avgwind-ymaxtime|20130305205026|// average windspeed timestamp max of this year:|:
wind0avgwind-amin|0.0|// average windspeed min of all time:|:
wind0avgwind-amintime|20130303184700|// average windspeed timestamp min of all time:|:
wind0avgwind-amax|8.9|// average windspeed max of all time:|:
wind0avgwind-amaxtime|20130305205026|// average windspeed timestamp max of all time:|:
wind0avgwind-starttime|20130303184700|// average windspeed timestamp of first recorded data:|:
wind0dir-act|336|// wind direction most recent:|:
wind0dir-val5|311|// wind direction value 5 minutes ago:|:
wind0dir-val10|344|// wind direction value 10 minutes ago:|:
wind0dir-val15|27|// wind direction value 15 minutes ago:|:
wind0dir-val30|342|// wind direction value 30 minutes ago:|:
wind0dir-val60|28|// wind direction value 60 minutes ago:|:
wind0dir-hmin|0|// wind direction min of this hour:|:
wind0dir-hmintime|20130317130624|// wind direction timestamp min of this hour:|:
wind0dir-hmax|359|// wind direction max of this hour:|:
wind0dir-hmaxtime|20130317130630|// wind direction timestamp max of this hour:|:
wind0dir-dmin|0|// wind direction min of today:|:
wind0dir-dmintime|20130317000026|// wind direction timestamp min of today:|:
wind0dir-dmax|359|// wind direction max of today:|:
wind0dir-dmaxtime|20130317102050|// wind direction timestamp max of today:|:
wind0dir-ydmin|0|// wind direction min of yesterday:|:
wind0dir-ydmintime|20130316103650|// wind direction timestamp min of yesterday:|:
wind0dir-ydmax|359|// wind direction max of yesterday:|:
wind0dir-ydmaxtime|20130316101655|// wind direction timestamp max of yesterday:|:
wind0dir-mmin|0|// wind direction min of this month:|:
wind0dir-mmintime|20130303162747|// wind direction timestamp min of this month:|:
wind0dir-mmax|359|// wind direction max of this month:|:
wind0dir-mmaxtime|20130303164439|// wind direction timestamp max of this month:|:
wind0dir-ymin|0|// wind direction min of this year:|:
wind0dir-ymintime|20130303162747|// wind direction timestamp min of this year:|:
wind0dir-ymax|359|// wind direction max of this year:|:
wind0dir-ymaxtime|20130303164439|// wind direction timestamp max of this year:|:
wind0dir-amin|0|// wind direction min of all time:|:
wind0dir-amintime|20130303162747|// wind direction timestamp min of all time:|:
wind0dir-amax|359|// wind direction max of all time:|:
wind0dir-amaxtime|20130303164439|// wind direction timestamp max of all time:|:
wind0dir-starttime|20130303162747|// wind direction timestamp of first recorded data:|:
wind0chill-act|66.6|// outdoor wind chill temperature most recent:|:
wind0chill-val5|--|// outdoor wind chill temperature value 5 minutes ago:|:
wind0chill-val10|--|// outdoor wind chill temperature value 10 minutes ago:|:
wind0chill-val15|--|// outdoor wind chill temperature value 15 minutes ago:|:
wind0chill-val30|--|// outdoor wind chill temperature value 30 minutes ago:|:
wind0chill-val60|--|// outdoor wind chill temperature value 60 minutes ago:|:
wind0chill-hmin|65.7|// outdoor wind chill temperature min of this hour:|:
wind0chill-hmintime|20130317130117|// outdoor wind chill temperature timestamp min of this hour:|:
wind0chill-hmax|66.6|// outdoor wind chill temperature max of this hour:|:
wind0chill-hmaxtime|20130317131650|// outdoor wind chill temperature timestamp max of this hour:|:
wind0chill-dmin|40.6|// outdoor wind chill temperature min of today:|:
wind0chill-dmintime|20130317072025|// outdoor wind chill temperature timestamp min of today:|:
wind0chill-dmax|66.6|// outdoor wind chill temperature max of today:|:
wind0chill-dmaxtime|20130317131650|// outdoor wind chill temperature timestamp max of today:|:
wind0chill-ydmin|47.1|// outdoor wind chill temperature min of yesterday:|:
wind0chill-ydmintime|20130316075254|// outdoor wind chill temperature timestamp min of yesterday:|:
wind0chill-ydmax|73.9|// outdoor wind chill temperature max of yesterday:|:
wind0chill-ydmaxtime|20130316150944|// outdoor wind chill temperature timestamp max of yesterday:|:
wind0chill-mmin|37.6|// outdoor wind chill temperature min of this month:|:
wind0chill-mmintime|20130307052439|// outdoor wind chill temperature timestamp min of this month:|:
wind0chill-mmax|79.5|// outdoor wind chill temperature max of this month:|:
wind0chill-mmaxtime|20130313170754|// outdoor wind chill temperature timestamp max of this month:|:
wind0chill-ymin|37.6|// outdoor wind chill temperature min of this year:|:
wind0chill-ymintime|20130307052439|// outdoor wind chill temperature timestamp min of this year:|:
wind0chill-ymax|79.5|// outdoor wind chill temperature max of this year:|:
wind0chill-ymaxtime|20130313170754|// outdoor wind chill temperature timestamp max of this year:|:
wind0chill-amin|37.6|// outdoor wind chill temperature min of all time:|:
wind0chill-amintime|20130307052439|// outdoor wind chill temperature timestamp min of all time:|:
wind0chill-amax|79.5|// outdoor wind chill temperature max of all time:|:
wind0chill-amaxtime|20130313170754|// outdoor wind chill temperature timestamp max of all time:|:
wind0chill-starttime|20130307052439|// outdoor wind chill temperature timestamp of first recorded data:|:
rain0rate-act|0.00|// rain rate most recent:|:
rain0rate-val5|0.00|// rain rate value 5 minutes ago:|:
rain0rate-val10|0.00|// rain rate value 10 minutes ago:|:
rain0rate-val15|0.00|// rain rate value 15 minutes ago:|:
rain0rate-val30|0.00|// rain rate value 30 minutes ago:|:
rain0rate-val60|0.00|// rain rate value 60 minutes ago:|:
rain0rate-hmin|0.00|// rain rate min of this hour:|:
rain0rate-hmintime|20130317130019|// rain rate timestamp min of this hour:|:
rain0rate-hmax|0.00|// rain rate max of this hour:|:
rain0rate-hmaxtime|20130317130019|// rain rate timestamp max of this hour:|:
rain0rate-dmin|0.00|// rain rate min of today:|:
rain0rate-dmintime|20130317000026|// rain rate timestamp min of today:|:
rain0rate-dmax|0.00|// rain rate max of today:|:
rain0rate-dmaxtime|20130317000026|// rain rate timestamp max of today:|:
rain0rate-ydmin|0.00|// rain rate min of yesterday:|:
rain0rate-ydmintime|20130316000015|// rain rate timestamp min of yesterday:|:
rain0rate-ydmax|0.00|// rain rate max of yesterday:|:
rain0rate-ydmaxtime|20130316000015|// rain rate timestamp max of yesterday:|:
rain0rate-mmin|0.00|// rain rate min of this month:|:
rain0rate-mmintime|20130303162725|// rain rate timestamp min of this month:|:
rain0rate-mmax|0.57|// rain rate max of this month:|:
rain0rate-mmaxtime|20130306015014|// rain rate timestamp max of this month:|:
rain0rate-ymin|0.00|// rain rate min of this year:|:
rain0rate-ymintime|20130303162725|// rain rate timestamp min of this year:|:
rain0rate-ymax|0.57|// rain rate max of this year:|:
rain0rate-ymaxtime|20130306015014|// rain rate timestamp max of this year:|:
rain0rate-amin|0.00|// rain rate min of all time:|:
rain0rate-amintime|20130303162725|// rain rate timestamp min of all time:|:
rain0rate-amax|0.57|// rain rate max of all time:|:
rain0rate-amaxtime|20130306015014|// rain rate timestamp max of all time:|:
rain0rate-starttime|20130303162725|// rain rate timestamp of first recorded data:|:
rain0total-act|17.83|// rain most recent:|:
rain0total-val5|17.83|// rain value 5 minutes ago:|:
rain0total-val10|17.83|// rain value 10 minutes ago:|:
rain0total-val15|17.83|// rain value 15 minutes ago:|:
rain0total-val30|17.83|// rain value 30 minutes ago:|:
rain0total-val60|17.83|// rain value 60 minutes ago:|:
rain0total-hmin|17.83|// rain min of this hour:|:
rain0total-hmintime|20130303162725|// rain timestamp min of this hour:|:
rain0total-hmax|0.00|// rain max of this hour:|:
rain0total-hmaxtime|20130303162725|// rain timestamp max of this hour:|:
rain0total-dmin|17.83|// rain min of today:|:
rain0total-dmintime|20130303162725|// rain timestamp min of today:|:
rain0total-dmax|0.00|// rain max of today:|:
rain0total-dmaxtime|20130303162725|// rain timestamp max of today:|:
rain0total-ydmin|17.83|// rain min of yesterday:|:
rain0total-ydmintime|--|// rain timestamp min of yesterday:|:
rain0total-ydmax|0.00|// rain max of yesterday:|:
rain0total-ydmaxtime|--|// rain timestamp max of yesterday:|:
rain0total-mmin|17.83|// rain min of this month:|:
rain0total-mmintime|20130303162725|// rain timestamp min of this month:|:
rain0total-mmax|1.33|// rain max of this month:|:
rain0total-mmaxtime|20130303162725|// rain timestamp max of this month:|:
rain0total-ymin|17.83|// rain min of this year:|:
rain0total-ymintime|20130303162725|// rain timestamp min of this year:|:
rain0total-ymax|1.33|// rain max of this year:|:
rain0total-ymaxtime|20130303162725|// rain timestamp max of this year:|:
rain0total-amin|17.83|// rain min of all time:|:
rain0total-amintime|20130303162725|// rain timestamp min of all time:|:
rain0total-amax|1.33|// rain max of all time:|:
rain0total-amaxtime|20130303162725|// rain timestamp max of all time:|:
rain0total-starttime|20130303162725|// rain timestamp of first recorded data:|:
uv0index-act|6.4|// uv index most recent:|:
uv0index-val5|6.5|// uv index value 5 minutes ago:|:
uv0index-val10|6.5|// uv index value 10 minutes ago:|:
uv0index-val15|6.0|// uv index value 15 minutes ago:|:
uv0index-val30|6.3|// uv index value 30 minutes ago:|:
uv0index-val60|5.8|// uv index value 60 minutes ago:|:
uv0index-hmin|6.0|// uv index min of this hour:|:
uv0index-hmintime|20130317130234|// uv index timestamp min of this hour:|:
uv0index-hmax|6.5|// uv index max of this hour:|:
uv0index-hmaxtime|20130317130643|// uv index timestamp max of this hour:|:
uv0index-dmin|0.0|// uv index min of today:|:
uv0index-dmintime|20130317000026|// uv index timestamp min of today:|:
uv0index-dmax|6.5|// uv index max of today:|:
uv0index-dmaxtime|20130317125758|// uv index timestamp max of today:|:
uv0index-ydmin|0.0|// uv index min of yesterday:|:
uv0index-ydmintime|20130316000015|// uv index timestamp min of yesterday:|:
uv0index-ydmax|6.7|// uv index max of yesterday:|:
uv0index-ydmaxtime|20130316130143|// uv index timestamp max of yesterday:|:
uv0index-mmin|0.0|// uv index min of this month:|:
uv0index-mmintime|20130303164455|// uv index timestamp min of this month:|:
uv0index-mmax|7.5|// uv index max of this month:|:
uv0index-mmaxtime|20130315123642|// uv index timestamp max of this month:|:
uv0index-ymin|0.0|// uv index min of this year:|:
uv0index-ymintime|20130303164455|// uv index timestamp min of this year:|:
uv0index-ymax|7.5|// uv index max of this year:|:
uv0index-ymaxtime|20130315123642|// uv index timestamp max of this year:|:
uv0index-amin|0.0|// uv index min of all time:|:
uv0index-amintime|20130303164455|// uv index timestamp min of all time:|:
uv0index-amax|7.5|// uv index max of all time:|:
uv0index-amaxtime|20130315123642|// uv index timestamp max of all time:|:
uv0index-starttime|20130303164455|// uv index timestamp of first recorded data:|:
sol0rad-act|776|// solar rad most recent:|:
sol0rad-val5|825|// solar rad value 5 minutes ago:|:
sol0rad-val10|822|// solar rad value 10 minutes ago:|:
sol0rad-val15|785|// solar rad value 15 minutes ago:|:
sol0rad-val30|795|// solar rad value 30 minutes ago:|:
sol0rad-val60|771|// solar rad value 60 minutes ago:|:
sol0rad-hmin|720|// solar rad min of this hour:|:
sol0rad-hmintime|20130317130314|// solar rad timestamp min of this hour:|:
sol0rad-hmax|827|// solar rad max of this hour:|:
sol0rad-hmaxtime|20130317131334|// solar rad timestamp max of this hour:|:
sol0rad-dmin|0|// solar rad min of today:|:
sol0rad-dmintime|20130317000026|// solar rad timestamp min of today:|:
sol0rad-dmax|827|// solar rad max of today:|:
sol0rad-dmaxtime|20130317131334|// solar rad timestamp max of today:|:
sol0rad-ydmin|0|// solar rad min of yesterday:|:
sol0rad-ydmintime|20130316000015|// solar rad timestamp min of yesterday:|:
sol0rad-ydmax|867|// solar rad max of yesterday:|:
sol0rad-ydmaxtime|20130316120903|// solar rad timestamp max of yesterday:|:
sol0rad-mmin|0|// solar rad min of this month:|:
sol0rad-mmintime|20130303180445|// solar rad timestamp min of this month:|:
sol0rad-mmax|934|// solar rad max of this month:|:
sol0rad-mmaxtime|20130308113948|// solar rad timestamp max of this month:|:
sol0rad-ymin|0|// solar rad min of this year:|:
sol0rad-ymintime|20130303180445|// solar rad timestamp min of this year:|:
sol0rad-ymax|934|// solar rad max of this year:|:
sol0rad-ymaxtime|20130308113948|// solar rad timestamp max of this year:|:
sol0rad-amin|0|// solar rad min of all time:|:
sol0rad-amintime|20130303180445|// solar rad timestamp min of all time:|:
sol0rad-amax|934|// solar rad max of all time:|:
sol0rad-amaxtime|20130308113948|// solar rad timestamp max of all time:|:
sol0rad-starttime|20130303180445|// solar rad timestamp of first recorded data:|:
rain0total-daysum|0.00|// rain total today:|:
rain0total-monthsum|1.33|// rain total this month:|:
rain0total-ydaysum|0.00|// rain total yesterday:|:
rain0total-sum60|0.00|// rain total last 60 minutes:|:
'; // END_OF_RAW_DATA_LINES;

// end of generation script

// put data in  array
//
$WX = array();
global $WX;
$WXComment = array();
$data = explode(":|:",$rawdatalines);
$nscanned = 0;
foreach ($data as $v => $line) {
  list($vname,$vval,$vcomment) = explode("|",trim($line).'|||');
  if ($vname <> "" and strpos($vval,'$') === false) {
    $WX[$vname] = trim($vval);
    if($vcomment <> "") { $WXComment[$vname] = trim($vcomment); }
  }
  $nscanned++;
}
if(isset($_REQUEST['debug'])) {
  print "<!-- loaded $nscanned $WXsoftware \$WX[] entries -->\n";
}

if (isset($_REQUEST["sce"]) and strtolower($_REQUEST["sce"]) == "dump" ) {

  print "<pre>\n";
  print "// \$WX[] array size = $nscanned entries.\n";
  foreach ($WX as $key => $val) {
	  $t =  "\$WX['$key'] = '$val';";
	  if(isset($WXComment[$key])) {$t .=  " $WXComment[$key]"; }
	  print "$t\n";
  }
  print "</pre>\n";

}
if(file_exists("MB-defs.php")) { include_once("MB-defs.php"); }
?>