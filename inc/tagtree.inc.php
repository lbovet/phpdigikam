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

require_once('tree.inc.php');
require_once('config.inc.php');

/**
 * Queries the digikam database (Table Tags) and represents the
 * information in a hierarchical tree (a tree of the tags)
 */
class TagTree extends Tree {
	function __construct($db) {
		$this->_db = $db;

		parent::__construct();

		//Get the tag tree from the database
		$tagsQuery = $this->_db->query('
		 SELECT id, pid, name FROM Tags ORDER BY pid ASC');
		$rows = $tagsQuery->fetchAll();
		$tagsQuery = NULL;

		$this->_tagRows = array();
		$this->_tagsIds = array();

		//Build to arrays:
		//_tagsIds: The ids used
		//          This is necessary as the tag's id aren't numbered correctly
		//_tagRows: Fast way to lookup the data of a tag by it's id
		$i = 0;
		foreach($rows as $row) {
			$this->_tagRows[$row['id']] = array(
			 'name' => $row['name'],
			 'pid'  => $row['pid'],
			 'id'   => $row['id']);

			$this->_tagsIds[] = $row['id'];

			$i++;
		}

		$this->buildTagTreeRecursive(0, $this->root());
	}

	public function buildTagTreeRecursive($pid, $parentNode) {
		//Go through all ids
		//Use the _tagsIds for this
		//FIXME: As this is a recursive function, we better not use foreach
		for($i=0; $i<count($this->_tagsIds); $i++) {
			$id = $this->_tagsIds[$i];

			$tag_pid = $this->tagPropertyById($id, 'pid');

			if($tag_pid == $pid) {
	
				$tag_id  = $this->tagPropertyById($id, 'id');
				$tag_name = $this->tagPropertyById($id, 'name');
			
				$node = new Node($tag_id, $tag_name);
				$parentNode->addChild($node);
				$this->buildTagTreeRecursive($tag_id, $node);
			}
		}
	}

	//The html output for the Parent Tag > Child Tag > Child Tag links
	public function htmlPathToTag($id) {
		global $_config;

		$path = $this->pathToNode($id);
	
		$i=0; $ret="";
		foreach($path as $step) {
			if($i!=0)
				$ret.=sprintf('<img alt="&gt;" src="%s/icons/arrow.gif" />&nbsp;', $_config["selfUrl"]);
			$ret.=Photoalbum::mklink('tag', $step->key(), $step->data());
			$i++;
		}
		return $ret;
	}

	//Get a tag's data by it's id from the array we initialized earlier
	public function tagPropertyById($id, $property) {
		return $this->_tagRows[$id][$property];
	}

	private $_tagRows;
	private $_tagsIds;
	private $_db;
};

?>