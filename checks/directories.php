<?php

class DirectoriesCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
	
		$ret = true;
		$found = false;
		
		foreach ( $php_files as $name => $file ) {
			checkcount();
			if ( strpos($name, '.git') !== false || strpos($name, '.svn') !== false ) $found = true;		
		}

		foreach ( $css_files as $name => $file ) {
			checkcount();
			if ( strpos($name, '.git') !== false || strpos($name, '.svn') !== false ) $found = true;		
		}

		foreach ( $other_files as $name => $file ) {
			checkcount();
			if ( strpos($name, '.git') !== false || strpos($name, '.svn') !== false ) $found = true;		
		}
		
		if ($found) {
			$this->error[] = "<span class='tc-lead tc-required'>REQUIRED</span>: Please remove any extraneous directories like .git or .svn from the ZIP file before uploading it.";
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new DirectoriesCheck;