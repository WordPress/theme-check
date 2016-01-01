<?php
/**
 * Checks for Gravatars.
 */
class GravatarCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		$php = implode( ' ', $php_files );

		checkcount();

		if ( ( strpos( $php, 'get_avatar' ) === false ) && ( strpos( $php, 'wp_list_comments' ) === false ) ) {
			$this->error[] = '<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__("This theme doesn't seem to support the standard avatar functions. Use <strong>get_avatar</strong> or <strong>wp_list_comments</strong> to add this support.", 'theme-check' );
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new GravatarCheck;