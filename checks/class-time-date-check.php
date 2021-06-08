<?php
/**
 * Check if hard coded dates are used
 *
 * @package Theme Check
 */

/**
 * Check if hard coded dates are used.
 */
class Time_Date_Check implements themecheck {
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
			'/\sdate_i18n\(\s?["|\'][A-Za-z\s]+\s?["|\']\)/' => 'date_i18n( get_option( \'date_format\' ) )',
			'/[^get_]the_date\(\s?["|\'][A-Za-z\s]+\s?["|\']\)/' => 'the_date( get_option( \'date_format\' ) )',
			'/[^get_]the_time\(\s?["|\'][A-Za-z\s]+\s?["|\']\)/' => 'the_time( get_option( \'date_format\' ) )',
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename      = tc_filename( $php_key );
					$matches[0]    = str_replace( array( '"', "'" ), '', $matches[0] );
					$error         = trim( esc_html( rtrim( $matches[0], '(' ) ) );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-info">%s</span> %s',
						__( 'INFO', 'theme-check' ),
						sprintf(
							__( 'At least one hard coded date was found in the file %1$s. Consider %2$s instead.', 'theme-check' ),
							'<strong>' . $filename . '</strong>',
							"<strong>get_option( 'date_format' )</strong>"
						)
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

$themechecks[] = new Time_Date_Check();
