<?php
/**
 * Check for prohibited words and theme names.
 */

class ThemeNameCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;
		global $themename;
		global $data;

		/* Remove the link added by get_theme_data_from_contents. */
		$author = wp_strip_all_tags( $data['Author'] );

		/* Get the name, as added in style.css */
		$title = $data[ 'Title' ];

		checkcount();
		/* If the author is not the WordPress team, check for prohibited names. */
		if ( 'the WordPress team' !== $author ) {

			$blacklist = array( 'twentyten', 'twentyeleven', 'twentytwelve', 'twentythirteen', 'twentyfourteen', 'twentyfifteen',
			'twentysixteen', 'twentyseventeen', 'twentyeighteen', 'twentynineteen', 'twentytwenty', 'twentytwentyone',
			'twentytwentytwo', 'twentytwentythree', 'twentytwentyfour', 'twentytwentyfive', 'twentytwentysix',
			'twentytwentyseven', 'twentytwentyeight', 'twentytwentynine', 'twentythirty'
			);

			foreach ( $blacklist as $blacklisted_theme_name ) {
				if ( stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), $blacklisted_theme_name ) !== false ) {
					$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . sprintf( __( 'Theme names in the Twenty* series are reserved for default themes. Found %1$s.', 'theme-check' ), '<strong>' . $blacklisted_theme_name . '</strong>' );
				}
			}
		}

		checkcount();
		/* Check for prohibited words in the folder name. */
		if ( stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), 'wordpress' ) !== false
			|| stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), 'theme' ) !== false ) {
			$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Theme names should not contain the words theme or WordPress.', 'theme-check' );
		}

		checkcount();
		/* Check for prohibited words in style.css. */
		if ( stripos( strtolower( preg_replace( '/[^a-z]/', '', $title ) ), 'wordpress' ) !== false
			|| stripos( strtolower( preg_replace( '/[^a-z]/', '', $title ) ), 'theme' ) !== false ) {
			$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Theme names should not contain the words theme or WordPress.', 'theme-check' );
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new ThemeNameCheck();
