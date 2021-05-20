<?php
class PHPShortTagsCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		foreach ( $php_files as $file_path => $file_content ) {
			checkcount();

			if ( preg_match( '/<\?(\=?)(?!php|xml)/i', $file_content ) ) {
				$grep          = tc_preg( '/<\?(\=?)(?!php|xml)/', $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s %s',
					__( 'WARNING', 'theme-check' ),
					sprintf(
						__( 'Found PHP short tags in file %s.', 'theme-check' ),
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

$themechecks[] = new PHPShortTagsCheck();
