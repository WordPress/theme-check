<?php
/**
 * Check if directories only intended for development are included
 *
 * @package Theme Check
 */

/**
 * Check if directories only intended for development are included.
 *
 * Check if directories only intended for development are included. If they are, require them to be removed.
 */
class Directories_Check implements themecheck {
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

		$excluded_directories = array(
			'.git',
			'.svn',
			'.hg',
			'.bzr',
		);

		$ret = true;

		$all_filenames = array_merge(
			array_keys( $php_files ),
			array_keys( $css_files ),
			array_keys( $other_files )
		);

		foreach ( $all_filenames as $path ) {
			checkcount();

			$filename = basename( $path );

			if ( in_array( $filename, $excluded_directories, true ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Please remove any extraneous directories like .git or .svn from the ZIP file before uploading it.', 'theme-check' )
				);
				$ret           = false;
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

$themechecks[] = new Directories_Check();
