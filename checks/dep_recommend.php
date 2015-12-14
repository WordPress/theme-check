<?php
// recommended deprecations checks... After some time, these will move into deprecated.php and become required.
class Deprecated_Recommended implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$grep = '';

		$ret = true;

		$checks = array(
			array( 'preview_theme' => '', '4.3' ),
			array( '_preview_theme_template_filter' => '', '4.3' ),
			array( '_preview_theme_stylesheet_filter' => '', '4.3' ),
			array( 'preview_theme_ob_filter' => '', '4.3' ),
			array( 'preview_theme_ob_filter_callback' => '', '4.3' ),
			array( 'wp_richedit_pre' => '', '4.3' ),
			array( 'wp_htmledit_pre' => '', '4.3' ),
			array( 'wp_ajax_wp_fullscreen_save_post' => '', '4.3' ),
			
			array( 'post_permalink' => 'get_permalink', '4.4'),
			array( 'wp_get_http' => 'WP_Http', '4.4'),
			array( 'force_ssl_login' => 'force_ssl_admin', '4.4'),
			array( 'create_empty_blog' => '', '4.4'),
			array( 'get_admin_users_for_domain' => '', '4.4'),
			//array( 'flush_widget_cache' => '', '4.4'),
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $alt => $check ) {
				checkcount();
				$version = $check;
				$key = key( $check );
				$alt = $check[ $key ];
				if ( preg_match( '/[\s?]' . $key . '\(/', $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = ltrim( rtrim( $matches[0], '(' ) );
					$version = $check[0];
					$grep = tc_grep( $error, $php_key );

					// Point out the deprecated function.
					$error_msg = sprintf(
						__( '%1$s found in the file %2$s. Deprecated since version %3$s.', 'theme-check' ),
						'<strong>' . $error . '()</strong>',
						'<strong>' . $filename . '</strong>',
						'<strong>' . $version . '</strong>'
					);

					// Add alternative function when available.
					if ( $alt ) {
						$error_msg .= ' ' . sprintf( __( 'Use %s instead.', 'theme-check' ), '<strong>' . $alt . '</strong>' );
					}

					// Add the precise code match that was found.
					$error_msg .= $grep;

					// Add the finalized error message.
					$this->error[] = '<span class="tc-lead tc-recommended">' . __('RECOMMENDED','theme-check') . '</span>: ' . $error_msg;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Deprecated_Recommended;