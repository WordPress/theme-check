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
				$name_t = tc_strxchr( $name, '/themes/',8 );
				$name = str_replace( $name_t[0], '', $name );
				$this->error[] = "<span class='tc-lead tc-info'>INFO</span>: Non-printable characters were found in the <strong>{$name}</strong> file. You may want to check this file for errors.";
			}
		}
		
		// return the pass/fail
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new NonPrintableCheck;
