<?php

class SearchFormCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;
		$checks = array( '/(include\s?\(\s?TEMPLATEPATH\s?\.?\s?["|\']\/searchform.php["|\']\s?\))/' => sprintf( __( 'Use %1$s instead of including %2$s directly.', 'theme-check' ), '<strong>get_search_form()</strong>', '<strong>searchform.php</strong>' ) );
		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ($checks as $key => $check) {
				checkcount();
				if ( preg_match( $key, $phpfile, $out ) ) {
					$grep = tc_preg( $key, $php_key );
					$filename = tc_filename( $php_key );
					$this->error[] = sprintf( '<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__( '%1$s was found in the file %2$s.', 'theme-check'), '<strong>searchform.php</strong>', '<strong>' . $filename . '</strong>') . ' '. $check . $grep;
					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new SearchFormCheck;