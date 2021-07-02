<?php
/**
 * Check for filesystem operations and HTTP requests.
 *
 * @package Theme Check
 */

/**
 * Check for filesystem operations and HTTP requests.
 */
class Filesystem_HTTP_Check implements themecheck {
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
			// Filesystem operations are not advised. file_get_contents() is allowed.
			'/[^a-z0-9](?<!_)(readfile|fopen)\s?\(/i' => __( 'File read operations should use file_get_contents() but are discouraged unless required', 'theme-check' ),
			'/[^a-z0-9](?<!_)(fopen|fclose|fread|fwrite|file_put_contents)\s?\(/i' => __( 'File write operations should are avoided unless necessary', 'theme-check' ),
			// HTTP Requests should use WP_HTTP.
			'/[^a-z0-9](?<!_)(curl_exec|curl_init|fsockopen|pfsockopen|stream_context_create)\s?\(/' => __( 'HTTP requests should be made using the WordPress HTTP wrappers, such as wp_safe_remote_get() and wp_safe_remote_post()', 'theme-check' ),
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();

				if ( preg_match_all( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );

					foreach ( $matches[1] as $match ) {
						$error = ltrim( $match, '(' );
						$error = rtrim( $error, '(' );

						$grep          = tc_grep( $error, $php_key );
						$this->error[] = sprintf(
							'<span class="tc-lead tc-warning">%s</span>: %s %s',
							__( 'WARNING', 'theme-check' ),
							sprintf(
								__( '%1$s was found in the file %2$s. %3$s.', 'theme-check' ),
								'<strong>' . $error . '</strong>',
								'<strong>' . $filename . '</strong>',
								$check
							),
							$grep
						);
					}
				}
			}
		}

		foreach ( $php_files as $php_key => $phpfile ) {

			$checks = array(
				// WP_Filesystem should only be used for theme upgrade operations. It should not be used to avoid the fopen()/file_put_contents()/etc warnings.
				'/[^a-z0-9](?<!_)(WP_Filesystem)\s?\(/i' => __( 'Theme Check is not able to determine if WP_Filesystem is used correctly. WP_Filesystem should only be used for theme upgrade operations, not for all file operations. Before continuing, you must manually review the code. Consider using file_get_contents(), scandir(), or glob(). It is not recommended to make changes to third-party frameworks that use WP_Filesystem, for example, TGMPA', 'theme-check' ),
			);

			foreach ( $checks as $key => $check ) {
				checkcount();

				if ( preg_match_all( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );

					foreach ( $matches[1] as $match ) {
						$error = ltrim( $match, '(' );
						$error = rtrim( $error, '(' );

						$grep          = tc_grep( $error, $php_key );
						$this->error[] = sprintf(
							'<span class="tc-lead tc-info">%s</span>: %s <br><code>%s</code>',
							__( 'INFO', 'theme-check' ),
							sprintf(
								__( '%1$s was found in the file %2$s. %3$s.', 'theme-check' ),
								'<strong>' . $error . '</strong>',
								'<strong>' . $filename . '</strong>',
								$check
							),
							$grep
						);
					}
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

$themechecks[] = new Filesystem_HTTP_Check();
