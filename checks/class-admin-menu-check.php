<?php
/**
 * Check for creation of admin menus
 *
 * Checks that user levels are not used when creating admin menus.
 * Checks that only add_theme_page, add_menu_page and add_submenu_page are used to create admin menus.
 *
 * @package Theme Check
 */

/**
 * Check for creation of admin menus.
 *
 * Checks that user levels are not used when creating admin menus.
 * Checks that only add_theme_page, add_menu_page and add_submenu_page are used to create admin menus.
 */
class Admin_Menu_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		// Check for levels deprecated in 2.0 in creating menus.
		$checks = array(
			'/([^_](add_(admin|submenu|menu|dashboard|posts|media|links|pages|comments|theme|plugins|users|management|options)_page)\s?\([^,]*,[^,]*,\s[\'|"]?(level_[0-9]|[0-9])[^;|\r|\r\n]*)/' => __( 'User levels were deprecated in <strong>2.0</strong>. Please see <a href="https://wordpress.org/support/article/roles-and-capabilities/">Roles and Capabilities</a>', 'theme-check' ),
			'/[^a-z0-9](current_user_can\s?\(\s?[\'\"]level_[0-9][\'\"]\s?\))[^\r|\r\n]*/' => __( 'User levels were deprecated in <strong>2.0</strong>. Please see <a href="https://wordpress.org/support/article/roles-and-capabilities/">Roles and Capabilities</a>', 'theme-check' ),
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename      = tc_filename( $php_key );
					$grep          = tc_grep( isset( $matches[2] ) ? $matches[2] : $matches[1], $php_key );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-warning">%s</span>: <strong>%s</strong>. %s %s',
						__( 'WARNING', 'theme-check' ),
						$filename,
						$check,
						$grep
					);
				}
			}
		}

		// Check for add_admin_page's, except for add_theme_page.
		// Note to TGMPA: Stop trying to bypass theme check.
		$checks = array(
			'/(?<!function)[^_>:](add_[a-z]+_page)/' => _x(
				'Themes should not use <strong>%s()</strong>.',
				'function name',
				'theme-check'
			),
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match_all( $key, $phpfile, $matches ) ) {
					foreach ( $matches[1] as $match ) {
						if ( in_array( $match, array( 'add_theme_page', 'add_menu_page', 'add_submenu_page' ), true ) ) {
							continue;
						}
						$filename   = tc_filename( $php_key );
						$error      = ltrim( rtrim( $match, '(' ) );
						$grep       = tc_grep( $error, $php_key );
						$notallowed = sprintf( $check, $match );

						$this->error[] = sprintf(
							'<span class="tc-lead tc-recommended">%s</span>: <strong>%s</strong>. %s %s',
							__( 'RECOMMENDED', 'theme-check' ),
							$filename,
							$notallowed,
							$grep
						);
					}
				}
			}
		}

		return true;
	}

	/**
	 * Get error messages from the checks.
	 *
	 * @return array Error message.
	 */
	public function getError() {
		return $this->error;
	}
}

$themechecks[] = new Admin_Menu_Check();
