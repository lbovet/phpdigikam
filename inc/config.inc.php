<?php
// C O N F I G / / / / / / / / / / / / 

//Language
require_once('lang/en.lang.php');

//Albums to show
$_config['restrictedAlbums'] = "Albums.id IN (1,2,3,4,5,6,7)";
//Paths
$_config['digikamDb'] = "/home/shaman/photokam/out2/digikam3.db";
$_config['photosPath'] = "/home/shaman/photokam/out2";
$_config['convertBin'] = "/usr/bin/convert";
$_config['exifBin'] = "/usr/bin/exif";
//Image and thumbnail sizes
$_config['thumbSize'] = "240";
$_config['imageSize'] = "720";
//Layout
$_config['numCols'] = "3";
$_config['photosPerPage'] = "12";
// / / / / / / / / / / / / / / / / / / 

//These should be automatically correct
$_config['selfDir']=substr($_SERVER['SCRIPT_FILENAME'], 0,
		                          strrpos($_SERVER['SCRIPT_FILENAME'], '/'));
$_config['selfUrl']=substr($_SERVER['SCRIPT_NAME'], 0,
		                          strrpos($_SERVER['SCRIPT_NAME'], '/'));
$_config['scriptname']=substr(strrchr($_SERVER['SCRIPT_NAME'], '/'),1);
?>
