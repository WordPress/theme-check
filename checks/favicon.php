<?php

class FaviconCheck implements themecheck {
	protected $error = array();

		function check( $php_files, $css_files, $other_files ) {

		$ret = true;
		
		checkcount();

		foreach ( $php_files as $file_path => $file_content ) {

			$filename = tc_filename( $file_path );

			if ( preg_match( '/(<link rel=[\'"]icon[\'"])|(<link rel=[\'"]apple-touch-icon-precomposed[\'"])|(<meta name=[\'"]msapplication-TileImage[\'"])/', $file_content, $matches ) ) {
				$this->error[] = sprintf( '<span class="tc-lead tc-info">' . __('INFO','theme-check') . '</span>: ' . __( 'Possible Favicon found in %1$s. Favicons are handled by the Site Icon setting in the customizer since version 4.3.', 'theme-check' ), 
					'<strong>' . $filename . '</strong>');				
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new FaviconCheck;