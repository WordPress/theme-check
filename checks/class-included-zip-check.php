<?php
/**
 * Check if zip files are included
 *
 * @package Theme Check
 */

/**
 * Check if zip files are included.
 */
class Included_Zip_Check implements themecheck {
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

		foreach ( $other_files as $other_key => $otherfile ) {
			array_push( $filenames, strtolower( basename( $other_key ) ) );
		}

		$blocklist = array(
			'\.zip' => __( 'Zipped Plugin', 'theme-check' ),
		);

		checkcount();

		foreach ( $blocklist as $file => $reason ) {
			if ( $filename = preg_grep( '/' . $file . '/', $filenames ) ) {
				$error         = implode( ' ', array_unique( $filename ) );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( '<strong>Zip file found.</strong> Plugins are not allowed in themes. The zip file found was <em>%s</em>.', 'theme-check' ),
						$error
					)
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

$themechecks[] = new Included_Zip_Check();
