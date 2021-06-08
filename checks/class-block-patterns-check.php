<?php
/**
 * Check if block patterns and block styles are included
 *
 * @package Theme Check
 */

/**
 * Check if block patterns and block styles are included.
 *
 * Check if block patterns and block styles are included, if not, recommend it.
 */
class Block_Patterns_Check implements themecheck {
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

		$php = implode( ' ', $php_files );

		if ( strpos( $php, 'register_block_pattern' ) === false ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( 'No reference to <strong>register_block_pattern</strong> was found in the theme. Theme authors are encouraged to implement custom block patterns as a transition to block themes.', 'theme-check' )
			);
		}

		if ( strpos( $php, 'register_block_style' ) === false ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( 'No reference to <strong>register_block_style</strong> was found in the theme. Theme authors are encouraged to implement new block styles as a transition to block themes.', 'theme-check' )
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

$themechecks[] = new Block_Patterns_Check();
