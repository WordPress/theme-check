<?php
/**
 * Check if iframes are used
 *
 * @package Theme Check
 */

/**
 * Check if iframes are used.
 *
 * Check if iframes are used. if they are, inform that the content need to be manually checked.
 */
class Iframe_Check implements themecheck {
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
		$ret = true;

		$checks = array(
			'/<(iframe)[^>]*>/' => __( 'iframes are sometimes used to load unwanted adverts and code on your site', 'theme-check' ),
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename      = tc_filename( $php_key );
					$error         = ltrim( $matches[1], '(' );
					$error         = rtrim( $error, '(' );
					$grep          = tc_grep( $error, $php_key );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-info">%s</span>: %s %s',
						__( 'INFO', 'theme-check' ),
						sprintf(
							__( '%1$s was found in the file %2$s %3$s.', 'theme-check' ),
							'<strong>' . $error . '</strong>',
							'<strong>' . $filename . '</strong>',
							$check
						),
						$grep
					);
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

$themechecks[] = new Iframe_Check();
