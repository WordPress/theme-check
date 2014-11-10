<?php
/**
 * Checks for a correct body_class() implementation:
 * 
 * Warns if body_class() is not present in <body>. This is a warning because of theme frameworks using wrapper functions for this.
 * Recommends to use the body_class filter for adding classes.
 */

class TC_Body_Class_Check implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		foreach ( $php_files as $file_path => $file_content ) {
			// Check whether the file contains the <body> tag.
			if ( preg_match( '/(<body.*)/', $file_content, $matches ) ) {

				// $matches contains the whole line containing the <body> tag.
				$body_line = trim( $matches[0] );

				// Is there body_class present?
				if ( false == preg_match( '/\sbody_class\(/', $body_line ) ) {
					echo 'No Body Class';
					$this->error[] = '<span class="tc-lead tc-warning">'.__( 'WARNING', 'theme-check' ) . '</span>: '.
						sprintf( __( 'There needs to be a call to the %1$s function in the %2$s HTML tag.', 'theme-check' ) . ' %3$s %4$s',
						'<code>' . esc_html( 'body_class()' ) . '</code>',
						'<code>' . esc_html( '<body>' ) . '</code>',
						'<strong>' . esc_html( tc_filename( $file_path ) ) . '</strong>',
						tc_grep( $body_line, $file_path )
					);
				} else {
					// Theres <body> and body_class, are there classes passed as a parameter?	
					if ( false === strpos( $file_content, 'body_class()' ) ) {
						$this->error[] = '<span class="tc-lead tc-recommended">'.__( 'RECOMMENDED', 'theme-check' ) . '</span>: '.
							sprintf( __( 'The %1$s filter should be used instead of the %2$s parameter of %3$s.', 'theme-check' ) . ' %4$s %5$s',
							'<code>' . esc_html( 'body_class' ) . '</code>',
							'<code>' . esc_html( '$class' ) . '</code>',
							'<code>' . esc_html( '<body>' ) . '</code>',
							'<strong>' . esc_html( tc_filename( $file_path ) ) . '</strong>',
							tc_grep( $body_line, $file_path )
						);
					}
				}
			}

		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new TC_Body_Class_Check;
