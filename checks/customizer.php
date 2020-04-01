<?php
/**
 * Checks for the Customizer.
 */

class CustomizerCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		checkcount();

		/**
		 * Check whether every Customizer setting has a sanitization callback set.
		 */
		foreach ( $php_files as $file_path => $file_content ) {
			// Get the arguments passed to the add_setting method.
			if ( preg_match_all( '/\$wp_customize->add_setting\(([^;]+)/', $file_content, $matches ) ) {
				// The full match is in [0], the match group in [1].
				foreach ( $matches[1] as $match ) {
					// Check if we have sanitize_callback or sanitize_js_callback.
					if ( false === strpos( $match, 'sanitize_callback' ) && false === strpos( $match, 'sanitize_js_callback' ) ) {
						/* Clean up our match to be able to present the results better. */
						$match         = preg_split( '/,/', $match );
						$filename      = tc_filename( $file_path );
						$grep          = tc_preg( $match[0], $file_path );
						$grep          = preg_split( '/,/', $grep );
						$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Found a Customizer setting called %1$s in %2$s that did not have a sanitization callback function. ', 'theme-check' ) . __( 'Every call to the <strong>add_setting()</strong> method needs to have a sanitization callback function passed.', 'theme-check' ),
							'<strong>' . $match[0] . '</strong>',
							'<strong>' . $filename . '</strong>'
						) . $grep[0];
						$ret = false;
					} else {
						// There's a callback, check that no empty parameter is passed.
						if ( preg_match( '/[\'"](?:sanitize_callback|sanitize_js_callback)[\'"]\s*=>\s*[\'"]\s*[\'"]/', $match ) ) {
							$match         = preg_split( '/,/', $match );
							$filename      = tc_filename( $file_path );
							$grep          = tc_preg( '/[\'"](?:sanitize_callback|sanitize_js_callback)[\'"]\s*=>\s*[\'"]\s*[\'"]/', $file_path );
							$grep          = preg_split( '/,/', $grep );
							$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Found a Customizer setting called %1$s in %2$s that had an empty value passed as sanitization callback. You need to pass a function name as sanitization callback.', 'theme-check' ),
								'<strong>' . $match[0] . '</strong>',
								'<strong>' . $filename . '</strong>'
							) . $grep[0];
							$ret = false;
						}
					}
				}
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new CustomizerCheck();
