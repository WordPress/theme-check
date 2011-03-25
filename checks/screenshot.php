<?php
class Screenshot_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;
		$filenames = array();

		foreach ( $other_files as $other_key => $otherfile ) {
			array_push( $filenames, strtolower( basename( $other_key ) ) );
		}

		checkcount();
		if ( in_array( 'screenshot.png', $filenames ) ) {
			foreach ( $other_files as $other_key => $otherfile ) {
				if ( basename( $other_key ) === 'screenshot.png' && preg_match( '/.*themes\/[^\/]*\/screenshot\.png/', $other_key ))  {
					// we have or screenshot!
					$image = getimagesize( $other_key );
					if ( $image[0] > 320 || $image[1] > 240 ) {
						$this->error[] = __( "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: Screenshot is wrong size! Detected: <strong>{$image[0]}x{$image[1]}px</strong>. Maximum allowed size is 320x240px.", "themecheck" );
					}
				}
			}
		} else {
			$this->error[] = __( "<span class='tc-lead tc-warning'>WARNING</span>: No screenshot detected! Please include a screenshot.png.", "themecheck" );
			$ret = false;
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Screenshot_Checks;