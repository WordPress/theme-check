<?php
/**
 * Check if theme and author URI are the same and that wordpress.org is not used for theme URI
 *
 * @package Theme Check
 */

/**
 * Check if theme and author URI are the same and that wordpress.org is not used for theme URI.
 */
class URI_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Theme information. Author URI, Author name
	 *
	 * @var array $theme
	 */
	protected $theme = array();

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

		if ( ! empty( $this->theme['AuthorURI'] ) && ! empty( $this->theme['URI'] ) ) {

			if ( strtolower( preg_replace( '/https?:\/\/|www./i', '', trim( $this->theme['URI'], '/' ) ) ) == strtolower( preg_replace( '/https?:\/\/|www./i', '', trim( $this->theme['AuthorURI'], '/' ) ) ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Your Theme URI and Author URI should not be the same.', 'theme-check' )
				);
				$ret           = false;
			}

			// We allow .org user profiles as Author URI, so only check the Theme URI. We also allow WordPress.com links.
			if (
				$this->theme['AuthorName'] != 'the WordPress team' &&
				( stripos( $this->theme['URI'], 'wordpress.org' ) || stripos( $this->theme['URI'], 'w.org' ) )
			) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Using a WordPress.org Theme URI is reserved for official themes.', 'theme-check' )
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
