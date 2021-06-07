<?php
/**
 * Checks if core scripts are deregistered.
 *
 * Themes must use WordPress’ default libraries.
 * WordPress includes a number of libraries such as jQuery.
 * For security and stability reasons themes may not include those libraries in their own code.
 * Instead themes must use the versions of those libraries packaged with WordPress.
 *
 * @package Theme Check
 */

/**
 * Checks if core scripts are deregistered.
 */
class Deregister_Check implements themecheck {
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
						__( 'Found wp_deregister_script in %1$s. Themes must not deregister scripts that are included in WordPress. Deregistering third party scripts that are registered by parent themes is allowed.', 'theme-check' ),
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

$themechecks[] = new Deregister_Check();
