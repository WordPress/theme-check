<?php
/**
 * Check for wp_title and title tags.
 *
 * @package Theme Check
 */

/**
 * Checks for the title:
 * Is there a call to wp_title()?
 * Are there <title> and </title> tags?
 *
 * See: https://make.wordpress.org/themes/handbook/review/required/theme-check-plugin/
 */
class Title_Check implements themecheck {
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
		$php = implode( ' ', $php_files );

		foreach ( $php_files as $file_path => $file_content ) {

			// Check whether there is a call to wp_title().
			checkcount();
			if ( false !== strpos( $file_content, 'wp_title(' ) ) {
				$ret           = false;
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'The theme must not use <strong>wp_title()</strong>. Found wp_title() in %1$s.', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					)
				);
			}

			// Look for anything that looks like <svg>...</svg> and exclude it (inline svg's have titles too).
			$file_content = preg_replace( '/<svg.*>.*<\/svg>/s', '', $file_content );

			// Look for <title> and </title> tags.
			checkcount();
			if ( ( false !== strpos( $file_content, '<title>' ) ) || ( false !== strpos( $file_content, '</title>' ) ) ) {
				$ret           = false;
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'The theme must not use <strong>&lt;title&gt;</strong> tags. Found the tag in %1$s.', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					)
				);
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

$themechecks[] = new Title_Check();
