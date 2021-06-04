<?php
/**
 * Check if non-printable characters are included
 *
 * @package Theme Check
 */

/**
 * Check if non-printable characters are included.
 */
class Non_Printable_Check implements themecheck {
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

		foreach ( $php_files as $file_path => $file_content ) {
			checkcount();

			// 09 = tab.
			// 0A = line feed.
			// 0D = new line.
			if ( preg_match( '/[\x00-\x08\x0B-\x0C\x0E-\x1F]/', $file_content, $matches ) ) {
				$grep          = tc_preg( '/[\x00-\x08\x0B-\x0C\x0E-\x1F]/', $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s %s',
					__( 'WARNING', 'theme-check' ),
					sprintf(
						__( 'Non-printable characters were found in the %s file. You may want to check this file for errors.', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					),
					$grep
				);
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

$themechecks[] = new Non_Printable_Check();
