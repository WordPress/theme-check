<?php

// do some basic checks for strings
class Basic_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$php = implode( ' ', $php_files );
		$grep = '';
		$ret = true;

		$checks = array(
			'DOCTYPE' => __( 'See: <a href="http://codex.wordpress.org/HTML_to_XHTML">http://codex.wordpress.org/HTML_to_XHTML</a><pre>&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"<br />"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"?&gt;</pre>', 'themecheck' ),
			'wp_footer\(' => __( 'See: <a href="http://codex.wordpress.org/Function_Reference/wp_footer">wp_footer</a><pre> &lt;?php wp_footer(); ?&gt;</pre>', 'themecheck' ),
			'wp_head\(' => __( 'See: <a href="http://codex.wordpress.org/Function_Reference/wp_head">wp_head</a><pre> &lt;?php wp_head(); ?&gt;</pre>', 'themecheck' ),
			'language_attributes' => __( 'See: <a href="http://codex.wordpress.org/Function_Reference/language_attributes">language_attributes</a><pre>&lt;html &lt;?php language_attributes(); ?&gt;</pre>', 'themecheck' ),
			'charset' => __( 'There must be a charset defined in the Content-Type or the meta charset tag in the head.', 'themecheck' ),
			'add_theme_support\(\s?("|\')automatic-feed-links("|\')\s?\)' => __( 'See: <a href="http://codex.wordpress.org/Function_Reference/add_theme_support">add_theme_support</a><pre> &lt;?php add_theme_support( $feature ); ?&gt;</pre>', 'themecheck' ),
			'register_sidebar[s]?\(' => __( 'See: <ahref="http://codex.wordpress.org/Function_Reference/register_sidebar">register_sidebar</a><pre> &lt;?php register_sidebar( $args ); ?&gt;</pre>', 'themecheck' ),
			'dynamic_sidebar\(' => __( 'See: <a href="http://codex.wordpress.org/Function_Reference/dynamic_sidebar">dynamic_sidebar</a><pre> &lt;?php dynamic_sidebar( $index ); ?&gt;</pre>', 'themecheck' ),
			'comments_template\(' => __( 'See: <a href="http://codex.wordpress.org/Template_Tags/comments_template">comments_template</a><pre> &lt;?php comments_template( $file, $separate_comments ); ?&gt;</pre>', 'themecheck' ),
			'wp_list_comments\(' => __( 'See: <a href="http://codex.wordpress.org/Template_Tags/wp_list_comments">wp_list_comments</a><pre> &lt;?php wp_list_comments( $args ); ?&gt;</pre>', 'themecheck' ),
			'comment_form\(' => __( 'See: <a href="http://codex.wordpress.org/Template_Tags/comment_form">comment_form</a><pre> &lt;?php comment_form(); ?&gt;</pre>', 'themecheck' ),
			'wp_enqueue_script\(\s?("|\')comment-reply("|\')\s?\)' => __( 'See: <a href="http://codex.wordpress.org/Migrating_Plugins_and_Themes_to_2.7/Enhanced_Comment_Display">Migrating Plugins and Themes to 2.7/Enhanced Comment Display</a><pre> &lt;?php if ( is_singular() ) wp_enqueue_script( "comment-reply" ); ?&gt;</pre>', 'themecheck' ),
			'<body.*body_class\(' => __( 'See: <a href="http://codex.wordpress.org/Template_Tags/body_class">body_class</a><pre> &lt;?php body_class( $class ); ?&gt;</pre>', 'themecheck' ),
			'post_class\(' => __( 'See: <a href="http://codex.wordpress.org/Template_Tags/post_class">post_class</a><pre> &lt;div id="post-&lt;?php the_ID(); ?&gt;" &lt;?php post_class(); ?&gt;&gt;</pre>', 'themecheck' )
			);

		foreach ($checks as $key => $check) {
			checkcount();
			if ( !preg_match( '/' . $key . '/i', $php ) ) {
				if ( $key === 'add_theme_support\(\s?("|\')automatic-feed-links("|\')\s?\)' ) $key = 'add_theme_support( \'automatic-feed-links\' )';
				if ( $key === 'wp_enqueue_script\(\s?("|\')comment-reply("|\')\s?\)' ) $key = 'wp_enqueue_script( \'comment-reply\' )';
				if ( $key === '<body.*body_class\(' ) $key = 'body_class call in body tag';
				if ( $key === 'register_sidebar[s]?\(' ) $key = 'register_sidebar() or register_sidebars()';
				$key = rtrim($key,'\(');
				$this->error[] = "<span class='tc-lead tc-required'>REQUIRED</span>: Could not find <strong>{$key}</strong>. {$check}";
				$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Basic_Checks;
