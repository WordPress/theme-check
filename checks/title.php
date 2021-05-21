<?php
/**
 * Checks for the title:
 * Is there a call to wp_title()?
 * Are there <title> and </title> tags?
 *
 * See: https://make.wordpress.org/themes/handbook/review/required/theme-check-plugin/
 */
class Title_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		$php = implode( ' ', $php_files );

		foreach ( $php_files as $file_path => $file_content ) {

			// Check whether there is a call to wp_title().
			checkcount();
			if ( false !== strpos( $file_content, 'wp_title(' ) ) {
				$ret           = false;
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'The theme must not use <strong>wp_title()</strong>. Found wp_title() in %1$s.', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					)
				);
			}

			// Look for anything that looks like <svg>...</svg> and exclude it (inline svg's have titles too).
			$file_content = preg_replace( '/<svg.*>.*<\/svg>/s', '', $file_content );

			// Look for <title> and </title> tags.
			checkcount();
			if ( ( false !== strpos( $file_content, '<title>' ) ) || ( false !== strpos( $file_content, '</title>' ) ) ) {
				$ret           = false;
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'The theme must not use <strong>&lt;title&gt;</strong> tags. Found the tag in %1$s.', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					)
				);
			}
		}

		return $ret;
	}

	function getError() {
		return $this->error;
	}
}

$themechecks[] = new Title_Checks();
