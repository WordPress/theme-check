<?php
class Style_Needed implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$css = implode(' ', $css_files);
		$ret = true;

		$checks = array(
			'[ \t\/*#]*Theme Name:' => '<strong>Theme name:</strong> is missing from your style.css header.',
			'[ \t\/*#]*Description:' => '<strong>Description:</strong> is missing from your style.css header.',
			'[ \t\/*#]*Author:' => '<strong>Author:</strong> is missing from your style.css header.',
			'[ \t\/*#]*Version' => '<strong>Version:</strong> is missing from your style.css header.',
			'[ \t\/*#]*License:' => '<strong>License:</strong> is missing from your style.css header.',
			'[ \t\/*#]*License URI:' => '<strong>License URI:</strong> is missing from your style.css header.',
			'\.alignleft' => '<strong>.alignleft</strong> css class is needed in your theme css.',
			'\.alignright' => '<strong>.alignright</strong> css class is needed in your theme css.',
			'\.aligncenter' => '<strong>.aligncenter</strong> css class is needed in your theme css.',
			'\.wp-caption' => '<strong>.wp-caption</strong> css class is needed in your theme css.',
			'\.wp-caption-text' => '<strong>.wp-caption-text</strong> css class is needed in your theme css.',
			'\.gallery-caption' => '<strong>.gallery-caption</strong> css class is needed in your theme css.',
		);

		foreach ($checks as $key => $check) {
			checkcount();
			if ( !preg_match( '/' . $key . '/i', $css, $matches ) ) {
				$this->error[] = "<span class='tc-lead tc-required'>REQUIRED</span>: {$check}";
				$ret = false;
			}
		}

		return $ret;
	}
	function getError() { return $this->error; }
}
$themechecks[] = new Style_Needed;
