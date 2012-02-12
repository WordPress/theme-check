<?php

class IncludeCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$checks = array( '/(?<![a-z0-9_])(?:requir|includ)e(?:_once)?\s?\(/' => __( 'The theme appears to use include or require. If these are being used to include separate sections of a template from independent files, then <strong>get_template_part()</strong> should be used instead.', 'themecheck' ) );

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = '/(?<![a-z0-9_])(?:requir|includ)e(?:_once)?\s?\(/';
					$grep = tc_preg( $error, $php_key );
					if ( basename($filename) !== 'functions.php' ) $this->error[] = sprintf ( __( '<span class="tc-lead tc-info">INFO</span>: <strong>%1$s</strong> %2$s %3$s', 'themecheck' ), $filename, $check, $grep );
				}
			}

		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new IncludeCheck;