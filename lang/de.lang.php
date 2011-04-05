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
	'Fotos';
$i18n['executeUpdateScript'] = 
	'Bitte das Skript &bdquo;update.sh&ldquo; zum Aktualisieren der Thumbnails ausf&uuml;hren.';
$i18n['debugFooter'] =
	'Diese Seite wurde mit %s SQLite-Anfragen in %s Sekunden generiert.';
$i18n['noImagesOnPage'] =
	'Keine Bilder auf dieser Seite!';
$i18n['noSuchTag'] =
	'Solch ein Tag oder Album gibt es nicht, Zugang restriktiert
	 oder die Seite existiert nicht';
$i18n['photoAlbums'] =
	'Fotoalben';
$i18n['imagesInAlbum'] =
	'Bilder im Album';
$i18n['imagesWithTag'] =
	'Bilder mit Tag';
$i18n['Tags'] =
	'Tags';

//Setup
$i18n['Continue'] =
	'Weiter...';
$i18n['selectLanguage'] =
	'W&auml;hle eine Sprache:';
$i18n['setup_digikamDbPath'] =
	'Absoluter Pfad zur digikam Datenbank<br />
	 (normalerweise digikam3.db im digikam Foto Ordner)';
$i18n['setup_photosPath'] =
	'Absoluter Pfad zum digikam Foto Ordner';
$i18n['setup_convertBin'] =
	'Absoluter Pfad zum &bdquo;convert&ldquo; (Image Magick) Programm';
$i18n['setup_exifBin'] =
	'Absoluter Pfad zum &bdquo;exif&ldquo; Programm';
$i18n['setup_thumbSize'] =
	'Breite von Thumbnails (pixel)';
$i18n['setup_imagesSize'] =
	'Breite von Bildern (pixel)';
$i18n['setup_numCols'] =
	'Anzahl Spalten';
$i18n['setup_photosPerPage'] =
	'Anzahl Fotos pro Albumseite';
$i18n['setup_fillOutForm'] =
	'Formular unten ausf&uuml;llen und &bdquo;Weiter...&ldquo;';
$i18n['dbConnectSuccess'] =
	'Datenbankverbindung erfolgreich :-)';
$i18n['dbConnectFailure'] =
	'Datenbankverbindung NICHT erfolgreich';
$i18n['setup_CheckAlbumsToShow'] =
	'W&auml;hle diejenigen Alben aus die angezeigt werden sollen:';
$i18n['writeConfigFailure'] =
	'Konnte die Datei &bdquo;config.inc.php&ldquo; nicht schreiben. Speichere
	 sie selbst im Ordner &bdquo;inc/&ldquo;';
$i18n['reloadPage'] =
	'Dann muss diese Seite neu geladen werden.';
$i18n['writeConfigSuccess'] =
	'Dies wurde als &bdquo;inc/config.inc.php&ldquo; Datei geschrieben:';
$i18n['generateThumbnails'] =
	'Erzeuge Thumbnails:';
$i18n['setup_lastStep'] =
	'<p>Als letzter Schritt m&uuml;ssen die Thumbnails und verkleinerten
	 Bilder erzeugt werden. Um dies zu tun gehe <a href="%s/%s/update">
	hierhin</a> und f&uuml;hre dann das generierte Skript aus.</p>';

//Update
$i18n['writeUpdateFailure'] =
	'Konnte die Datei nicht zum Schreiben &ouml;ffnen, speichere sie selbst
	 als &bdquo;update.sh&ldquo;';
$i18n['writeUpdateSuccess'] =
	'&bdquo;update.sh&ldquo; Skript geschrieben.';

//Quotes
$i18n['lq'] =
	'&bdquo;';
$i18n['rq'] =
	'&ldquo;';

?>