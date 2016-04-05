<?php

class CustomCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;
		$php = implode( ' ', $php_files );

		checkcount();

		if ( ! preg_match( '#add_theme_support\s?\(\s?[\'|"]custom-header#', $php ) ) {
			$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.sprintf( __( 'No reference to %s was found in the theme.', 'theme-check'), '<strong>add_theme_support( "custom-header", $args )</strong>' ).' '.__( 'It is recommended that the theme implement this functionality if using an image for the header.', 'theme-check' );
		}

		if ( ! preg_match( '#add_theme_support\s?\(\s?[\'|"]custom-background#', $php ) ) {
			$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.sprintf( __( 'No reference to %s was found in the theme.', 'theme-check'), '<strong>add_theme_support( "custom-background", $args )</strong>' ).' '.__( 'If the theme uses background images or solid colors for the background, then it is recommended that the theme implement this functionality.', 'theme-check' );
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new CustomCheck;