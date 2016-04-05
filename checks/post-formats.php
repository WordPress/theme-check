<?php

class PostFormatCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		$php = implode( ' ', $php_files );
		$css = implode( ' ', $css_files );

		checkcount();

		$checks = array(
			'/add_theme_support\(\s?("|\')post-formats("|\')/m'
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $check ) {
				checkcount();
				if ( preg_match( $check, $phpfile, $matches ) ) {
					if ( !strpos( $php, 'get_post_format' ) && !strpos( $php, 'has_post_format' ) && !strpos( $css, '.format' ) ) {
						$filename = tc_filename( $php_key );
						$matches[0] = str_replace(array('"',"'"),'', $matches[0]);
						$error = esc_html( rtrim($matches[0], '(' ) );
						$grep = tc_grep( rtrim($matches[0], '(' ), $php_key);
						$this->error[] = '<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.sprintf( __('%1$s was found in the file %2$s.', 'theme-check'), '<strong>' . $error . '</strong>', '<strong>' . $filename . '</strong>' ).' '.sprintf( __('However %1$s and/or %2$s were not found, and no use of formats in the CSS was detected.', 'theme-check'), '<strong>get_post_format</strong>', '<strong>has_post_format</strong>' );
						$ret = false;
					}
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new PostFormatCheck;