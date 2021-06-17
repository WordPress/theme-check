<?php
/**
 * Check that the theme is not generated.
 *
 * @package Theme Check
 */

/**
 * Check that the theme is not generated.
 */
class Generated_Check implements themecheck {
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

		// Combine all the php files into one string to make it easier to search.
		$php = implode( ' ', $php_files );

		checkcount();

		if (
			// Artisteer.
			strpos( $php, 'art_normalize_widget_style_tokens' ) !== false
			|| strpos( $php, 'art_include_lib' ) !== false
			|| strpos( $php, '_remove_last_slash($url) {' ) !== false
			|| strpos( $php, 'adi_normalize_widget_style_tokens' ) !== false
			|| strpos( $php, 'm_normalize_widget_style_tokens' ) !== false
			|| strpos( $php, "bw = '<!--- BEGIN Widget --->';" ) !== false
			|| strpos( $php, "ew = '<!-- end_widget -->';" ) !== false
			|| strpos( $php, "end_widget' => '<!-- end_widget -->'" ) !== false
			// Lubith.
			|| strpos( $php, 'Lubith' ) !== false
			// Templatetoaster.
			|| strpos( $php, 'templatetoaster_' ) !== false
			|| strpos( $php, 'Templatetoaster_' ) !== false
			|| strpos( $php, '@package templatetoaster' ) !== false
			// wpthemegenerator.
			|| strpos( $php, 'wptg_' ) !== false
		) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-warning">%s</span>: %s',
				__( 'WARNING', 'theme-check' ),
				__( 'This theme appears to have been auto-generated.', 'theme-check' )
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

$themechecks[] = new Generated_Check();
