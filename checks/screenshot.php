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
					if ( $image[0] > 640 || $image[1] > 480 ) {
						$this->error[] = sprintf(__('<span class="tc-lead tc-recommended">RECOMMENDED</span>: Screenshot is wrong size! Detected: <strong>%1$sx%2$spx</strong>. Maximum allowed size is 640x480px.', 'themecheck'), $image[0], $image[1]);
					}
					if ( $image[1] / $image[0] != 0.75 ) {
						$this->error[] = __('<span class="tc-lead tc-recommended">RECOMMENDED</span>: Screenshot dimensions are wrong! Ratio of width to height should be 4:3.', 'themecheck');
					}
					if ( $image[0] != 600 || $image[1] != 450 ) {
						$this->error[] = __('<span class="tc-lead tc-recommended">RECOMMENDED</span>: Screenshot size should be 600x450, to account for HiDPI displays. Any 4:3 image size is acceptable, but 600x450 is preferred.', 'themecheck');
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