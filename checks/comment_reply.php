<?php
class Comment_Reply implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$php = implode( ' ', $php_files );

		checkcount();

		if ( ! preg_match( '/wp_enqueue_script\(\s?("|\')comment-reply("|\')/i', $php ) ) {
			if ( ! preg_match( '/comment-reply/', $php ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-recommended">%s</span>: %s',
					__( 'RECOMMENDED', 'theme-check' ),
					__( 'Could not find the <strong>comment-reply</strong> script enqueued.', 'theme-check' )
				);
			} else {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-info">%s</span>: %s',
					__( 'INFO', 'theme-check' ),
					__( 'Could not find the <strong>comment-reply</strong> script enqueued, however a reference to \'comment-reply\' was found. Make sure that the comment-reply script is being enqueued properly on singular pages.', 'theme-check' )
				);
			}
		}

		return true;
	}

	function getError() {
		return $this->error;
	}
}

$themechecks[] = new Comment_Reply();
