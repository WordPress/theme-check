<?php
/**
 * Check if JQuery is enqueued on the front end.
 *
 * @package Theme Check
 */

/**
 * Check if JQuery is enqueued on the front end.
 */
class JQuery_Check implements themecheck {
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

		if ( ! preg_match( '/wp_enqueue_script\(\s?("|\')jquery("|\')/i', $php ) ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-warning">%s</span>: %s',
				__( 'RECOMMENDED', 'theme-check' ),
				sprintf(
					'JQuery is enqueued on the front end, however you may not need it, see <a href="%s">this article</a> for tips on switching to vanilla JavaScript.',
					'https://tobiasahlin.com/blog/move-from-jquery-to-vanilla-javascript/'
				)
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

$themechecks[] = new JQuery_Check();
