<?php
/**
 * Checks that Full-Site Editing themes have the required files.
 *
 * @package Theme Check
 */

/**
 * Class FSE_Required_Files_Check
 *
 * Checks that Full-Site Editing themes have the required files.
 *
 * This check is not added to the global array of checks, because it doesn't apply to all themes.
 */
class FSE_Required_Files_Check implements themecheck {
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

		$filenames = array();

		foreach ( $other_files as $php_key => $phpfile ) {
			array_push( $filenames, tc_filename( $php_key ) );
		}

		if ( ! in_array( 'theme.json', $filenames ) ) {
			$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Could not find the file theme.json in the theme.', 'theme-check' ), '<strong>theme.json</strong>' );
			$ret           = false;
		}

		if ( ! in_array( 'block-templates/index.html', $filenames ) && ! in_array( 'templates/index.html', $filenames ) ) {
			$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Could not find the index.html template in the theme.', 'theme-check' ), '<strong>index.html</strong>' );
			$ret           = false;
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
