<?php

class More_Deprecated implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;

		$checks = array(
			'get_bloginfo\(\s?("|\')home("|\')\s?\)' => 'get_bloginfo( \'url\' )',
			'bloginfo\(\s?("|\')home("|\')\s?\)' => 'bloginfo( \'url\' )'
			);

		foreach ($php_files as $php_key => $phpfile) {
			foreach ($checks as $key => $check) {
			checkcount();
				if ( preg_match( '/[\s|]' . $key . '/', $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = rtrim($matches[0],'(');
					$grep = tc_grep( $error, $php_key);
					$this->error[] = "<span class='tc-lead tc-required'>REQUIRED</span>: <strong>{$error}</strong> was found in the file <strong>{$filename}</strong>. Use <strong>{$check}</strong> instead.{$grep}";
					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new More_Deprecated;
