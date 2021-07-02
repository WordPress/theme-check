<?php
/**
 * Checks for favicons.
 *
 * Note: The check for the icon file is in filenames.php.
 *
 * @package Theme Check
 */

/**
 * Checks for favicons.
 */
class Favicon_Check implements themecheck {
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

		checkcount();

		foreach ( $php_files as $file_path => $file_content ) {

			$filename = tc_filename( $file_path );

			if (
				preg_match( '/(<link rel=[\'"](icon|shortcut icon|apple-touch-icon.*)[\'"])/i', $file_content ) ||
				preg_match( '/(<meta name=[\'"]msapplication-TileImage[\'"])/i', $file_content )
			) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-info">%s</span>: %s',
					__( 'INFO', 'theme-check' ),
					sprintf(
						__( 'Possible Favicon found in %1$s. Favicons are handled by the Site Icon setting in the customizer since version 4.3.', 'theme-check' ),
						'<strong>' . $filename . '</strong>'
					)
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

$themechecks[] = new Favicon_Check();
