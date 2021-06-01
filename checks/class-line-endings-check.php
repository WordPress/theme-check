<?php
/**
 * Check if more than one line ending style is used.
 *
 * @package Theme Check
 */

/**
 * Check if more than one line ending style is used.
 */
class Line_Endings_Check implements themecheck {
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
		foreach ( $php_files as $php_key => $phpfile ) {
			if ( preg_match( "/\r\n/", $phpfile ) ) {
				if ( preg_match( "/[^\r]\n/", $phpfile ) ) {
					$filename      = tc_filename( $php_key );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-required">%s</span>: %s',
						__( 'REQUIRED', 'theme-check' ),
						sprintf(
							__( 'Both DOS and UNIX style line endings were found in the file %s. This causes a problem with SVN repositories and must be corrected before the theme can be accepted. Please change the file to use only one style of line endings.', 'theme-check' ),
							'<strong>' . $filename . '</strong>'
						)
					);
					$ret           = false;
				}
			}
		}
		foreach ( $css_files as $css_key => $cssfile ) {
			if ( preg_match( "/\r\n/", $cssfile ) ) {
				if ( preg_match( "/[^\r]\n/", $cssfile ) ) {
					$filename      = tc_filename( $css_key );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-required">%s</span>: %s',
						__( 'REQUIRED', 'theme-check' ),
						sprintf(
							__( 'Both DOS and UNIX style line endings were found in the file %1$s. This causes a problem with SVN repositories and must be corrected before the theme can be accepted. Please change the file to use only one style of line endings.', 'theme-check' ),
							'<strong>' . $filename . '</strong>'
						)
					);
					$ret           = false;
				}
			}
		}
		foreach ( $other_files as $oth_key => $othfile ) {
			$e = pathinfo( $oth_key );
			if ( isset( $e['extension'] ) && in_array( $e['extension'], array( 'txt', 'js' ) ) ) {
				if ( preg_match( "/\r\n/", $othfile ) ) {
					if ( preg_match( "/[^\r]\n/", $othfile ) ) {
						$filename      = tc_filename( $oth_key );
						$this->error[] = sprintf(
							'<span class="tc-lead tc-required">%s</span>: %s',
							__( 'REQUIRED', 'theme-check' ),
							sprintf(
								__( 'Both DOS and UNIX style line endings were found in the file %1$s. This causes a problem with SVN repositories and must be corrected before the theme can be accepted. Please change the file to use only one style of line endings.', 'theme-check' ),
								'<strong>' . $filename . '</strong>'
							)
						);
						$ret           = false;
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

$themechecks[] = new Line_Endings_Check();
