<?php

class TextDomainCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;
		$css = implode( ' ', $css_files );
		checkcount();

		if ( strpos( $css, 'Theme Name: Twenty Ten' ) ) return $ret;

		$checks = array(
		'/_[e|_]\([^,]*,\s?[\'|"]twentyten[\'|"]\s?\)/' => __( 'twentyten text domain is being used!', 'themecheck' ), 
		'/_[e|_]\(\s?[\'|"][^\'|"]*[\'|"]\s?\);/' => __( 'You have not included a text domain!', 'themecheck' ) );

		foreach ( $php_files as $php_key => $phpfile ) {
		foreach ( $checks as $key => $check ) {
		checkcount();
			if ( preg_match( $key, $phpfile, $matches ) ) {
				$filename = tc_filename( $php_key );
				$error = tc_grep( $matches[0], $php_key );
				$this->error[] = __( "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: Text domain problems in <strong>{$filename}</strong>. {$check}{$error}", "themecheck" );
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new TextDomainCheck;