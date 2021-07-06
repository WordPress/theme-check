<?php
/**
 * Check for Requires PHP versioning
 *
 * @package Theme Check
 */

/**
 * Check for PHP versioning.
 * Does "Requires PHP" include patch versions (e.g. 7.4.1)?
 * If so, recommend including major and minor verisions only (e.g. 7.4)
 *
 * See: https://developer.wordpress.org/themes/basics/main-stylesheet-style-css/#explanations
 */
class Version_Requires_PHP_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Theme information. Requires PHP,
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

		if ( ! empty( $this->theme->get( 'RequiresPHP' ) ) ) {

			$req_php_decimal_count = substr_count( $this->theme->get( 'RequiresPHP' ), '.' );

			if ( $req_php_decimal_count > 1 ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-recommended">%s</span>: %s',
					__( 'RECOMMENDED', 'theme-check' ),
					__( '<strong>Requires PHP</strong> is recommended to have major and minor versions only (e.g. 7.4). Patch version is not needed (e.g. 7.4.1).', 'theme-check' )
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

$themechecks[] = new Version_Requires_PHP_Check();
