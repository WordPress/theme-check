<?php
/**
 * Check if a <script> tag is included in header.php or footer.php
 *
 * @package Theme Check
 */

/**
 * Check if:
 * A <script> tag is included in header.php or footer.php
 */
class Script_Tag_Check implements themecheck {
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
		foreach ( $php_files as $file_path => $file_content ) {
			checkcount();

			// This check is limited to header.php and footer.php.
			$filename = tc_filename( $file_path );
			if ( ! in_array( $filename, array( 'header.php', 'footer.php' ) ) ) {
				continue;
			}

			if ( false !== stripos( $file_content, '<script' ) ) {
				$grep          = tc_preg( '/<script/i', $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-recommended">%s</span>: %s %s',
					__( 'RECOMMENDED', 'theme-check' ),
					sprintf(
						__( 'Found a script tag in %s. Scripts and styles need to be enqueued or added via a hook, otherwise it is more difficult to remove or replace them with plugins or child themes.', 'theme-check' ),
						'<strong>' . $filename . '</strong>'
					),
					$grep
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

$themechecks[] = new Script_Tag_Check();
