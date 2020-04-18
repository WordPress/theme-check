<?php
/**
 * Checks for favicons.
 * Note that the check for the icon file is in filenames.php.
 */

class FaviconCheck implements themecheck {
	protected $error = array();

		function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		checkcount();

		foreach ( $php_files as $file_path => $file_content ) {

			$filename = tc_filename( $file_path );

			if ( preg_match( '/(<link rel=[\'"]icon[\'"])|(<link rel=[\'"]shortcut icon[\'"])|(<link rel=[\'"]apple-touch-icon.*[\'"])|(<meta name=[\'"]msapplication-TileImage[\'"])/i', $file_content, $matches ) ) {
				$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Possible Favicon found in %1$s. Favicons are handled by the Site Icon setting in the customizer since version 4.3.', 'theme-check' ),
					'<strong>' . $filename . '</strong>'
				);
				$ret           = false;
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new FaviconCheck();
