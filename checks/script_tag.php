<?php
/**
 * Check if:
 * A <script> tag is included in header.php or footer.php
 */
class Script_Tag implements themecheck {
	protected $error = array();

		function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		checkcount();
		/**
		 * This check is limited to header.php and footer.php.
		 */
		foreach ( $php_files as $file_path => $file_content ) {
			$filename = tc_filename( $file_path );
			if ( ! in_array( $filename, array( 'header.php', 'footer.php' ) ) ) {
				continue;
			}

			if ( preg_match( '/<script/i', $file_content ) ) {
				$error         = '/<script/i';
				$grep          = tc_preg( $error, $file_path );
				$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Found a script tag in %s. Scripts and styles needs to be enqueued or added via a hook, not hard coded.', 'theme-check' ),
				'<strong>' . $filename . '</strong>' ) . $grep;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Script_Tag();
