README file for Base-USA integration of Curly's nws-alerts package to the template set.
Version 1.00 - 28-Jul-2012 - initial release

My thanks to Curly at http://www.weather.ricksturf.com/ for graciously allowing his nws-alerts
script package to be included in the Base-USA distribution.  This script set is a replacement
for his original AtomFeed set of scripts and runs with or without cron support.  If you are
using more than 4 warning/county zones in the list, you will receive a message on your page
to nag you to run the nws-alerts.php via cron (if you haven't already configured it to run via cron).
For 5 or more zones, running nws-alerts via cron is highly recommended to keep the loading of your
pages fast (no waiting for the NWS website to respond with the current alerts you selected).

To enable cron operation, first setup a cron to run nws-alerts.php every 5 minutes.
On linux/unix servers, a crontab entry like:

*/5 * * * * /usr/bin/php5 -q /path/to/your/htdocs/nws-alerts.php

Note: change '/usr/bin/php5' to the correct location for PHP Version 5 on your webserver 
change '/path/to/your/htdocs' to the path of your document root of your template website.

For Windows , please use Curly's guide for setting up the scheduler for this:
You will need to set up a cron job, use the Windows Task Scheduler, or a method that you prefer, to call the nws-alerts.php file at specified intervals. Since each web host has there own way of setting up a cron job, you will need to find out how to do this by getting the directions from your web host.
Using the Windows Task Scheduler has been amazingly successful in doing this.
Sample are available here: 
For WinXP     http://www.weather.ricksturf.com//scripts/SetUp_XPcron.zip
For WinVISTA  http://www.weather.ricksturf.com//scripts/SetUp_VistaCron.zip

After the cron is set up and running, then change nws-alerts-config.php

$noCron        = true;                     // true=not using cron, update data when cache file expires   

to

$noCron        = false;                     // true=not using cron, update data when cache file expires 

and upload the changed nws-alerts-config.php to your website.
You should be able to see the results (status) at
http://your.weather.website/cache/nws-notes.txt

Ken True - 28-Jul-2012


Changes to Base-USA template files:

------------ Settings.php ------------

a new section is added to specify the NWS warning zone/county zones to be used
-------
// NWS Alerts package configuration (for Curly's nws-alerts scripts)
// "Location|ZoneCode|CountyCode[|CountyCode]..."
// Note: if more than 4 zone/county codes are used, a message will appear if you are NOT using
//    cron to provide updates.
// Note: additional/optional nws-alerts configuration is in nws-alerts-config.php file
$SITE['NWSalertsCodes'] = array(
  "Santa Clara Valley|CAZ513|CAC085",
//  "Santa Cruz Mtns|CAZ512|CAC081|CAC085|CAC087",
  "Santa Cruz|CAZ529|CAC087",
//  "Monterey|CAZ530|CAC053",
//  "South/East Bay|CAZ508|CAC081",
//  "San Mateo Coast|CAZ509|CAC081",
//  "San Francisco|CAZ006|CAC075"
);
$SITE['NWSalertsSidebar'] = true; // =true to insert in menubar, =false no insert to menubar
// 
-------

------------ menubar.php ------------

added support for the sidebar alert icons from nws-alerts 

-------
<?php if(
      isset($SITE['NWSalertsSidebar']) and $SITE['NWSalertsSidebar'] and
      isset($SITE['NWSalertsCodes']) and count($SITE['NWSalertsCodes']) > 0) { ?>
<!-- nws-alerts icons -->
<p class="sideBarTitle" style="text-align:center"><?php langtrans('Alerts'); ?></p>
<?php
include_once("nws-alerts-config.php"); // include the config file
include($cacheFileDir.$iconFileName); // include the big icon file
// construct menu bar icons
$bigIcos = '<div style="text-align:center">'."\n";
foreach($bigIcons as $bigI) {
$bigIcos .= $bigI;
}
$bigIcos .= " <br />\n</div>\n<!-- end nws-alerts icons -->\n";
echo $bigIcos; ?>
<?php } // end of NWS alerts sidebar ?>
<!-- end external links -->
-------

------------ header.php ------------

added support for noCron=true running of nws-alerts

-------
require_once("common.php");

// add support for noCron=true fetch of nws-alerts to get current alerts
    if(isset($SITE['NWSalertsCodes']) and count($SITE['NWSalertsCodes']) > 0) {
		include_once("nws-alerts-config.php"); // load the configuration for nws-alerts
		if(isset($noCron) and $noCron) {
			print "<!-- nws-alerts noCron=true .. running nws-alerts.php inline -->\n";
			include_once("nws-alerts.php");
		}
	}
-------

------------ wxindex.php ------------

added support for nws-alerts to display as replacement for atom-advisory/atom-top-warning scripts

-------
<div id="main-copy">
    
<?php // insert desired warning box at top of page

  if(isset($SITE['NWSalertsCodes']) and count($SITE['NWSalertsCodes']) > 0) {
	// Add nws-alerts alert box cache file
	include_once("nws-alerts-config.php");
	include($cacheFileDir.$aboxFileName);
	// Insert nws-alerts alert box
	echo $alertBox;
	?>
<script type="text/javascript" src="nws-alertmap.js"></script>
<?php
	  
  } else { // use atom scripts of choice
	if ($useTopWarning) {
	  include_once("atom-top-warning.php");
	} else {
	 print "      <div class=\"advisoryBox\">\n";
	 $_REQUEST['inc'] = 'y';
	 $_REQUEST['summary'] = 'Y';
	 include_once("atom-advisory.php");
	 print "      </div>\n";
	}
  }
?>

-------


------------ wxadvisory.php ------------

added support for nws-alerts to display as replacement for atom-advisory/atom-top-warning scripts
-------
<div id="main-copy">
 
<?php // insert desired warning box at top of page

  if(isset($SITE['NWSalertsCodes']) and count($SITE['NWSalertsCodes']) > 0) {
    include_once("nws-alerts-summary-inc.php");  

  } else { // use atom scripts of choice
?> 
	  <h3>Watches, Warnings, and Advisories</h3> 
        
    <div class="advisoryBox" style="text-align: left; background-color:#FFFF99">
	<?php 
	   $_REQUEST['inc'] = 'y';
	   include("atom-advisory.php");
	 ?>
	</div>

<?php } // end nws-alerts / original atom alerts selection ?>

<img src="http://maps.wunderground.com/data/severe/current_severe_nostatefarm.gif?dontcache=y" width="630" height="480" border="0" alt="national advisories"/>

</div><!-- end main-copy -->
-------

------------ new files added to Base-USA distribution ------------

nws-alerts-config.php  - additional/optional configuration for nws-alerts.php
nws-alerts.php
nws-alerts-summary-inc.php
nws-alerts-details-inc.php
wxnws-details.php
nws-shapefile.txt
nws-alertmap.js
nws-rssfeed.xml (created by nws-alerts.php)

./cache/ files created by nws-alerts.php script:
  nws-alertsBoxData.php
  nws-alertsIconData.php
  nws-alertsMainData.php
  nws-notes.txt
  
./alert-images/ graphics files added/updated
  A-advisory.png
  A-air.png
  A-alert.png
  A-none.png
  A-state.png
  A-statement.png
  A-warn.png
  A-watch.png
  BNK.gif
  NOAAlogo1.png
  
------- end of README file --------
