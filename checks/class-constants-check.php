<?php
/**
 * Checks if deprecated constants are included
 *
 * @package Theme Check
 */

/**
 * Checks if deprecated constants are included.
 *
 * Checks if deprecated constants are included. If they are, require them to be removed.
 *
 * @see https://core.trac.wordpress.org/changeset/20212
 */
class Constants_Check implements themecheck {
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

		$checks = array(
			'STYLESHEETPATH'      => 'get_stylesheet_directory()',
			'TEMPLATEPATH'        => 'get_template_directory()',
			'PLUGINDIR'           => 'WP_PLUGIN_DIR',
			'MUPLUGINDIR'         => 'WPMU_PLUGIN_DIR',
			'HEADER_IMAGE'        => 'add_theme_support( \'custom-header\' )',
			'NO_HEADER_TEXT'      => 'add_theme_support( \'custom-header\' )',
			'HEADER_TEXTCOLOR'    => 'add_theme_support( \'custom-header\' )',
			'HEADER_IMAGE_WIDTH'  => 'add_theme_support( \'custom-header\' )',
			'HEADER_IMAGE_HEIGHT' => 'add_theme_support( \'custom-header\' )',
			'BACKGROUND_COLOR'    => 'add_theme_support( \'custom-background\' )',
			'BACKGROUND_IMAGE'    => 'add_theme_support( \'custom-background\' )',
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( '/[\s|\'|\"]' . $key . '(?:\'|"|;|\s)/', $phpfile, $matches ) ) {
					$filename      = tc_filename( $php_key );
					$error         = ltrim( rtrim( $matches[0], '(' ), '\'"' );
					$grep          = tc_grep( $error, $php_key );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-required">%s</span>: %s %s',
						__( 'REQUIRED', 'theme-check' ),
						sprintf(
							__( '%1$s was found in the file %2$s. Use %3$s instead.', 'theme-check' ),
							'<strong>' . $error . '</strong>',
							'<strong>' . $filename . '</strong>',
							'<strong>' . $check . '</strong>'
						),
						$grep
					);
					$ret           = false;
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

$themechecks[] = new Constants_Check();
