<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Check for prohibited words and theme names.
 *
 * @link https://make.wordpress.org/themes/handbook/review/required/#naming
 */

/**
 * Check for the words "theme", "WordPress" and the official theme names.
 */
class ThemeNameCheck implements themecheck {
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
		global $themename;
		global $data;

		/* Remove the link added by get_theme_data_from_contents. */
		$author = wp_strip_all_tags( $data['Author'] );
		/* Get the name, as added in style.css but lower case. */
		$title = strtolower( $data['Title'] );

		checkcount();
		/* If the author is not the WordPress team, check for prohibited names. */
		if ( 'the WordPress team' !== $author ) {
			$blacklist = array( 'twentyten', 'twentyeleven', 'twentytwelve', 'twentythirteen', 'twentyfourteen', 'twentyfifteen',
			'twentysixteen', 'twentyseventeen', 'twentyeighteen', 'twentynineteen', 'twentytwenty', 'twentytwentyone',
			'twentytwentytwo', 'twentytwentythree', 'twentytwentyfour', 'twentytwentyfive', 'twentytwentysix',
			'twentytwentyseven', 'twentytwentyeight', 'twentytwentynine', 'twentythirty'
			);

			foreach ( $blacklist as $blacklisted_theme_name ) {
				if ( stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), $blacklisted_theme_name ) !== false ) {
					$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . sprintf( __( 'Theme names in the Twenty* series are reserved for default themes. Found %1$s.', 'theme-check' ), '<strong>' . $blacklisted_theme_name . '</strong>' );
				}
			}
		}

		checkcount();
		/* Check for prohibited words in the folder name. */
		if ( stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), 'wordpress' ) !== false // phpcs:ignore WordPress.WP.CapitalPDangit
			|| stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), 'theme' ) !== false ) {
			$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Theme folder names must not contain the words <strong>theme</strong> or <strong>WordPress</strong>.', 'theme-check' );
		}

		checkcount();
		/* Check for prohibited words in style.css. */
		if ( stripos( preg_replace( '/[^a-z]/', '', $title ), 'wordpress' ) !== false // phpcs:ignore WordPress.WP.CapitalPDangit
			|| stripos( preg_replace( '/[^a-z]/', '', $title ), 'theme' ) !== false ) {
			$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Theme names must not contain the words <strong>theme</strong> or <strong>WordPress</strong>.', 'theme-check' );
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

$themechecks[] = new ThemeNameCheck();
