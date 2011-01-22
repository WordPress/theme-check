<?php

class CSSAlignOptionalCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$css = implode( ' ', $css_files );

		$ret = true;

		$checks = array(
			'\.sticky' => '.sticky',
			'\.bypostauthor' => '.bypostauthor',
		);

		foreach ( $checks as $key => $check ) {
			checkcount();
			if ( !preg_match( '/' . $key . '/', $css, $matches ) ) {
				$this->error[] = "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: The CSS is missing the <strong>{$check}</strong> class.";
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new CSSAlignOptionalCheck;