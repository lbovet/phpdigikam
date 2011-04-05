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

/**
 * This is used to generate a script called update.sh
 *
 * With that shell script the thumnails and reduced size images
 * are generated and copied into the web server's directory.
 * The 'exif' tool is used as well to rotate an image if appropriate
 */
class UpdateScript {
	private function color($text, $color) {
		if($color == 'green')
			$color = $this->green;
		elseif($color == 'violet')
			$color = $this->violet;
	
		return $color.$text.$this->_normal;
	}

	function __construct($db) {
		global $_config;
		global $i18n;

		$this->_green  = "\033[0;40;32m";
		$this->_violet = "\033[0;35;40m";
		$this->_normal = "\033[0m";

		$this->_db = $db;

		echo "<h2>{$i18n['executeUpdateScript']}</h2>";

		$f ="#!/bin/bash\n\n";
		$f.="#This files was generated on ".date("d.m.y, H:m")." UTC\n";
		$f.="#All changes made to this file will be LOST!\n\n";
	
		$rows = $this->_db->query(
			'SELECT Albums.id, Albums.url FROM Albums
		   WHERE '.$_config['restrictedAlbums']
		)->fetchAll();
		$numRows1 = count($rows);
		$count1 = 0;
	
		foreach ($rows as $row) {
			$albumDir = Photoalbum::stripLeadingSlash($row["url"]);
			$f.="echo \"".
					$this->color("* Create images for album \"$albumDir\"", $this->_green).
					" (".(++$count1)." of $numRows1".")\"\n";
			$f.="mkdir -p thumbs/".$albumDir."\n";
			$f.="mkdir -p images/".$albumDir."\n";
	
			$f.="CONVERT=\"".$_config["convertBin"]."\"\n";
			$f.="EXIF=\"".$_config["exifBin"]."\"\n";
			$f.="FROM=\"".$_config["photosPath"]."\"\n";
			$f.="TO=\"".$_config["selfDir"]."\"\n";

			$rows2 = $this->_db->query(
				"SELECT Albums.url||'/'||Images.name AS path, Images.id, 
				 Images.name AS filename 
				 FROM Images, Albums 
				 WHERE Albums.id=".$row['id']." AND Albums.id=Images.dirid"
			)->fetchAll();
			$numRows2 = count($rows2);
			$count2 = 0;
	
			foreach($rows2 as $row2) {
				$path = substr($row2["path"], 1,strlen($row2["path"])-1);
				$f.="echo \"".$this->color("  * ".$row2["filename"], $this->_violet)
							." (".(++$count2)." of $numRows2".")\"\n";
	
				$f.="if( [[ \"\$FROM/$path\" -nt \"\$TO/thumbs/$path\" ]] ) then\n";
	
				# Rotate the image if needed
				$f.="  rotate=\"\"\n".
	"  if( [[ `\$EXIF \$FROM/$path | grep Orientation | grep right | wc -l` -gt 0 ]] ) then\n".
	"    rotate=\" -rotate 90\"\n".
	"  fi\n";
	
				$f.="echo \"    thumbnail\"\n";
				$cmd  = "\$CONVERT \$rotate -resize x".$_config['thumbSize']." ".
								"\$FROM/$path \$TO/thumbs/$path";
				$f.="echo \"    reduced size image\"\n";
				$cmd2 = "\$CONVERT \$rotate -resize x".$_config['imageSize']." ".
								"\$FROM/$path \$TO/images/$path";
	
				$f.=$cmd."\n";
				$f.=$cmd2."\n";
	
				$f.="else\necho \"    is up to date\"\nfi\n";
			}
		}
		
		$fh = fopen('update.sh', 'w');
		if(!$fh) {
			echo "<h3 style=\"color: red\">{$i18n['writeUpdateFailure']}</h3>";
		}
		else {
			@fwrite($fh, $f);
			@fclose($fh);
			echo "<h3 style=\"color: green\">{$i18n['writeUpdateSuccess']}</h3>";
		}
		
		echo "<textarea cols=\"90\" rows=\"20\">$f</textarea>";
	}

	private $_green;
	private $_violet;
	private $_normal;
	private $_db;
};

?>