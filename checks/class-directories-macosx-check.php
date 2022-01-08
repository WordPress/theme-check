<?php
/**
 * Check if Macosx directory is included
 *
 * @package Theme Check
 */

/**
 * Check if Macosx directory is included.
 *
 * Check if Macosx directory is included. If it is, require them to be removed.
 */
class Directories_Macosx_Check implements themecheck {
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
			'__MACOSX',
		);

		$ret = true;

		$path = get_template_directory();
		$directories = scandir( $path );

		foreach ( $directories as $dir ) {
			checkcount();

			if ( in_array( $dir, $excluded_directories, true ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Please remove any extraneous directories like <strong>.git</strong>, <strong>.svn</strong> or <strong>__MACOSX</strong> from the ZIP file before uploading it. For the <a href="https://make.wordpress.org/themes/handbook/review/required/#9-files">List of disallowed files</a>', 'theme-check' )
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

$themechecks[] = new Directories_Macosx_Check();
