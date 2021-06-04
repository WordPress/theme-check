<?php
/**
 * Checks if nav menus are supported and if theme location or menu names are used
 *
 * @package Theme Check
 */

/**
 * Checks if nav menus are supported and if theme location or menu names are used.
 */
class Nav_Menu_Check implements themecheck {
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

		$ret        = true;
		$name_check = false;

		$php = implode( ' ', $php_files );

		checkcount();
		if ( strpos( $php, 'nav_menu' ) === false ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( "No reference to nav_menu's was found in the theme. Note that if your theme has a menu bar, it is required to use the WordPress nav_menu functionality for it.", 'theme-check' )
			);
		}

		// Look for add_theme_support( 'menus' ).
		checkcount();
		if ( preg_match( '/add_theme_support\s*\(\s?("|\')menus("|\')\s?\)/', $php ) ) {
			/* translators: 1: function found, 2: function to be used */
			$this->error[] = sprintf(
				'<span class="tc-lead tc-required">%s</span>: %s',
				__( 'REQUIRED', 'theme-check' ),
				sprintf(
					__( 'Reference to %1$s was found in the theme. This should be removed and %2$s used instead.', 'theme-check' ),
					'<strong>add_theme_support( "menus" )</strong>',
					'<a href="https://developer.wordpress.org/reference/functions/register_nav_menus/">register_nav_menus()</a>'
				)
			);
			$ret           = false;
		}

		foreach ( $php_files as $file_path => $file_content ) {
			$filename = tc_filename( $file_path );

			// We are checking for wp_nav_menu( specifically, to allow wp_nav_menu  and wp_nav_menu_item in filters etc.
			if ( strpos( $file_content, 'wp_nav_menu(' ) !== false ) {
				$menu_part = explode( 'wp_nav_menu(', $file_content );
				$menu_part = explode( ';', $menu_part[1] );

				// If there is a menu, check for a theme location, which is required.
				// Check if the arguments are placed outside wp_nav_menu.
				checkcount();
				if ( strpos( $menu_part[0], '$' ) !== false && strpos( $menu_part[0], 'theme_location' ) === false ) {
					$menu_args     = explode( '$', $menu_part[0], 1 );
					$name          = explode( ')', $menu_args[0] );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-warning">%s</span>: %s',
						__( 'WARNING', 'theme-check' ),
						sprintf(
							__( 'A menu without a theme_location was found in %1$s. %2$s is used inside wp_nav_menu(). You must manually check if the theme_location is included.', 'theme-check' ),
							'<strong>' . $filename . '</strong>',
							'<code>' . $name[0] . '</code>'
						)
					);
					$name_check    = true;
				} else {
					checkcount();
					if ( strpos( $menu_part[0], 'theme_location' ) === false ) {
						$this->error[] = sprintf(
							'<span class="tc-lead tc-required">%s</span>: %s',
							__( 'REQUIRED', 'theme-check' ),
							sprintf(
								__( 'A menu without a theme_location was found in %1$s.', 'theme-check' ),
								'<strong>' . $filename . '</strong>'
							)
						);
						$ret           = false;
						$name_check    = true;
					}
				}

				// We only need to warn for the menu name if theme location is not set.
				checkcount();
				if ( $name_check === true && preg_match( '/("|\')menu("|\').*?=>/', $menu_part[0] ) ) {
					$this->error[] = sprintf(
						'<span class="tc-lead tc-required">%s</span>: %s',
						__( 'REQUIRED', 'theme-check' ),
						sprintf(
							__( 'A menu name is being used for a menu in %1$s. By using menu name, the menu would be required to have the exact same name in the WordPress admin area. Use a theme_location instead.', 'theme-check' ),
							'<strong>' . $filename . '</strong>'
						)
					);
					$ret           = false;
				}
			}
		}

		return $ret;
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

$themechecks[] = new Nav_Menu_Check();
