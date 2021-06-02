<?php
/**
 * Check for basic functions needed for classic themes to work correctly
 *
 * @package Theme Check
 */

/**
 * Check for basic functions needed for classic themes to work correctly.
 */
class Basic_Check implements themecheck {
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

		$php  = implode( ' ', $php_files );
		$grep = '';
		$ret  = true;

		$checks = array(
			'DOCTYPE'             => __( 'See: <a href="https://codex.wordpress.org/HTML_to_XHTML">https://codex.wordpress.org/HTML_to_XHTML</a><pre>&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"<br />"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"?&gt;</pre>', 'theme-check' ),
			'(wp_body_open\s*\()|(do_action\s*\(\s*["\']wp_body_open)' => __( 'See: <a href="https://developer.wordpress.org/reference/functions/wp_body_open/">wp_body_open</a><pre> &lt;?php wp_body_open(); ?&gt;</pre>', 'theme-check' ),
			'wp_footer\s*\('      => __( 'See: <a href="https://developer.wordpress.org/reference/functions/wp_footer/">wp_footer</a><pre> &lt;?php wp_footer(); ?&gt;</pre>', 'theme-check' ),
			'wp_head\s*\('        => __( 'See: <a href="https://developer.wordpress.org/reference/functions/wp_head/">wp_head</a><pre> &lt;?php wp_head(); ?&gt;</pre>', 'theme-check' ),
			'language_attributes' => __( 'See: <a href="https://developer.wordpress.org/reference/functions/language_attributes/">language_attributes</a><pre>&lt;html &lt;?php language_attributes(); ?&gt;</pre>', 'theme-check' ),
			'charset'             => __( 'There must be a charset defined in the Content-Type or the meta charset tag in the head.', 'theme-check' ),
			'add_theme_support\s*\(\s?("|\')automatic-feed-links("|\')\s?\)' => __( 'See: <a href="https://developer.wordpress.org/reference/functions/add_theme_support/">add_theme_support</a><pre> &lt;?php add_theme_support( $feature ); ?&gt;</pre>', 'theme-check' ),
			'body_class\s*\('     => __( 'See: <a href="https://developer.wordpress.org/reference/functions/body_class/">body_class</a><pre> &lt;?php body_class( $class ); ?&gt;</pre>', 'theme-check' ),
			'wp_link_pages\s*\('  => __( 'See: <a href="https://developer.wordpress.org/reference/functions/wp_link_pages/">wp_link_pages</a><pre> &lt;?php wp_link_pages( $args ); ?&gt;</pre>', 'theme-check' ),
			'post_class\s*\('     => __( 'See: <a href="https://developer.wordpress.org/reference/functions/post_class/">post_class</a><pre> &lt;div id="post-&lt;?php the_ID(); ?&gt;" &lt;?php post_class(); ?&gt;&gt;</pre>', 'theme-check' ),
		);

		foreach ( $checks as $key => $check ) {
			checkcount();
			if ( ! preg_match( '/' . $key . '/i', $php ) ) {
				if ( $key === 'add_theme_support\s*\(\s?("|\')automatic-feed-links("|\')\s?\)' ) {
					$key = __( 'add_theme_support( \'automatic-feed-links\' )', 'theme-check' );
				}
				if ( $key === 'body_class\s*\(' ) {
					$key = __( 'body_class call in body tag', 'theme-check' );
				}
				if ( $key === '(wp_body_open\s*\()|(do_action\s*\(\s*["\']wp_body_open)' ) {
					$key = __( 'wp_body_open action or function call at the very top of the body just after the opening body tag', 'theme-check' );
				}
				$key           = str_replace( '\s*\(', '', $key );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'Could not find %1$s. %2$s', 'theme-check' ),
						'<strong>' . $key . '</strong>',
						$check
					)
				);
				$ret           = false;
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

$themechecks[] = new Basic_Check();
