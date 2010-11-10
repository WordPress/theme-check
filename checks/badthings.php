<?php

// search for some bad things
class Bad_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
		// combine all the php files into one string to make it easier to search
	//	$php = implode(' ', $php_files);

		$ret = true;

		// things to check for
		$checks = array(
			'/[\s|]eval\([^\$|\'](.){25}/' => 'eval() is not allowed.',
			'/base64_decode/ms' => 'base64_decode() is not allowed',
			'/uudecode/ms' => 'uudecode() is not allowed',
			'/str_rot13/ms' => 'str_rot13() is not allowed',
			'/[^_]unescape/ms' => 'unescape() is not allowed',
			'/cx=[0-9]{21}:[a-z0-9]{10}/ms' => 'Google search code detected',
			'/pub-[0-9]{16}/' => 'Googe advertising code detected'

			);
$grep = '';
		foreach ($php_files as $php_key => $phpfile) {
		foreach ($checks as $key => $check) {
		checkcount();
			if ( preg_match( $key, $phpfile, $matches ) ) {
			    $filename = basename($php_key);
				$error = rtrim($matches[0],'(');
$grep = tc_grep( $error, $php_key);
				$this->error[] = "CRITICALFound <strong>{$error}</strong> in the file <strong>{$filename}</strong> {$check}.{$grep}";
				$ret = false;
			}
		}
}

			$checks = array(
			'/uudecode/ms' => 'uudecode() is not allowed',
			'/unescape/ms' => 'unescape() is not allowed',
			'/cx=[0-9]{21}:[a-z0-9]{10}/ms' => 'Google search code detected',
			'/pub-[0-9]{16}/' => 'Googe advertising code detected'
			);


		foreach ($other_files as $php_key => $phpfile) {
		foreach ($checks as $key => $check) {
		checkcount();
			if ( preg_match( $key, $phpfile, $matches ) ) {
			    $filename = basename($php_key);
				$error = rtrim($matches[0],'(');
$grep = tc_grep( $error, $php_key);
				$this->error[] = "CRITICALFound <strong>{$error}</strong> in the file <strong>{$filename}</strong> {$check}.{$grep}";
				$ret = false;
			}
		}
}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Bad_Checks;
