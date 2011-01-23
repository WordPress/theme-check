<?php
class AdminMenu implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;


//check for user roles deprecated in 2.0.

		$checks = array(
			'/(add_(admin|submenu|menu|dashboard|posts|media|links|pages|comments|theme|plugins|users|management|options)_page\s?\(.*?\);)/' => __( 'User levels were deprecated in <strong>2.0</strong>. Please see <a href="http://codex.wordpress.org/Roles_and_Capabilities">Roles_and_Capabilities</a>', 'theme-check' )
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					if ( preg_match( '/,\s?[0-9]\s?,/', $matches[0] ) ) {
					$grep = tc_preg( '/add_(?:p(?:ost|age|lugin)s|m(?:edia|anagement)|admin|submenu|dashboard|(?:link|comment|user|option)s|theme)_page/', $php_key );
					$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: <strong>{$filename}</strong>. {$check}{$grep}";
					$ret = false;
}
				}
			}
		}

//check for add_admin_page

		$checks = array(
			'/(add_(admin|submenu|menu|dashboard|posts|media|links|pages|comments|plugins|users|management|options)_page\()/' => __( 'Themes should use <strong>add_theme_page()</strong> for adding admin pages.', 'theme-check' )
			);


		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = rtrim( $matches[0], '(' );
					$grep = tc_grep( $error, $php_key );
					$this->error[] = "<span class='tc-lead tc-required'>REQUIRED</span>: <strong>{$filename}</strong>. {$check}{$grep}";
					$ret = false;
				}
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new AdminMenu;