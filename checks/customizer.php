<?php

/**
 * Checks for the Customizer.
 */

class CustomizerCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;

		checkcount();

		/**
		 * Check whether every Customizer setting has a sanitization callback set.
		 */
		foreach ( $php_files as $file_path => $file_content ) {

			$filename = tc_filename( $file_path );

			// Get the arguments passed to the add_setting method
			if ( preg_match_all( '/\$wp_customize->add_setting\(([^;]+)/', $file_content, $matches ) ) {
				// The full match is in [0], the match group in [1]
				foreach ( $matches[1] as $match ) {
					// Check if we have sanitize_callback or sanitize_js_callback
					if ( false === strpos( $match, 'sanitize_callback' ) && false === strpos( $match, 'sanitize_js_callback' ) ) {
						
						$missing_callback = explode( ",", $match );

						//We are only interested in the first occurance of the setting name, so lets add add_setting( back.
						$missing_callback = "add_setting(" . $missing_callback[0];
						
						$grep = tc_grep( $missing_callback,  $file_path );

						$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __('REQUIRED','theme-check') . '</span>: ' . __( 'Found a Customizer setting that did not have a sanitization callback function in %1$s. Every call to the <strong>add_setting()</strong> method needs to have a sanitization callback function passed.', 'theme-check' ),
						 '<strong>' . $filename . '</strong>') . $grep;

						$ret = false;
					} else {
						// There's a callback, check that no empty parameter is passed.
						if ( preg_match( '/[\'"](?:sanitize_callback|sanitize_js_callback)[\'"]\s*(?:=>\s*[\'"]\s*[\'"]|=>\s,)/', $match ) ) {

							$missing_callback = explode( "sanitize", $match );
							$missing_callback = explode( ",", $missing_callback[1] );
							//repair our little explosion...
							$missing_callback = "'sanitize" .  $missing_callback[0] . ",";
							
							$grep = tc_grep( $missing_callback,  $file_path );

							//Note: tc_grep converts " to ', so the printed error will not be identical to the actual code, but the line will be correct.							
							$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __('REQUIRED','theme-check') . '</span>: ' . __( 'Found a Customizer setting that had an empty value passed as sanitization callback in %1$s. You need to pass a function name as sanitization callback.', 'theme-check' ),
							 '<strong>' . $filename . '</strong>') . $grep;

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
$themechecks[] = new CustomizerCheck;