<?php
/*
Copyright 2006-2011
Author: Thorben Kröger <thorbenk@gmx.net>
        Laurent Bovet <laurent.bovet@windmaster.ch>

This file is part of phpdigikam

phpdigikam is free software; you can redistribute it
and/or modify it under the terms of the GNU General
Public License as published by the Free Software Foundation;
either version 2, or (at your option)
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

/**
 * Main file.
 * Everything is handled in class Photoalbum.
 */

if(!file_exists('inc/config.inc.php') && !strstr($_SERVER['REQUEST_URI'], 'setup')) {
	$scriptname = substr(strrchr($_SERVER['SCRIPT_NAME'], '/'),1);
	$url = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
	header("Location: $url/$scriptname/setup");
	exit;
}

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('UTC');

require_once('inc/photoalbum.inc.php');
$album = new Photoalbum();

?>
