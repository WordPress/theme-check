<?php

class EditorStyleCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		checkcount();
		$ret = true;

		$php = implode( ' ', $php_files );

		if ( strpos( $php, 'add_editor_style' ) === false ) {
			$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.sprintf( __( 'No reference to %s was found in the theme.', 'theme-check'), '<strong>add_editor_style()</strong>' ).' '.__( 'It is recommended that the theme implement editor styling, so as to make the editor content match the resulting post output in the theme, for a better user experience.', 'theme-check' );
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new EditorStyleCheck;