<?php
/**
 * Checks if jQuery is loaded by the theme.
 *
 * @package Theme Check
 */

/**
 * Checks if jQuery is loaded by the theme.
 *
 * Themes load jQuery both by enqueueing it directly and by listing it as a dependency
 * of one of their own scripts, so both cases are reported.
 */
class JQuery_Check implements themecheck {
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
		/*
		 * The jQuery script handles registered by WordPress: `jquery` itself, the
		 * `jquery-core` and `jquery-migrate` scripts it is built from, and the
		 * `jquery-ui-*` family, every member of which depends on `jquery`. Enqueueing
		 * any of them - or depending on any of them - results in jQuery being loaded.
		 */
		$handle = 'jquery(?:-core|-migrate|-ui-[a-z0-9_-]+)?';

		// jQuery enqueued directly, for example wp_enqueue_script( 'jquery' ).
		$direct_regex = '/\bwp_enqueue_script\s*\(\s*([\'"])' . $handle . '\1/i';

		/*
		 * jQuery pulled in as a dependency of another script, for example
		 * wp_enqueue_script( 'theme-script', $src, array( 'jquery' ), $ver, true ) or the
		 * short array syntax [ 'jquery' ]. `[^;]` keeps the match inside a single
		 * statement, so the dependency list has to belong to the call that opened it.
		 */
		$dependency_regex = '/\bwp_(?:enqueue|register)_script\s*\([^;]{0,300}?(?:array\s*\(|\[)[^)\]]*?([\'"])' . $handle . '\1/i';

		checkcount();

		foreach ( $php_files as $file_path => $file_content ) {
			if ( ! preg_match( $direct_regex, $file_content ) && ! preg_match( $dependency_regex, $file_content ) ) {
				continue;
			}

			$grep = tc_preg( '/jquery/i', $file_path );

			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s %s',
				__( 'RECOMMENDED', 'theme-check' ),
				sprintf(
					/* translators: 1: file name, 2: URL of an article about the performance impact of jQuery. */
					__( 'jQuery is loaded by %1$s, either enqueued directly or listed as a dependency of another script. You may not need it, see <a href="%2$s">this article</a> for tips on switching to vanilla JavaScript.', 'theme-check' ),
					'<strong>' . tc_filename( $file_path ) . '</strong>',
					'https://make.wordpress.org/themes/2021/10/04/the-performance-impact-of-using-jquery-in-wordpress-themes/'
				),
				$grep
			);
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

$themechecks[] = new JQuery_Check();
