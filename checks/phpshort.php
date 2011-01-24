<?php
class PHPShortTagsCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		foreach ( $php_files as $php_key => $phpfile ) {
			checkcount();
			if ( preg_match( '/<\?(\=?)(?!php|xml)/', $phpfile ) ) {
			$filename = tc_filename( $php_key );
			$grep = tc_preg( '/<\?(\=?)(?!php|xml)/', $php_key );
			$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: Found PHP short tags in file <strong>{$filename}</strong>.{$grep}";
			$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new PHPShortTagsCheck;