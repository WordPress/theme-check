<?php
class AdminMenu implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;


//check for user roles deprecated in 2.0.

		$checks = array(
			'/(add_(admin|submenu|theme)_page\s?\x28.*,\s?[0-9]\s?,)/' => 'User levels were deprecated in <strong>2.0</strong>. Please see <a href="http://codex.wordpress.org/Roles_and_Capabilities">Roles_and_Capabilities</a>'
			);

		foreach ($php_files as $php_key => $phpfile) {
			foreach ($checks as $key => $check) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = $matches[0];
					$grep = tc_preg( '/(add_(admin|submenu|theme)_page\s?\x28.*,\s?[0-9]\s?,)/', $php_key);
					if ( !$grep ) $grep = tc_preg( '/(add_(admin|submenu|theme)_page)/', $php_key);

					$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: <strong>{$filename}</strong>. {$check}{$grep}";
					$ret = false;
				}
			}
		}

//check for add_admin_page

		$checks = array(
			'/add_admin_page\(/' => 'Themes should use <strong>add_theme_page()</strong> for adding admin pages.'
			);


		foreach ($php_files as $php_key => $phpfile) {
			foreach ($checks as $key => $check) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = rtrim( $matches[0], '(' );
					$grep = tc_grep( $error, $php_key);
					$this->error[] = "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: <strong>{$filename}</strong>. {$check}{$grep}";

				}
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new AdminMenu;