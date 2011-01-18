<?php

class SearchFormCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		// combine all the php files into one string to make it easier to search
		$php = implode(' ', $php_files);
		checkcount();
		$ret = true;

		if ( preg_match( '/(include\s?\(\s?TEMPLATEPATH\s?\.?\s?["|\']\/searchform.php["|\']\s?\))/', $php ) ) {
			$this->error[] = "<span class='tc-lead tc-required'>REQUIRED</span>: Please use <strong>get_search_form()</strong> instead of including searchform.php directly.";
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new SearchFormCheck;
