<?php
/**
 * Checks for missing suggested style headers.
 */
class Style_Suggested implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		$css = implode( ' ', $css_files );

		checkcount();

		$checks = array(
			'[ \t\/*#]*Theme URI:' => 'Theme URI:',
			'[ \t\/*#]*Author URI:' => 'Author URI:',
		);

		foreach ($checks as $key => $check) {
			if ( !preg_match( '/' . $key . '/i', $css, $matches ) ) {
				$this->error[] = sprintf('<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.__('%s is missing from your style.css header.', 'theme-check'), '<strong>' . $check . '</strong>' );
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Style_Suggested;