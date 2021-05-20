<?php
class NonPrintableCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		foreach ( $php_files as $file_path => $file_content ) {
			checkcount();

			// 09 = tab
			// 0A = line feed
			// 0D = new line
			if ( preg_match( '/[\x00-\x08\x0B-\x0C\x0E-\x1F]/', $file_content, $matches ) ) {
				$grep          = tc_preg( '/[\x00-\x08\x0B-\x0C\x0E-\x1F]/', $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s %s',
					__( 'WARNING', 'theme-check' ),
					sprintf(
						__( 'Non-printable characters were found in the %s file. You may want to check this file for errors.', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					),
					$grep
				);
			}
		}

		return true;
	}

	function getError() {
		return $this->error;
	}
}

$themechecks[] = new NonPrintableCheck();
