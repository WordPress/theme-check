<?php
/**
 * Check if post formats are supported
 *
 * @package Theme Check
 */

/**
 * Check if post formats are supported.
 */
class Post_Format_Check implements themecheck {
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

		$php = implode( ' ', $php_files );
		$css = implode( ' ', $css_files );

		checkcount();

		$checks = array(
			'/add_theme_support\(\s?("|\')post-formats("|\')/m',
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $check ) {
				checkcount();
				if ( preg_match( $check, $phpfile, $matches ) ) {
					if (
						! strpos( $php, 'get_post_format' ) &&
						! strpos( $php, 'has_post_format' ) &&
						! strpos( $css, '.format' )
					) {
						$filename      = tc_filename( $php_key );
						$matches[0]    = str_replace( array( '"', "'" ), '', $matches[0] );
						$error         = esc_html( rtrim( $matches[0], '(' ) );
						$grep          = tc_grep( rtrim( $matches[0], '(' ), $php_key );
						$this->error[] = sprintf(
							'<span class="tc-lead tc-info">%s</span> %s',
							__( 'INFO', 'theme-check' ),
							sprintf(
								__( '%1$s was found in the file %2$s. However get_post_format and/or has_post_format were not found, and no use of formats in the CSS was detected.', 'theme-check' ),
								'<strong>' . $error . '</strong>',
								'<strong>' . $filename . '</strong>'
							)
						);
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

$themechecks[] = new Post_Format_Check();
