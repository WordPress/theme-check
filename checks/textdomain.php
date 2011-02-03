<?php

class TextDomainCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;
		$css = implode( ' ', $css_files );
		checkcount();

		if ( strpos( $css, 'Theme Name: Twenty Ten' ) ) return $ret;

		$checks = array(
		'/_[e|_]\([^,]*,\s?[\'|"]twentyten[\'|"]\s?\)/' => 'twentyten text domain is being used!', 
		'/_[e|_]\(\s?[\'|"][^\'|"]*[\'|"]\s?\);/' => 'You have not included a text domain!' );

		foreach ( $php_files as $php_key => $phpfile ) {
		foreach ( $checks as $key => $check ) {
		checkcount();
			if ( preg_match( $key, $phpfile, $matches ) ) {
				$filename = tc_filename( $php_key );
				$error = tc_grep( $matches[0], $php_key );
				$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: Text domain problems in <strong>{$filename}</strong>. {$check}{$error}";
				$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new TextDomainCheck;