<?php
/**
 * Checks for common escaping issues.
 */

class EscapingCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$checks = array(
			'/="<\?php esc_html_e/'             => __( 'Use esc_attr_e() inside HTML attributes, and esc_url() for link attributes', 'theme-check' ),
			'/="<\?php echo esc_html__/'        => __( 'Use esc_attr__() inside HTML attributes, and esc_url() for link attributes', 'theme-check' ),
			'/="<\?php esc_html\(/'             => __( 'Use esc_attr() inside HTML attributes, and esc_url() for link attributes', 'theme-check' ),
			'/><\?php echo esc_attr__/'         => __( 'Only use esc_attr__() inside HTML attributes. Use esc_html__() between HTML tags', 'theme-check' ),
			'/><\?php esc_attr_e/'              => __( 'Only use esc_attr_e() inside HTML attributes. Use esc_html_e() between HTML tags', 'theme-check' ),
			'/echo home_url/'                   => __( 'home_url() must be escaped. Use esc_url() for link attributes', 'theme-check' ),
			'/href=.*\. home_url/'              => __( 'home_url() must be escaped. Use esc_url() for link attributes', 'theme-check' ),
			'/echo get_template_directory_uri/' => __( 'get_template_directory_uri() must be escaped when output as part of a link or image source. Use esc_url() for link attributes', 'theme-check' ),
		);

		foreach ( $php_files as $php_key => $phpfile ) {

			checkcount();
			if ( false !== strpos( $phpfile, 'echo get_theme_mod' ) ) {
				$filename      = tc_filename( $php_key );
				$error         = 'echo get_theme_mod';
				$grep          = tc_grep( $error, $php_key );
				$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Found %1$s in %2$s. <a href="%3$s" target="_blank">Theme options must be escaped (Opens in a new window).</a>. ', 'theme-check' ),
					'<code>' . esc_html( $error ) . '</code>',
					'<strong>' . $filename . '</strong>',
					'https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/#escaping-securing-output'
				) . $grep;

				$ret = false;
			}

			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error    = $matches[0];
					$grep     = tc_grep( $error, $php_key );
					$this->error[] = sprintf( '<span class="tc-lead tc-warning">' . __( 'WARNING', 'theme-check' ) . '</span>: ' . __( 'Found %1$s in %2$s. %3$s. A manual review is needed.', 'theme-check' ),
						'<code>' . esc_html( $error ) . '</code>',
						'<strong>' . $filename . '</strong>',
						$check
					) . $grep;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new EscapingCheck();
