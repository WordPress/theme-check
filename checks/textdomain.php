<?php

class TextDomainCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;
		$css = implode(' ', $css_files);
		checkcount();

		if ( strpos( $css, 'Theme Name: Twenty Ten' ) ) return $ret;

		$checks1 = '/(_[_|e]\([^,;\r\n]*,[^\)]*\))/';
		foreach ( $php_files as $php_key => $phpfile ) {
			checkcount();
			if ( preg_match_all( $checks1, $phpfile, $matches ) ) {

				if ( $out = preg_grep( '/twentyten/', $matches[0] ) ) {
					$filename = tc_filename( $php_key );
					foreach( $out as $error ) {
						$grep .= tc_grep( $error , $php_key );
// html_print_r($out);
					}
				$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: Theme is using <strong>twentyten</strong> as the textdomain in <strong>{$filename}</strong>.{$grep}";
				$ret = false;
				}
			}

		}
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new TextDomainCheck;