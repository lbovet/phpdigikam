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

require_once('stopwatch.inc.php');

/**
 * Main class.
 */
class Photoalbum {
	function __construct() {
		global $_config;
		global $i18n;

		$this->_stopwatch = new Stopwatch();

		//Parse this page's URL
		$this->parseUrl();

		//If we have to display the setup page return and do not
		//connect to the database yet
		if(isset($_GET['setup'])) {
			require_once('lang/en.lang.php');
			require_once('inc/setup_forms.inc.php');

			include('inc/header.inc.html');
			return;
		}

		include('inc/header.inc.html');

		require_once('config.inc.php');
		require_once('informativepdo.inc.php');
		require_once('imagespagedata.inc.php');
		require_once('tagtree.inc.php');

		//Connect to database and load tag data
		$this->_db = new InformativePDO('sqlite:/'.$_config['digikamDb']);
		$this->_tagTree = new TagTree($this->_db);

		//Link to homepage only if not viewing image
		if(!isset($_GET['image'])) {
			print("<p style=\"float:right\">\n");
			printf("\t<a href=\"%s/%s\">", $_config["selfUrl"], $_config["scriptname"]);
			printf("<img src=\"%s/icons/gohome.png\" alt=\"Home\" border=\"0\" /></a>\n</p>\n\n",
			 $_config["selfUrl"]);
		}
		
		//Call the different album functions 
		if(isset($_GET['album'])) {
			$this->htmlAlbumPage($_GET['album']);
		}
		else if(isset($_GET['tag'])) {
			$this->htmlTagPage($_GET['tag']);
		}
		else if(isset($_GET['image'])) {
			$this->htmlFullsizeImagePage($_GET['image']);
		}
		else if(isset($_GET['update'])) {
			require_once('inc/shellscript.inc.php');
			new UpdateScript($this->_db);
		}
		else {
			$this->htmlAlbumList();
			$this->htmlTagTree();
		}
	}

	function __destruct() {
		global $i18n;

		echo '<p align="left" style="font-size:smaller">';
		
		//print('&copy; Thorben Kr&ouml;ger, 2006. ');
		if(!isset($_GET['setup'])) {
			printf($i18n['debugFooter'], $this->_db->queryCount(),
						round($this->_stopwatch->stop(), 2));
		}

		echo "</p>\n";

		include('footer.inc.html');
	}

	/**
   * Here we take the script's URL and examine it.
	 * Depending on what is found, the appropriate index is set in the
	 * $_GET array just as if we'd passed ?arg=value
	 * This eliminates the use of "?" and "&" which makes it more
	 * wget friendly
	 */
	private function parseUrl() {
		//Probably mod_rewrite could be used instead

		$matches = array();

		if(preg_match('@/tag/([0-9]+)@',
			$_SERVER['REQUEST_URI'], $matches) > 0) {
			$_GET['tag'] = $matches[1];
		}
		else if(preg_match("@/image/(.*)/(.*).html@U",
						$_SERVER['REQUEST_URI'], $matches) > 0) {
			$_GET['image'] = $matches[1]."/".$matches[2];
		}
		else if(preg_match("@/album/([0-9]+)@",
						$_SERVER['REQUEST_URI'], $matches) > 0) {
			$_GET['album'] = $matches[1];
		}
		else if(preg_match('@/update@U',
						$_SERVER['REQUEST_URI'], $matches) > 0) {
			$_GET['update'] = true;
		}
		else if(preg_match('@/setup@U',
						$_SERVER['REQUEST_URI'], $matches) > 0) {
			$_GET['setup'] = true;
		}
	
		//Check filename for page_number.html
		if(preg_match('@/page_([0-9]+).html@',
			$_SERVER['REQUEST_URI'], $matches) > 0) {
			$_GET['page'] = $matches[1];
		}
	}

