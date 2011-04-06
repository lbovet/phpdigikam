<?php
// C O N F I G / / / / / / / / / / / / 

//Language
require_once('lang/en.lang.php');

//Albums to hide
$_config['restrictedAlbums'] = "Albums.id NOT IN (1, 3, 5, 15, 50, 76, 103, 116, 159, 186)";

// tags to hide
$_config['restrictedTags'] = "tagid=154";

//Paths
$_config['digikamDb'] = "/Users/lolo/Sites/pictures/images/digikam4.db";
$_config['photosPath'] = "/Volumes/Trop Dur/Images/Library/";
$_config['thumbnails'] = "/Volumes/Trop Dur/Images/.library-thumbs/";
$_config['convertBin'] = "/usr/bin/convert";
$_config['exifBin'] = "/usr/bin/exif";
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
