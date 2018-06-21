<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (World-ML template set)
############################################################################
#
#	Project:	Sample Included Website Design
#	Module:		Settings-weather.php
#	Purpose:	Provides the Site Settings Used Throughout the Site
# 	Authors:	Kevin W. Reed <kreed@tnet.com>
#				TNET Services, Inc.
#               Ken True <webmaster@saratoga-weather.org>
#               Saratoga-Weather.org
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
// Version 1.01 - 28-Sep-2011 - added $SITE['yesterdayData'] to settings
// Version 1.02 - 31-Mar-2012 - added $SITE['overrideRain'] to settings
// Version 1.03 - 08-Mar-2013 - adapted for Meteobridge
global $SITE;
#---------------------------------------------------------------------------
#  required settings for Meteobridge software
#---------------------------------------------------------------------------
$SITE['WXtags']        = './MBtags.php';  // for weather variables
$SITE['ajaxScript']    = 'ajaxMBwx.js'; // for AJAX enabled display
// relative directory and name of Meteobridge MBrealtime.txt file uploaded via HTTP Push
// to conds.php script on your weather website
$SITE['realtimefile'] = './MBrealtime.txt';
#
$SITE['conditionsMETAR'] = 'KSJC';  // set to nearby METAR for current conditions icon/text
//  comment out conditionsMETAR if no nearby METAR.. conditions icon/text will not be displayed
$SITE['overrideRain']   = true; // =true then rain rate will set rain words instead of METAR rain words
#                               // =false - no change to METAR rain words (station rain rate not used)
$SITE['DavisVP']  = true;  // set to false if not a Davis VP weather station
$SITE['UV']       = true;  // set to false if no UV sensor
$SITE['SOLAR']    = true;  // set to false if no Solar sensor
##########################################################################
# end of configurable settings
#
// default settings needed for various pages when the weather software plugin is not installed.
// do not change these
$SITE['WXsoftware']     = 'MB';  // 'WD' for Weather-Display, 'CU' for Virtual Weather Station,
                                 // 'CU' for Cumulus, 'CU' for Cumulus, 'WXS' for WXSolution
                                 // 'MH' for Meteohub
$SITE['WXsoftwareURL'] = 'http://www.meteobridge.com/wiki/index.php/Main_Page';
$SITE['WXsoftwareLongName'] = 'Meteobridge';
$SITE['ajaxDashboard'] = './ajax-dashboard.php';
$SITE['trendsPage']     = 'MB-trends-inc.php'; // CU-specific trends page
?>