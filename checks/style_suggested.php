<?php
class Style_Suggested implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		// combine all the css files into one string to make it easier to search
		$css = implode( ' ', $css_files );

		checkcount();
		$ret = true;

		$checks = array(
			'[ \t\/*#]*Theme URI:'  => 'Theme URI:',
			'[ \t\/*#]*Author URI:' => 'Author URI:',
		);

		foreach ( $checks as $key => $check ) {
			if ( ! preg_match( '/' . $key . '/i', $css, $matches ) ) {
				$this->error[] = sprintf( '<span class="tc-lead tc-recommended">' . __( 'RECOMMENDED', 'theme-check' ) . '</span>: ' . __( '%s is missing from your style.css header.', 'theme-check' ), '<strong>' . $check . '</strong>' );
			}
		}

		$css_class_checks = array(
			'\.sticky'          => __( '<strong>.sticky</strong> css class is recommended in your theme css.', 'theme-check' ),
			'\.bypostauthor'    => __( '<strong>.bypostauthor</strong> css class is recommended in your theme css.', 'theme-check' ),
			'\.alignleft'       => __( '<strong>.alignleft</strong> css class is recommended in your theme css.', 'theme-check' ),
			'\.alignright'      => __( '<strong>.alignright</strong> css class is recommended in your theme css.', 'theme-check' ),
			'\.aligncenter'     => __( '<strong>.aligncenter</strong> css class is recommended in your theme css.', 'theme-check' ),
			'\.wp-caption'      => __( '<strong>.wp-caption</strong> css class is recommended in your theme css.', 'theme-check' ),
			'\.wp-caption-text' => __( '<strong>.wp-caption-text</strong> css class is recommended in your theme css.', 'theme-check' ),
			'\.gallery-caption' => __( '<strong>.gallery-caption</strong> css class is recommended in your theme css.', 'theme-check' ),
		);

		foreach ( $css_class_checks as $key => $check ) {
			if ( ! preg_match( '/' . $key . '/i', $css, $matches ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-recommended">%s</span>: %s',
					__( 'RECOMMENDED', 'theme-check' ),
					$check
				);
			}
		}

		return $ret;
	}

	function getError() {
		return $this->error;
	}
}
$themechecks[] = new Style_Suggested();
