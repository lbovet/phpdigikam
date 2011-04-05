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
 * Basic class representing a html form.
 * It just renders it's own input-elements ( added via add() )
 */
class FormBase {
	private $_widgets;
	private $_formName;

	function __construct() {
		$this->_widgets = array();
	}
	public function add($w) {
		array_push($this->_widgets, $w);
	}
	public function render($submitCaption = 'Go') {
		echo "<table>\n\t<form method=\"post\" action=\"{$_SERVER["REQUEST_URI"]}\">\n";
		for($i = 0; $i < count($this->_widgets); $i++) {
			if(get_class($this->_widgets[$i]) == 'WidgetHidden') {
				$this->_widgets[$i]->render();
			}
			else {
				echo "\t<tr>\n\t\t<td>".$this->_widgets[$i]->description()."</td>\n\t\t<td>";
				$this->_widgets[$i]->render();
				echo "\n\t\t</td>\n\t</tr>\n";
			}
		}
		echo "\t<tr>\n\t\t<td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"$submitCaption\" />\n\t\t</td>\n\t</tr>\n";
		echo "\t</form>\n</table>\n";
	}
};

/**
 * Base class for a html input (=widget)
 */
class WidgetBase {
	private $_name;
	private $_defaultValue;
	private $_description;
	
	public function __construct($description, $name, $defaultValue='') {
		$this->setName($name);
		$this->setDefaultValue($defaultValue);
		$this->setDescription($description);
	}
	public function name() {
		return $this->_name;
	}
	public function setName($name) {
		$this->_name = $name;
	}
	public function defaultValue() {
		return $this->_defaultValue;
	}
	public function setDefaultValue($v) {
		$this->_defaultValue = $v;
	}
	public function value() {
		return $_POST[$this->name()];
	}
	public function setDescription($description) {
		$this->_description = $description;
	}
	public function description() {
		return $this->_description;
	}
	public function render() {
	}
};

/**
 * input type=text
 */
class WidgetText extends WidgetBase {
	public function __construct($description, $name, $defaultValue='') {
		parent::__construct($description, $name, $defaultValue);

		$this->_size=20;
	}

	public function render() {
		echo "<input type=\"text\" size=\"{$this->_size}\" name=\"{$this->name()}\" value=\"{$this->defaultValue()}\" />\n";
	}

	public function setSize($size) {
		$this->_size = $size;
	}

	private $_size;
};

/**
 * textarea
 */
class WidgetTextarea extends WidgetBase {
	public function render() {
		echo "<textarea name=\"{$this->name()}\" cols=\"50\" rows=\"15\" wrap=\"virtual\">{$this->defaultValue()}</textarea>\n";
	}
}

/**
 * select
 */
class WidgetDropdown extends WidgetBase {
	public function __construct($values, $description, $name, $defaultValue) {
		parent::__construct($description, $name, $defaultValue);
		$this->_choices = $values;
	}
	public function render() {
		printf('<select name="%s">'."\n", $this->name());
		foreach($this->_choices as $key => $val) {
			printf('<option value="%s"', $key);
			if($key == $this->defaultValue())
				print(' selected');
			printf(">%s</option>\n", $val);
		}
		print("</select>\n");
	}

	private $_choices;
}

/**
 * input type=hidden
 */
class WidgetHidden extends WidgetBase {
	function __construct($name, $value) {
		parent::__construct('', $name, $value);
	}
	public function render() {
		printf('<input type="hidden" name="%s" value="%s" />', $this->name(), $this->defaultValue());
	}
}

/**
 * input type=checkbox
 */
class WidgetCheck extends WidgetBase {
	public function __construct($description, $name, $value) {
		parent::__construct($description, $name, $value);
	}
	public function render() {
		echo '<table style="text-align: left"><tr><td>';
		foreach($this->defaultValue() as $key => $val) {
			printf('<input type="checkbox" name="%s[]" value="%s" />&nbsp;%s<br />'."\n",
		 	$this->name(), $key, $val);
		}
		echo '</td></tr></table>';
	}
}

/**
 * Form to select the albums that should be exported
 * Handles also writing the config.inc.php file
 */
class Page3 extends FormBase {
	public function render($submitCaption = 'Go') {
		global $i18n;

		require_once('informativepdo.inc.php');
		$db = new InformativePDO('sqlite:/'.$_POST['digikamDb']);
		if($db) {
			printf('<h3 style="color: green">%s</h3>',
			 $i18n['dbConnectSuccess']
			);
		}
		else {
			printf('<h3 style="color: red">%s</h3>', $i18n['dbConnectFailure']);
			die();
		}

		$rows = $db->query('SELECT url, id FROM Albums')->fetchAll();

		$a=array();
		foreach($rows as $row)
			$a[$row['id']] = $row['url'];

		$this->add(new WidgetCheck($i18n['setup_CheckAlbumsToShow'], 'restrictedAlbums', $a));
		$this->add(new WidgetHidden('sent', 'page3'));

		$this->add(new WidgetHidden('digikamDb', $_POST['digikamDb']));
		$this->add(new WidgetHidden('photosPath', $_POST['photosPath']));
		$this->add(new WidgetHidden('convertBin', $_POST['convertBin']));
		$this->add(new WidgetHidden('exifBin', $_POST['exifBin']));
		$this->add(new WidgetHidden('thumbSize', $_POST['thumbSize']));
		$this->add(new WidgetHidden('imageSize', $_POST['imageSize']));
		$this->add(new WidgetHidden('numCols', $_POST['numCols']));
		$this->add(new WidgetHidden('photosPerPage', $_POST['photosPerPage']));
		$this->add(new WidgetHidden('language', $_POST['language']));

		parent::render($submitCaption);
	}

