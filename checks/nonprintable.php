<?php

class NonPrintableCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
		$ret = true;

		foreach ($php_files as $name=>$content) {
		checkcount();
			// 09 = tab
			// 0A = line feed
			// 0D = new line
			if ( preg_match('/[\x00-\x08\x0B-\x0C\x0E-\x1F\x80-\xFF]/',$content, $matches) ) {
				$this->error[] = "INFONon-printable characters were found in the <strong>{$name}</strong> file. You may want to check this file for errors.";
			}
		}
		
		// return the pass/fail
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new NonPrintableCheck;
