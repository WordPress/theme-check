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
			)
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

		foreach ( $php_files as $php_key => $phpfile ) {
			checkcount();
			if ( strpos( $phpfile, 'wp_custom_css_cb' ) !== false ) {
				$filename      = tc_filename( $php_key );
				$grep          = tc_grep( 'wp_custom_css_cb', $php_key );
				$this->error[] = '<span class="tc-lead tc-warning">' . __( 'WARNING', 'theme-check' ) . '</span>: ' . sprintf( __( 'The theme uses %1$s in %2$s. Themes must re-add the Custom CSS callback (%3$s) if they need to move it from the header. A manual review is needed. %4$s', 'theme-check' ),
					'<strong>remove_action(‘wp_head’, ‘wp_custom_css_cb’)</strong>',
					$filename,
					'wp_custom_css_cb',
					$grep,
				);
			}

			checkcount();
			if ( preg_match( "/remove_action\(.*(\"|')wp_head(\"|').*\);/", $phpfile ) ) {
				$filename = tc_filename( $php_key );
				// Get all the results.
				$grep = tc_grep( 'remove_action', $php_key );
				// Split the results to be able to present them on one line each.
				$results = preg_split( '/<pre/', $grep );
				foreach ( $results as $line ) {
					if ( strpos( $line, 'wp_head' ) !== false && strpos( $line, 'wp_custom_css_cb' ) === false ) {
						$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . sprintf( __( 'The theme uses %1$s in %2$s. Themes are not allowed to remove non-presentational hooks. %3$s', 'theme-check' ),
							'<strong>remove_action(‘wp_head’, ‘ ’)</strong>',
							$filename,
							'<pre ' . $line,
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

$themechecks[] = new Plugin_Territory();
