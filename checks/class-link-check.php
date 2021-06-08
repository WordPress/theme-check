<?php
/**
 * Check if there are hard-coded links
 *
 * @package Theme Check
 */

/**
 * Check if there are hard-coded links
 *
 * Check if there are hard-coded links other than theme or author URI.
 */
class Link_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Theme information. Author URI, theme URI, Author name
	 *
	 * @var object $theme
	 */
	protected $theme;

	function set_context( $data ) {
		if ( isset( $data['theme'] ) ) {
			$this->theme = $data['theme'];
		}
	}

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {

		foreach ( $php_files as $php_key => $phpfile ) {
			checkcount();
			$grep = '';
			// Regex borrowed from TAC.
			$url_re   = '([[:alnum:]\-\.])+(\\.)([[:alnum:]]){2,4}([[:blank:][:alnum:]\/\+\=\%\&\_\\\.\~\?\-]*)';
			$title_re = '[[:blank:][:alnum:][:punct:]]*';   // 0 or more: any num, letter(upper/lower) or any punc symbol.
			$space_re = '(\\s*)';
			if ( preg_match_all( '/(<a)(\\s+)(href' . $space_re . '=' . $space_re . '"' . $space_re . '((http|https|ftp):\\/\\/)?)' . $url_re . '("' . $space_re . $title_re . $space_re . '>)' . $title_re . '(<\\/a>)/is', $phpfile, $out, PREG_SET_ORDER ) ) {
				$filename = tc_filename( $php_key );
				foreach ( $out as $key ) {
					if ( preg_match( '/\<a\s?href\s?=\s?["|\'](.*?)[\'|"](.*?)\>(.*?)\<\/a\>/is', $key[0], $stripped ) ) {
						if (
							! empty( $this->theme->get( 'AuthorURI' ) ) &&
							! empty( $this->theme->get( 'ThemeURI' ) ) &&
							$stripped[1] &&
							! strpos( $stripped[1], $this->theme->get( 'ThemeURI' ) ) &&
							! strpos( $stripped[1], $this->theme->get( 'AuthorURI' ) ) &&
							! stripos( $stripped[1], 'WordPress.' )
						) {
							$grep .= tc_grep( $stripped[1], $php_key );
						}
					}
				}
				if ( $grep ) {
					$this->error[] = sprintf(
						'<span class="tc-lead tc-info">%s</span>: %s %s',
						__( 'INFO', 'theme-check' ),
						sprintf(
							__( 'Possible hard-coded links were found in the file %s.', 'theme-check' ),
							'<strong>' . $filename . '</strong>'
						),
						$grep
					);
				}
			}
		}

		return true;
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

$themechecks[] = new Link_Check();
