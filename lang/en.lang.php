<?php
/*
Author: Thorben Kröger <thorbenk@gmx.net>

This program is free software; you can redistribute it
and/or modify it under the terms of the GNU General
Public License as published by the Free Software Foundation;
either version 2, or (at your option)
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

//Main
$i18n['photos'] =
	'Photos';
$i18n['executeUpdateScript'] = 
	'Please execute &quot;update.sh&quot; to update thumbnails.';
$i18n['debugFooter'] =
	'Took %s SQLite queries and %s seconds to generate.';
$i18n['noImagesOnPage'] =
	'No images on this page!';
$i18n['noSuchTag'] =
	'No such tag or album, access restricted or page does not exist';
$i18n['photoAlbums'] =
	'Photo Albums';
$i18n['imagesInAlbum'] =
	'Images in Album';
$i18n['imagesWithTag'] =
	'Images with Tag';
$i18n['Tags'] =
	'Tags';

//Setup
$i18n['Continue'] =
	'Continue...';
$i18n['selectLanguage'] =
	'Select a language:';
$i18n['setup_digikamDbPath'] =
	'Absolute path to the digikam database<br />
	(usually called digikam3.db and located in your digikam photos folder)';
$i18n['setup_photosPath'] =
	'Absolute path to the digikam photos folder';
$i18n['setup_convertBin'] =
	'Absolute path to the &quot;convert&quot; (Image Magick) executable';
$i18n['setup_exifBin'] =
	'Absolute path to the &quot;exif&quot; executable';
$i18n['setup_thumbSize'] =
	'Width of thumbnails (pixel)';
$i18n['setup_imagesSize'] =
	'Width of images (pixel)';
$i18n['setup_numCols'] =
	'Number of columns';
$i18n['setup_photosPerPage'] =
	'Number of photos per album page';
$i18n['setup_fillOutForm'] =
	'Please fill out the form below and click &quot;Continue&quot;';
$i18n['dbConnectSuccess'] =
	'Database connection successfully established :-)';
$i18n['dbConnectFailure'] =
	'Database connection could NOT be established.';
$i18n['setup_CheckAlbumsToShow'] =
	'Check the albums you want to show:';
$i18n['writeConfigFailure'] =
	'Could not write &quot;config.inc.php&quot; file. Save it yourself
	 in folder &quot;inc/&quot;';
$i18n['reloadPage'] =
	'You then need to reload this page.';
$i18n['writeConfigSuccess'] =
	'This has been written as your &quot;inc/config.inc.php&quot; file:';
$i18n['generateThumbnails'] =
	'Generate thumbnails:';
$i18n['setup_lastStep'] =
	'<p>As a last step the thumbnails and reduced size images have to be
	generated. For this go to <a href="%s/%s/update">
	this location</a> and then execute the generated script.</p>';

//Update
$i18n['writeUpdateFailure'] =
	'Could not open file to write, save manually as &quot;update.sh&quot;';
$i18n['writeUpdateSuccess'] =
	'&quot;update.sh&quot script written.';

//Quotes
$i18n['lq'] =
	'&quot;';
$i18n['rq'] =
	'&quot;';

?>