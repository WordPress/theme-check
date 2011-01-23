<?php

class TagCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );
		checkcount();
		$ret = true;
		if ( strpos( $php, 'the_tags' ) === false && strpos( $php, 'get_the_tag_list' ) === false && strpos( $php, 'get_the_term_list' ) === false ) {
			$this->error[] = __( "<span class='tc-lead tc-required'>REQUIRED</span>: This theme doesn't seem to display tags. Modify it to display tags in appropriate locations.", "themecheck" );
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new TagCheck;