<?php

class Comments implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$php  = implode( ' ', $php_files );

		$checks = array(
			'comments_template\s*\(' => __( 'See: <a href="https://developer.wordpress.org/reference/functions/comments_template/">comments_template</a><pre> &lt;?php comments_template( $file, $separate_comments ); ?&gt;</pre>', 'theme-check' ),
			'wp_list_comments\s*\('  => __( 'See: <a href="https://developer.wordpress.org/reference/functions/wp_list_comments/">wp_list_comments</a><pre> &lt;?php wp_list_comments( $args ); ?&gt;</pre>', 'theme-check' ),
			'comment_form\s*\('      => __( 'See: <a href="https://developer.wordpress.org/reference/functions/comment_form/">comment_form</a><pre> &lt;?php comment_form(); ?&gt;</pre>', 'theme-check' ),
		);

		foreach ( $checks as $key => $check ) {
			checkcount();
			if ( ! preg_match( '/' . $key . '/i', $php ) ) {
				$key           = str_replace( '\s*\(', '', $key );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-recommended">%s</span>: %s',
					__( 'RECOMMENDED', 'theme-check' ),
					sprintf(
						__( 'Could not find %1$s. %2$s', 'theme-check' ),
						'<strong>' . $key . '</strong>',
						$check
					)
				);
			}
		}

		return true;
	}

	function getError() {
		return $this->error;
	}
}

$themechecks[] = new Comments();
