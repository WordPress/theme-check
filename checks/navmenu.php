<?php

class NavMenuCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );
		checkcount();
		if ( strpos( $php, 'nav_menu' ) === false ) {
			$this->error[] = __( "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: No reference to nav_menu's was found in the theme. Note that if your theme has a menu bar, it is required to use the WordPress nav_menu functionality for it.", "themecheck" );
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new NavMenuCheck;