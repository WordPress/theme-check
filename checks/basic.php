<?php

// do some basic checks for strings
class Basic_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$php = implode( ' ', $php_files );
		$grep = '';
		$ret = true;

		$checks = array(
			'DOCTYPE' => sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/HTML_to_XHTML">https://codex.wordpress.org/HTML_to_XHTML</a><pre> &lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"<br />"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"?&gt;</pre>' ),
			'wp_footer\s*\(' => sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/Function_Reference/wp_footer">wp_footer</a><pre> &lt;?php wp_footer(); ?&gt;</pre>' ),
			'wp_head\s*\(' => sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/Function_Reference/wp_head">wp_head</a><pre> &lt;?php wp_head(); ?&gt;</pre>' ),
			'language_attributes' => sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/Function_Reference/language_attributes">language_attributes</a><pre> &lt;html &lt;?php language_attributes(); ?&gt;</pre>' ),
			'charset' => __( 'There must be a charset defined in the Content-Type or the meta charset tag in the head.', 'theme-check' ),
			'add_theme_support\s*\(\s?("|\')automatic-feed-links("|\')\s?\)' => sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/Function_Reference/add_theme_support">add_theme_support</a><pre> &lt;?php add_theme_support( $feature ); ?&gt;</pre>' ),
			'comments_template\s*\(' => sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/Template_Tags/comments_template">comments_template</a><pre> &lt;?php comments_template( $file, $separate_comments ); ?&gt;</pre>' ),
			'wp_list_comments\s*\(' => sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/Template_Tags/wp_list_comments">wp_list_comments</a><pre> &lt;?php wp_list_comments( $args ); ?&gt;</pre>' ),
			'comment_form\s*\(' => sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/Template_Tags/comment_form">comment_form</a><pre> &lt;?php comment_form(); ?&gt;</pre>' ),
			'body_class\s*\(' => sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/Template_Tags/body_class">body_class</a><pre> &lt;?php body_class( $class ); ?&gt;</pre>' ),
			'wp_link_pages\s*\(' => sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/Function_Reference/wp_link_pages">wp_link_pages</a><pre> &lt;?php wp_link_pages( $args ); ?&gt;</pre>' ),
			'post_class\s*\(' => sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/Template_Tags/post_class">post_class</a><pre> &lt;div id="post-&lt;?php the_ID(); ?&gt;" &lt;?php post_class(); ?&gt;&gt;</pre>' )
			);

		foreach ($checks as $key => $check) {
			checkcount();
			if ( !preg_match( '/' . $key . '/i', $php ) ) {
				if ( $key === 'add_theme_support\s*\(\s?("|\')automatic-feed-links("|\')\s?\)' ) $key = '<strong>add_theme_support( \'automatic-feed-links\' )</strong>';
				if ( $key === 'body_class\s*\(' ) $key = sprintf( __( '%s call in body tag', 'theme-check'), '<strong>body_class</strong>' );
				$key = str_replace( '\s*\(', '', $key );
				$this->error[] = sprintf( '<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__('Could not find %s.', 'theme-check' ), $key ) . ' ' . $check ;
				$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Basic_Checks;
