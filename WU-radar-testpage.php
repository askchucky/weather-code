<?php
// Version 1.01 - 21-Feb-2017 - added improved Saratoga template support

global $SITE;
$doSWXTemplate = (file_exists("Settings.php") and 
                  file_exists("include-style-switcher.php") and 
				  file_exists("common.php"))?true:false;
if($doSWXTemplate) {
	include_once("Settings.php");
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>WU-radar testpage</title>
</head>

<body style="font-family:Arial, Helvetica, sans-serif;color: #0000FF;background-color: #FFFFCC">
<?php if(!isset($_REQUEST['show'])) { ?>
<p>Test page for WU-radar-inc.php.  Use <a href="?show=loc#WUtop"><strong>this link</strong></a> to start script with location setting aid enabled.</p>
<?php } /* end Show the reminder */ ?>
<div style="width: 640px;background-color: #000000;color:#FFFFFF; font-size: 14px">
<?php include("WU-radar-inc.php"); ?>
</div>
</body>
</html>
