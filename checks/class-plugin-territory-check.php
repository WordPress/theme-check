<?php
/**
 * Checks for Plugin Territory Guidelines.
 *
 * @link https://make.wordpress.org/themes/handbook/review/required/#presentation-vs-functionality
 *
 * @package Theme Check
 */

/**
 * Checks for Plugin Territory
 */
class Plugin_Territory_Check implements themecheck {
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

		// Functions that are required to be removed from the theme.
		$forbidden_functions = array(
			'register_post_type',
			'register_taxonomy',
			'register_block_type',
			'add_role',
			'add_shortcode',
			'registerBlockType',
		);

		// Hooks (actions & filters) that are required to be removed from the theme.
		$forbidden_hooks = array(
			'filter' => array(
				'mime_types',
				'upload_mimes',
				'user_contactmethods',
			),
		);

		/**
		 * Check for removal of non presentational hooks.
		 */
		$blocklist = array(
			'wp_head'           => array(
				'wp_generator', // @link https://developer.wordpress.org/reference/functions/wp_generator/
				'feed_links', // @link https://developer.wordpress.org/reference/functions/feed_links/
				'feed_links_extra', // @link https://developer.wordpress.org/reference/functions/feed_links_extra/
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
			'template_redirect' => array(
				'rest_output_link_header', // @link https://developer.wordpress.org/reference/functions/rest_output_link_header/
				'wp_shortlink_header', // @link https://developer.wordpress.org/reference/functions/wp_shortlink_header/
				'redirect_canonical',  // @link https://developer.wordpress.org/reference/functions/redirect_canonical/
			),
		);

		foreach ( $php_files as $php_key => $phpfile ) {

			foreach ( $forbidden_functions as $function ) {
				checkcount();
				if ( preg_match( '/[\s?]' . $function . '\s?\(/', $phpfile ) ) {
					$filename      = tc_filename( $php_key );
					$grep          = tc_grep( $function, $php_key );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-required">%s</span>: %s',
						__( 'REQUIRED', 'theme-check' ),
						sprintf(
							__( 'The theme uses the %1$s function in the file %2$s. %1$s is plugin-territory functionality and must not be used in themes. Use a plugin instead. %3$s', 'theme-check' ),
							'<strong>' . esc_html( $function ) . '()</strong>',
							esc_html( $filename ),
							$grep
						)
					);
					$ret           = false;
				}
			}

			foreach ( $forbidden_hooks as $type => $hooks ) {
				foreach ( $hooks as $hook ) {
					checkcount();
					if ( preg_match( '/[\s?]add_' . $type . '\s*\(\s*([\'"])' . $hook . '([\'"])\s*,/', $phpfile ) ) {
						$filename      = tc_filename( $php_key );
						$grep          = tc_grep( $hook, $php_key );
						$this->error[] = sprintf(
							'<span class="tc-lead tc-required">%s</span>: %s',
							__( 'REQUIRED', 'theme-check' ),
							sprintf(
								__( 'The theme uses the %1$s %2$s in the file %3$s. This is plugin-territory functionality and must not be used in themes. Use a plugin instead. %4$s', 'theme-check' ),
								'<strong>' . esc_html( $hook ) . '</strong>',
								esc_html( $type ),
								$filename,
								$grep
							)
						);
						$ret           = false;
					}
				}
			}

			foreach ( $blocklist as $hook => $functions ) {
				foreach ( $functions as $function ) {
					checkcount();
					if ( preg_match( '/[\s?]remove_action\s*\(\s*([\'"])' . $hook . '([\'"])\s*,\s*([\'"])' . $function . '([\'"])/', $phpfile ) ) {
						$filename      = tc_filename( $php_key );
						$grep          = tc_preg( '/' . $hook . '([\'"])\s*,\s*([\'"])' . $function . '([\'"])/', $php_key );
						$this->error[] = sprintf(
							'<span class="tc-lead tc-required">%s</span>: %s',
							__( 'REQUIRED', 'theme-check' ),
							sprintf(
								__( 'The theme uses <strong>remove_action %1$s %2$s</strong> in the file %3$s. This is plugin-territory functionality and must not be used in themes. Use a plugin instead. %4$s', 'theme-check' ),
								esc_html( $hook ),
								esc_html( $function ),
								$filename,
								$grep
							)
						);
						$ret           = false;
					}
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

$themechecks[] = new Plugin_Territory_Check();
