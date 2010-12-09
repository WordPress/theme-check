<?php
class WormCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
		$ret = true;

		$checks = array(
			'/wshell\.php/'=>'Worm activity detected!'

			);

		foreach ($php_files as $php_key => $phpfile) {
			foreach ($checks as $check=>$error) {
				checkcount();
				if ( preg_match( $check, $phpfile, $matches ) ) {
					$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: <strong>{$error}</strong>";
					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new WormCheck;
