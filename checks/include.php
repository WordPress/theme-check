<?php

class IncludeCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;

		$checks = array( '/(?:include(?:_once)?\x28|require(?:_once)?\x28)/' => __( 'The theme appears to use include or require. If these are being used to include separate sections of a template from independent files, then <strong>get_template_part()</strong> should be used instead.', 'theme-check' ) );

		foreach ($php_files as $php_key => $phpfile) {
			foreach ($checks as $key => $check) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = '/(?:include(?:_once)?\x28|require(?:_once)?\x28)[^;]*;/';
					$grep = tc_preg( $error, $php_key);
					$this->error[] = "<span class='tc-lead tc-info'>INFO</span>: {$check} {$grep}";
				}
			}

		}
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new IncludeCheck;
