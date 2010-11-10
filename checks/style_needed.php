<?php

// search for some bad things
class Style_Needed implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		// combine all the php files into one string to make it easier to search
		$css = implode(' ', $css_files);

		$ret = true;

		// things to check for
		$checks = array(
'^Theme Name:' => 'Theme name:',
'^Theme URI:' => 'Theme URI:',
'^Description:' => 'Description:',
'^Author:' => 'Author:',
'^Version' => 'Version:',
'\.alignleft' => '.alignleft',
'\.aligncenter' => '.aligncenter',
'\.wp-caption' => '.wp-caption',
'\.wp-caption-text' => '.wp-caption-text',
'\.gallery-caption' => '.gallery-caption',
'\.alignright' => '.alignright'
			);


		foreach ($checks as $key => $check) {
		checkcount();
			if ( !preg_match( '/' . $key . '/mi', $css, $matches ) ) {
				$this->error[] = "CSSNEEDED<strong>{$check}</strong> is missing.";
				$ret = false;
			}


		}


		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Style_Needed;
