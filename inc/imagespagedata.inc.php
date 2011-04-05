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
 * This data structure holds the data for one displayed page of photos
 * For this, it has $_imagesArray, an associative array gotten from the
 * sqlite query and $_count, the total number of photos for the query,
 * which is later used to calculate the number of pages etc.
 */
class ImagesPageData {
	function __construct($imagesArray, $count) {
		$this->_count = $count;
		$this->_imagesArray = $imagesArray;
	}

	public function count() {
		return $this->_count;
	}

	public function imagesArray() {
		return $this->_imagesArray;
	}

	private $_count;
	private $_imagesArray;
}

?>