<?php

class PostFormatCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
	
		$ret = true;
		
		// combine all the php files into one string to make it easier to search
		$php = implode(' ', $php_files);
checkcount();
		if ( strpos( $php, 'the_post_thumbnail' ) === false ) {
			$this->error[] = "RECOMMENDEDNo reference to <strong>post-formats</strong> was found in the theme. It is recommended that the theme implement this functionality.";
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new PostFormatCheck;
