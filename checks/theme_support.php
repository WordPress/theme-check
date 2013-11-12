<?php
class Theme_Support implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$checks = array(
//			'/add_theme_support\(\s?("|\')custom-headers("|\')\s?\)/' => 'add_custom_image_header()',
//			'/add_theme_support\(\s?("|\')custom-background("|\')\s?\)/' => 'add_custom_background()',
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$matches[0] = str_replace(array('"',"'"),'', $matches[0]);
					$error = esc_html( rtrim( $matches[0], '(' ) );
					$grep = tc_grep( rtrim( $matches[0], '(' ), $php_key );
					$this->error[] = sprintf( '<span class="tc-lead tc-required">'. __( 'REQUIRED', 'theme-check' ) . '</span>: ' .
						/* translators: 1: function found, 2: filename, 3: replacement function, 4: grep result from file */
						__( '<strong>%1$s</strong> was found in the file <strong>%2$s</strong>. Use <strong>%3$s</strong> instead.%4$s', 'theme-check'), $error, $filename, $check, $grep );
					$ret = false;
				}
			}

		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Theme_support;