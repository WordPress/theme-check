<?php
/**
 * Check classic themes for add_theme_support( 'title-tag' ).
 *
 * @package Theme Check
 */

/**
 * Check classic themes for add_theme_support( 'title-tag' ).
 */
class Theme_Support_Title_Tag_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		$php = implode( ' ', $php_files );

		// Look for add_theme_support( 'title-tag' ).
		if ( ! preg_match( '#add_theme_support\s?\(\s?[\'|"]title-tag#', $php ) ) {
			$ret           = false;
			$this->error[] = sprintf(
				'<span class="tc-lead tc-required">%s</span>: %s',
				__( 'REQUIRED', 'theme-check' ),
				__( 'No reference to <strong>add_theme_support( "title-tag" )</strong> was found in the theme.', 'theme-check' )
			);
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

$themechecks[] = new Theme_Support_Title_Tag_Check();