	public function processData() {
		global $i18n;

		$restrictedAlbums = 'Albums.id IN (';
		$i=0;
		foreach($_POST['restrictedAlbums'] as $ralbum) {
			if($i!=0)
				$restrictedAlbums.=',';
			$restrictedAlbums.=$ralbum;
			$i++;
		}
		$restrictedAlbums.=')';
	
		$c="<?php\n";
		$c.="// C O N F I G / / / / / / / / / / / / \n\n";
		$c.="//Language\n";
		$c.="require_once('lang/{$_POST['language']}.lang.php');\n\n";
		$c.="//Albums to show\n";
		$c.="\$_config['restrictedAlbums'] = \"$restrictedAlbums\";\n";
		$c.="//Paths\n";
		$c.="\$_config['digikamDb'] = \"{$_POST['digikamDb']}\";\n";
		$c.="\$_config['photosPath'] = \"{$_POST['photosPath']}\";\n";
		$c.="\$_config['convertBin'] = \"{$_POST['convertBin']}\";\n";
		$c.="\$_config['exifBin'] = \"{$_POST['exifBin']}\";\n";
		$c.="//Image and thumbnail sizes\n";
		$c.="\$_config['thumbSize'] = \"{$_POST['thumbSize']}\";\n";
		$c.="\$_config['imageSize'] = \"{$_POST['imageSize']}\";\n";
		$c.="//Layout\n";
		$c.="\$_config['numCols'] = \"{$_POST['numCols']}\";\n";
		$c.="\$_config['photosPerPage'] = \"{$_POST['photosPerPage']}\";\n";
	
		$c.="// / / / / / / / / / / / / / / / / / / \n\n";
		$c.="//These should be automatically correct\n";
		$c.="\$_config['selfDir']=substr(\$_SERVER['SCRIPT_FILENAME'], 0,
		                          strrpos(\$_SERVER['SCRIPT_FILENAME'], '/'));\n";
		$c.="\$_config['selfUrl']=substr(\$_SERVER['SCRIPT_NAME'], 0,
		                          strrpos(\$_SERVER['SCRIPT_NAME'], '/'));\n";
		$c.="\$_config['scriptname']=substr(strrchr(\$_SERVER['SCRIPT_NAME'], '/'),1);\n";
		$c.="?>\n";
	
		$fh = fopen('inc/config.inc.php', 'w');
		fwrite($fh, $c);
		fclose($fh);
	
		if(!$fh) {
			printf('<h3 style="color: red">%s</h3>', $i18n['writeConfigFailure']);
			printf('<p>%s</p>', $i18n['reloadPage']);
		}
		else {
			printf('<h2 style="color: green">%s</h2>', $i18n['writeConfigSuccess']);
		}
		echo '<textarea cols="85" rows="27">'.$c.'</textarea>';
	
		@include('config.inc.php');	
	
		print("<h2>{$i18n['generateThumbnails']}</h2>");
		printf($i18n['setup_lastStep'], $_config["selfUrl"], $_config["scriptname"]);
	}
};

/**
 * Form that asks for paths and basic layout options
 */
class Page2 extends FormBase {
	function __construct() {
		global $i18n;
		parent::__construct();

		$w = new WidgetText($i18n['setup_digikamDbPath'], 'digikamDb');
		$w->setSize(40);
		$this->add($w);
		$w = new WidgetText($i18n['setup_photosPath'], 'photosPath');
		$w->setSize(40);
		$this->add($w);
		$this->add(new WidgetText($i18n['setup_convertBin'], 'convertBin', '/usr/bin/convert' ));
		$this->add(new WidgetText($i18n['setup_exifBin'], 'exifBin', '/usr/bin/exif' ));
		$this->add(new WidgetText($i18n['setup_thumbSize'], 'thumbSize', '240' ));
		$this->add(new WidgetText($i18n['setup_imagesSize'], 'imageSize', '720' ));
		$this->add(new WidgetText($i18n['setup_numCols'], 'numCols', '3' ));
		$this->add(new WidgetText($i18n['setup_photosPerPage'], 'photosPerPage', '12' ));
		$this->add(new WidgetHidden('language', $_POST['language']));
		$this->add(new WidgetHidden('sent', 'page2'));
	}
};

/**
 * Form that asks for the language
 */
class Page1 extends FormBase {
	function __construct() {
		global $languages;
		global $i18n;

		parent::__construct();
		require_once('inc/languages.inc.php');

		$this->add(new WidgetDropdown($languages, $i18n['selectLanguage'], 'language', 'en'));
		$this->add(new WidgetHidden('sent', 'page1'));
	}
};

//////////////////////////////////////////////////////////////////////////

echo "<h1>Setup PHPDigikam Script</h1>";

if(!isset($_POST['sent'])) {
	$page1 = new Page1();
	$page1->render($i18n['Continue']);
}
else {
	$i18n = array();
	require("lang/{$_POST['language']}.lang.php");

	if($_POST['sent'] == 'page1') {
		$page2 = new Page2();
		$page2->render($i18n['Continue']);
	}
	else if($_POST['sent'] == 'page2') {
		$page3 = new Page3();
		$page3->render($i18n['Continue']);
	}
	else if($_POST['sent'] == 'page3') {
		$page3 = new Page3();
		$page3->processData();
	}
}
