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
 * A node for our tree.
 * A node has one parent and a variable number of children,
 * a key() for lookup and a data() for payload
 */
class Node {
	private $_parent;
	private $_children = array();
	private $_data;
	private $_key;
	private $_path;

	function __construct($key, $data, $path) {
		$this->_data = $data;
		$this->_key = $key;
		$this->_path = $path;
	}

	public function itsParent() {
		return $this->_parent;
	}

	public function setParent($parent) {
		$this->_parent = $parent;
	}
	
	public function children() {
		return $this->_children;
	}

	public function numChildren() {
		return count($this->_children);
	}

	public function isLeaf() {
		return $this->numChildren() == 0;
	}

	public function addChild($child) {
		$child->setParent($this);
		$this->_children[] = $child;
	}

	public function data() {
		return $this->_data;
	}
	public function key() {
		return $this->_key;
	}
	public function path() {
		return $this->_path;
	}
};

/**
 * A Tree.
 */
class Tree {
	private $_rootNode;

	function __construct() {
		$this->_rootNode = new Node(NULL, NULL, NULL);
	}

	public function isRoot($node) {
		return $node == $this->_rootNode;
	}

	public function root() {
		return $this->_rootNode;
	}

	public function addNode($node, $parent) {
		$parent->addChild($node);
	}

	public function renderRecursive($node, $level) {
		foreach($node->children() as $child) {
			echo str_repeat('  ', $level);
			echo $child->data()." (".$child->key().")";
			echo "\n";
			$this->renderRecursive($child, $level+1);
		}
	}
	public function render() {
		echo "<pre>";
		$this->renderRecursive($this->root(),0);
		echo "</pre>";
	}

	public function findNode($key /*$parent*/) {
		$parent;
		switch(count(func_get_args())) {
			case 1: $parent = $this->root(); break;
			case 2: $parent = func_get_arg(1); break;
			default: die('Not allowed');
		}

		$found = NULL;

		//FIXME: foreach doesn't work!
		for($i=0; $i<count($parent->children()); $i++) {
			$array = $parent->children();
			$child = $array[$i];

			if($child->key() == $key) {
				$found = $child;
			}
			else if($found == NULL) {
				$found = $this->findNode($key, $child);
			}
		}
		return $found;
	}

	public function pathToNode($key) {
		$path = array();
		$node = $this->findNode($key, $this->root());

		while($node != $this->root()) {
			$path[] = $node;
			$node = $node->itsParent();
		}

		return array_reverse($path);
	}

	private function numNodesBelowRecursive($parent) {
		$count = 1;
		foreach($parent->children() as $child) {
			$count+=$this->numNodesBelowRecursive($child);
		}
		return $count;
	}
	public function numNodesBelow($parent) {
		return $this->numNodesBelowRecursive($parent)-1;
	}

	public function nodesBelow($parent, &$nodesArray) {
		foreach($parent->children() as $child) {
			$nodesArray[] = $child;
			$this->nodesBelow($child, $nodesArray);
		}
	}
};

?>
