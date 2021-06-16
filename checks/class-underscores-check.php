<?php
/**
 * Check if the theme is a copy of underscores.
 *
 * @package Theme Check
 */

/**
 * Check if:
 * Theme or author URI refers to _s
 * The readme is a copy of _s
 * footer credit link refers to _s
 */
class Underscores_Check implements themecheck {
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
		$ret = true;

		checkcount();
		if ( ! empty( $this->theme->get( 'AuthorURI' ) ) || ! empty( $this->theme->get( 'ThemeURI' ) ) ) {

			if (
				stripos( $this->theme->get( 'ThemeURI' ), 'underscores.me' ) ||
				stripos( $this->theme->get( 'AuthorURI' ), 'underscores.me' )
			) {
				$ret           = false;
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Found a copy of Underscores. Using underscores.me as Theme URI or Author URI is not allowed. Update the files for your own theme.', 'theme-check' )
				);
			}
		}

		checkcount();
		foreach ( $other_files as $file_path => $file_content ) {
			if (
				preg_match( "/Hi. I'm a starter theme called `_s`, or `underscores`, if you like./", $file_content ) ||
				preg_match( "/Hi. I'm a starter theme called <code>_s<\/code>, or <em>underscores<\/em>, if/", $file_content )
			) {
				$ret           = false;
				$grep          = tc_preg( "/Hi. I'm a starter theme called/", $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'Found a copy of Underscores. See %1$s. <a href="https://github.com/Automattic/_s" target="_new">Learn how to update the files for your own theme.</a>', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					),
					$grep
				);
			}
		}

		/**
		 * This check is limited to footer.php, since we are looking for clones of underscores.
		 */
		checkcount();
		foreach ( $php_files as $file_path => $file_content ) {
			$filename = tc_filename( $file_path );
			if ( 'footer.php' === $filename && preg_match( '/Underscores.me/i', $file_content ) ) {
				$ret           = false;
				$grep          = tc_preg( '/Underscores.me/i', $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'Found a copy of Underscores. See %1$s. Update the files for your own theme.', 'theme-check' ),
						'<strong>' . $filename . '</strong>'
					),
					$grep
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

$themechecks[] = new Underscores_Check();
