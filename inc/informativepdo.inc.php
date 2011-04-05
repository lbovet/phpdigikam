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
 * Class that wraps around PHP's PDO class to count the number of queries
 * to the database that where executed.
 */
class InformativePDO extends PDO {
	function __construct($dataSourceName) {
		PDO::__construct($dataSourceName);
		$this->_count=0;
	}

	public function query($sql) {
		$this->_count++;
		return PDO::query($sql);
	}

	public function queryCount() {
		return $this->_count;
	}

	private $_count;
};

?>