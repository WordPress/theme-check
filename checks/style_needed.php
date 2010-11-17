<?php

// search for some bad things
class Style_Needed implements themecheck {
	protected $error = array();
	function check( $php_files, $css_files, $other_files) {
		// combine all the php files into one string to make it easier to search
		$css = implode(' ', $css_files);
		$ret = true;
		// things to check for
		$checks = array(
		'^(\s+|\t+|)Theme Name:' => '<strong>Theme name:</strong> is missing from your style.css header.',
		'^(\s+|\t+|)Theme URI:' => '<strong>Theme URI:</strong> is missing from your style.css header.',
		'^(\s+|\t+|)Description:' => '<strong>Description:</strong> is missing from your style.css header.',
		'^(\s+|\t+|)Author:' => '<strong>Author:</strong> is missing from your style.css header.',
		'^(\s+|\t+|)Version' => '<strong>Version:</strong> is missing from your style.css header.',
		'^(\s+|\t+|)License:' => '<strong>License:</strong> is missing from your style.css header.',
		'^(\s+|\t+|)License URI:' => '<strong>License URI:</strong> is missing from your style.css header.',		
		'\.alignleft' => '<strong>.alignleft</strong> css class is needed in your theme css.',
		'\.alignright' => '<strong>.alignright</strong> css class is needed in your theme css.',
		'\.aligncenter' => '<strong>.aligncenter</strong> css class is needed in your theme css.',
		'\.wp-caption' => '<strong>.wp-caption</strong> css class is needed in your theme css.',
		'\.wp-caption-text' => '<strong>.wp-caption-text</strong> css class is needed in your theme css.',
		'\.gallery-caption' => '<strong>.gallery-caption</strong> css class is needed in your theme css.',
		'\.sticky' => '<strong>.sticky</strong> css class is needed in your theme css.',
		'\.bypostauthor' => '<strong>.bypostauthor</strong> css class is needed in your theme css.'
		);
		foreach ($checks as $key => $check) {
		checkcount();
			if ( !preg_match( '/' . $key . '/mi', $css, $matches ) ) {
				$this->error[] = "REQUIRED{$check}";
				$ret = false;
			}
		}
		return $ret;
	}
	function getError() { return $this->error; }
}
$themechecks[] = new Style_Needed;
