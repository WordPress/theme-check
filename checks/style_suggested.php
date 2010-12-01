<?php
class Style_Suggested implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		// combine all the css files into one string to make it easier to search
		$css = implode(' ', $css_files);
		
		checkcount();
		$ret = true;

		$checks = array(
			'^Tags:' => 'Tags:'
			);

		foreach ($checks as $key => $check) {
			if ( !preg_match( '/' . $key . '/mi', $css, $matches ) ) {
				$this->error[] = "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: <strong>{$check}</strong> is missing from your style.css header.";
				$ret = false;
			}
		}
		
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Style_Suggested;
