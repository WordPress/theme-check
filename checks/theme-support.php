<?php

class ThemeSupport implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$php = implode( ' ', $php_files );

		$checks = array(
			'#add_theme_support\s?\(\s?[\'|"]custom-header#' => __( 'No reference to <strong>add_theme_support( "custom-header", $args )</strong> was found in the theme. It is recommended that the theme implement this functionality if using an image for the header.', 'theme-check' ),
			'#add_theme_support\s?\(\s?[\'|"]custom-background#' => __( 'No reference to <strong>add_theme_support( "custom-background", $args )</strong> was found in the theme. If the theme uses background images or solid colors for the background, then it is recommended that the theme implement this functionality.', 'theme-check' ),
			'#add_theme_support\s?\(\s?[\'|"]custom-logo#' => __( 'No reference to <strong>add_theme_support( "custom-logo", $args )</strong> was found in the theme. It is recommended that the theme implement this functionality.', 'theme-check' ),
			'#add_theme_support\s?\(\s?[\'|"]html5#'       => __( 'No reference to <strong>add_theme_support( "html5", $args )</strong> was found in the theme. It is strongly recommended that the theme implement this functionality.', 'theme-check' ),
			'#add_theme_support\s?\(\s?[\'|"]responsive-embeds#' => __( 'No reference to <strong>add_theme_support( "responsive-embeds" )</strong> was found in the theme. It is recommended that the theme implement this functionality.', 'theme-check' ),
			'#add_theme_support\s?\(\s?[\'|"]align-wide#'  => __( 'No reference to <strong>add_theme_support( "align-wide" )</strong> was found in the theme. It is recommended that the theme implement this functionality.', 'theme-check' ),
			'#add_theme_support\s?\(\s?[\'|"]wp-block-styles#' => __( 'No reference to <strong>add_theme_support( "wp-block-styles" )</strong> was found in the theme. It is recommended that the theme implement this functionality.', 'theme-check' ),
		);

		foreach ( $checks as $key => $check ) {
			checkcount();
			if ( ! preg_match( $key, $php ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-recommended">%s</span>: %s',
					__( 'RECOMMENDED', 'theme-check' ),
					$check
				);
			}
		}

		return true;
	}

	function getError() {
		return $this->error;
	}
}
$themechecks[] = new ThemeSupport();