	/**
	 * Print all the image tags belonging to the image with $imageId
	 * for the thumbnail view
	 */
	private function htmlTagsForImage($imageId) {
		/* FIXME: Is this faster?
		   SELECT name, id FROM Tags
		   WHERE id IN (SELECT tagid FROM ImageTags
		   WHERE imageid=$imageId) ORDER BY name";  */

		$imageTags = $this->_db->query(
			'SELECT Tags.name, Tags.id FROM Tags INNER JOIN ImageTags
			 ON (ImageTags.tagid = Tags.id)
			 WHERE ImageTags.imageid = '.$imageId
		);
		$rows = $imageTags->fetchAll();

		echo "\n<!--ImageTags //-->\n<table>\n\t<tr>\n\t\t<td align=\"left\">\n";
		foreach ($rows as $row) {
			echo "\t\t\t".$this->_tagTree->htmlPathToTag($row['id'])."<br />\n";
		}
		echo "\t\t</td>\n\t</tr>\n</table>\n\n";
	}

	/**
	 * Render the $pageData (of class ImagesPageData) as a page of photo
	 * thumbnails.
	 * Manage the pages.
	 */
	private function thumbnailPage(&$pageData) {
		global $_config;
		global $i18n;
	
		if(count($pageData->imagesArray()) == 0) {
			printf('<h2>%s</h2><p>%s</p>'."\n", $i18n['noImagesOnPage'],
			      $i18n['noSuchTag']);
			return;
		}
	
		$numCols = $_config["numCols"];
	
		$page = (isset($_GET["page"])) ? $_GET["page"] : 1;
		
		$this->pageNavigation($pageData);
	
		echo '<table cellpadding="5" width="100%">';
		echo "\t<tr>\n";
	
		$i=0;
		foreach ($pageData->imagesArray() as $img) {
			if($i>0 && $i%$numCols==0) {print "\t</tr>\n\t<tr>\n";}
	
			$path = $this->stripLeadingSlash($img["path"]);
			print "\t\t<td align=\"center\">\n";
	
			//if(!empty($img['caption']))
				// print "\t\t\t<h4>{$img['caption']}</h4>\n";
	
			$can_url='file://'.$_config['photosPath'].'/'.$path;
			$thumb = md5($can_url).'.png';		

			echo "\t\t\t".$this->mkLink('image', $path,
									"<img alt=\"{$path}\" src=\"/photos/thumbnails/{$thumb}\" />")."\n";
/*			echo "\t\t\t".$this->mkLink('image', $path,
									"<img alt=\"{$path}\" src=\"{$_config['selfUrl']}/thumbs/$path\" />")."\n";*/

			$this->htmlTagsForImage($img['id']);
	
			print "\t\t</td>\n";
	
			$i++;
		}
		if($i%$numCols!=0) {
			for($j=$numCols-$i%$numCols; $j>0; $j--) {
				print "\t\t<td>&nbsp;</td>\n";
			}
		}
		if($i != 0) {
			print "\t</tr>\n";
		}
		print "</table>\n";
	
		$this->pageNavigation($pageData);
	}

	/**
	 * Examine the $pageData (of type ImagesPageData) and generate the
	 * html code for the page navigation bar
	 */
	private function pageNavigation(&$pageData) {
		global $_config;
		global $_db;
	
		$numPages = floor($pageData->count() / $_config['photosPerPage'])+1;
		if($numPages == 1) return;
		
		$page = (isset($_GET['page'])) ? $_GET['page'] : 1;
	
		echo "\n<!--Page Navigation //-->\n";
		if($page > 1)
			echo "<a href=\"{$this->hrefWithPage($page-1)}\">&lt;&lt; Prev&nbsp;</a>\n";
		for($i=1; $i<=$numPages; $i++) {
			if($i != $page)
				echo "<a href=\"{$this->hrefWithPage($i)}\">$i</a>&nbsp;\n";
			else
				echo "$i &nbsp;\n";
		}
		if($page < $numPages)
			echo "<a href=\"{$this->hrefWithPage($page+1)}\">&nbsp;Next &gt;&gt;</a>\n";
		echo "\n";
	}

