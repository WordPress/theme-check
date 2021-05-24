<?php

class ContentWidthCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );
		checkcount();
		if (
			strpos( $php, '$content_width' ) === false &&
			strpos( $php, '$GLOBALS' . "['content_width']" ) === false &&
			! preg_match( '/add_filter\(\s?("|\')embed_defaults/', $php ) &&
			! preg_match( '/add_filter\(\s?("|\')content_width/', $php )
		) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( 'No content width has been defined. Example: <pre>if ( ! isset( $content_width ) ) $content_width = 900;</pre>', 'theme-check' )
			);
		}

		return true;
	}

	function getError() {
		return $this->error;
	}
}
$themechecks[] = new ContentWidthCheck();
