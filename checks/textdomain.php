<?php

class TextDomainCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;

		$php = implode(' ', $php_files);
		$css = implode(' ', $css_files);
		checkcount();

		if ( strpos( $css, 'Theme Name: Twenty Ten' ) ) return $ret;

		if ( preg_match( '/[__|_e]\(\s?[\'|"].*[\'|"],\s?[\'|"]twentyten[\'|"]\s?\)/', $php ) ) {
			$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: Theme is using <strong>twentyten</strong> as the textdomain!";
			$ret = false;
		return $ret;
		}

// experimental check if i18n exists and has no domain?
/*		if ( preg_match( '/[__|_e]\(/', $php ) &&  !preg_match( '/_[_|e]\(\s?["|\'].*[\'|"],\s[\'|"][a-z-?]+["|\']\s?\)/', $php, $out)  ) {
			$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: Theme is using i18n but there is no textdomain!";
			$ret = false;
		return $ret;
		}
*/

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new TextDomainCheck;
