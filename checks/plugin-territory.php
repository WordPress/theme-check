<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Checks for Plugin Territory Guidelines.
 *
 * @link https://make.wordpress.org/themes/handbook/review/required/#presentation-vs-functionality
 */

/**
 * Checks for Plugin Territory
 */
class Plugin_Territory implements themecheck {
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
		$php = implode( ' ', $php_files );

		// Functions that are required to be removed from the theme.
		$forbidden_functions = array(
			'register_post_type',
			'register_taxonomy',
			'wp_add_dashboard_widget',
			'register_block_type',
		);

		foreach ( $forbidden_functions as $function ) {
			checkcount();
			if ( preg_match( '/[\s?]' . $function . '\s?\(/', $php ) ) {
				$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . sprintf( __( 'The theme uses the %s function, which is plugin-territory functionality.', 'theme-check' ), '<strong>' . esc_html( $function ) . '()</strong>' );
				$ret           = false;
			}
		}

		// Shortcodes can't be used in the post content, so warn about them.
		if ( false !== strpos( $php, 'add_shortcode(' ) ) {
			checkcount();
			$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . sprintf( __( 'The theme uses the %s function. Custom post-content shortcodes are plugin-territory functionality.', 'theme-check' ), '<strong>add_shortcode()</strong>' );
			$ret           = false;
		}

		// Hooks (actions & filters) that are required to be removed from the theme.
		$forbidden_hooks = array(
			'filter' => array(
				'mime_types',
				'upload_mimes',
				'user_contactmethods',
			),
			'action' => array(
				'wp_dashboard_setup',
			),
		);

		foreach ( $forbidden_hooks as $type => $hooks ) {
			foreach ( $hooks as $hook ) {
				checkcount();
				if ( preg_match( '/[\s?]add_' . $type . '\s*\(\s*([\'"])' . $hook . '([\'"])\s*,/', $php ) ) {
					$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . sprintf( __( 'The theme uses the %1$s %2$s, which is plugin-territory functionality.', 'theme-check' ), '<strong>' . esc_html( $hook ) . '</strong>', esc_html( $type ) );
					$ret           = false;
				}
			}
		}

		/**
		 * Check for removal of non presentational hooks.
		 * Removing emojis is also not allowed.
		 */
		$blocklist = array(
			'wp_head'             => array(
				'wp_generator', // @link https://developer.wordpress.org/reference/functions/wp_generator/
				'feed_links', // @link https://developer.wordpress.org/reference/functions/feed_links/
				'feed_links_extra', // @link https://developer.wordpress.org/reference/functions/feed_links_extra/
				'print_emoji_detection_script', // @link https://developer.wordpress.org/reference/functions/print_emoji_detection_script/
				'wp_resource_hints', // @link https://developer.wordpress.org/reference/functions/wp_resource_hints/
				'adjacent_posts_rel_link_wp_head', // @link https://developer.wordpress.org/reference/functions/adjacent_posts_rel_link_wp_head/
				'wp_shortlink_wp_head', // @link https://developer.wordpress.org/reference/functions/wp_shortlink_wp_head/
				'_admin_bar_bump_cb', // @link https://developer.wordpress.org/reference/functions/_admin_bar_bump_cb/
				'rsd_link', // @link https://developer.wordpress.org/reference/functions/rsd_link/
				'rest_output_link_wp_head', // @link https://developer.wordpress.org/reference/functions/rest_output_link_wp_head/
				'wp_oembed_add_discovery_links', // @link https://developer.wordpress.org/reference/functions/wp_oembed_add_discovery_links/
				'wp_oembed_add_host_js', // @link https://developer.wordpress.org/reference/functions/wp_oembed_add_host_js/
				'rel_canonical', // @link https://developer.wordpress.org/reference/functions/rel_canonical/
			),
			'wp_print_styles'     => array(
				'print_emoji_styles', // @link https://developer.wordpress.org/reference/functions/print_emoji_styles/
			),
			'admin_print_scripts' => array(
				'print_emoji_detection_script', //@link https://developer.wordpress.org/reference/functions/print_emoji_detection_script/
			),
			'admin_print_styles'  => array(
				'print_emoji_styles', // @link https://developer.wordpress.org/reference/functions/print_emoji_styles/
			),
			'template_redirect'   => array(
				'rest_output_link_header', // @link https://developer.wordpress.org/reference/functions/rest_output_link_header/
				'wp_shortlink_header', // @link https://developer.wordpress.org/reference/functions/wp_shortlink_header/
				'redirect_canonical',  // @link https://developer.wordpress.org/reference/functions/redirect_canonical/
			),
		);

		foreach ( $blocklist as $hook => $functions ) {
			foreach ( $functions as $function ) {
				checkcount();
				if ( preg_match( '/[\s?]remove_action\s*\(\s*([\'"])' . $hook . '([\'"])\s*,\s*([\'"])' . $function . '([\'"])/', $php ) ) {
					$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . sprintf( __( 'The theme uses <strong>remove_action %1$s %2$s</strong>, which is plugin-territory functionality.', 'theme-check' ),
						esc_html( $hook ),
						esc_html( $function )
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

$themechecks[] = new Plugin_Territory();
