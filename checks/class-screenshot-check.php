<?php
/**
 * Check if the screenshot size is correct
 *
 * @package Theme Check
 */

/**
 * Check if the screenshot size is correct.
 */
class Screenshot_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {

		$ret       = true;
		$filenames = array();

		foreach ( $other_files as $other_key => $otherfile ) {
			array_push( $filenames, strtolower( basename( $other_key ) ) );
		}

		checkcount();

		if (
			in_array( 'screenshot.png', $filenames ) ||
			in_array( 'screenshot.jpg', $filenames )
		) {

			foreach ( $other_files as $other_key => $otherfile ) {

				if (
					(
						basename( $other_key ) === 'screenshot.png' ||
						basename( $other_key ) === 'screenshot.jpg'
					) &&
					preg_match( '/.*themes\/[^\/]*\/screenshot\.(png|jpg)/', $other_key )
				) {
					// we have our screenshot!
					$image = getimagesize( $other_key );
					if ( $image[0] > 1200 || $image[1] > 900 ) {
						$this->error[] = sprintf(
							'<span class="tc-lead tc-required">%s</span> %s',
							__( 'REQUIRED', 'theme-check' ),
							sprintf(
								__( 'Screenshot is wrong size! Detected: %s. Maximum allowed size is 1200x900px.', 'theme-check' ),
								'<strong>' . $image[0] . 'x' . $image[1] . '</strong>'
							)
						);
						$ret           = false;
					}
					if ( $image[1] / $image[0] != 0.75 ) {
						$this->error[] = sprintf(
							'<span class="tc-lead tc-required">%s</span> %s',
							__( 'REQUIRED', 'theme-check' ),
							__( 'Screenshot dimensions are wrong! Ratio of width to height should be 4:3.', 'theme-check' )
						);
						$ret           = false;
					}
					if ( $image[0] != 1200 || $image[1] != 900 ) {
						$this->error[] = sprintf(
							'<span class="tc-lead tc-recommended">%s</span> %s',
							__( 'RECOMMENDED', 'theme-check' ),
							__( 'Screenshot size should be 1200x900, to account for HiDPI displays. Any 4:3 image size is acceptable, but 1200x900 is preferred.', 'theme-check' )
						);
					}
				}
			}
		} else {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-required">%s</span> %s',
				__( 'REQUIRED', 'theme-check' ),
				__( 'No screenshot detected! Please include a screenshot.png or screenshot.jpg.', 'theme-check' )
			);
			$ret           = false;
		}
		return $ret;
	}

	/**
	 * Get error messages from the checks.
	 *
	 * @return array Error message.
	 */
	public function getError() {
		return $this->error;
	}
}

$themechecks[] = new Screenshot_Check();
