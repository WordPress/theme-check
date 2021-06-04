<?php
/**
 * Check for various I18N errors
 *
 * @package Theme Check
 */

/**
 * Check for various I18N errors.
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
		$ret   = true;
		$error = '';
		checkcount();

		// Make sure the tokenizer is available.
		if ( ! function_exists( 'token_get_all' ) ) {
			return true;
		}

		foreach ( $php_files as $php_key => $phpfile ) {
			$error = '';

			$stmts = array();
			foreach ( array( '_e(', '__(', '_e (', '__ (' ) as $finder ) {
				$search = $phpfile;
				while ( ( $pos = strpos( $search, $finder ) ) !== false ) {
					$search = substr( $search, $pos );
					$open   = 1;
					$i      = strpos( $search, '(' ) + 1;
					while ( $open > 0 ) {
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
							$filename = tc_filename( $php_key );
							$grep     = tc_grep( ltrim( $match ), $php_key );
							preg_match( '/[^\s]*\s[0-9]+/', $grep, $line );
							$error = '';
							if ( isset( $line[0] ) ) {
								$error = ( ! strpos( $error, $line[0] ) ) ? $grep : '';
							}
							$this->error[] = sprintf(
								'<span class="tc-lead tc-recommended">%s</span>: %s',
								__( 'RECOMMENDED', 'theme-check' ),
								sprintf(
									__( 'Possible variable %1$s found in translation function in %2$s. Translation function calls must NOT contain PHP variables. %3$s', 'theme-check' ),
									'<strong>' . $token[1] . '</strong>',
									'<strong>' . $filename . '</strong>',
									$error
								)
							);
							break; // Stop looking at the tokens on this line once a variable is found.
						}
					}
				}
			}
		}
		return $ret;
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
