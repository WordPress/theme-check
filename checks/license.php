<?php

// do some basic checks for strings
class License_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		// combine all the php files into one string to make it easier to search
		$ret = true;


	//	foreach ($other_files as $other_key => $otherfile) {
		checkcount();
		
		foreach ($other_files as $other_key => $otherfile) {
		
		if (basename($other_key) == 'license.txt') $found = true;
		
		
		}
		
		
		
		
		
		
		
		
		
		if ( !$found ) {
				
		// licence.txt not found, look in header...
		$css = implode(' ', $css_files);
		$checks = array(
		'((^Licence:.*Licence\sURI:)|(^Licence\sURI:.*Licence:))' => ' you must include a licence.txt or put <strong>License:</strong> and <strong>Licence URI:</strong> in style.css header.',
		);
		
				foreach ($checks as $key => $check) {
			if ( !preg_match( '/' . $key . '/ms', $css, $matches ) ) {
				$this->error[] = "REQUIRED<strong>license.txt</strong> is missing{$check}.";
				$ret = false;
			}
		
		
		
		
		
		
		}
		}

		// return the pass/fail
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new License_Checks;
