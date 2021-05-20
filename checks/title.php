<?php
/**
 * Checks for the title:
 * Are there <title> and </title> tags?
 * Is there a call to wp_title()?
 * There can't be any hardcoded text in the <title> tag.
 *
 * See: https://make.wordpress.org/themes/handbook/review/required/theme-check-plugin/
 */
class Title_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		$php = implode( ' ', $php_files );

		// Look for add_theme_support( 'title-tag' ) first.
		$titletag = true;
		if ( ! preg_match( '#add_theme_support\s?\(\s?[\'|"]title-tag#', $php ) ) {
			$ret           = false;
			$titletag      = false;
			$this->error[] = sprintf(
				'<span class="tc-lead tc-required">%s</span>: %s',
				__( 'REQUIRED', 'theme-check' ),
				__( 'No reference to <strong>add_theme_support( "title-tag" )</strong> was found in the theme.', 'theme-check' )
			);
		}

		foreach ( $php_files as $file_path => $file_content ) {

			// Look for <title> and </title> tags.
			checkcount();
			if (
				! $titletag &&
				( false !== strpos( $php, '<title>' ) || false !== strpos( $php, '</title>' ) )
			) {
				$ret           = false;
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'The theme must not use the <strong>&lt;title&gt;</strong> tags. Found the tag in %1$s.', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					)
				);
			}

			// Check whether there is a call to wp_title().
			checkcount();
			if ( ! $titletag && false !== strpos( $php, 'wp_title(' ) ) {
				$ret           = false;
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'The theme must not call to <strong>wp_title()</strong>. Found wp_title() in %1$s.', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					)
				);
			}

			// Check whether the the <title> tag contains something besides a call to wp_title().
			checkcount();

			// Look for anything that looks like <svg>...</svg> and exclude it (inline svg's have titles too).
			$file_content = preg_replace( '/<svg.*>.*<\/svg>/s', '', $file_content );

			// First looks ahead to see of there's <title>...</title>.
			// Then performs a negative look ahead for <title> wp_title(...); </title>.
			if ( preg_match( '/(?=<title>(.*)<\/title>)(?!<title>\s*<\?php\s*wp_title\([^\)]*\);?\s*\?>\s*<\/title>)/s', $file_content ) ) {
				$ret           = false;
				$grep          = tc_preg( '/<title>/', $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( '%1$s: The <strong>&lt;title&gt;</strong> tags can only contain a call to <strong>wp_title()</strong>. Use the <strong>wp_title filter</strong> to modify the output.', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>'
					),
					$grep
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
