<?php

class GravatarCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
	
		// combine all the php files into one string to make it easier to search
		$php = implode(' ', $php_files);
		checkcount();
		$ret = true;		
		if ( ( strpos( $php, 'get_avatar' ) === false ) && ( strpos( $php, 'wp_list_comments' ) === false ) ) {
			$this->error[] = "REQUIREDThis theme doesn't seem to support the standard avatar functions. Use <strong>get_avatar</strong> or <strong>wp_list_comments</strong> to add this support.";
			$ret = false;
		}
		
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new GravatarCheck;
