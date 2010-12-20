<?php
class WormCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
		$ret = true;

		$checks = array(
			'/wshell\.php/'=>'<strong>PHP shell was found!</strong>',

			);

		foreach ($php_files as $php_key => $phpfile) {
			foreach ($checks as $key => $check) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = $matches[0];
					$grep = tc_grep( $error, $php_key);
					$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: {$check} in <strong>{$filename}</strong>{$grep}";
					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new WormCheck;
