<?php
/**
 * Check if disallowed directories are included
 *
 * @package Theme Check
 */

/**
 * Check if disallowed directories are included.
 *
 * Checks each file path for disallowed directory names such as .git or __MACOSX.
 * Empty disallowed directories will not be detected as they contain no files.
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
	 * @param array $php_files   File paths and content for PHP files.
	 * @param array $css_files   File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {

		$excluded_directories = array(
			'__MACOSX',
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

			$path_segments = explode( '/', str_replace( '\\', '/', $path ) );

			if ( array_intersect( $path_segments, $excluded_directories ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Please remove any extraneous directories like <strong>.git</strong>, <strong>.svn</strong> or <strong>__MACOSX</strong> from the ZIP file before uploading it.', 'theme-check' )
				);
				$ret           = false;
				break;
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
