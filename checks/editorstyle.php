<?php

class EditorStyleCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
		checkcount();
		$ret = true;
		
		$php = implode(' ', $php_files);

		if ( strpos( $php, 'add_editor_style' ) === false ) {
			$this->error[] = "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: No reference to <strong>add_editor_style()</strong> was found in the theme. It is recommended that the theme implement editor styling, so as to make the editor content match the resulting post output in the theme, for a better user experience.";
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new EditorStyleCheck;
