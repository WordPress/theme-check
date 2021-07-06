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

		$php = implode( ' ', $php_files );

		foreach ( $php_files as $file_path => $file_content ) {

			// Check whether there is a call to wp_title().
			checkcount();
			if ( preg_match( '/\bwp_title\b/', $file_content ) ) {
				$filename      = tc_filename( $file_path );
				$grep          = tc_grep( 'wp_title(', $file_path ); // tc_grep does not use preg_match, so there is a known risk for false positives here.
				$this->error[] = sprintf(
					'<span class="tc-lead tc-recommended">%s</span>: %s. %s',
					__( 'RECOMMENDED', 'theme-check' ),
					sprintf(
						__( '<strong>wp_title()</strong> was found in the file %1$s. wp_title was historically used for the document &lt;title&gt; tag and was never intended for other purposes. Use <strong>add_theme_support( "title-tag" )</strong> instead', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					),
					$grep
				);
			}

			// Look for anything that looks like <svg>...</svg> and exclude it (inline svg's have titles too).
			$file_content = preg_replace( '/<svg.*>.*<\/svg>/s', '', $file_content );

			// Look for <title> and </title> tags.
			checkcount();
			if ( ( false !== strpos( $file_content, '<title>' ) ) || ( false !== strpos( $file_content, '</title>' ) ) ) {
				$filename      = tc_filename( $file_path );
				$grep          = tc_grep( '<title>', $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-recommended">%s</span>: %s. %s',
					__( 'RECOMMENDED', 'theme-check' ),
					sprintf(
						__( '<strong>&lt;title&gt;</strong> tag was found in the file %1$s. Document titles must not be hard coded, use <strong>add_theme_support( "title-tag" )</strong> instead', 'theme-check' ),
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

$themechecks[] = new Title_Check();
