<?php
/**
 * Check for prohibited words and theme names.
 */


class ThemeNameCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		checkcount();
		$ret = true;
		global $themename;

		/* Get the author name from style.css. */
		foreach ( $css_files as $cssfile => $content ) {
			if ( basename( $cssfile ) === 'style.css' ) {
				$data = get_theme_data_from_contents( $content );
			}
		}

		/* Remove the link added by get_theme_data_from_contents */
		$author = wp_strip_all_tags( $data['Author'] );

		/* If the author is not the WordPress team, check for prohibited names */
		if ( 'the WordPress team' !== $author ) {

			$blacklist = array( 'twentyten', 'twentyeleven', 'twentytwelve', 'twentythirteen', 'twentyfourteen', 'twentyfifteen',
			'twentysixteen', 'twentyseventeen', 'twentyeighteen', 'twentynineteen', 'twentytwenty', 'twentytwentyone',
			'twentytwentytwo', 'twentytwentythree', 'twentytwentyfour', 'twentytwentyfive', 'twentytwentysix',
			'twentytwentyseven', 'twentytwentyeight', 'twentytwentynine', 'twentythirty'
			);

			foreach ( $blacklist as $key ) {
				if ( stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), $key ) !== false ) {
					$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . sprintf( __( 'Theme names in the Twenty* series are reserved for default themes. Found %1$s.', 'theme-check' ), '<strong>' . $key . '</strong>' );
					$ret = false;
				}
			}
		}

		/* Check for prohibited words. */
		if ( stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), 'wordpress' ) !== false
			|| stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), 'theme' ) !== false ) {
			$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Theme names should not contain the words theme or WordPress.', 'theme-check' );
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new ThemeNameCheck();
