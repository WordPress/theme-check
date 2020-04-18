<?php
/**
 * Checks for common escaping issues.
 */

class EscapingCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$checks = array(
			'/echo get_theme_mod/'              => __( 'Theme options must be escaped.', 'theme-check' ),
			'/="<\?php esc_html_e/'             => __( 'Use esc_attr_e() inside HTML attributes.', 'theme-check' ),
			'/="<\?php echo esc_html__/'        => __( 'Use esc_attr__() inside HTML attributes.', 'theme-check' ),
			'/echo home_url/'                   => __( 'home_url() must be escaped.', 'theme-check' ),
			'/href=.*\. home_url/'              => __( 'home_url() must be escaped.', 'theme-check' ),
			'/echo get_template_directory_uri/' => __( 'get_template_directory_uri() must be escaped when output as part of a link or image source.', 'theme-check' ),
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error    = $matches[0];
					$grep     = tc_grep( $error, $php_key );
					$this->error[] = sprintf('<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Found %1$s in %2$s. %3$s', 'theme-check' ),
						'<code>' . esc_html( $error ) . '</code>',
						'<strong>' . $filename . '</strong>',
						$check
					) . $grep;

					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new EscapingCheck();
