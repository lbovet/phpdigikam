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
 * Simple to class to measure the time that elapses from it's construction
 * till the moment stop() is called.
 * This is used to show the page-generation-time.
 */
class Stopwatch {
	function __construct() {
		$mtime = microtime(); 
		$mtime = explode(" ", $mtime); 
		$mtime = $mtime[1] + $mtime[0]; 
		$this->starttime = $mtime;
	}

	public function stop() {
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0]; 
		$endtime = $mtime; 
		return ($endtime - $this->starttime);
	}

	private $_starttime;
};

?>