<?php

class SearchFormCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret    = true;
		$checks = array(
			'/(include\s?\(\s?TEMPLATEPATH\s?\.?\s?["|\']\/searchform.php["|\']\s?\))/' => __( 'Please use <strong>get_search_form()</strong> instead of including searchform.php directly.', 'theme-check' ),
		);

		foreach ( $php_files as $file_path => $file_contents ) {
			foreach ( $checks as $regex => $check_text ) {
				checkcount();

				if ( preg_match( $regex, $file_contents, $out ) ) {
					$ret           = false;
					$grep          = tc_preg( $key, $file_path );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-required">%s</span> %s %s %s',
						__( 'REQUIRED', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>',
						$check_text,
						$grep
					);
				}
			}
		}
		return $ret;
	}

	function getError() {
		return $this->error;
	}
}
$themechecks[] = new SearchFormCheck();
