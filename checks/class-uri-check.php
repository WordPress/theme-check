<?php
/**
 * Check that wordpress.org is not used for theme URI
 *
 * @package Theme Check
 */

/**
 * Check that wordpress.org is not used for theme URI.
 */
class URI_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Theme information. Author URI, theme URI, Author name
	 *
	 * @var object $theme
	 */
	protected $theme;

	function set_context( $data ) {
		if ( isset( $data['theme'] ) ) {
			$this->theme = $data['theme'];
		}
	}

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {
		checkcount();
		$ret = true;

		if ( ! empty( $this->theme->get( 'ThemeURI' ) ) ) {

			// We allow .org user profiles as Author URI, so only check the Theme URI. We also allow WordPress.com links.
			if (
				$this->theme->get( 'Author' ) != 'the WordPress team' &&
				( stripos( $this->theme->get( 'ThemeURI' ), 'wordpress.org' ) || stripos( $this->theme->get( 'ThemeURI' ), 'w.org' ) )
			) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Using a WordPress.org Theme URI is reserved for default and bundled themes (Twenty * series).', 'theme-check' )
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

$themechecks[] = new URI_Check();
