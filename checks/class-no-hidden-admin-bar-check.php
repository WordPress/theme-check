<?php
/**
 * Checks if the admin bar gets hidden by the theme
 *
 * @package Theme Check
 */

/**
 * Checks if the admin bar gets hidden by the theme.
 */
class No_Hidden_Admin_Bar_Check implements themecheck {
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

		$php_regex = "/(add_filter(\s*)\((\s*)(\"|')show_admin_bar(\"|')(\s*)(.*))|(([^\S])show_admin_bar(\s*)\((.*))/";
		$css_regex = '/(#wpadminbar)/';

		checkcount();
		// Check php files for filter show_admin_bar, show_admin_bar_front, and show_admin_bar().
		foreach ( $php_files as $file_path => $file_content ) {

			if ( preg_match( $php_regex, $file_content, $matches ) ) {
				$grep          = tc_preg( '/show_admin_bar/', $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s %s',
					__( 'WARNING', 'theme-check' ),
					sprintf(
						__( '%1$s Themes are not allowed to hide the admin bar. This warning must be manually checked.', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					),
					$grep
				);
			}
		}

		checkcount();
		// Check CSS Files for #wpadminbar.
		foreach ( $css_files as $file_path => $file_content ) {

			// Don't print minified files.
			if ( strpos( $file_path, '.min.' ) === false ) {
				$grep = tc_preg( '/#wpadminbar/', $file_path );
			} else {
				$grep = '';
			}

			if ( preg_match( $css_regex, $file_content, $matches ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s %s',
					__( 'WARNING', 'theme-check' ),
					sprintf(
						__( 'The theme is using `#wpadminbar` in %1$s. Hiding the admin bar is not allowed. This warning must be manually checked.', 'theme-check' ),
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

$themechecks[] = new No_Hidden_Admin_Bar_Check();
