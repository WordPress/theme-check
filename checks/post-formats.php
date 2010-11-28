<?php

class PostFormatCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
		$ret = true;
		
		// combine all the php files into one string to make it easier to search
		$php = implode(' ', $php_files);
checkcount();

		$checks = array(
		'/add_theme_support\((\s|)("|\')post-formats("|\')/m'
			);

		foreach ($php_files as $php_key => $phpfile) {
		foreach ($checks as $check) {
		checkcount();
			if ( preg_match( $check, $phpfile, $matches ) ) {
if ( !strpos( $php, 'get_post_format' ) || !strpos( $php, 'has_post_format' ) ) {
			    $filename = basename($php_key);
				$error = esc_html( rtrim($matches[0],'(') );
$grep = tc_grep( rtrim($matches[0],'('), $php_key);
				$this->error[] = "REQUIRED<strong>{$error}</strong> was found in the file <strong>{$filename}</strong>. However get_post_format and/or has_post_format were not found!";
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
