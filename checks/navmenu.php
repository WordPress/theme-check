<?php

class NavMenuCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );
		checkcount();
		if ( strpos( $php, 'nav_menu' ) === false ) {
			$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.sprintf( __( 'No reference to %s was found in the theme.', 'theme-check'), '<strong>nav_menu</strong>' ).' '.sprintf(__( 'Note that if your theme has a menu bar, it is required to use the WordPress %s functionality for it.', 'theme-check' ), '<strong>nav_menu</strong>' );
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new NavMenuCheck;