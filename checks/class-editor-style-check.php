<?php
/**
 * Check if add_editor_style is included
 *
 * @package Theme Check
 */

/**
 * Check if add_editor_style is included.
 *
 * Check if add_editor_style is included. If not, recommend it.
 */
class Editor_Style_Check implements themecheck {
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
		checkcount();
		$ret = true;

		$php = implode( ' ', $php_files );

		if ( strpos( $php, 'add_editor_style' ) === false ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( 'No reference to <strong>add_editor_style()</strong> was found in the theme. It is recommended that the theme implement editor styling, so as to make the editor content match the resulting post output in the theme, for a better user experience.', 'theme-check' )
			);
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

$themechecks[] = new Editor_Style_Check();
