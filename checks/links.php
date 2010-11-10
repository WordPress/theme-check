<?php

// search for some bad things
class Check_Links implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;

		// things to check for

		foreach ($php_files as $php_key => $phpfile) {
		checkcount();
		$grep = '';
			if ( preg_match_all( '/(<a\s[^>]*?title=([\'"])?([^>]*?(?=\2))\2?([^>]*?)>)(.*?)(<\/a>)/is', $phpfile, $out, PREG_SET_ORDER ) ) {
			    $filename = basename($php_key);
			    foreach( $out as $key ) {
				if ( !strpos( $key[0], 'http://wordpress.org/' ) ) {
				$grep .= tc_grep( $key[0], $php_key);	
				$this->error[] = "INFOPossible hard-coded links were found in the file <strong>{$filename}</strong>.{$grep}";
				$ret = false;				
				}
				}

			}
}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Check_Links;
