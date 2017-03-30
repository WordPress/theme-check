<?php

class NavMenuCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );
		checkcount();
		if ( strpos( $php, 'nav_menu' ) === false ) {
			$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.__("No reference to nav_menu's was found in the theme. Note that if your theme has a menu bar, it is required to use the WordPress nav_menu functionality for it.", 'theme-check' );
		}

		// Look for add_theme_support( 'menus' ).
		checkcount();
		if ( preg_match( '/add_theme_support\s*\(\s?("|\')menus("|\')\s?\)/', $php ) ) {
			/* translators: 1: function found, 2: function to be used */
			$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check') . '</span>: ' . sprintf( __( 'Reference to %1$s was found in the theme. This should be removed and %2$s used instead.', 'theme-check' ), '<strong>add_theme_support( "menus" )</strong>', '<a href="https://developer.wordpress.org/reference/functions/register_nav_menus/">register_nav_menus()</a>' );
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new NavMenuCheck;
