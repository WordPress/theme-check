<?php
class Tag_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;

		foreach ($php_files as $php_key => $phpfile) {
			checkcount();
			if (preg_match( '/<\?(?!php|xml)/', $phpfile)){
			$file = basename($php_key);
			$grep = tc_grep( $error, $php_key);
			$this->error[] = "CRITICALFound PHP short tags in file <strong>{$file}</strong>.{$grep}";
			$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Tag_Checks;
