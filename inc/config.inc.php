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
// C O N F I G / / / / / / / / / / / / 

//Language
require_once('lang/en.lang.php');

//Albums to hide
$_config['restrictedAlbums'] = "Albums.id NOT IN (1, 3, 5, 15, 50, 76, 103, 116, 159, 186)";

// tags to hide
$_config['restrictedTags'] = "tagid=154";

//Paths

// The database file
$_config['digikamDb'] = "/Users/lolo/Sites/pictures/images/digikam4.db";

// Where the photos are
$_config['photosPath'] = "/Volumes/Trop Dur/Images/Library/";

// Where the thumbnails are (if you copy them from ~/.thumbnails/large)
// or where they will be created
$_config['thumbnails'] = "/Volumes/Trop Dur/Images/.library-thumbs/";

// Utilities
$_config['convertBin'] = "/usr/bin/convert";
$_config['exifBin'] = "/usr/bin/exif";

// Leading path of the actual photo directory to compute the correct thumb hash
$_config['thumbHashPath'] = "/home/shaman/Pictures/Library2/";

//Image and thumbnail sizes
$_config['thumbSize'] = "240";
$_config['imageSize'] = "720";

//Layout
$_config['numCols'] = "4";
$_config['photosPerPage'] = "40";
// / / / / / / / / / / / / / / / / / / 

//These should be automatically correct
$_config['selfDir']=substr($_SERVER['SCRIPT_FILENAME'], 0,
		                          strrpos($_SERVER['SCRIPT_FILENAME'], '/'));
$_config['selfUrl']=substr($_SERVER['SCRIPT_NAME'], 0,
		                          strrpos($_SERVER['SCRIPT_NAME'], '/'));
$_config['scriptname']=substr(strrchr($_SERVER['SCRIPT_NAME'], '/'),1);
?>
