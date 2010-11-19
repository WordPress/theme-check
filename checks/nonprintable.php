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
			$grep = tc_grep( $matches[0], $name, $code = 1, true );
			$name_t = tc_strxchr( $name, '/themes/',8 );
			$name = str_replace( $name_t[0], '', $name );
			$this->error[] = "INFONon-printable characters were found in the <strong>{$name}</strong> file. You may want to check this file for errors.{$grep}";
			}
		}
		
		// return the pass/fail
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new NonPrintableCheck;
