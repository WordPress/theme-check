<?php
/**
 * Checks for common escaping issues.
 *
 * @link https://make.wordpress.org/themes/handbook/review/required/#code
 *
 * @package Theme Check
 */

/**
 * Checks for common escaping issues.
 */
class Escaping_Check implements themecheck {
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

		$ret = true;

		$warnings = array(
			'/="<\?php esc_html_e/'      => __( 'Use esc_attr_e() inside HTML attributes, and esc_url() for link attributes', 'theme-check' ),
			'/="<\?php echo esc_html__/' => __( 'Use esc_attr__() inside HTML attributes, and esc_url() for link attributes', 'theme-check' ),
			'/="<\?php esc_html\(/'      => __( 'Use esc_attr() inside HTML attributes, and esc_url() for link attributes', 'theme-check' ),
			'/><\?php echo esc_attr\(/'  => __( 'Only use esc_attr() inside HTML attributes. Use esc_html() between HTML tags', 'theme-check' ),
			'/><\?php echo esc_attr__/'  => __( 'Only use esc_attr__() inside HTML attributes. Use esc_html__() between HTML tags', 'theme-check' ),
			'/><\?php esc_attr_e/'       => __( 'Only use esc_attr_e() inside HTML attributes. Use esc_html_e() between HTML tags', 'theme-check' ),
		);

		$required = array(
			'/echo home_url/'                   => __( 'home_url() must be escaped. Use esc_url() for link attributes', 'theme-check' ),
			'/echo get_template_directory_uri/' => __( 'get_template_directory_uri() must be escaped when output as part of a link or image source. Use esc_url() for link attributes', 'theme-check' ),
		);

		foreach ( $php_files as $php_key => $phpfile ) {

			checkcount();
			if ( false !== strpos( $phpfile, 'echo get_theme_mod' ) ) {
				$filename      = tc_filename( $php_key );
				$error         = 'echo get_theme_mod';
				$grep          = tc_grep( $error, $php_key );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s %s',
					__( 'WARNING', 'theme-check' ),
					sprintf(
						__( 'Found %1$s in %2$s. <a href="%3$s" target="_blank">Theme options must be escaped (Opens in a new window).</a>. ', 'theme-check' ),
						'<code>' . esc_html( $error ) . '</code>',
						'<strong>' . $filename . '</strong>',
						'https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/#escaping-securing-output'
					),
					$grep
				);

			}

			foreach ( $warnings as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename      = tc_filename( $php_key );
					$error         = $matches[0];
					$grep          = tc_grep( $error, $php_key );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-warning">%s</span>: %s %s',
						__( 'WARNING', 'theme-check' ),
						sprintf(
							__( 'Found %1$s in %2$s. %3$s. A manual review is needed.', 'theme-check' ),
							'<code>' . esc_html( $error ) . '</code>',
							'<strong>' . $filename . '</strong>',
							$check
						),
						$grep
					);
				}
			}

			foreach ( $required as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename      = tc_filename( $php_key );
					$error         = $matches[0];
					$grep          = tc_grep( $error, $php_key );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-required">%s</span>: %s %s',
						__( 'REQUIRED', 'theme-check' ),
						sprintf(
							__( 'Found %1$s in %2$s. %3$s. A manual review is needed.', 'theme-check' ),
							'<code>' . esc_html( $error ) . '</code>',
							'<strong>' . $filename . '</strong>',
							$check
						),
						$grep
					);

					$ret = false;
				}
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

$themechecks[] = new Escaping_Check();
