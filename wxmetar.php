<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (Canada/World-ML template set)
############################################################################
#
#   Project:    Sample Included Website Design
#   Module:     sample.php
#   Purpose:    Sample Page
#   Authors:    Kevin W. Reed <kreed@tnet.com>
#               TNET Services, Inc.
#
# 	Copyright:	(c) 1992-2007 Copyright TNET Services, Inc.
############################################################################
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA
############################################################################
#	This document uses Tab 4 Settings
############################################################################
# Version 1.00 - 17-Nov-2011 - initial release
# Version 1.01 - 27-Nov-2011 - display 'Distance to station' mods
#
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE = langtransstr($SITE['organ']) . " - " .langtransstr('Nearby METAR Reports');
$showGizmo = true;  // set to false to exclude the gizmo
include("top.php");
############################################################################
?>
<style type="text/css">
.bidi {
	unicode-bidi: embed;
}
</style>
</head>
<body>
<?php
############################################################################
include("header.php");
############################################################################
include("menubar.php");
############################################################################
?>

<div id="main-copy">
  
	<h1><?php langtrans('Nearby METAR Reports'); ?></h1>
    <p>&nbsp;</p>

<?php
// Customize this list with your nearby METARs by
// using http://saratoga-weather.org/wxtemplates/find-metar.php to create the list below

$MetarList = array( // set this list to your local METARs 
 // Metar(ICAO) | Name of station | dist-mi | dist-km | direction |
  'KSJC|San Jose, California, USA|9|14|NE|', // lat=37.3667,long=-121.9167, elev=24, dated=30-SEP-11
  'KNUQ|Moffett Nas/Mtn, California, USA|9|14|N|', // lat=37.4000,long=-122.0500, elev=12, dated=30-SEP-11
  'KPAO|Palo Alto, California, USA|14|23|NNW|', // lat=37.4667,long=-122.1167, elev=2, dated=30-SEP-11
  'KRHV|San Jose/Reid, California, USA|12|19|ENE|', // lat=37.3167,long=-121.8167, elev=41, dated=30-SEP-11
  'KOAK|Oakland, California, USA|31|50|NNW|', // lat=37.7000,long=-122.2167, elev=26, dated=30-SEP-11
  'KSQL|San Carlos Airpo, California, USA|21|34|NW|', // lat=37.5167,long=-122.2500, elev=1, dated=30-SEP-11
  'KSFO|San Francisco, California, USA|30|49|NW|', // lat=37.6167,long=-122.3667, elev=3, dated=30-SEP-11
  'KE16|San Martin, California, USA|27|43|ESE|', // lat=37.0833,long=-121.6000, elev=86, dated=30-SEP-11
  'KWVI|Watsonville, California, USA|27|44|SSE|', // lat=36.9333,long=-121.7833, elev=43, dated=30-SEP-11
  'KHAF|Half Moon Bay, California, USA|31|50|WNW|', // lat=37.5167,long=-122.5000, elev=21, dated=30-SEP-11
  'KHWD|Hayward, California, USA|28|44|N|', // lat=37.6667,long=-122.1167, elev=21, dated=30-SEP-11
  'KLVK|Livermore, California, USA|31|51|NNE|', // lat=37.7000,long=-121.8167, elev=117, dated=30-SEP-11
// list generated Sun, 27-Nov-2011 9:01pm PST at http://saratoga-weather.org/wxtemplates/find-metar.php
);
$maxAge = 75*60; // max age for metar in seconds = 75 minutes
// end of customizations
#
# Note: you do not need to change the below settings .. your current values from Settings.php
# will be applied and replace what you change below.
#
$condIconDir = './ajax-images/';  // directory for ajax-images with trailing slash
$condIconType = '.jpg'; // default type='.jpg' -- use '.gif' for animated icons from http://www.meteotreviglio.com/
$uomTemp = '&deg;F';
$uomBaro = ' inHg';
$uomWind = ' mph';
$uomRain = ' in';
// optional settings for the Wind Rose graphic in ajaxwindiconwr as wrName . winddir . wrType
$wrName   = 'wr-';       // first part of the graphic filename (followed by winddir to complete it)
$wrType   = '.png';      // extension of the graphic filename
$wrHeight = '58';        // windrose graphic height=
$wrWidth  = '58';        // windrose graphic width=
$wrCalm   = 'wr-calm.png';  // set to full name of graphic for calm display ('wr-calm.gif')
$Lang = 'en'; // default language used (for Windrose display)
?>
<?php
  if(file_exists("include-metar-display.php")) {
	  include_once("include-metar-display.php");
  } else {
	  print "<p>Sorry.. include-metar-display.php not found</p>\n";
  }
?>
    
</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>