	/**
	 * Generate the html code to display the full-sized image with
	 * path $url
	 */
	private function htmlFullsizeImagePage($url) {
		global $_config;
	
		print('<p align="center"><a href="javascript: history.go(-1)">'."\n");
		printf("<div style='height:720px;'><img style='height: 100%%;'  src=\"%s/images/%s\" /></div></a></p>\n", $_config["selfUrl"], $url);

	}

	/**
	 * Generate html code for a list of all available photo albums
	 */
	private function htmlAlbumList() {
		global $_db;
		global $_config;
		global $i18n;
	
		$rows = $this->_db->query(
			'SELECT Albums.id, Albums.url, Albums.date,
			 Albums.caption, Albums.collection, I.name
			 FROM Albums LEFT OUTER JOIN Images AS I
			 ON Albums.icon=I.id WHERE '.$_config['restrictedAlbums']
		)->fetchAll();
	
		printf("<h1>%s</h1>\n<ul>\n", $i18n['photoAlbums']);
		foreach ($rows as $row) {
			echo "\t<li>";
			echo $this->mkLink('album', $row['id'],
			                    $this->stripLeadingSlash($row['url']));
			echo "</li>\n";
		}
		echo "</ul>\n\n";
	}

	/**
	 * Display all images in album with id $albumId as a paged thumbnail
	 * page
	 */
	private function htmlAlbumPage($albumId) {
		global $_db;
		global $i18n;
	
		//Get data of images on this page
		$albumPageRows = $this->_db->query(
			'SELECT Albums.url||\'/\'||Images.name AS path, Albums.url,
			 Images.id, Images.caption
			 FROM Images, Albums
			 WHERE Albums.id='.$albumId.' AND Albums.id=Images.dirid
			 ORDER BY Images.datetime DESC '.$this->limitClause()
		)->fetchAll();
	
		//Get total number of images in this album
		$numResults = $this->_db->query(
			'SELECT COUNT(*) FROM Images, Albums
			 WHERE Albums.id='.$albumId.' AND Albums.id=Images.dirid'
		)->fetchColumn();
	
		if(count($numResults) > 0) {
			printf('<h2>%s '.$i18n['lq'].'%s'.$i18n['rq']."</h2>\n",
			      $i18n['imagesInAlbum'],
			      $this->stripLeadingSlash($albumPageRows[0]['url']));
		}
		$this->thumbnailPage( new ImagesPageData($albumPageRows, $numResults) );
	}

	/**
	 * Display all images with tag (id: $tagId) as a paged thumbnail page
	 */
	private function htmlTagPage($tagId) {
		global $i18n;

		printf('<h2>%s '.$i18n['lq'].'%s'.$i18n['rq']."</h2>\n",
		 $i18n['imagesWithTag'],
		 $this->_tagTree->tagPropertyById($tagId, 'name'));

		$t = $this->imagesWithTag($tagId);
		$this->thumbnailPage($t);
	}

	/**
	 * The WHERE part of a SQL-Query to get all images associated with
	 * $tagId. This is necessary if the tag has children and we want to
	 * Show the images which have these child tags set too.
	 */
	private function whereClause($tagId) {
		global $_tagTree;
	
		$nodesBelow = array();
	
		$find = $this->_tagTree->findNode($tagId);
	
		$this->_tagTree->nodesBelow($find, $nodesBelow);
	
		$whereClause = "";
		if(count($nodesBelow) == 0) {
			$whereClause = 'tagid='.$tagId.' ';
		}
		else{
			$whereClause = 'tagid IN (';
			$i=0;
			foreach($nodesBelow as $node) {
				if($i!=0) {$whereClause.=', ';}
				$whereClause.=$node->key();
				$i++;
			}
			$whereClause.=')';
		}
		return $whereClause;
	}

	/**
	 * Return whether there are any images with tag (id: $tagId)
	 */
	private function hasImagesWithTag($tagId) {
		global $_config;
	
		return $this->_db->query(
			'SELECT Images.id FROM Images, Albums
			 WHERE Images.id IN
			 (SELECT imageid FROM ImageTags
			  WHERE '.$this->whereClause($tagId).'
			  AND '.$_config['restrictedAlbums'].'
			 ) 
			 AND Albums.id=Images.dirid LIMIT 0,1'
		)->fetchColumn() > 0;
	}

