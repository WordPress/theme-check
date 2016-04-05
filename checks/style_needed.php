<?php
class Style_Needed implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$css = implode( ' ', $css_files );
		$ret = true;

		$checks = array(
			'[ \t\/*#]*Theme Name:' => 	sprintf( __( '%s is missing from your style.css header.', 'theme-check' ), '<strong>Theme name:</strong>' ),
			'[ \t\/*#]*Description:' => sprintf( __( '%s is missing from your style.css header.', 'theme-check' ), '<strong>Description:</strong>' ),
			'[ \t\/*#]*Author:' => 		sprintf( __( '%s is missing from your style.css header.', 'theme-check' ), '<strong>Author:</strong>' ),
			'[ \t\/*#]*Version' => 		sprintf( __( '%s is missing from your style.css header.', 'theme-check' ), '<strong>Version:</strong>' ),
			'[ \t\/*#]*License:' => 	sprintf( __( '%s is missing from your style.css header.', 'theme-check' ), '<strong>License:</strong>' ),
			'[ \t\/*#]*License URI:' => sprintf( __( '%s is missing from your style.css header.', 'theme-check' ), '<strong>License URI:</strong>' ),
			'\.sticky' => 				sprintf( __( '%s css class is needed in your theme css.', 'theme-check' ), '<strong>.sticky</strong>' ),
			'\.bypostauthor' => 		sprintf( __( '%s css class is needed in your theme css.', 'theme-check' ), '<strong>.bypostauthor</strong>' ),
			'\.alignleft' => 			sprintf( __( '%s css class is needed in your theme css.', 'theme-check' ), '<strong>.alignleft</strong>' ),
			'\.alignright' => 			sprintf( __( '%s css class is needed in your theme css.', 'theme-check' ), '<strong>.alignright</strong>' ),
			'\.aligncenter' => 			sprintf( __( '%s css class is needed in your theme css.', 'theme-check' ), '<strong>.aligncenter</strong>' ),
			'\.wp-caption' => 			sprintf( __( '%s css class is needed in your theme css.', 'theme-check' ), '<strong>.wp-caption</strong>' ),
			'\.wp-caption-text' => 		sprintf( __( '%s css class is needed in your theme css.', 'theme-check' ), '<strong>.wp-caption-text</strong>' ),
			'\.gallery-caption' => 		sprintf( __( '%s css class is needed in your theme css.', 'theme-check' ), '<strong>.gallery-caption</strong>' ),
			'\.screen-reader-text' => 	sprintf( __( '%s css class is needed in your theme css.', 'theme-check' ),  '<strong>.screen-reader-text</strong>' ) . ' ' . __('See <a href="http://codex.wordpress.org/CSS#WordPress_Generated_Classes">the Codex</a> for an example implementation.', 'theme-check' )
		);

		foreach ($checks as $key => $check) {
			checkcount();
			if ( !preg_match( '/' . $key . '/i', $css, $matches ) ) {
				$this->error[] = "<span class='tc-lead tc-required'>" . __('REQUIRED', 'theme-check' ) . "</span>: " . $check;
				$ret = false;
			}
		}

		return $ret;
	}
	function getError() { return $this->error; }
}
$themechecks[] = new Style_Needed;
