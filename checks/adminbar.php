<?php
/**
 * This checks if the admin bar gets hidden by the theme.
 **/
class NoHiddenAdminBar implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret      = true;

		$php_regex = "/(add_filter(\s*)\((\s*)(\"|')show_admin_bar(\"|')(\s*)(.*))|(([^\S])show_admin_bar(\s*)\((.*))/";
		$css_regex = "/(#wpadminbar)/";

		checkcount();
		// Check php files for filter show_admin_bar, show_admin_bar_front, and show_admin_bar().
		foreach ( $php_files as $file_path => $file_content ) {

			$filename = tc_filename( $file_path );

			if ( preg_match( $php_regex, $file_content, $matches ) ) {

				$error = '/show_admin_bar/';
				$grep  = tc_preg( $error, $file_path );

				$this->error[] = sprintf( '<span class="tc-lead tc-warning">' . __( 'WARNING', 'theme-check' ) . '</span>: ' . __( '%1$s Themes are not allowed to hide the admin bar. This warning must be manually checked.', 'theme-check' ),
				'<strong>' . $filename . '</strong>' ) . $grep;
			}
		}

		checkcount();
		// Check CSS Files for #wpadminbar.
		foreach ( $css_files as $file_path => $file_content ) {

			$filename = tc_filename( $file_path );
			$error    = '/#wpadminbar/';
			// Don't print minified files.
			if ( strpos( $filename, '.min.' ) === false ) {
				$grep = tc_preg( $error, $file_path );
			} else {
				$grep = '';
			}

			if ( preg_match( $css_regex, $file_content, $matches ) ) {
				$this->error[] = sprintf( '<span class="tc-lead tc-warning">' . __( 'WARNING', 'theme-check' ) . '</span>: ' . __( 'The theme is using `#wpadminbar` in %1$s. Hiding the admin bar is not allowed. This warning must be manually checked.', 'theme-check' ),
				'<strong>' . $filename . '</strong>' ) . $grep;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new NoHiddenAdminBar();
