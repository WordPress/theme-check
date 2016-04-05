<?php
class Comment_Reply implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$php = implode( ' ', $php_files );
		$ret = true;

		checkcount();

		if ( ! preg_match( '/wp_enqueue_script\(\s?("|\')comment-reply("|\')/i', $php ) ) {
			if ( ! preg_match( '/comment-reply/', $php ) ) {
				$check = sprintf( __( 'See: %s', 'theme-check' ), '<a href="https://codex.wordpress.org/Migrating_Plugins_and_Themes_to_2.7/Enhanced_Comment_Display">Migrating Plugins and Themes to 2.7/Enhanced Comment Display</a><pre> &lt;?php if ( is_singular() ) wp_enqueue_script( "comment-reply" ); ?&gt;</pre>' );
				$this->error[] = sprintf('<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__('Could not find the %s script enqueued.', 'theme-check'), '<strong>comment-reply</strong>' ).$check;
				$ret = false;
			} else {
				$this->error[] = '<span class="tc-lead tc-info">'.__('INFO','theme-check').'</span>: '.sprintf(__('Could not find the %s script enqueued, however a reference to \'comment-reply\' was found. Make sure that the comment-reply script is being enqueued properly on singular pages.', 'theme-check'), '<strong>comment-reply</strong>');
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Comment_Reply;
