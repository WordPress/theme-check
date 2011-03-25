<?php
class IframeCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		$checks = array(
			'/<(iframe)[^>]*>/' => __( 'iframes are sometimes used to load unwanted adverts and code on your site', 'themecheck' )
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = ltrim( $matches[1], '(' );
					$error = rtrim( $error, '(' );
					$grep = tc_grep( $error, $php_key );
					$this->error[] = "<span class='tc-lead tc-info'>INFO</span>: <strong>{$error}</strong> was found in the file <strong>{$filename}</strong> {$check}.{$grep}";
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new IframeCheck;