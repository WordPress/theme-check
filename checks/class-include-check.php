<?php
/**
 * Check if include or required is used.
 *
 * @package Theme Check
 */

/**
 * Check if include or required is used.
 *
 * Check if include or required is used. If they are, inform that they should not be used for templates.
 */
class Include_Check implements themecheck {
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
		$checks = array(
			'/(?<![a-z0-9_\'"])(?:require|include)(?:_once)?\s?[\'"\(]/i' => __( 'The theme appears to use include or require. If these are being used to include separate sections of a template from independent files, then <strong>get_template_part()</strong> should be used instead.', 'theme-check' ),
		);

		foreach ( $php_files as $file_path => $file_content ) {
			foreach ( $checks as $check_regex => $check ) {
				checkcount();

				$filename = tc_filename( $file_path );
				// This doesn't apply to functions.php.
				if ( $filename === 'functions.php' ) {
					continue;
				}

				if ( preg_match( $check_regex, $file_content, $matches ) ) {
					$grep          = tc_preg( '/(?<![a-z0-9_\'"])(?:require|include)(?:_once)?\s?[\'"\(]/i', $file_path );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-info">%s</span>: <strong>%s</strong> %s %s',
						__( 'INFO', 'theme-check' ),
						$filename,
						$check,
						$grep
					);
				}
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

$themechecks[] = new Include_Check();
