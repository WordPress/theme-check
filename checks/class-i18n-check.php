<?php
/**
 * Check if there are PHP variables in translation strings.
 *
 * @package Theme Check
 */

/**
 * Check if there are PHP variables in translation strings.
 */
class I18N_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {

		checkcount();

		// Make sure the tokenizer is available.
		if ( ! function_exists( 'token_get_all' ) ) {
			return true;
		}

		foreach ( $php_files as $php_key => $phpfile ) {
			$stmts = array();
			foreach ( array( '_e(', '__(', '_e (', '__ (' ) as $finder ) {
				$search = $phpfile;
				while ( ( $pos = strpos( $search, $finder ) ) !== false &&
					strpos( $search, 'pll__' ) === false &&
					strpos( $search, 'pll_e' ) === false ) {
					$search = substr( $search, $pos );
					$open   = 1;
					$i      = strpos( $search, '(' ) + 1;
					while ( $open > 0 && isset( $search[ $i ] ) ) {
						switch ( $search[ $i ] ) {
							case '(':
								$open++;
								break;
							case ')':
								$open--;
								break;
						}
						$i++;
					}
					$stmts[] = substr( $search, 0, $i );
					$search  = substr( $search, $i );
				}
			}

			foreach ( $stmts as $match ) {
				$tokens = token_get_all( '<?php ' . $match . ';' );

				if ( ! empty( $tokens ) ) {
					foreach ( $tokens as $token ) {
						if ( is_array( $token ) && in_array( $token[0], array( T_VARIABLE ) ) ) {
							$filename      = tc_filename( $php_key );
							$grep          = tc_grep( ltrim( $token[1] ), $php_key );
							$this->error[] = sprintf(
								'<span class="tc-lead tc-recommended">%s</span>: %s',
								__( 'RECOMMENDED', 'theme-check' ),
								sprintf(
									__( 'Possible variable %1$s found in translation function in %2$s. Translation function calls must not contain PHP variables, use placeholders instead. See <a href="%3$s" target="_blank">Internationalization Guidelines (Opens in a new window)</a>. %4$s', 'theme-check' ),
									'<strong>' . $token[1] . '</strong>',
									'<strong>' . $filename . '</strong>',
									'https://developer.wordpress.org/apis/handbook/internationalization/internationalization-guidelines/#variables',
									$grep
								)
							);
							break; // Stop looking at the tokens on this line once a variable is found.
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Get error messages from the checks.
	 *
	 * @return array Error message.
	 */
	public function getError() {
		return $this->error;
	}
}

$themechecks[] = new I18N_Check();
