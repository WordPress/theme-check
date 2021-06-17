<?php
/**
 * Checks for the use of deprecated function parameters.
 *
 * @package Theme Check
 */

/**
 * Checks for the use of deprecated function parameters.
 */
class Deprecated_Param_Check implements themecheck {
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
			'get_bloginfo' => array(
				'home'                 => 'home_url()',
				'url'                  => 'home_url()',
				'wpurl'                => 'site_url()',
				'stylesheet_directory' => 'get_stylesheet_directory_uri()',
				'template_directory'   => 'get_template_directory_uri()',
				'template_url'         => 'get_template_directory_uri()',
				'text_direction'       => 'is_rtl()',
				'feed_url'             => "get_feed_link( 'feed' ), where feed is rss, rss2 or atom",
			),
			'bloginfo'     => array(
				'home'                 => 'echo esc_url( home_url() )',
				'url'                  => 'echo esc_url( home_url() )',
				'wpurl'                => 'echo esc_url( site_url() )',
				'stylesheet_directory' => 'echo esc_url( get_stylesheet_directory_uri() )',
				'template_directory'   => 'echo esc_url( get_template_directory_uri() )',
				'template_url'         => 'echo esc_url( get_template_directory_uri() )',
				'text_direction'       => 'is_rtl()',
				'feed_url'             => "echo esc_url( get_feed_link( 'feed' ) ), where feed is rss, rss2 or atom",
			),
			'get_option'   => array(
				'home'     => 'home_url()',
				'site_url' => 'site_url()',
			),
		);

		foreach ( $php_files as $php_key => $php_file ) {
			// Loop through all functions.
			foreach ( $checks as $function => $data ) {
				checkcount();

				// Loop through the parameters and look for all function/parameter combinations.
				foreach ( $data as $parameter => $replacement ) {
					if ( preg_match( '/' . $function . '\(\s*("|\')' . $parameter . '("|\')\s*\)/', $php_file, $matches ) ) {
						$filename      = tc_filename( $php_key );
						$error         = ltrim( rtrim( $matches[0], '(' ) );
						$grep          = tc_grep( $error, $php_key );
						$this->error[] = sprintf(
							'<span class="tc-lead tc-warning">%s</span>: %s %s',
							__( 'WARNING', 'theme-check' ),
							sprintf(
								__( '%1$s was found in the file %2$s. Use %3$s instead.', 'theme-check' ),
								'<strong>' . $error . '</strong>',
								'<strong>' . $filename . '</strong>',
								'<strong>' . $replacement . '</strong>'
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

$themechecks[] = new Deprecated_Param_Check();
