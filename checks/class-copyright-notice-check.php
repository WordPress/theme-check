<?php
/**
 * Checks if the theme has a copyright notice.
 *
 * @package Theme Check
 */

/**
 * Checks if the theme has a copyright notice.
 */
class Copyright_Notice_Check implements themecheck {
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

		// Get a list of file names and check for the readme.
		$other_filenames = array();
		foreach ( $other_files as $path => $contents ) {
			$other_filenames[] = tc_filename( $path );
			if ( tc_filename( $path ) == 'readme.txt' || tc_filename( $path ) == 'readme.md' ) {
				$readme = $contents;
				break;
			}
		}

		$css  = implode( ' ', $css_files );
		$files = $css . $contents;

		checkcount();
		// Check for Copyright and (C).
		if ( ! preg_match( '/[ \t\/*#]*Copyright/i', $files, $matches ) && ! preg_match( '/[ \t\/*#]*\(C\)/i', $files, $matches ) ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-warning">%s</span>: %s %s',
				__( 'WARNING', 'theme-check' ),
				__( 'Could not find a copyright notice for the theme. A copyright notice is needed if your theme is licenced as GPL.', 'theme-check' ),
				'<a href="' . esc_url( 'https://www.gnu.org/licenses/gpl-howto.html' ) . '" target="_blank">' . __( 'Learn how to add a copyright notice (opens in a new window).', 'theme-check' ) . '</a>'
			);

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

$themechecks[] = new Copyright_Notice_Check();
