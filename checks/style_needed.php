<?php
class Style_Needed implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$css = implode( ' ', $css_files );
		$ret = true;

		$checks = array(
			'[ \t\/*#]*Theme Name:' => __( '<strong>Theme name:</strong> is missing from your style.css header.', 'themecheck' ),
			'[ \t\/*#]*Description:' => __( '<strong>Description:</strong> is missing from your style.css header.', 'themecheck' ),
			'[ \t\/*#]*Author:' => __( '<strong>Author:</strong> is missing from your style.css header.', 'themecheck' ),
			'[ \t\/*#]*Version' => __( '<strong>Version:</strong> is missing from your style.css header.', 'themecheck' ),
			'[ \t\/*#]*License:' => __( '<strong>License:</strong> is missing from your style.css header.', 'themecheck' ),
			'[ \t\/*#]*License URI:' => __( '<strong>License URI:</strong> is missing from your style.css header.', 'themecheck' ),
			'\.alignleft' => __( '<strong>.alignleft</strong> css class is needed in your theme css.', 'themecheck' ),
			'\.alignright' => __( '<strong>.alignright</strong> css class is needed in your theme css.', 'themecheck' ),
			'\.aligncenter' => __( '<strong>.aligncenter</strong> css class is needed in your theme css.', 'themecheck' ),
			'\.wp-caption' => __( '<strong>.wp-caption</strong> css class is needed in your theme css.', 'themecheck' ),
			'\.wp-caption-text' => __( '<strong>.wp-caption-text</strong> css class is needed in your theme css.', 'themecheck' ),
			'\.gallery-caption' => __( '<strong>.gallery-caption</strong> css class is needed in your theme css.', 'themecheck' )
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