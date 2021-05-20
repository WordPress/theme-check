<?php
class DeregisterCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		foreach ( $php_files as $file_path => $file_content ) {
			checkcount();

			if ( false !== strpos( $file_content, 'wp_deregister_script' ) ) {
				$grep          = tc_preg( '/wp_deregister_script/', $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s %s',
					__( 'WARNING', 'theme-check' ),
					sprintf(
						__( 'Found wp_deregister_script in %1$s. Themes must not deregister core scripts.', 'theme-check' ),
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
$themechecks[] = new DeregisterCheck();