	/**
	 * Return an ImagesPageData for the query: "All images which have tag
	 * (id: $tagId)"
	 */
	private function imagesWithTag($tagId) {
		global $_config;
	
		$rows = array();
	
		$whereClause = $this->whereClause($tagId);
	
		//Get data of images on this page
		$albumPageRows = $this->_db->query(
			'SELECT Albums.url||\'/\'||Images.name AS path, Images.id,
			 Images.caption FROM Images, Albums
			 WHERE Images.id IN
			 (SELECT imageid FROM ImageTags
			  WHERE '.$whereClause.'
			  AND '.$_config['restrictedAlbums'].'
			 ) 
			 AND Albums.id=Images.dirid
			 ORDER BY Images.datetime DESC '.$this->limitClause()
		)->fetchAll();
	
		//Number of images total in this "album"
		$numResults = $this->_db->query(
			'SELECT COUNT(*) FROM Images, Albums
			 WHERE Images.id IN
			 (SELECT imageid FROM ImageTags
			  WHERE '.$whereClause.'
			  AND '.$_config['restrictedAlbums'].'
		   ) 
			 AND Albums.id=Images.dirid'
		)->fetchColumn();
	
		return new ImagesPageData($albumPageRows, $numResults);
	}

	/**
	 * html code for a link to a tag, album or image query
	 * This has to consider the url's syntax described in parseUrl()
	 */
	static public function mkLink($var, $val, $caption) {
		global $_config;
	
		$ret = "<a href=\"{$_config['selfUrl']}/{$_config["scriptname"]}/";
	
		switch($var) {
			case 'tag':   $ret.="tag/$val"; break;
			case 'album': $ret.="album/$val"; break;
			case 'image': $ret.="image/{$val}.html"; break;
			default: die('This should not happen');
		}

		return $ret."\">$caption</a>";
	}

	/**
	 * Chop of the first character of a string
	 */
	public function stripLeadingSlash($string) {
		return substr($string, 1,strlen($string)-1);
	}

	/**
	 * Generate the LIMIT part of a SQL-query to make the paged thumbnail
	 * view possible
	 */
	private function limitClause() {
		global $_config;
	
		$page = 1;
		if(isset($_GET["page"])) {
			$page = $_GET["page"];
		}
		return 'LIMIT '.(($page-1)*$_config['photosPerPage']-1).','
			.($_config['photosPerPage']);
	}
	
	/**
	 * Modify the URL so that the page $page will be shown
	 * See parseUrl() for the URL's syntax
	 */
	private function hrefWithPage($page) {
		return (strstr( $_SERVER['REQUEST_URI'], 'page_')) ?
			preg_replace('@page_([0-9]+)@', 'page_'.$page, $_SERVER['REQUEST_URI'])
			:  "{$_SERVER['REQUEST_URI']}/page_$page.html";
	}

	/**
	 * Generate html code for a tree of all available tags
	 * We have to consider that tags should not be shown if no images are
	 * associated with it. This makes it slow.
	 */
	private function htmlTagTreeRecursive($node, $level) {
		if($node->isLeaf()) return;
	
		//Go through all ids
		echo str_repeat("\t", $level)."<ul>\n";
		foreach($node->children() as $child) {
			if($this->hasImagesWithTag($child->key())) {
				echo str_repeat("\t", $level+1);
				echo '<li>'.$this->mkLink('tag', $child->key(), $child->data())."</li>\n";
				$this->htmlTagTreeRecursive($child, $level+1);
			}
		}
		echo str_repeat("\t", $level)."</ul>\n";
	}
	private function htmlTagTree() {
		global $i18n;

		echo "<h1>{$i18n['Tags']}</h1>\n";
		$this->htmlTagTreeRecursive($this->_tagTree->root(), 0);
	}

	private $_db;
	private $_tagTree;
	private $_stopwatch;
};

?>
