<?php

/**
 * Checks for the Customizer.
 */

class CustomizerCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		checkcount();

		/**
		 * Check whether every Customizer setting has a sanitization callback set.
		 */
		foreach ( $php_files as $file_path => $file_content ) {

			// Get the arguments passed to the add_setting method
			if ( false !== strpos( strtolower( $file_content ), '$wp_customize->add_setting(' ) ) {

				$tokens_pattern = array( '$wp_customize', '->', 'add_setting', '(' );
				$tokens = token_get_all( $file_content );
				$matches = array();

				/**
				 * Loop through every token until $wp_customize is encountered. Then loop through the
				 * following tokens until the end of statement and test the matched group with a pattern.
				 */

				while ( list( $key, $token ) = each( $tokens ) ) {
					if ( ! is_array( $token ) || strtolower( $token[1] ) != '$wp_customize' )
						continue;

					$tokens_group = array( $token );

					while ( list( $key, $token ) = each( $tokens ) ) {

						// Skip whitespaces.
						if ( is_array( $token ) && ( T_WHITESPACE == $token[0] || T_COMMENT == $token[0] ) )
							continue;

						$tokens_group[] = $token;

						if ( is_string( $token ) && ';' == $token )
							break;
					}

					$match_test = array_map( function( $item ) {
						return is_array( $item ) ? $item[1] : $item;
					}, $tokens_group );

					if ( array_slice( $match_test, 0, count( $tokens_pattern ) ) == $tokens_pattern ) {
						$matches[] = $tokens_group;
					}
				}

				/**
				 * Loop through each match until a sanitize_callback string is encountered, then look into the
				 * following tokens ( => 'value' ) and check the value for emptiness.
				 */

				foreach ( $matches as $tokens ) {
					$found = false;

					while ( list( $key, $token ) = each( $tokens ) ) {
						if ( ! is_array( $token ) )
							continue;

						if ( T_CONSTANT_ENCAPSED_STRING != $token[0] || ! in_array( trim( $token[1], '\'"' ), array( 'sanitize_callback', 'sanitize_js_callback' ) ) )
							continue;

						// A sanitize callback argument was found.
						$found = true;

						list( $key, $double_arrow ) = each( $tokens );
						list( $key, $value ) = each( $tokens );

						$value = is_array( $value ) ? $value[1] : $value;
						$value = trim( $value, '\'"' );
						if ( empty( $value ) ) {
							$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Found a Customizer setting that had an empty value passed as sanitization callback. You need to pass a function name as sanitization callback.', 'theme-check' );
							return false;
						}
					}

					if ( ! $found ) {
						$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Found a Customizer setting that did not have a sanitization callback function. Every call to the <strong>add_setting()</strong> method needs to have a sanitization callback function passed.', 'theme-check' );
						return false;
					}
				}
			}
		}

		return true;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new CustomizerCheck;