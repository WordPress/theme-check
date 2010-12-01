<?php

class CustomCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
	
		$ret = true;
		$php = implode(' ', $php_files);

		checkcount();

		if ( strpos( $php, 'add_custom_image_header' ) === false ) {
			$this->error[] = "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: No reference to <strong>add_custom_image_header</strong> was found in the theme. It is recommended that the theme implement this functionality if using an image for the header.";
		}

		if ( strpos( $php, 'add_custom_background' ) === false ) {
			$this->error[] = "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: No reference to <strong>add_custom_background()</strong> was found in the theme. If the theme uses background images or solid colors for the background, then it is recommended that the theme implement this functionality.";
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new CustomCheck;
