<?php

class VersionCheck implements themecheck {
	protected $error = array();

	function tc_templatepath( $file ) {
		$filename = ( preg_match( '/themes\/([a-z0-9-]*\/.*)/', $file, $out ) ) ? $out[1] : basename( $file );
		return $filename;
	}

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		checkcount();

		$checks = array(
			'[ \t\/*#]@version' => '@version',
			);

		foreach ( $php_files as $php_key => $phpfile ) {

			// leave out functions.php as this is a special case
			if ( tc_filename( $php_key ) == 'functions.php' ) { continue; }

			foreach ($checks as $key => $check) {

				// We don't need to write to the file, so just open for reading.
				// This is needed, because comments are stripped out in the main plugin
				$fp = fopen( $php_key, 'r' );

				// Pull only the first 8kiB of the file in.
				$file_data = fread( $fp, 8192 );

				// PHP will close file handle, but we are good citizens. :)
				fclose( $fp );

				// Make sure we catch CR-only line endings.
				$file_data = str_replace( "\r", "\n", $file_data );

				if ( !preg_match( '/' . $key . '/i', $file_data, $matches ) ) {

					//$filename = tc_filename( $php_key );
					$filename = self::tc_templatepath( $php_key );

					// Point out the missing line.
					$error_msg = sprintf(
						__( '%1$s is missing in the file %2$s.', 'theme-check' ),
						'<strong>' . $check . '</strong>',
						'<strong>' . $filename . '</strong>'
					);

					// Add the finalized error message.
					$this->error[] = '<span class="tc-lead tc-recommended">' . __('RECOMMENDED','theme-check') . '</span>: ' . $error_msg;
						
				}
			}

		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new VersionCheck;