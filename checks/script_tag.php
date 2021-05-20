<?php
/**
 * Check if:
 * A <script> tag is included in header.php or footer.php
 */
class Script_Tag implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		foreach ( $php_files as $file_path => $file_content ) {
			checkcount();

			// This check is limited to header.php and footer.php.
			$filename = tc_filename( $file_path );
			if ( ! in_array( $filename, array( 'header.php', 'footer.php' ) ) ) {
				continue;
			}

			if ( false !== stripos( $file_content, '<script' ) ) {
				$grep          = tc_preg( '/<script/i', $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'Found a script tag in %s. Scripts and styles needs to be enqueued or added via a hook, not hard coded.', 'theme-check' ),
						'<strong>' . $filename . '</strong>'
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
$themechecks[] = new Script_Tag();
