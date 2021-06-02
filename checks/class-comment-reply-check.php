<?php
/**
 * Checks if the comment reply script is included in classic themes
 *
 * @package Theme Check
 */

/**
 * Checks if the comment reply script is included in classic themes.
 *
 * Checks if the comment reply script is included. If not, recommend it.
 */
class Comment_Reply_Check implements themecheck {
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

		checkcount();

		if ( ! preg_match( '/wp_enqueue_script\(\s?("|\')comment-reply("|\')/i', $php ) ) {
			if ( ! preg_match( '/comment-reply/', $php ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-recommended">%s</span>: %s',
					__( 'RECOMMENDED', 'theme-check' ),
					__( 'Could not find the <strong>comment-reply</strong> script enqueued.', 'theme-check' )
				);
			} else {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-info">%s</span>: %s',
					__( 'INFO', 'theme-check' ),
					__( 'Could not find the <strong>comment-reply</strong> script enqueued, however a reference to \'comment-reply\' was found. Make sure that the comment-reply script is being enqueued properly on singular pages.', 'theme-check' )
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

$themechecks[] = new Comment_Reply_Check();
