<?php

class IncludeCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$checks = array( '/(?<![a-z0-9_])(?:requir|includ)e(?:_once)?\s?[\'"\(]/' => sprintf( __( 'The theme appears to use %1$s or %2$s. If these are being used to include separate sections of a template from independent files, then %3$s should be used instead.', 'theme-check' ), '<strong>include</strong>', '<strong>require</strong>', '<strong>get_template_part()</strong>' ) );

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = '/(?<![a-z0-9_])(?:requir|includ)e(?:_once)?\s?[\'"\(]/';
					$grep = tc_preg( $error, $php_key );
					if ( basename($filename) !== 'functions.php' ) $this->error[] = sprintf ( '<span class="tc-lead tc-info">'.__('INFO','theme-check').'</span>: '.__( '%1$s was found in the file %2$s.', 'theme-check'), '<strong>require</strong>/<strong>include</strong>', '<strong>' . $filename . '</strong>') . ' ' . $check . $grep ;
				}
			}

		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new IncludeCheck;