<?php
class Theme_Support implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$checks = array(
			'/add_theme_support\(\s?("|\')custom-headers("|\')\s?\)/' => 'add_custom_image_header()',
			'/add_theme_support\(\s?("|\')custom-background("|\')\s?\)/' => 'add_custom_background()',
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$matches[0] = str_replace(array('"',"'"),'', $matches[0]);
					$error = esc_html( rtrim( $matches[0], '(' ) );
					$grep = tc_grep( rtrim( $matches[0], '(' ), $php_key );
					$this->error[] = "<span class='tc-lead tc-required'>REQUIRED</span>: <strong>{$error}</strong> was found in the file <strong>{$filename}</strong>. Use <strong>{$check}</strong> instead.{$grep}";
					$ret = false;
				}
			}

		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Theme_support;