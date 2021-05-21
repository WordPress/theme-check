<?php

class Theme_Support_Title_Tag implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		$php = implode( ' ', $php_files );

		// Look for add_theme_support( 'title-tag' ).
		if ( ! preg_match( '#add_theme_support\s?\(\s?[\'|"]title-tag#', $php ) ) {
			$ret           = false;
			$this->error[] = sprintf(
				'<span class="tc-lead tc-required">%s</span>: %s',
				__( 'REQUIRED', 'theme-check' ),
				__( 'No reference to <strong>add_theme_support( "title-tag" )</strong> was found in the theme.', 'theme-check' )
			);
		}

		return $ret;
	}

	function getError() {
		return $this->error;
	}
}

$themechecks[] = new Theme_Support_Title_Tag();
