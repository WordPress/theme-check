<?php

class IncludeCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
	
		$ret = true;
		
		$php = implode(' ', $php_files);
		
		checkcount();
		
		if ( preg_match( '/include[\s|]*\(/', $php ) != 0 || preg_match( '/require[\s|]*\(/', $php ) != 0 ) {
			$this->error[] = "INFOThe theme appears to use include or require. If these are being used to include separate sections of a template from independant files, then <strong>get_template_part()</strong> should be used instead.";
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new IncludeCheck;
