<?php

class Deprecated_Define implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$grep = '';

		$ret = true;

		$checks = array(
			array( "NO_HEADER_TEXT" => "add_theme_support( 'custom-header' )", '3.4' ),
			array( "HEADER_IMAGE" => "add_theme_support( 'custom-header' )", '3.4' ),
			array( "HEADER_IMAGE_WIDTH" => "add_theme_support( 'custom-header' )", '3.4' ),
			array( "HEADER_IMAGE_HEIGHT" => "add_theme_support( 'custom-header' )", '3.4' ),
			array( "HEADER_TEXTCOLOR" => "add_theme_support( 'custom-header' )", '3.4' ),
			array( "BACKGROUND_COLOR" => "add_theme_support( 'custom-background' )", '3.4' ),
			array( "BACKGROUND_IMAGE" => "add_theme_support( 'custom-background' )", '3.4' ),
		);
		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $alt => $check ) {
				checkcount();
				$key = key( $check );
				$alt = $check[ $key ];
				if ( preg_match( '/' . $key . '/', $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = $key;
					$version = $check[0];
					$grep = tc_grep( $error, $php_key );

					// Point out the deprecated function.
					$error_msg = sprintf(
						__( '%1$s found in the file %2$s. Deprecated since version %3$s.', 'theme-check' ),
						'<strong>' . $error . '</strong>',
						'<strong>' . $filename . '</strong>',
						'<strong>' . $version . '</strong>'
					);

					// Add alternative function when available.
					if ( $alt ) {
						$error_msg .= ' ' . sprintf( __( 'Use %s instead.', 'theme-check' ), '<strong>' . $alt . '</strong>' );
					}

					// Add the precise code match that was found.
					$error_msg .= $grep;

					// Add the finalized error message.
					$this->error[] = '<span class="tc-lead tc-required">' . __('REQUIRED','theme-check') . '</span>: ' . $error_msg;

					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Deprecated_Define;
