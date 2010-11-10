<?php

class CustomCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
	
		$ret = true;
		
		// combine all the php files into one string to make it easier to search
		$php = implode(' ', $php_files);
checkcount();
		if ( strpos( $php, 'add_custom_image_header' ) === false ) {
			$this->error[] = "RECOMMENDEDNo reference to add_custom_image_header() was found in the theme. It is recommended that the theme implement this functionality if using an image for the header.";
		}

		if ( strpos( $php, 'add_custom_background' ) === false ) {
			$this->error[] = "RECOMMENDEDNo reference to add_custom_background() was found in the theme. If the theme uses background images or solid colors for the background, then it is recommended that the theme implement this functionality.";
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new CustomCheck;
