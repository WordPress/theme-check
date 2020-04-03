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
			$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'No reference to <strong>add_theme_support( "title-tag" )</strong> was found in the theme.', 'theme-check' );
			$titletag      = false;
			$ret           = false;
		}

		foreach ( $php_files as $file_path => $file_content ) {

			$filename = tc_filename( $file_path );

			// Look for <title> and </title> tags.
			checkcount();
			if ( ( 0 <= strpos( $php, '<title>' ) || 0 <= strpos( $php, '</title>' ) ) && ! $titletag  ) {
				$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . sprintf( __( 'The theme must not use the <strong>&lt;title&gt;</strong> tags. Found the tag in %1$s.', 'theme-check' ),
					'<strong>' . $filename . '</strong>'
				);
				$ret           = false;
			}

			// Check whether there is a call to wp_title().
			checkcount();
			if ( 0 <= strpos( $php, 'wp_title(' ) && ! $titletag ) {
				$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . sprintf( __( 'The theme must not call to <strong>wp_title()</strong>. Found wp_title() in %1$s.', 'theme-check' ),
					'<strong>' . $filename . '</strong>'
				);
				$ret           = false;
			}

			// Check whether the the <title> tag contains something besides a call to wp_title().
			checkcount();
			// Look for anything that looks like <svg>...</svg> and exclude it (inline svg's have titles too).
			$file_content = preg_replace( '/<svg.*>.*<\/svg>/s', '', $file_content );
			// First looks ahead to see of there's <title>...</title>.
			// Then performs a negative look ahead for <title> wp_title(...); </title>.
			$error = '/<title>/';
			$grep  = tc_preg( $error, $file_path );

			if ( preg_match( '/(?=<title>(.*)<\/title>)(?!<title>\s*<\?php\s*wp_title\([^\)]*\);?\s*\?>\s*<\/title>)/s', $file_content ) ) {
				$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check').'</span>: ' . sprintf( __( '%1$s: The <strong>&lt;title&gt;</strong> tags can only contain a call to <strong>wp_title()</strong>. Use the <strong>wp_title filter</strong> to modify the output.', 'theme-check' ),
					'<strong>' . $filename . '</strong>'
				) . $grep;
				$ret           = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Title_Checks();
