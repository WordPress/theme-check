<?php

/**
 * Class FSE_Required_Files
 *
 * Checks that Full-Site Editing themes have the required files.
 *
 * This check is not added to the global array of checks, because it doesn't apply to all themes.
 */
class FSE_Required_Files implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$filenames = array();

		foreach ( $php_files as $php_key => $phpfile ) {
			array_push( $filenames, tc_filename( $php_key ) );
		}
		foreach ( $other_files as $php_key => $phpfile ) {
			array_push( $filenames, tc_filename( $php_key ) );
		}
		foreach ( $css_files as $php_key => $phpfile ) {
			array_push( $filenames, tc_filename( $php_key ) );
		}

		$musthave = array(
			'block-templates/index.html',
			'experimental-theme.json',
			'readme.txt',
			'style.css',
		);

		foreach ( $musthave as $file ) {
			if ( ! in_array( $file, $filenames ) ) {
				$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Could not find the file %s in the theme.', 'theme-check' ), '<strong>' . $file . '</strong>' );
				$ret           = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
